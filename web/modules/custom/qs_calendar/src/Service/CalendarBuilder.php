<?php

namespace Drupal\qs_calendar\Service;

use Drupal\Core\Datetime\DrupalDateTime;

/**
 * CalendarBuilder.
 */
class CalendarBuilder {

  /**
   * Class constructor.
   */
  public function __construct() {
  }

  /**
   * Generate an iterator of dates between 2 dates.
   *
   * @param \Drupal\Core\Datetime\DrupalDateTime $date_start
   *   The start date for the period.
   * @param \Drupal\Core\Datetime\DrupalDateTime $date_end
   *   The end date for the period.
   *
   * @return \DatePeriod
   *   A date period for iteration over a set of dates.
   */
  public function build(DrupalDateTime $date_start, DrupalDateTime $date_end) {
    $interval = new \DateInterval('P1D');
    $start = \DateTime::createFromFormat('Y-m-d', $date_start->format('Y-m-d'));
    $end = \DateTime::createFromFormat('Y-m-d', $date_end->format('Y-m-d'));

    $start->setTime(0, 0);
    $end->setTime(23, 59, 59);

    // The last parameter is used to keep the start date in the period.
    return new \DatePeriod($start, $interval, $end, 0);
  }

  /**
   * Get the first day for the $date month.
   *
   * @param \Drupal\Core\Datetime\DrupalDateTime $date
   *   The date object.
   *
   * @return \Drupal\Core\Datetime\DrupalDateTime
   *   The first day month.
   */
  public function getFirstMondayMonth(DrupalDateTime $date) {
    $cloned = clone $date;
    $cloned->modify('first day of this month');

    return $cloned;
  }

  /**
   * Get the first monday for the $date month.
   *
   * @param \Drupal\Core\Datetime\DrupalDateTime $date
   *   The date object.
   *
   * @return \Drupal\Core\Datetime\DrupalDateTime
   *   The first monday month.
   */
  public function getFirstMondayMonthFullWeek(DrupalDateTime $date) {
    $cloned = clone $date;
    $cloned->modify('first day of this month')->modify('monday this week');

    return $cloned;
  }

  /**
   * Get the last day for the $date month.
   *
   * @param \Drupal\Core\Datetime\DrupalDateTime $date
   *   The date object.
   *
   * @return \Drupal\Core\Datetime\DrupalDateTime
   *   The last day month.
   */
  public function getLastSundayMonth(DrupalDateTime $date) {
    $cloned = clone $date;
    $cloned->modify('last day of this month');

    return $cloned;
  }

  /**
   * Get the last sunday for the $date month.
   *
   * @param \Drupal\Core\Datetime\DrupalDateTime $date
   *   The date object.
   *
   * @return \Drupal\Core\Datetime\DrupalDateTime
   *   The last sunday month.
   */
  public function getLastSundayMonthFullWeek(DrupalDateTime $date) {
    $cloned = clone $date;
    $cloned->modify('last day of this month')->modify('sunday this week');

    return $cloned;
  }

  /**
   * Get the monday for the $date week.
   *
   * @param \Drupal\Core\Datetime\DrupalDateTime $date
   *   The date object.
   *
   * @return \Drupal\Core\Datetime\DrupalDateTime
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
   * @param \Drupal\Core\Datetime\DrupalDateTime $date
   *   The date object.
   *
   * @return \Drupal\Core\Datetime\DrupalDateTime
   *   The sunday.
   */
  public function getSundayWeek(DrupalDateTime $date) {
    $cloned = clone $date;
    $cloned->modify('sunday this week');

    return $cloned;
  }

}
