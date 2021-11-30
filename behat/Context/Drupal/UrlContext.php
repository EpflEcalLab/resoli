<?php

namespace Drupal\Behat\Context\Drupal;

use Behat\Gherkin\Node\TableNode;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use PHPUnit\Framework\Assert as PHPUnit_Framework_Assert;

/**
 * Defines url features from the specific context.
 */
class UrlContext extends RawDrupalContext {

  /**
   * Ensure the current page URL matches expression w/ GET params.
   *
   * Example: The url should match "/login" with parameter:
   * | username |
   * | wengerk |
   * Example: The url should match "/forget-password" with parameters:
   * | username | mail |
   * | wengerk | wenger.kev@gmail.com |
   *
   * @Given /the (?i)url(?-i) should match (?P<pattern>"(?:[^"]|\\")*") with parameter(s?):/
   */
  public function theUrlShouldMatchWithParameters($pattern, TableNode $parameters): void {
    $assert_session = $this->assertSession();
    $assert_session->addressMatches($this->fixStepArgument($pattern));
    $url = $this->getSession()->getCurrentUrl();

    $parsed_url = parse_url($url);

    if (!isset($parsed_url['query']) || empty($parsed_url['query'])) {
      throw new \Exception(sprintf('Missing GET parameters from "%s".', urldecode($url)));
    }

    $query = NULL;
    parse_str($parsed_url['query'], $query);
    $decoded_query = urldecode($parsed_url['query']);

    $properties = $parameters->getRow(0);
    $values = $parameters->getRow(1);

    // Assert the proper number of query parameters are found.
    PHPUnit_Framework_Assert::assertCount(count($properties), $query);

    foreach ($properties as $index => $property) {
      $parts = NULL;
      $has_match = preg_match('~(' . preg_quote($property, '~') . ')=([^&]+)~', $decoded_query, $parts);

      if (!$has_match) {
        throw new \Exception(sprintf('Failed asserting property "%s" exists on URL parameters "%s".', $property, $decoded_query));
      }

      // If the second row is given, then check for matching values.
      if (empty($values)) {
        continue;
      }

      PHPUnit_Framework_Assert::assertEquals($values[$index], $parts[2]);
    }
  }

  /**
   * Returns fixed step argument (with \\" replaced back to ").
   *
   * @param string $argument
   *   The string to fix argument in.
   *
   * @return string
   *   The fixed step argument.
   */
  protected function fixStepArgument($argument): string {
    return str_replace('\\"', '"', $argument);
  }

}
