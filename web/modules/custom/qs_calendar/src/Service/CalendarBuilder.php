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

  /**
   * Get the monday for the $date week.
   *
   * @param Drupal\Core\Datetime\DrupalDateTime $date
   *   The date object.
   *
   * @return Drupal\Core\Datetime\DrupalDateTime
   *   The monday.
   */
  public function getMondayWeek(DrupalDateTime $date) {
    $cloned = clone $date;
    $cloned->modify('monday this week');
    return $cloned;
  }

  /**
   * Get the sunday for the $date week.
   *
   * @param Drupal\Core\Datetime\DrupalDateTime $date
   *   The date object.
   *
   * @return Drupal\Core\Datetime\DrupalDateTime
   *   The sunday.
   */
  public function getSundayWeek(DrupalDateTime $date) {
    $cloned = clone $date;
    $cloned->modify('sunday this week');
    return $cloned;
  }

  /**
   * Get the first monday for the $date month.
   *
   * @param Drupal\Core\Datetime\DrupalDateTime $date
   *   The date object.
   *
   * @return Drupal\Core\Datetime\DrupalDateTime
   *   The first monday month.
   */
  public function getFirstMondayMonthFullWeek(DrupalDateTime $date) {
    $cloned = clone $date;
    $cloned->modify('first day of this month')->modify('monday this week');
    return $cloned;
  }

  /**
   * Get the last sunday for the $date month.
   *
   * @param Drupal\Core\Datetime\DrupalDateTime $date
   *   The date object.
   *
   * @return Drupal\Core\Datetime\DrupalDateTime
   *   The last sunday month.
   */
  public function getLastSundayMonthFullWeek(DrupalDateTime $date) {
    $cloned = clone $date;
    $cloned->modify('last day of this month')->modify('sunday this week');
    return $cloned;
  }

}
