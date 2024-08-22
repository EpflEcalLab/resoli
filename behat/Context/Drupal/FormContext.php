<?php

namespace Drupal\Behat\Context\Drupal;

use Drupal\Core\File\FileExists;
use Drupal\Core\File\FileSystemInterface;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Mink\Exception\ElementNotFoundException;
use Drupal\Component\Utility\Random;
use Behat\Gherkin\Node\TableNode;
use Drupal\Component\FileSecurity\FileSecurity;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Defines application features from the specific context.
 *
 * @codingStandardsIgnoreFile
 */
class FormContext extends RawDrupalContext {

  /**
   * The base URL.
   *
   * @var string
   */
  protected $baseUrl;

  /**
   * Collection of file URI which will be uploaded.
   *
   * @var string[]
   */
  private $attachments;

  /**
   * Initializes context.
   *
   * Every scenario gets its own context instance.
   * You can also pass arbitrary arguments to the
   * context constructor through behat.yml.
   */
  public function __construct($base_url) {
    $this->baseUrl = $base_url;
  }

  /**
   * Verify a given from is visible with a given action.
   *
   * @throw ElementNotFoundException
   *
   * @Given I should see a form with action :action
   *
   * @throws ElementNotFoundException
   */
  public function iShouldSeeFormWithAction($action) {
    $form_relative = $this->getSession()->getPage()->find('css', 'form[action="' . $action . '"]');

    $action = $this->baseUrl . $action;
    $form_absolute = $this->getSession()->getPage()->find('css', 'form[action="' . $action . '"]');

    if (!$form_relative && !$form_absolute) {
      throw new ElementNotFoundException($this->getSession(), 'form', 'action', $action);
    }
  }

  /**
   * Verify a given from isn't visible with a given action.
   *
   * @throw ElementNotFoundException
   *
   * @Given I should not see a form with action :action
   *
   * @throws ElementNotFoundException
   */
  public function iShouldNotSeeFormWithAction($action) {
    $form_relative = $this->getSession()->getPage()->find('css', 'form[action="' . $action . '"]');

    $action = $this->baseUrl . $action;
    $form_absolute = $this->getSession()->getPage()->find('css', 'form[action="' . $action . '"]');

    if ($form_relative || $form_absolute) {
      throw new \Exception(sprintf('Found a form with action "%s".', $action));
    }
  }

  /**
   * Assert the given option of <select> is selected.
   *
   * @Then /^the option "([^"]*)" from select "([^"]*)" is selected$/
   *
   * @throws Exception
   */
  public function theOptionFromSelectIsSelected($optionValue, $select) {
    $selectField = $this->getSession()->getPage()->find('css', $select);
    if (NULL === $selectField) {
      throw new \Exception(sprintf('The select "%s" was not found in the page %s', $select, $this->getSession()->getCurrentUrl()));
    }

    $optionField = $selectField->find('xpath', "//option[@selected='selected']");
    if (NULL === $optionField) {
      throw new \Exception(sprintf('No option is selected in the %s select in the page %s', $select, $this->getSession()->getCurrentUrl()));
    }

    if ($optionField->getValue() != $optionValue) {
      throw new \Exception(sprintf('The option "%s" was not selected in the page %s, %s was selected', $optionValue, $this->getSession()->getCurrentUrl(), $optionField->getValue()));
    }
  }

  /**
   * Make sure the given private:// folder exists & is writtable.
   *
   * @Then /^create the private folder "([^"]*)"$/
   *
   * @throws \Exception
   */
  public function createPrivateFolder($folder) {
    /** @var \Drupal\Core\File\FileSystemInterface $fso */
    $fso = \Drupal::service('file_system');
    $private_path = $fso->realpath('private://' . $folder);

    if (!is_dir($private_path)) {
      throw new \Exception(sprintf('Value "%s" is not a directory.', $private_path));
    }

    $fso->prepareDirectory($private_path, FileSystemInterface::CREATE_DIRECTORY);
    $fso->prepareDirectory($private_path, FileSystemInterface::MODIFY_PERMISSIONS);
    FileSecurity::writeHtaccess($private_path, TRUE);
  }

  /**
   * Generate file on the public directory.
   *
   * The file will be be attached as Uploaded file uploaded on next call of
   * ::iSendRequestWithBody or ::iSendUpload.
   *
   * @Then /^create "([^"]*)" file "([^"]*)" to be uploaded$/
   */
  public function createFileToBeUploaded($number, $extension) {
    $random = new Random();
    /** @var \Drupal\Core\File\FileSystemInterface $fso */
    $fso = \Drupal::service('file_system');

    $dirname = 'public://behat-files/';
    $fso->prepareDirectory($dirname, FileSystemInterface::CREATE_DIRECTORY);

    for ($i = 0; $i < $number; $i++) {
      $filename = $random->name(10, TRUE) . '.' . $extension;
      $destination = $dirname . $filename;
      $data = $random->paragraphs(3);
      $file = \Drupal::service('file.repository')->writeData($data, $destination, FileExists::Error);

      $uploaded_file = [
        'name' => $filename,
        'tmp_name' => $fso->realpath($file->getFileUri()),
      ];

      $this->attachments[] = [
        $uploaded_file,
      ];
    }
  }

  /**
   * Send a HTTP request with a specifiy body & previously created file(s).
   *
   * @Then /^I send "([^"]*)" request to "([^"]*)" with parameters:$/
   */
  public function iSendRequestWithBody($method, $url, TableNode $parameters) {
    /** @var \Symfony\Component\BrowserKit\HttpBrowser $client */
    $client = $this->getSession()->getDriver()->getClient();
    $client->request($method, $this->baseUrl . $url, $parameters->getRowsHash(), [
      'files' => $this->attachments,
    ]);
  }

  /**
   * Send a POST request with previously created file(s).
   *
   * @Then /^I send "([^"]*)" request to "([^"]*)"$/
   */
  public function iSendUpload($method, $url) {
    /** @var \Symfony\Component\BrowserKit\HttpBrowser $client */
    $client = $this->getSession()->getDriver()->getClient();
    $client->request($method, $this->baseUrl . $url, [], [
      'files' => $this->attachments,
    ]);
  }

  /**
   * Checks, that form field with specified id has specified pattern.
   *
   * Example: Then the "#edit-username" field should match regex "/([0-5][0-9])/"
   *
   * @Then the :field field should match regex :regex
   */
  public function assertFieldFromatted($field, $regex) {
    $field = $this->getSession()->getPage()->find('css', $field);
    if (NULL === $field) {
      throw new \Exception(sprintf('The field "%s" was not found in the page %s', $field, $this->getSession()->getCurrentUrl()));
    }

    $value = $field->getValue();
    $matches = preg_match($regex, $value, $matches);

    if ($matches !== 1) {
      throw new \Exception(sprintf('The value "%s" does not match the pattern %s', $value, $regex));
    }
  }

  /**
   * @Given /^I fill hidden field "([^"]*)" with "([^"]*)"$/
   */
  public function iFillHiddenFieldWith($field, $value)
  {
    $this->getSession()->getPage()->find('css',
      'input[name="'.$field.'"]')->setValue($value);
  }

}
