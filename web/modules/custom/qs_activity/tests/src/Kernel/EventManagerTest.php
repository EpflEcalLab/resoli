<?php

namespace Drupal\Tests\qs_activity\Kernel;

use Drupal\node\NodeInterface;
use Drupal\qs_test\Kernel\ResoliKernelTestBase;

/**
 * @coversDefaultClass \Drupal\qs_activity\Service\EventManager
 *
 * @group qs
 * @group qs_kernel
 * @group qs_activity
 * @group qs_activity_kernel
 *
 * @internal
 */
final class EventManagerTest extends ResoliKernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'qs_subscription',
    'qs_activity',
  ];

  /**
   * The entity QS Event Manager.
   *
   * @var \Drupal\qs_activity\Service\EventManager
   */
  protected $eventManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->setupActivities();
    $this->setupEvents();

    $this->eventManager = $this->container->get('qs_activity.event_manager');
  }

  /**
   * @covers ::getNext
   */
  public function testGetNext() {
    $activity_1 = $this->entityTypeManager->getStorage('node')->create([
      'type' => 'activity',
      'title' => 'activity-1',
    ]);
    $activity_1->save();

    $activity_2 = $this->entityTypeManager->getStorage('node')->create([
      'type' => 'activity',
      'title' => 'activity-2',
    ]);
    $activity_2->save();

    // Past Event.
    $event_1 = $this->entityTypeManager->getStorage('node')->create([
      'type' => 'event',
      'title' => 'event-1',
      'field_activity' => $activity_1->id(),
      'field_start_at' => '2018-01-20T00:00:00',
      'field_end_at' => '2018-01-20T02:00:00',
    ]);
    $event_1->save();

    // Far Futur Event.
    $event_2 = $this->entityTypeManager->getStorage('node')->create([
      'type' => 'event',
      'title' => 'event-2',
      'field_activity' => $activity_1->id(),
      'field_start_at' => '2028-01-20T14:00:00',
      'field_end_at' => '2028-01-20T18:00:00',
    ]);
    $event_2->save();

    // Very Close Futur Event.
    $event_3 = $this->entityTypeManager->getStorage('node')->create([
      'type' => 'event',
      'title' => 'event-3',
      'field_activity' => $activity_1->id(),
      'field_start_at' => '2022-08-20T14:00:00',
      'field_end_at' => '2022-08-20T18:00:00',
    ]);
    $event_3->save();

    // Not so Close Futur Event.
    $event_4 = $this->entityTypeManager->getStorage('node')->create([
      'type' => 'event',
      'title' => 'event-4',
      'field_activity' => $activity_1->id(),
      'field_start_at' => '2022-08-22T14:00:00',
      'field_end_at' => '2022-08-22T18:00:00',
    ]);
    $event_4->save();

    // Only Futur Event.
    $event_5 = $this->entityTypeManager->getStorage('node')->create([
      'type' => 'event',
      'title' => 'event-5',
      'field_activity' => $activity_2->id(),
      'field_start_at' => '2021-08-20T14:00:00',
      'field_end_at' => '2021-08-20T18:00:00',
    ]);
    $event_5->save();

    // Get the closest event from now for each given activity.
    $next_event = $this->eventManager->getNext([$activity_1->id()]);

    self::assertCount(1, $next_event);
    self::assertContainsOnlyInstancesOf(NodeInterface::class, $next_event);
    self::assertArrayHasKey($event_3->id(), $next_event);
    self::assertEquals($event_3->id(), $next_event[$event_3->id()]->id());

    // Get the closest event from now for each given activity.
    $next_event = $this->eventManager->getNext([
      $activity_1->id(),
      $activity_2->id(),
    ]);

    self::assertCount(2, $next_event);
    self::assertContainsOnlyInstancesOf(NodeInterface::class, $next_event);
    self::assertArrayHasKey($event_3->id(), $next_event);
    self::assertArrayHasKey($event_5->id(), $next_event);
    self::assertEquals($event_3->id(), $next_event[$event_3->id()]->id());
    self::assertEquals($event_5->id(), $next_event[$event_5->id()]->id());
  }

}
