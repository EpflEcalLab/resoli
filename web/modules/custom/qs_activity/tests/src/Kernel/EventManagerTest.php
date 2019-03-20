<?php

namespace Drupal\Tests\wd_loan\Kernel\Creditum;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\node\NodeInterface;
use Drupal\qs_test\NodeTestTrait;
use Drupal\field\Tests\EntityReference\EntityReferenceTestTrait;

/**
 * @coversDefaultClass \Drupal\qs_activity\Service\EventManager
 *
 * @group qs
 * @group qs_kernel
 * @group qs_activity
 * @group qs_activity_kernel
 */
class EventManagerTest extends EntityKernelTestBase {
  use NodeTestTrait;
  use EntityReferenceTestTrait;

  /**
   * Entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity QS Event Manager.
   *
   * @var \Drupal\qs_activity\Service\EventManager
   */
  protected $eventManager;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'user',
    'system',
    'field',
    'datetime',
    'text',
    'node',
    'qs_acl',
    'qs_subscription',
    'qs_test',
    'qs_activity',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->eventManager = $this->container->get('qs_activity.event_manager');
    $this->entityTypeManager = $this->container->get('entity_type.manager');

    $this->createNodeType('activity');
    $this->createNodeType('event');

    $this->createNodeField('field_start_at', 'datetime', 'event');
    $this->createNodeField('field_end_at', 'datetime', 'event');

    // Create The activity field link between Event & Activity.
    $this->createEntityReferenceField(
      'node',
      'event',
      'field_activity',
      NULL,
      'node',
      'default',
      [],
      1
    );
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
      'type'           => 'event',
      'title'          => 'event-1',
      'field_activity' => $activity_1->id(),
      'field_start_at' => '2018-01-20T00:00:00',
      'field_end_at'   => '2018-01-20T02:00:00',
    ]);
    $event_1->save();

    // Far Futur Event.
    $event_2 = $this->entityTypeManager->getStorage('node')->create([
      'type'           => 'event',
      'title'          => 'event-2',
      'field_activity' => $activity_1->id(),
      'field_start_at' => '2028-01-20T14:00:00',
      'field_end_at'   => '2028-01-20T18:00:00',
    ]);
    $event_2->save();

    // Very Close Futur Event.
    $event_3 = $this->entityTypeManager->getStorage('node')->create([
      'type'           => 'event',
      'title'          => 'event-3',
      'field_activity' => $activity_1->id(),
      'field_start_at' => '2022-08-20T14:00:00',
      'field_end_at'   => '2022-08-20T18:00:00',
    ]);
    $event_3->save();

    // Not so Close Futur Event.
    $event_4 = $this->entityTypeManager->getStorage('node')->create([
      'type'           => 'event',
      'title'          => 'event-4',
      'field_activity' => $activity_1->id(),
      'field_start_at' => '2022-08-22T14:00:00',
      'field_end_at'   => '2022-08-22T18:00:00',
    ]);
    $event_4->save();

    // Only Futur Event.
    $event_5 = $this->entityTypeManager->getStorage('node')->create([
      'type'           => 'event',
      'title'          => 'event-5',
      'field_activity' => $activity_2->id(),
      'field_start_at' => '2021-08-20T14:00:00',
      'field_end_at'   => '2021-08-20T18:00:00',
    ]);
    $event_5->save();

    // Get the closest event from now for each given activity.
    $next_event = $this->eventManager->getNext([$activity_1->id()]);

    $this->assertCount(1, $next_event);
    $this->assertContainsOnlyInstancesOf(NodeInterface::class, $next_event);
    $this->assertArrayHasKey($event_3->id(), $next_event);
    $this->assertEquals($event_3->id(), $next_event[$event_3->id()]->id());

    // Get the closest event from now for each given activity.
    $next_event = $this->eventManager->getNext([$activity_1->id(), $activity_2->id()]);

    $this->assertCount(2, $next_event);
    $this->assertContainsOnlyInstancesOf(NodeInterface::class, $next_event);
    $this->assertArrayHasKey($event_3->id(), $next_event);
    $this->assertArrayHasKey($event_5->id(), $next_event);
    $this->assertEquals($event_3->id(), $next_event[$event_3->id()]->id());
    $this->assertEquals($event_5->id(), $next_event[$event_5->id()]->id());
  }

}
