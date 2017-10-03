<?php

namespace Drupal\qs_calendar\Service;

use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Calendarbuilder.
 */
class Calendarbuilder {

  /**
   * Class constructor.
   */
  public function __construct() {

  }

  public function getMondayWeek(DrupalDateTime $date) {
    $cloned = clone $date;
    $cloned->modify('monday this week');
    return $cloned;
  }

  public function getFridayWeek(DrupalDateTime $date) {

  }

  public function getFirstMondayMonth(DrupalDateTime $date) {

  }

  public function getLastFridayMonth(DrupalDateTime $date) {

  }
}
