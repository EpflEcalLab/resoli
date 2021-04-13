<?php

namespace Drupal\Behat\Context\Drupal;

use Drupal\DrupalExtension\Context\RawDrupalContext;

/**
 * Defines Json features from the specific context.
 */
class JsonContext extends RawDrupalContext {

  /**
   * Ensure the JSON response contain the given field.
   *
   * @param string $name
   *   The field name.
   *
   * @Given /^the JSON response should contain field "([^"]*)"$/
   *
   * @throws \Exception
   */
  public function theResponseHasField($name) {
    if (!array_key_exists($name, $this->getResponseData())) {
      throw new \Exception("Field '{$name}' not found in response.");
    }
  }

  /**
   * Ensure the JSON response does not contain the given field.
   *
   * @param string $name
   *   The field name.
   *
   * @Then /^in JSON response there is no field called "([^"]*)"$/
   *
   * @throws \Exception
   */
  public function theResponseShouldNotHaveField($name) {
    if (array_key_exists($name, $this->getResponseData())) {
      throw new \Exception("Field '{$name}' should not have been found in response, but was.");
    }
  }

  /**
   * Ensure the JSON response contain the given field with a specific value.
   *
   * @param string $name
   *   The field name.
   * @param string $value
   *   The field value.
   *
   * @Then /^the field "([^"]+)" in the JSON response should contain "([^"]*)"$/
   *
   * @throws \Exception
   */
  public function valueOfTheFieldContain($name, $value) {
    $this->theResponseHasField($name);

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
   * Get the response body.
   *
   * @return string
   *   The response body.
   */
  public function getResponseBody() {
    return (string) $this->getSession()->getDriver()->getContent();
  }

  /**
   * Get the response body decoded as JSON.
   *
   * @return mixed
   *   The response.
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
   *   The decoded string.
   *
   * @throws \Exception
   *
   * @see http://www.php.net/json_last_error
   */
  protected function decodeJson($string) {
    $json = json_decode($string, TRUE);

    if (json_last_error() === JSON_ERROR_NONE) {
      return $json;
    }

    switch (json_last_error()) {
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
