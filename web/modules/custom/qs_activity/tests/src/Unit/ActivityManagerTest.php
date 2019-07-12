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
   * Test the pagination dates.
   *
   * @group toto
   * @covers ::getPaginationFromDate
   * @dataProvider getPaginationFromDateProvider
   */
  public function testGetPaginationFromDate($start_date, $expected) {
    $dates = ActivityManager::getPaginationFromDate($start_date);

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
    $now = new DateTime();
    return [
      'TODAY' => [
        $now,
        [
          'start' => new DateTime('Monday this week 00:00'),
          'end' => new DateTime('Sunday this week +3 weeks 23:59:59'),
          'prev' => new DateTime('Monday this week -4 weeks 00:00'),
          'next' => new DateTime('next Monday +3 weeks 00:00'),
        ],
      ],
      'PREVIOUS DATE' => [
        new DateTime('Monday this week -4 weeks'),
        [
          'start' => new DateTime('Monday this week 00:00'),
          'end' => new DateTime('Sunday this week +3 weeks 23:59:59'),
          'prev' => new DateTime('Monday this week -4 weeks 00:00'),
          'next' => new DateTime('next Monday +3 weeks 00:00'),
        ],
      ],
      'NEXT DATE' => [
        new DateTime('next Monday +3 weeks 00:00'),
        [
          'start' => new DateTime('next Monday +3 weeks 00:00'),
          'end' => new DateTime('next Sunday +7 weeks 23:59:59'),
          'prev' => $now,
          'next' => new DateTime('next Monday +7 weeks 00:00'),
        ],
      ],
      'NEXT DATE + 1 day' => [
        new DateTime('next Thursday +3 weeks 00:00'),
        [
          'start' => new DateTime('next Monday +3 weeks 00:00'),
          'end' => new DateTime('next Sunday +7 weeks 23:59:59'),
          'prev' => $now,
          'next' => new DateTime('next Monday +7 weeks 00:00'),
        ],
      ],
    ];
  }

}
