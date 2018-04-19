<?php

namespace Drupal\Tests\qs_calendar\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\qs_calendar\Service\CalendarBuilder;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * @coversDefaultClass \Drupal\qs_calendar\Service\CalendarBuilder
 *
 * Be careful, 2016 is a bissextile year - see tests it :D
 *
 * @group qs
 * @group qs_kernel
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
  protected function setUp() {
    parent::setUp();
    $this->calendarBuilder = \Drupal::service('qs_calendar.calendar_builder');
  }

  /**
   * @covers Drupal\qs_calendar\Service\CalendarBuilder::getMondayWeek
   * @dataProvider getMondayWeekProvider
   */
  public function testGetMondayWeek($date, $expected) {
    $date = DrupalDateTime::createFromFormat('Y-m-d', $date);
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
      ['2016-12-26', '2016-12-26'],
      ['2018-01-01', '2018-01-01'],
      ['2017-01-01', '2016-12-26'],
    ];
  }

  /**
   * @covers \Drupal\qs_calendar\Service\CalendarBuilder::getSundayWeek
   * @dataProvider getSundayWeekProvider
   */
  public function testGetSundayWeek($date, $expected) {
    $date = DrupalDateTime::createFromFormat('Y-m-d', $date);
    $sunday = $this->calendarBuilder->getSundayWeek($date);
    $this->assertInstanceOf('Drupal\Core\Datetime\DrupalDateTime', $sunday);
    $this->assertEqual($sunday->format('Y-m-d'), $expected);
  }

  /**
   * Tests provider for testGetSundayWeek.
   *
   * @return array
   *   Return an array of arrays containg date formatted Y-m-d.
   */
  public function getSundayWeekProvider() {
    return [
      ['2017-10-03', '2017-10-08'],
      ['2017-10-08', '2017-10-08'],
      ['2018-03-07', '2018-03-11'],
      ['2000-06-06', '2000-06-11'],
      ['2000-06-11', '2000-06-11'],
      ['2000-06-03', '2000-06-04'],
      ['2000-05-29', '2000-06-04'],
      ['2000-02-29', '2000-03-05'],
      ['2000-02-28', '2000-03-05'],
      ['2010-02-26', '2010-02-28'],
      ['2017-04-01', '2017-04-02'],
      ['2016-12-26', '2017-01-01'],
    ];
  }

  /**
   * @covers \Drupal\qs_calendar\Service\CalendarBuilder::getFirstMondayMonthFullWeek
   * @dataProvider getFirstMondayMonthProvider
   */
  public function testGetFirstMondayMonth($date, $expected) {
    $date = DrupalDateTime::createFromFormat('Y-m-d', $date);
    $monday = $this->calendarBuilder->getFirstMondayMonthFullWeek($date);
    $this->assertInstanceOf('Drupal\Core\Datetime\DrupalDateTime', $monday);
    $this->assertEqual($monday->format('Y-m-d'), $expected);
  }

  /**
   * Tests provider for testGetFirstMondayMonth.
   *
   * @return array
   *   Return an array of arrays containg date formatted Y-m-d.
   */
  public function getFirstMondayMonthProvider() {
    return [
      ['2017-10-03', '2017-09-25'],
      ['2017-05-18', '2017-05-01'],
      ['2017-05-31', '2017-05-01'],
      ['2017-12-13', '2017-11-27'],
      ['2017-11-27', '2017-10-30'],
      ['2015-01-01', '2014-12-29'],
      ['2015-12-20', '2015-11-30'],
      ['2015-11-01', '2015-10-26'],
      ['2015-09-26', '2015-08-31'],
    ];
  }

  /**
   * @covers \Drupal\qs_calendar\Service\CalendarBuilder::getLastSundayMonthFullWeek
   * @dataProvider getLastSundayMonthProvider
   */
  public function testGetLastSundayMonth($date, $expected) {
    $date = DrupalDateTime::createFromFormat('Y-m-d', $date);
    $sunday = $this->calendarBuilder->getLastSundayMonthFullWeek($date);
    $this->assertInstanceOf('Drupal\Core\Datetime\DrupalDateTime', $sunday);
    $this->assertEqual($sunday->format('Y-m-d'), $expected);
  }

  /**
   * Tests provider for testGetLastSundayMonth.
   *
   * @return array
   *   Return an array of arrays containg date formatted Y-m-d.
   */
  public function getLastSundayMonthProvider() {
    return [
      ['2017-10-03', '2017-11-05'],
      ['2017-05-18', '2017-06-04'],
      ['2017-05-31', '2017-06-04'],
      ['2017-12-13', '2017-12-31'],
      ['2017-11-27', '2017-12-03'],
      ['2017-01-01', '2017-02-05'],
      ['2016-12-27', '2017-01-01'],
      ['2015-01-01', '2015-02-01'],
      ['2014-12-01', '2015-01-04'],
      ['2015-12-20', '2016-01-03'],
      ['2015-11-01', '2015-12-06'],
      ['2015-09-26', '2015-10-04'],
    ];
  }

  /**
   * @covers \Drupal\qs_calendar\Service\CalendarBuilder::build
   * @dataProvider buildProvider
   */
  public function testBuild($date_start, $date_end, $expected) {
    $start = DrupalDateTime::createFromFormat('Y-m-d', $date_start);
    $end   = DrupalDateTime::createFromFormat('Y-m-d', $date_end);

    $period = $this->calendarBuilder->build($start, $end);

    $this->assertInstanceOf('\DatePeriod', $period);
    $this->assertEqual(iterator_count($period), $expected);
  }

  /**
   * Tests provider for testGetLastSundayMonth.
   *
   * @return array
   *   Return an array of arrays contains date formatted Y-m-d.
   */
  public function buildProvider() {
    return [
      ['2017-10-01', '2017-10-02', 2],
      ['2017-10-01', '2017-10-01', 1],
      ['2017-10-01', '2017-09-01', 0],
      ['2014-12-29', '2015-02-01', 35],
      ['2015-02-06', '2015-03-01', 24],
      ['2018-12-29', '2015-02-01', 0],
      ['2015-12-01', '2016-03-09', 100],
      ['2015-12-01', '2017-10-30', 700],
    ];
  }

}
