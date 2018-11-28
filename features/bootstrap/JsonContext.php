<?php

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\SnippetAcceptingContext;

/**
 * Defines Json features from the specific context.
 */
class JsonContext extends RawDrupalContext implements SnippetAcceptingContext {

  /**
   * @Given /^the JSON response should contain field "([^"]*)"$/
   *
   * @param string $name
   *
   * @throws \Exception
   */
  public function theResponseHasAField($name) {
    if (!array_key_exists($name, $this->getResponseData())) {
      throw new \Exception("Field '{$name}' not found in response.");
    }
  }

  /**
   * @Then /^in JSON response there is no field called "([^"]*)"$/
   *
   * @param string $name
   *
   * @throws \Exception
   */
  public function theResponseShouldNotHaveAField($name) {
    if (array_key_exists($name, $this->getResponseData())) {
      throw new \Exception("Field '{$name}' should not have been found in response, but was.");
    }
  }

  /**
   * @Then /^the field "([^"]+)" in the JSON response should contain "([^"]*)"$/
   *
   * @param string $name
   * @param string $value
   *
   * @throws \Exception
   */
  public function valueOfTheFieldContain($name, $value) {
    $this->theResponseHasAField($name);

    $data = $this->getResponseData();
    if (strpos($data[$name], $value) === FALSE) {
      throw new \Exception(sprintf(
          'Value "%s" was expected for field "%s", but value "%s" found instead.',
          $value,
          $name,
          $data[$name]
      ));
    }
  }

  /**
   * @return string
   */
  public function getResponseBody() {
    return (string) $this->getSession()->getDriver()->getContent();
  }

  /**
   * @return mixed
   */
  public function getResponseData() {
    return $this->decodeJson($this->getResponseBody());
  }

  /**
   * Decode JSON string.
   *
   * @param string $string
   *   A JSON string.
   *
   * @return mixed
   *
   * @throws \Exception
   *
   * @see http://www.php.net/json_last_error
   */
  protected function decodeJson($string) {
    $json = json_decode($string, TRUE);
    switch (json_last_error()) {
      case JSON_ERROR_NONE:
        return $json;

      break;
      case JSON_ERROR_DEPTH:
        $message = 'Maximum stack depth exceeded';
        break;

      case JSON_ERROR_STATE_MISMATCH:
        $message = 'Underflow or the modes mismatch';
        break;

      case JSON_ERROR_CTRL_CHAR:
        $message = 'Unexpected control character found';
        break;

      case JSON_ERROR_SYNTAX:
        $message = 'Syntax error, malformed JSON';
        break;

      case JSON_ERROR_UTF8:
        $message = 'Malformed UTF-8 characters, possibly incorrectly encoded';
        break;

      default:
        $message = 'Unknown error';
        break;
    }
    throw new \Exception('JSON decoding error: ' . $message);
  }

}
