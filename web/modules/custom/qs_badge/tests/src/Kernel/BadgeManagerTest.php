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
class BadgeManagerTest extends ResoliKernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'qs_badge',
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

    $this->setupCommunities();
    $this->setupActivities();
    $this->setupEvents();

    $this->badgeManager = \Drupal::service('qs_badge.badge_manager');
  }

  /**
   * @covers ::countEventsByDates
   * @group kevin
   */
  public function testCountEventsByDatesDontMixCommunities() {
    $communities = $this->seedCommunities(2);

    $activities = $this->seedActivities($communities[1], 1);
    $activities += $this->seedActivities($communities[2], 1);

    $event = $this->entityTypeManager->getStorage('node')->create([
      'type' => 'event',
      'title' => $this->randomString(),
      'field_activity' => $activities[1]->id(),
      'field_start_at' => '2016-09-28T12:00:00',
      'field_end_at' => '2016-09-28T16:30:00',
    ]);
    $event->save();

    $event = $this->entityTypeManager->getStorage('node')->create([
      'type' => 'event',
      'title' => $this->randomString(),
      'field_activity' => $activities[1]->id(),
      'field_start_at' => '2016-09-28T13:00:00',
      'field_end_at' => '2016-09-28T14:00:00',
    ]);
    $event->save();

    $event = $this->entityTypeManager->getStorage('node')->create([
      'type' => 'event',
      'title' => $this->randomString(),
      'field_activity' => $activities[1]->id(),
      'field_start_at' => '2016-08-28T09:00:00',
      'field_end_at' => '2016-08-28T09:30:00',
    ]);
    $event->save();

    $event = $this->entityTypeManager->getStorage('node')->create([
      'type' => 'event',
      'title' => $this->randomString(),
      'field_activity' => $activities[2]->id(),
      'field_start_at' => '2016-09-28T12:00:00',
      'field_end_at' => '2016-09-28T16:30:00',
    ]);
    $event->save();

    $start = DrupalDateTime::createFromFormat('Y-m-d', '2016-07-27');
    $end = DrupalDateTime::createFromFormat('Y-m-d', '2016-09-29');

    $events = $this->badgeManager->countEventsByDates($communities[1], $start, $end);
    $this->assertInternalType('array', $events);
    $this->assertSame([
      '2016-08-28' => '1',
      '2016-09-28' => '2',
    ], $events);
  }

}
