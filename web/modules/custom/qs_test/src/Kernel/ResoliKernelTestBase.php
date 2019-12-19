<?php

namespace Drupal\qs_test\Kernel;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\taxonomy\TermInterface;
use Drupal\node\NodeInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\field\Tests\EntityReference\EntityReferenceTestTrait;
use Drupal\qs_test\TaxonomyTestTrait;
use Drupal\qs_test\UserTestTrait;
use Drupal\qs_test\NodeTestTrait;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

/**
 * Provides a base class for Quartiers Solidaires functional tests.
 */
class ResoliKernelTestBase extends EntityKernelTestBase {
  use UserTestTrait;
  use NodeTestTrait;
  use TaxonomyTestTrait;
  use EntityReferenceTestTrait;

  /**
   * Modules to enable.
   *
   * Note that when a child class declares its own $modules list, that list
   * doesn't override this one, it just extends it.
   *
   * @var array
   *
   * @see \Drupal\simpletest\WebTestBase::installModulesFromClassProperty()
   */
  public static $modules = [
    'user',
    'qs_test',
    'system',
    'field',
    'node',
    'taxonomy',
    'text',
    'datetime',
    'views',
    'path',
    'options',
    'qs_acl',
  ];

  /**
   * The Entity Type Manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    /** @var \Drupal\Core\Entity\EntityTypeManager $entityTypeManager */
    $this->entityTypeManager = $this->container->get('entity_type.manager');

    $this->installEntitySchema('user');
    $this->installSchema('system', 'router');

    $this->installEntitySchema('node');

    $this->installEntitySchema('taxonomy_term');
    $this->setupTaxonomy();

    $this->setupAnonymous();
  }

  /**
   * Prepare Community Taxonomy fields.
   */
  protected function setupCommunities() {
    $this->createVocabulary('communities');
  }

  /**
   * Prepare Activitiy Node fields.
   */
  protected function setupActivities() {
    $this->createNodeType('activity');

    // Add the field "Belongs to community".
    $this->createEntityReferenceField(
      'node',
      'activity',
      'field_community',
      NULL,
      'taxonomy_term'
    );

  }

  /**
   * Prepare Event Node fields.
   */
  protected function setupEvents() {
    $this->createNodeType('event');

    $this->createNodeField('field_start_at', 'datetime', 'event', [
      'datetime_type' => DateTimeItem::DATETIME_TYPE_DATETIME,
    ]);

    $this->createNodeField('field_end_at', 'datetime', 'event', [
      'datetime_type' => DateTimeItem::DATETIME_TYPE_DATETIME,
    ]);

    // Add the field "Belongs to activity".
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
   * Seed some communities for testing.
   *
   * @param int $number
   *   Number of communities to generate.
   *
   * @return \Drupal\taxonomy\TermInterface[]
   *   A collection of communities keyed by entity ID.
   */
  protected function seedCommunities($number) {
    $communities = [];
    for ($i = 0; $i < $number; $i++) {
      $entity = $this->entityTypeManager->getStorage('taxonomy_term')->create([
        'vid' => 'communities',
        'name' => $this->randomString(),
      ]);
      $entity->save();
      $communities[$entity->id()] = $entity;
    }
    return $communities;
  }

  /**
   * Seed some activities into the given community for testing.
   *
   * @param Drupal\taxonomy\TermInterface $community
   *   The communitiy to seed activities into.
   * @param int $number
   *   Number of activities to generate.
   *
   * @return \Drupal\node\NodeInterface[]
   *   A collection of activitiy keyed by entity ID.
   */
  protected function seedActivities(TermInterface $community, $number) {
    $activities = [];
    for ($i = 0; $i < $number; $i++) {
      $entity = $this->entityTypeManager->getStorage('node')->create([
        'type' => 'activity',
        'field_community' => $community->id(),
        'title' => $this->randomString(),
      ]);
      $entity->save();
      $activities[$entity->id()] = $entity;
    }
    return $activities;
  }

  /**
   * Seed some events into the given community for testing.
   *
   * @param \Drupal\node\NodeInterface $activity
   *   The community to seed activities into.
   * @param \Drupal\Core\Datetime\DrupalDateTime $min_date_start
   *   The minimum start date for generated events.
   * @param \Drupal\Core\Datetime\DrupalDateTime $max_date_end
   *   The maximum end date for generated events.
   * @param int $number
   *   Number of activities to generate.
   *
   * @return \Drupal\node\NodeInterface[]
   *   A collection of events keyed by entity ID.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function seedEvents(NodeInterface $activity, DrupalDateTime $min_date_start, DrupalDateTime $max_date_end, $number) {
    $events = [];
    for ($i = 0; $i < $number; $i++) {

      $random_timestamp = mt_rand($min_date_start->getTimestamp(), $max_date_end->getTimestamp());
      $start_at = new DrupalDateTime();
      $start_at->setTimestamp($random_timestamp);

      $random_timestamp = mt_rand($start_at->getTimestamp(), $max_date_end->getTimestamp());
      $end_at = new DrupalDateTime();
      $end_at->setTimestamp($random_timestamp);

      $entity = $this->entityTypeManager->getStorage('node')->create([
        'type' => 'event',
        'field_activity' => $activity->id(),
        'title' => $this->randomString(),
        'field_start_at' => $start_at->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT),
        'field_end_at' => $end_at->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT),
      ]);
      $entity->save();
      $events[$entity->id()] = $entity;
    }
    return $events;
  }

}
