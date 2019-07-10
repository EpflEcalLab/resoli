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

    $this->assertEquals($expected['start'], $dates['start'], 'Start date should be the same.');
    $this->assertEquals($expected['end'], $dates['end'], 'End date should be the same.');
    $this->assertEquals($expected['prev'], $dates['prev'], 'Previous date should be the same.');
    $this->assertEquals($expected['next'], $dates['next'], 'Next date should be the same.');
    $this->assertContainsOnlyInstancesOf(DateTime::class, $dates);
  }

  /**
   * Data provider for testing getPaginationFromDate.
   *
   * @return array
   *   The start date and the expected results.
   */
  public function getPaginationFromDateProvider() {
    return [
      'TODAY, 10 juillet 2019' => [
        new DateTime('July 10, 2019 10:32 UTC'),
        [
          'start' => new DateTime('July 8, 2019 00:00 UTC'),
          'end' => new DateTime('August 4, 2019 23:59:59 UTC'),
          'prev' => new DateTime('June 10, 2019 00:00 UTC'),
          'next' => new DateTime('August 5, 2019 00:00 UTC'),
        ],
      ],
      'PREVIOUS DATE, 10 juin 2019' => [
        new DateTime('June 10, 2019 00:00 UTC'),
        [
          'start' => new DateTime('June 10, 2019 00:00 UTC'),
          'end' => new DateTime('July 7, 2019 23:59:59 UTC'),
          'prev' => new DateTime('May 13, 2019 00:00 UTC'),
          'next' => new DateTime('July 8, 2019 00:00 UTC'),
        ],
      ],
      'NEXT DATE, 5 août 2019' => [
        new DateTime('August 5, 2019 00:00 UTC'),
        [
          'start' => new DateTime('August 5, 2019 00:00 UTC'),
          'end' => new DateTime('September 1, 2019 23:59:59 UTC'),
          'prev' => new DateTime('July 8, 2019 00:00 UTC'),
          'next' => new DateTime('September 2, 2019 00:00 UTC'),
        ],
      ],
      '12 février 2019, not UTC' => [
        new DateTime('February 12, 2019 12:45'),
        [
          'start' => new DateTime('February 11, 2019 00:00 UTC'),
          'end' => new DateTime('March 10, 2019 23:59:59 UTC'),
          'prev' => new DateTime('January 14, 2019 00:00 UTC'),
          'next' => new DateTime('March 11, 2019 00:00 UTC'),
        ],
      ],
      '28 décembre 2016' => [
        new DateTime('December 28, 2016 18:05'),
        [
          'start' => new DateTime('December 26, 2016 00:00 UTC'),
          'end' => new DateTime('January 22, 2017 23:59:59 UTC'),
          'prev' => new DateTime('November 28, 2016 00:00 UTC'),
          'next' => new DateTime('January 23, 2017 00:00 UTC'),
        ],
      ],
    ];
  }

}
