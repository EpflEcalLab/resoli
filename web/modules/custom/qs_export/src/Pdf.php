<?php

namespace Drupal\qs_export;

use Dompdf\Dompdf;
use Dompdf\Options;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Render\Renderer;

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
   * Download the pdf.
   *
   * Generate a standard PDF output with the given data.
   *
   * @param string $template_name
   *   The template name.
   * @param array $variables
   *   The variables to be given to the template.
   * @param string $document_title
   *   The name of the outputted PDF.
   */
  public function download(string $template_name, array $variables, string $document_title): void {
    // Instantiate the dompdf options.
    $options = new Options();
    $options->set('defaultPaperSize', 'a4');

    // Instantiate the dompdf.
    $dompdf = new Dompdf($options);

    $now = new DrupalDateTime();

    $variables['update'] = $this->dateFormatter->format($now->getTimestamp(), 'default_medium_date_only');

    // Twig template to be rendered.
    $template = [
      '#theme' => $template_name,
      '#variables' => $variables,
    ];

    $rendered = $this->renderer->render($template);

    $dompdf->loadHtml($rendered);
    // Render the HTML as PDF.
    $dompdf->render();

    // Set the footer with the pagination.
    $font = $dompdf->getFontMetrics()->getFont('Helvetica', 'normal');
    $dompdf
      ->getCanvas()
      ->page_text(298, 815, '{PAGE_NUM}/{PAGE_COUNT}', $font, 12, [0, 0, 0]);

    $dompdf->stream($document_title . '_' . $this->dateFormatter->format($now->getTimestamp(), 'html_date') . '.pdf');
  }

}
