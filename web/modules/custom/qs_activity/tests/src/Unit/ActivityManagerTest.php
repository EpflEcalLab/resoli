<?php

namespace Drupal\Tests\qs_activity\Unit;

use DateTime;
use Drupal\qs_activity\Service\ActivityManager;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\qs_activity\Service\ActivityManager
 *
 * @group qs
 * @group qs_unit
 * @group qs_activity
 * @group qs_activity_unit
 */
class ActivityManagerTest extends UnitTestCase {

  /**
   * The partially mocked QS Activity Manager.
   *
   * @var \Drupal\qs_activity\Service\ActivityManager
   */
  protected $eventManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->activityManager = $this->getMockBuilder(ActivityManager::class)
      ->disableOriginalConstructor()
      ->setMethods(['getNow'])
      ->getMock();
  }

  /**
   * Test the pagination dates.
   *
   * @covers ::getPaginationFromDate
   * @dataProvider getPaginationFromDateProvider
   */
  public function testGetPaginationFromDate(Datetime $now, Datetime $start_date, $expected) {
    $this->activityManager->expects($this->any())
      ->method('getNow')
      ->willReturn($now);

    $dates = $this->activityManager->getPaginationFromDate($start_date);

    $this->assertEquals($expected['start']->format('YmdHi'), $dates['start']->format('YmdHi'), 'Start date is the Monday of the given date or today if start is in the past.');
    $this->assertEquals($expected['end']->format('YmdHi'), $dates['end']->format('YmdHi'), 'End date is the Sunday (23:59:59) 4 weeks after the start date.');
    $this->assertEquals($expected['prev']->format('YmdHi'), $dates['prev']->format('YmdHi'), 'Previous date is 4 weeks before the start date.');
    $this->assertEquals($expected['next']->format('YmdHi'), $dates['next']->format('YmdHi'), 'Next date is 4 weeks after the end date.');
    $this->assertContainsOnlyInstancesOf(DateTime::class, $dates);
  }

  /**
   * Data provider for testing getPaginationFromDate.
   *
   * @return array
   *   The start date and the expected results.
   *
   * @throws \Exception
   */
  public function getPaginationFromDateProvider() {
    return [
      'start pagination with same day as now' => [
        new DateTime('18 july 2019 00:00'),
        new DateTime('18 july 2019 00:00'),
        [
          'start' => new DateTime('15 july 2019 00:00'),
          'end' => new DateTime('11 august 2019 23:59:59'),
          'prev' => new DateTime('17 june 2019 00:00'),
          'next' => new DateTime('12 august 2019 00:00'),
        ],
      ],
      'start pagination at the start week (monday) of now' => [
        new DateTime('18 july 2019 00:00'),
        new DateTime('15 july 2019 00:00'),
        [
          'start' => new DateTime('15 july 2019 00:00'),
          'end' => new DateTime('11 august 2019 23:59:59'),
          'prev' => new DateTime('17 june 2019 00:00'),
          'next' => new DateTime('12 august 2019 00:00'),
        ],
      ],
      'start pagination at the end week (sunday) of now' => [
        new DateTime('18 july 2019 00:00'),
        new DateTime('21 july 2019 00:00'),
        [
          'start' => new DateTime('15 july 2019 00:00'),
          'end' => new DateTime('11 august 2019 23:59:59'),
          'prev' => new DateTime('18 july 2019 00:00'),
          'next' => new DateTime('12 august 2019 00:00'),
        ],
      ],
      'start pagination one day after now' => [
        new DateTime('18 july 2019 00:00'),
        new DateTime('19 july 2019 00:00'),
        [
          'start' => new DateTime('15 july 2019 00:00'),
          'end' => new DateTime('11 august 2019 23:59:59'),
          'prev' => new DateTime('18 july 2019 00:00'),
          'next' => new DateTime('12 august 2019 00:00'),
        ],
      ],
      'start pagination 2 weeks before now' => [
        new DateTime('18 july 2019 00:00'),
        new DateTime('3 july 2019 00:00'),
        [
          'start' => new DateTime('15 july 2019 00:00'),
          'end' => new DateTime('11 august 2019 23:59:59'),
          'prev' => new DateTime('17 june 2019 00:00'),
          'next' => new DateTime('12 august 2019 00:00'),
        ],
      ],
      'start pagination 4 weeks ago from now' => [
        new DateTime('18 july 2019 00:00'),
        new DateTime('20 june 2019 00:00'),
        [
          'start' => new DateTime('15 july 2019 00:00'),
          'end' => new DateTime('11 august 2019 23:59:59'),
          'prev' => new DateTime('17 june 2019 00:00'),
          'next' => new DateTime('12 august 2019 00:00'),
        ],
      ],
      'start pagination 3 weeks after now' => [
        new DateTime('18 july 2019 00:00'),
        new DateTime('8 august 2019 00:00'),
        [
          'start' => new DateTime('5 august 2019 00:00'),
          'end' => new DateTime('1 september 2019 23:59:59'),
          'prev' => new DateTime('18 july 2019 00:00'),
          'next' => new DateTime('2 september 2019 00:00'),
        ],
      ],
      'start pagination 3 weeks + 1 day after now' => [
        new DateTime('18 july 2019 00:00'),
        new DateTime('9 august 2019 00:00'),
        [
          'start' => new DateTime('5 august 2019 00:00'),
          'end' => new DateTime('1 september 2019 23:59:59'),
          'prev' => new DateTime('18 july 2019 00:00'),
          'next' => new DateTime('2 september 2019 00:00'),
        ],
      ],
    ];
  }

}
