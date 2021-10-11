<?php

namespace Drupal\qs_site\TwigExtension;

use Drupal\bamboo_twig\TwigExtension\TwigExtensionBase;
use Twig\TwigFilter;

/**
 * Provides a 'Text' Twig Extensions.
 */
class Text extends TwigExtensionBase {

  /**
   * List of all Twig functions.
   */
  public function getFilters() {
    return [
      new TwigFilter('qs_striptags', [
        $this, 'striptags',
      ]),
    ];
  }

  /**
   * Unique identifier for this Twig extension.
   */
  public function getName() {
    return 'qs_site.twig.text';
  }

  /**
   * Strip HTML and PHP tags from a string.
   *
   * @param string $string
   *   The input string.
   * @param string $allowed_tags
   *   Optional parameter to specify tags which should not be stripped.
   *
   * @return string
   *   the stripped string.
   */
  public function striptags(string $string, string $allowed_tags = ''): string {
    return strip_tags($string, $allowed_tags);
  }

}
