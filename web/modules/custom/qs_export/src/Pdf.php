<?php

namespace Drupal\qs_export;

use Dompdf\Dompdf;
use Dompdf\Options;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Render\Renderer;

/**
 * Pdf exporter for Quartiers-Solidaires.
 *
 * Ensure a standard format for every Pdf export.
 */
class Pdf {
  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * Constructs a new Pdf instance.
   *
   * @param \Drupal\Core\Render\Renderer $renderer
   *   The renderer service.
   */
  public function __construct(Renderer $renderer) {
    $this->renderer = $renderer;
  }

  /**
   * Download the pdf.
   *
   * @param string $templateName
   *   The template name.
   * @param array $variables
   *   The variables to give to the template
   *
   *   The PDF generator for a giver template.
   */
  public function download($templateName, array $variables) {
    // Instantiate the dompdf options.
    $options = new Options();
    // $options->set('defaultFont', 'Open Sans');
    $options->set('defaultPaperSize', 'a4');

    // Instantiate the dompdf.
    $dompdf = new Dompdf($options);

    $now = new DrupalDateTime();

    $variables['update'] = $now->format('d.m.y');

    // Twig template to be rendered.
    $template = [
      '#theme' => $templateName,
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
      ->page_text(298, 815, "{PAGE_NUM}/{PAGE_COUNT}", $font, 12, [0, 0, 0]);

    $dompdf->stream('offres_' . $now->format('d_m_Y') . '.pdf');
  }

}
