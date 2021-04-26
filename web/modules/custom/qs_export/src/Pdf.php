<?php

namespace Drupal\qs_export;

use Dompdf\Dompdf;
use Dompdf\Options;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Pdf exporter for Quartiers-Solidaires.
 *
 * Ensure a standard format for every Pdf export.
 */
class Pdf {

  /**
   * Download the pdf.
   *
   * @param string $templateName
   * @param array $variables
   *
   * @return void
   *   The PDF generator for a giver template
   */
  public function download($templateName, $variables) {
    // Instantiate the dompdf options
    $options = new Options();
    //$options->set('defaultFont', 'Open Sans');
    $options->set('defaultPaperSize', 'a4');

    // Instantiate the dompdf
    $dompdf = new Dompdf($options);

    $now = new DrupalDateTime();

    $variables['update'] = $now->format('d.m.y');

    // Twig template to be rendered
    $template = [
      '#theme' => $templateName,
      '#variables' => $variables,
    ];
    $rendered = \Drupal::service('renderer')->render($template);

    $dompdf->loadHtml($rendered);
    // Render the HTML as PDF
    $dompdf->render();

    // Set the footer with the pagination
    $font = $dompdf->getFontMetrics()->getFont('Helvetica', 'normal');
    $dompdf->getCanvas()->page_text(298, 815, "{PAGE_NUM}/{PAGE_COUNT}", $font, 12, [0, 0, 0]);

    $dompdf->stream('offres_' . $now->format('d_m_Y') . '.pdf');
  }
}
