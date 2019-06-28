<?php

namespace Drupal\qs_export;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Drupal\Component\Utility\Html;
use Drupal\Component\Datetime\DateTimePlus;

/**
 * Excel exporter for Quartiers-Solidaires.
 *
 * Ensure a standard format for every Excel export.
 */
class Excel {

  /**
   * The spreadsheet.
   *
   * @var \PhpOffice\PhpSpreadsheet\Spreadsheet
   */
  private $spreadsheet;

  /**
   * Reminder if the spreadsheet has a title.
   *
   * @var array
   */
  private $title = [
    'visible' => FALSE,
    'col' => 'A',
    'row' => '1',
  ];

  /**
   * Reminder if the spreadsheet has a summary.
   *
   * @var array
   */
  private $summary = [
    'visible' => FALSE,
    'col' => 'A',
    'row' => '2',
  ];

  /**
   * Reminder if the spreadsheet has a summary.
   *
   * @var array
   */
  private $footer = [
    'visible' => FALSE,
    'col' => 'A',
    'row' => NULL,
  ];

  /**
   * Initialize the spreadsheet.
   */
  public function init() {
    $this->spreadsheet = new Spreadsheet();
  }

  /**
   * Set the title.
   *
   * @param string $title
   *   The caption to use.
   */
  public function setTitle($title) {
    $this->title['visible'] = TRUE;

    $worksheet = $this->spreadsheet->getActiveSheet();
    $safe_title = htmlspecialchars_decode($title, ENT_QUOTES);

    $this->spreadsheet->getProperties()
      ->setTitle($safe_title);

    $worksheet->setCellValue($this->title['col'] . $this->title['row'], $safe_title);
    $worksheet->getStyle($this->title['col'] . $this->title['row'])->getFont()->setBold(TRUE);
    $worksheet->getStyle($this->title['col'] . $this->title['row'])->getFont()->setSize(20);
  }

  /**
   * Set the summary.
   *
   * @param string $summary
   *   The summary to use.
   */
  public function setSummary($summary) {
    $this->summary['visible'] = TRUE;

    $worksheet = $this->spreadsheet->getActiveSheet();
    $safe_summary = htmlspecialchars_decode($summary, ENT_QUOTES);

    $worksheet->setCellValue($this->summary['col'] . $this->summary['row'], $safe_summary);
    $worksheet->getStyle($this->summary['col'] . $this->summary['row'])->getFont()->setSize(11);
  }

  /**
   * Set the footer.
   *
   * @param string $footer
   *   The footer to use.
   */
  public function setFooter($footer) {
    $this->footer['visible'] = TRUE;

    $worksheet = $this->spreadsheet->getActiveSheet();
    $safe_footer = htmlspecialchars_decode($footer, ENT_QUOTES);

    $this->footer['row'] = $worksheet->getHighestRow() + 3;

    $worksheet->setCellValue($this->footer['col'] . $this->footer['row'], $safe_footer);
    $worksheet->getStyle($this->footer['col'] . $this->footer['row'])->getFont()->setItalic(TRUE);
    $worksheet->getStyle($this->footer['col'] . $this->footer['row'])->getFont()->getColor()->setARGB('535353');
    $worksheet->getStyle($this->footer['col'] . $this->footer['row'])->getFont()->setSize(9);
  }

  /**
   * Set the default styles.
   */
  public function normalize() {
    $this->spreadsheet->getDefaultStyle()->getFont()->setName('Calibri');
    $this->spreadsheet->getDefaultStyle()->getFont()->setSize(10);
  }

  /**
   * Finalize the spreadsheet format & styles.
   *
   * Some styles may only apply once the whole file has been filled.
   */
  public function finalize() {
    $worksheet = $this->spreadsheet->getActiveSheet();

    // Merge the title into one single cell.
    if ($this->title['visible']) {
      $worksheet->mergeCells($this->title['col'] . $this->title['row'] . ':' . $worksheet->getHighestColumn() . $this->title['row']);
    }
    if ($this->summary['visible']) {
      $worksheet->mergeCells($this->summary['col'] . $this->summary['row'] . ':' . $worksheet->getHighestColumn() . $this->summary['row']);
    }
    if ($this->footer['visible']) {
      $worksheet->mergeCells($this->footer['col'] . $this->footer['row'] . ':' . $worksheet->getHighestColumn() . $this->footer['row']);
    }

    $highest_col = $worksheet->getHighestColumn();
    $highest_col_index = Coordinate::columnIndexFromString($highest_col);

    for ($i = 1; $i <= $highest_col_index; $i++) {
      $worksheet
        ->getColumnDimension(Coordinate::stringFromColumnIndex($i))
        ->setWidth(40);
    }
  }

  /**
   * Add a header to the spreadsheet.
   *
   * @param array $items
   *   The values of the header.
   */
  public function addHeader(array $items) {
    $worksheet = $this->spreadsheet->getActiveSheet();
    $row = $worksheet->getHighestRow() + 2;

    $i = 1;
    foreach ($items as $item) {
      $cell = $worksheet->getCellByColumnAndRow($i++, $row);
      $cell->getStyle()->getFont()->setBold(TRUE);
      $cell->getStyle()->getFont()->setSize(16);

      $safe_string = htmlspecialchars_decode($item, ENT_QUOTES);
      $cell->setValue($safe_string);
    }
  }

  /**
   * Add row to the spreadsheet.
   *
   * @param array $items
   *   The values of the row.
   */
  public function addRow(array $items) {
    $worksheet = $this->spreadsheet->getActiveSheet();

    $col = 1;
    $row = $worksheet->getHighestRow() + 1;

    foreach ($items as $item) {
      $cell = $worksheet->getCellByColumnAndRow($col++, $row);

      switch (TRUE) {
        case $item instanceof DateTimePlus:
          // Set the number format mask so that the excel timestamp will be
          // displayed as a human-readable date/time.
          $date = Date::PHPToExcel($item->getTimestamp());
          $cell->getStyle()->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DATETIME);
          $cell->setValue($date);
          break;

        default:
          $safe_string = htmlspecialchars_decode($item, ENT_QUOTES);
          $cell->setValue($safe_string);
          break;
      }

      // Odd & Even colors.
      if ($row % 2) {
        $cell->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('EBEBEB');
      }
    }

    $worksheet->getRowDimension($row)->setRowHeight(20);
  }

  /**
   * Download the spreadsheet.
   *
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   *   The spreadsheet usable on Excel.
   */
  public function download() {
    $writer = new Xlsx($this->spreadsheet);

    $filename = Html::cleanCssIdentifier($this->spreadsheet->getProperties()->getTitle()) . '.xlsx';

    $response = new StreamedResponse(
        function () use ($writer) {
            $writer->save('php://output');
        }
    );

    $response->headers->set('Content-Type', 'application/vnd.ms-excel');
    $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
    $response->headers->set('Cache-Control', 'max-age=0');

    return $response;
  }

}
