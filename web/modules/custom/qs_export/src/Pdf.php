<?php

namespace Drupal\qs_export;

use Dompdf\Dompdf;
use Dompdf\Options;
use Drupal\Component\Utility\Html;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Render\Renderer;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Pdf exporter for Quartiers-Solidaires.
 *
 * Ensure a standard format for every Pdf export.
 */
class Pdf {

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * The PDF.
   *
   * @var \Dompdf\Dompdf
   */
  private $dompdf;

  /**
   * The PDF title.
   *
   * @var string|null
   */
  private $title;

  /**
   * Constructs a new Pdf instance.
   *
   * @param \Drupal\Core\Datetime\DateFormatter $date_formatter
   *   The date formatter service.
   * @param \Drupal\Core\Render\Renderer $renderer
   *   The renderer service.
   */
  public function __construct(DateFormatter $date_formatter, Renderer $renderer) {
    $this->dateFormatter = $date_formatter;
    $this->renderer = $renderer;
  }

  /**
   * Add a footer pagination.
   */
  public function addPagination(): void {
    // Set the footer with the pagination.
    $font = $this->dompdf->getFontMetrics()->getFont('Helvetica', 'normal');
    $this->dompdf
      ->getCanvas()
      ->page_text(298, 815, '{PAGE_NUM}/{PAGE_COUNT}', $font, 12, [0, 0, 0]);
  }

  /**
   * Download the pdf.
   *
   * Generate a standard PDF output with the given data.
   *
   * @return \Symfony\Component\HttpFoundation\StreamedResponse
   *   The spreadsheet usable on Excel.
   */
  public function download(): StreamedResponse {
    $filename = Html::cleanCssIdentifier($this->title) . '.pdf';

    $pdf = $this->dompdf;
    $response = new StreamedResponse(
      static function () use ($pdf, $filename) {
        $pdf->stream($filename);
      }
    );

    $response->headers->set('Content-Type', 'application/pdf');
    $response->headers->set('Cache-Control', 'max-age=0');

    return $response;
  }

  /**
   * Initialize the PDF.
   */
  public function init(): void {
    // Instantiate the dompdf options.
    $options = new Options();
    $options->set('defaultPaperSize', 'a4');

    // Instantiate the dompdf.
    $this->dompdf = new Dompdf($options);
  }

  /**
   * Load and render given Twig template as HTML.
   */
  public function setContent(string $template_name, array $variables): void {
    $now = new DrupalDateTime();
    $variables['update'] = $this->dateFormatter->format($now->getTimestamp(), 'default_medium_date_only');

    // Twig template to be rendered.
    $template = [
      '#theme' => $template_name,
      '#variables' => $variables,
    ];

    $rendered = $this->renderer->render($template);

    $this->dompdf->loadHtml($rendered);
    // Render the HTML as PDF.
    $this->dompdf->render();
  }

  /**
   * Initialize the PDF.
   */
  public function setTitle(string $title): self {
    $this->title = $title;

    return $this;
  }

}
