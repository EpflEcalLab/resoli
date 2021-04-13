<?php

namespace Drupal\Behat\Context\Resoli;

use Drupal\DrupalExtension\Context\RawDrupalContext;

/**
 * Defines application features from the demo context.
 */
class DemoContext extends RawDrupalContext {

  /**
   * Enable Demo mode.
   *
   * @BeforeScenario @qs_demo
   */
  public function before($event) {
    // Activate demo mode here.
    $config = \Drupal::service('config.factory')->getEditable('qs_auth.settings');
    $config->set('demo_mode', TRUE)->save();
  }

  /**
   * Disable Demo mode.
   *
   * @AfterScenario @qs_demo
   */
  public function after($event) {
    // Deactivate demo mode here.
    $config = \Drupal::service('config.factory')->getEditable('qs_auth.settings');
    $config->set('demo_mode', FALSE)->save();
  }

}
