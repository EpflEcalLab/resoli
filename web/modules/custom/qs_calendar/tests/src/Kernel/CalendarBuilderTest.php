<?php

namespace Drupal\Tests\qs_calendar\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\qs_calendar\Service\CalendarBuilder;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * @coversDefaultClass \Drupal\qs_calendar\Service\CalendarBuilder
 *
 * @group qs_calendar
 * @group qs_calendar_kernel
 */
class CalendarBuilderTest extends KernelTestBase {
  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = ['qs_calendar'];

  /**
   * {@inheritdoc}
   */
  protected function getUp() {
    parent::getUp();

    // $connectionProphet = $this->prophesize('\Drupal\Core\Database\Connection');
    // $enTypeManagerProphet = $this->prophesize('\Drupal\Core\Entity\EntityTypeManagerInterface');
    // $queryFactoryProphet = $this->prophesize('\Drupal\Core\Entity\Query\QueryFactory');
    // $mailManagerProphet = $this->prophesize('\Drupal\Core\Mail\MailManagerInterface');
  }

  /**
   * @covers Drupal\qs_calendar\Service\CalendarBuilder::getMondayWeek
   * @dataProvider getMondayWeekProvider
   */
  public function testGetMondayWeek($date, $expected) {
    $date = DrupalDateTime::createFromFormat('Y-m-d', $date);

    $this->calendarBuilder = \Drupal::service('qs_calendar.calendar_builder');
    $monday = $this->calendarBuilder->getMondayWeek($date);

    $this->assertInstanceOf('Drupal\Core\Datetime\DrupalDateTime', $monday);
    $this->assertEqual($monday->format('Y-m-d'), $expected);
  }

  /**
   * Tests provider for testGetMondayWeek.
   *
   * @return array
   *   Return an array of arrays containg date formatted Y-m-d.
   */
  public function getMondayWeekProvider() {
    return [
      ['2017-10-03', '2017-10-02'],
      ['2017-10-02', '2017-10-02'],
      ['2018-03-07', '2018-03-05'],
      ['2000-06-06', '2000-06-05'],
      ['2000-06-05', '2000-06-05'],
      ['2000-06-03', '2000-05-29'],
      ['2000-02-29', '2000-02-28'],
      ['2000-02-28', '2000-02-28'],
      ['2010-02-26', '2010-02-22'],
      ['2017-04-01', '2017-03-27'],
    ];
  }

  /**
   * @covers Drupal\qs_calendar\Service\CalendarBuilder::getFridayWeek
   */
  public function testGetFridayWeek() {
  }

  /**
   * @covers Drupal\qs_calendar\Service\CalendarBuilder::getFirstMondayMonth
   */
  public function testGetFirstMondayMonth() {
  }

  /**
   * @covers Drupal\qs_calendar\Service\CalendarBuilder::getLastFridayMonth
   */
  public function testGetLastFridayMonth() {
  }
}
