<?php

namespace Drupal\Tests\qs_badge\Kernel;

use Drupal\qs_test\Kernel\ResoliKernelTestBase;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * @coversDefaultClass \Drupal\qs_badge\Service\BadgeManager
 *
 * @group qs
 * @group qs_kernel
 * @group qs_badge
 * @group qs_badge_kernel
 * @group kevin
 */
class BadgeManagerGetSubscriptionByDatesTest extends ResoliKernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'qs_badge',
    'qs_subscription',
  ];

  /**
   * The Badge Manager.
   *
   * @var \Drupal\qs_badge\Service\BadgeManager
   */
  protected $badgeManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Install every system configurations needs by FormBuilder.
    $this->installConfig(['system']);
    $this->installEntitySchema('subscription');

    $this->setupCommunities();
    $this->setupActivities();
    $this->setupEvents();

    $this->badgeManager = \Drupal::service('qs_badge.badge_manager');
  }

  /**
   * @covers ::getSubscriptionByDates
   */
  public function testGetSubscriptionByDates() {
    $community = $this->seedCommunities(1);
    $activity = $this->seedActivities($community[1], 1);

    $start = DrupalDateTime::createFromFormat('Y-m-d', '2016-07-27');
    $end = DrupalDateTime::createFromFormat('Y-m-d', '2017-09-29');
    $events = $this->seedEvents($activity[1], $start, $end, 9);

    $start = DrupalDateTime::createFromFormat('Y-m-d', '2016-07-27');
    $end = DrupalDateTime::createFromFormat('Y-m-d', '2020-09-29');

    $privilegies = $this->badgeManager->getSubscriptionByDates($community[1], $start, $end);

    $this->markTestIncomplete('Need works.');

    dump($privilegies);
    die();
    $this->assertInternalType('array', $events);
    $this->assertSame([
      '2016-09-28' => '1',
      '2019-08-28' => '2',
    ], $events);
  }

}
