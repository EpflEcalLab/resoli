<?php

namespace Drupal\qs_photo\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\taxonomy\TermInterface;

/**
 * PhotoManager.
 */
class PhotoManager {

  /**
   * The node Storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * The database connection to use.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Class constructor.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, Connection $connection) {
    $this->nodeStorage = $entity_type_manager->getStorage('node');
    $this->connection = $connection;
  }

  /**
   * Get photos for the given date range ordered by event end date.
   *
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community entity.
   * @param \Drupal\Core\Datetime\DrupalDateTime $date_start
   *   The start date.
   * @param \Drupal\Core\Datetime\DrupalDateTime $date_end
   *   The end date.
   *
   * @return \Drupal\node\NodeInterface[]
   *   A collection of node's Photo. Otherwise an empty array.
   */
  public function getByDate(TermInterface $community, DrupalDateTime $date_start, DrupalDateTime $date_end) {
    $query = $this->connection->select('node_field_data', 'photo');
    $query->fields('photo', ['nid'])
      ->condition('photo.type', 'photo')
      ->condition('photo.status', TRUE);

    $query->leftJoin('node__field_event', 'field_event', 'field_event.entity_id = photo.nid');
    $query->leftJoin('node__field_activity', 'field_activity', 'field_activity.entity_id = field_event.field_event_target_id');
    $query->leftJoin('node__field_community', 'field_community', 'field_community.entity_id = field_activity.field_activity_target_id');
    $query->condition('field_community.field_community_target_id', [$community->id()], 'IN');

    $query->leftJoin('node__field_end_at', 'field_end_at', 'field_end_at.entity_id = field_event.field_event_target_id');
    $query->condition('field_end_at.field_end_at_value', [$date_start->format('c'), $date_end->format('c')], 'BETWEEN');

    $query->orderBy('field_end_at.field_end_at_value', 'ASC');

    $rows = $query->execute()->fetchAll();

    $nids = [];
    foreach ($rows as $row) {
      $nids[$row->nid] = $row->nid;
    }

    $photos = NULL;
    if ($nids) {
      $photos = $this->nodeStorage->loadMultiple($nids);
    }

    return $photos;
  }

}
