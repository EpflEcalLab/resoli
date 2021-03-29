<?php

namespace Drupal\qs_export;

use Drupal\Component\Datetime\DateTimePlus;
use Drupal\Component\Utility\Html;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Helper\Html as PhpSpreadsheetHtml;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Excel exporter for Quartiers-Solidaires.
 *
 * Ensure a standard format for every Excel export.
 */
class Excel {

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
   * The spreadsheet.
   *
   * @var \PhpOffice\PhpSpreadsheet\Spreadsheet
   */
  private $spreadsheet;

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
   * Add a header to the spreadsheet.
   *
   * @param array $items
   *   The values of the header.
   * @param int $offset
   *   The header starting row offset.
   * @param array|null $styles
   *   Styles that will applied to the header.
   *
   * @throws \PhpOffice\PhpSpreadsheet\Exception
   */
  public function addHeader(array $items, int $offset = 2, ?array $styles = []) {
    $worksheet = $this->spreadsheet->getActiveSheet();
    $row = $worksheet->getHighestRow() + $offset;

    $i = 1;

    foreach ($items as $item) {
      $cell = $worksheet->getCellByColumnAndRow($i++, $row);
      $cell->getStyle()->getFont()->setBold(TRUE);
      $cell->getStyle()->getFont()->setSize(18);

      if (isset($styles['foreground'])) {
        $cell->getStyle()->getFont()->getColor()->setARGB($styles['foreground']);
      }

      $cell->getStyle()->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);
      $cell->getStyle()->getBorders()->getTop()->setBorderStyle(Border::BORDER_MEDIUM);
      $cell->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
      $cell->getStyle()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

      if (isset($styles['background'])) {
        $cell->getStyle()
          ->getFill()
          ->setFillType(Fill::FILL_SOLID)
          ->getStartColor()
          ->setARGB($styles['background']);
      }

      $safe_string = htmlspecialchars_decode($item, \ENT_QUOTES);
      $cell->setValue($safe_string);
    }

    if (isset($styles['repeat'])) {
      $worksheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($row, $row);
    }

    $worksheet->getRowDimension($row)->setRowHeight(23);
  }

  /**
   * Add row to the spreadsheet.
   *
   * @param array $items
   *   The values of the row.
   * @param array|null $styles
   *   The global styles for each rows, that can be overridden per row.
   */
  public function addRow(array $items, ?array $styles = []): void {
    $worksheet = $this->spreadsheet->getActiveSheet();

    $col = 1;
    $row = $worksheet->getHighestRow() + 1;

    foreach ($items as $item) {
      $cell = $worksheet->getCellByColumnAndRow($col++, $row);
      $cell->getStyle()->getFont()->setSize(12);

      // Get the raw values to write.
      $content = $item['value'];

      // Merge the Cell styles with the global row styles.
      if (isset($item['styles'])) {
        $styles = array_merge($styles, $item['styles']);
      }

      switch (TRUE) {
        case $content instanceof DateTimePlus:
          // Set the number format mask so that the excel timestamp will be
          // displayed as a human-readable date/time.
          $date = Date::PHPToExcel($content->getTimestamp());
          $cell->getStyle()->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DATETIME);
          $cell->setValue($date);

          break;

        case $content instanceof RichText:
          $cell->setValue($content);

          break;
        // Skip unprocessable content.
        case $content instanceof PhpSpreadsheetHtml:
          break;

        default:
          $safe_string = htmlspecialchars_decode($content, \ENT_QUOTES);
          $cell->setValue($safe_string);

          break;
      }

      if (isset($styles['txt-wrap'])) {
        $cell->getStyle()->getAlignment()->setWrapText(TRUE);
      }

      // Vertical align using the global style.
      if (isset($styles['v-alignment'])) {
        $cell->getStyle()->getAlignment()->setVertical($styles['v-alignment']);
      }

      // Horizontal align using the global style.
      if (isset($styles['h-alignment'])) {
        $cell->getStyle()->getAlignment()->setHorizontal($styles['v-alignment']);
      }

      // Odd & Even colors.
      if (isset($styles['odd-even-background']) && $row % 2) {
        $cell->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('d9d9d9');
      }
    }

    $worksheet->getRowDimension($row)->setRowHeight(32);
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
      static function () use ($writer) {
          $writer->save('php://output');
        }
    );

    $response->headers->set('Content-Type', 'application/vnd.ms-excel');
    $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
    $response->headers->set('Cache-Control', 'max-age=0');

    return $response;
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

    for ($i = 1; $i <= $highest_col_index; ++$i) {
      $worksheet
        ->getColumnDimension(Coordinate::stringFromColumnIndex($i))
        ->setWidth(40);
    }
  }

  /**
   * Initialize the spreadsheet.
   */
  public function init() {
    $this->spreadsheet = new Spreadsheet();

    $worksheet = $this->spreadsheet->getActiveSheet();
    $worksheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
    $worksheet->getPageSetup()->setFitToWidth(1);
  }

  /**
   * Style the last row to add a bottom border to it.
   */
  public function lastRowBorder(): void {
    $worksheet = $this->spreadsheet->getActiveSheet();

    $last_row = $worksheet->getHighestRow();
    $highest_col = $worksheet->getHighestColumn();
    $highest_col_index = Coordinate::columnIndexFromString($highest_col);

    for ($col = 1; $col <= $highest_col_index; ++$col) {
      $cell = $worksheet->getCellByColumnAndRow($col, $last_row);
      $cell->getStyle()->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);
    }
  }

  /**
   * Set the default styles.
   */
  public function normalize() {
    $this->spreadsheet->getDefaultStyle()->getFont()->setName('Calibri');
    $this->spreadsheet->getDefaultStyle()->getFont()->setSize(10);
  }

  /**
   * Set the Speadsheet columns dimensions.
   *
   * @param array $dimensions
   *   Collection of dimension keyed by column letter and dimension values.
   */
  public function selColDimensions(array $dimensions): void {
    $worksheet = $this->spreadsheet->getActiveSheet();

    foreach ($dimensions as $column => $dimension) {
      if ($dimension['width']) {
        $worksheet->getColumnDimension($column)->setAutoSize(FALSE);
        $worksheet->getColumnDimension($column)->setWidth($dimension['width']);
      }
    }
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
    $safe_footer = htmlspecialchars_decode($footer, \ENT_QUOTES);

    $this->footer['row'] = $worksheet->getHighestRow() + 3;

    $worksheet->setCellValue($this->footer['col'] . $this->footer['row'], $safe_footer);
    $worksheet->getStyle($this->footer['col'] . $this->footer['row'])->getFont()->setItalic(TRUE);
    $worksheet->getStyle($this->footer['col'] . $this->footer['row'])->getFont()->getColor()->setARGB('535353');
    $worksheet->getStyle($this->footer['col'] . $this->footer['row'])->getFont()->setSize(9);
  }

  /**
   * Set the Spreadsheet title.
   *
   * @param string $title
   *   The caption to use.
   */
  public function setSpreadsheetTitle($title): void {
    $safe_title = htmlspecialchars_decode($title, \ENT_QUOTES);
    $this->spreadsheet->getProperties()
      ->setTitle($safe_title);
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
    $safe_summary = htmlspecialchars_decode($summary, \ENT_QUOTES);

    $worksheet->setCellValue($this->summary['col'] . $this->summary['row'], $safe_summary);
    $worksheet->getStyle($this->summary['col'] . $this->summary['row'])->getFont()->setSize(11);
  }

  /**
   * Set the title.
   *
   * @param string $title
   *   The caption to use.
   */
  public function setTitle($title): void {
    $this->setSpreadsheetTitle($title);
    $safe_title = htmlspecialchars_decode($title, \ENT_QUOTES);

    $this->title['visible'] = TRUE;

    $worksheet = $this->spreadsheet->getActiveSheet();
    $worksheet->setCellValue($this->title['col'] . $this->title['row'], $safe_title);
    $worksheet->getStyle($this->title['col'] . $this->title['row'])->getFont()->setBold(TRUE);
    $worksheet->getStyle($this->title['col'] . $this->title['row'])->getFont()->setSize(20);
  }

}
