<?php

namespace Drupal\Behat\Context\Drupal;

use Behat\Mink\Exception\ElementHtmlException;
use Behat\Mink\Exception\ElementNotFoundException;
use Drupal\DrupalExtension\Context\RawDrupalContext;

/**
 * Defines Element features from the specific context.
 */
class ElementContext extends RawDrupalContext {

  /**
   * Verify a given link is visible with a given href attr.
   *
   * @Given I should see :label link with href :href
   *
   * @throws \Behat\Mink\Exception\ElementNotFoundException
   */
  public function iShouldSeeLinkWithHref($label, $href) {
    $link = $this->getSession()->getPage()->findLink($label);

    if (NULL === $link) {
      throw new ElementNotFoundException($this->getSession(), 'link', 'id|title|alt|text|data-title', $label);
    }

    if (strpos($link->getAttribute('href'), $href) === FALSE) {
      throw new ElementNotFoundException($this->getSession(), 'link', 'href', $href);
    }
  }

  /**
   * Verify a given link is not visible with a given href attr.
   *
   * @Given I should not see :label link
   *
   * @throws \Behat\Mink\Exception\ElementNotFoundException
   */
  public function iShouldNotSeeLinkWithHref($label) {
    $link = $this->getSession()->getPage()->findLink($label);
    if (NULL !== $link) {
      throw new ElementHtmlException('Link "' . $label . '" was found.', $this->getSession(), $link);
    }
  }

  /**
   * Verify the title of the page.
   *
   * @Given /^the page title should be "([^"]*)"$/
   *
   * @throws \Behat\Mink\Exception\ElementNotFoundException
   */
  public function thePageTitleShouldBe($expectedTitle) {
    $title = $this->getSession()->getPage()->find('css', '.page-title')->getText();

    if (strtolower($expectedTitle) !== strtolower($title)) {
      throw new ElementNotFoundException($this->getSession(), 'title', 'css', $expectedTitle . ' / But found : ' . $title);
    }
  }

  /**
   * Follow/Click link element with the provided CSS Selector.
   *
   * @Then I follow the link :element element
   *
   * @throws \Behat\Mink\Exception\ElementNotFoundException
   */
  public function iClickTheLinkElement($selector) {
    $element = $this->getSession()->getPage()->find('css', $selector);

    if (!$element) {
      throw new ElementNotFoundException($this->getSession()->getDriver(), 'element', 'css', $selector);
    }
    $element->click();
  }

  /**
   * Asserts then given element is disabled.
   *
   * @Then I should see a disabled :item
   *
   * @throws \Behat\Mink\Exception\ElementNotFoundException
   */
  public function assertIsDisabled($selector) {
    $element = $this->getSession()->getPage()->find('css', $selector);

    if (!$element) {
      throw new ElementNotFoundException($this->getSession()->getDriver(), 'element', 'css', $selector);
    }

    if ('disabled' !== $element->getAttribute('disabled')) {
      throw new ElementNotFoundException($this->getSession(), 'element', 'css', 'disabled');
    }
  }

  /**
   * Asserts then given element is in header.
   *
   * @Then /^I should see in the header "([^"]*)":"([^"]*)"$/
   */
  public function iShouldSeeInTheHeader($header, $value) {
    $headers = $this->getSession()->getResponseHeaders();
    $headers = array_change_key_case($headers, CASE_LOWER);

    if (isset($headers[strtolower($header)]) && $headers[strtolower($header)][0] != $value) {
      throw new \Exception(sprintf('Did not see %s with value %s.', $header, $value));
    }
  }

}
