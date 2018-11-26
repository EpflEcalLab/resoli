<?php

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Drupal\Component\Utility\Random;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class FormContext extends RawDrupalContext implements SnippetAcceptingContext {
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
   * @Then /^create the private folder$/
   *
   * @throws \Exception
   */
  public function createPrivateFolder() {
    $fso = \Drupal::service('file_system');
    $private_path = $fso->realpath('private://'.$folder);

    if (!is_dir($private_path)) {
      throw new \Exception(sprintf('Value "%s" is not a directory.', $private_path));
    }

    file_prepare_directory($private_path, FILE_CREATE_DIRECTORY);
    file_prepare_directory($private_path, FILE_MODIFY_PERMISSIONS);
    file_save_htaccess($private_path, TRUE);
  }

  /**
   * @Then /^create "([^"]*)" file "([^"]*)" to be uploaded$/
   */
  public function createFileToBeUploaded($number, $extension) {
    $random = new Random();
    $fso = \Drupal::service('file_system');

    $dirname = 'public://behat-files/';
    file_prepare_directory($dirname, FILE_CREATE_DIRECTORY);

    for ($i = 0; $i < $number; $i++) {
      $destination = $dirname . $random->name(10, TRUE) . '.' . $extension;
      $data        = $random->paragraphs(3);
      $file        = file_save_data($data, $destination, FILE_EXISTS_ERROR);

      $this->attachments[] = [
        $fso->realpath($file->getFileUri()),
      ];
    }
  }

  /**
   * @Then /^I send "([^"]*)" request to "([^"]*)" with parameters:$/
   */
  public function iSendRequestWithBody($method, $url, TableNode $parameters) {
    /** @var \Symfony\Component\BrowserKit\Client $client */
    $client = $this->getSession()->getDriver()->getClient();
    $client->request($method, $this->baseUrl . $url, $parameters->getRowsHash(), ['files' => $this->attachments]);
  }

  /**
   * @Then /^I send "([^"]*)" request to "([^"]*)"$/
   */
  public function iSendUpload($method, $url) {
    /** @var \Symfony\Component\BrowserKit\Client $client */
    $client = $this->getSession()->getDriver()->getClient();
    $client->request($method, $this->baseUrl . $url, [], [
      'files' => $this->attachments,
    ]);
  }

}
