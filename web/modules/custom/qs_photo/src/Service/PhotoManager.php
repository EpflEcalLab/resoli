<?php

namespace Drupal\qs_photo\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\node\NodeInterface;

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
   * The current user service.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * Class constructor.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, Connection $connection, AccountProxyInterface $current_user, AccessControl $acl) {
    $this->nodeStorage = $entity_type_manager->getStorage('node');
    $this->connection = $connection;
    $this->currentUser = $current_user;
    $this->acl = $acl;
  }

  /**
   * From given activities, get photos according ACL.
   *
   * @param \Drupal\node\NodeInterface[] $activities
   *   Activities collection to filter by ACL.
   *
   * @return \Drupal\node\NodeInterface[]
   *   A collection of node's Photo. Otherwise an empty array.
   */
  public function getByActivities(array $activities) {
    $nids = [];
    foreach ($activities as $activity) {
      $nids[$activity->nid->value] = $activity->nid->value;
    }

    // If the user has access bypass, display all photos, don't check access.
    if (!$this->acl->hasBypass()) {

      // From given $activities
      // Get only the ones which are Photos open to community.
      $query_open = $this->connection->select('node_field_data', 'activity');
      $query_open->fields('activity', ['nid']);
      $query_open->condition('activity.nid', $nids, 'IN');
      $query_open->leftJoin('node__field_community_access_gallery', 'access_gallery', 'access_gallery.entity_id = activity.nid');
      $query_open->condition('access_gallery.field_community_access_gallery_value', TRUE);

      $rows = $query_open->execute()->fetchAll();

      $opens_activity = [];
      foreach ($rows as $row) {
        $opens_activity[$row->nid] = $row->nid;
      }

      // From given $activities
      // Get only the ones where user has at least one privilege.
      $query_privileges = $this->connection->select('privileges', 'privileges');
      $query_privileges->fields('privileges', ['user', 'entity'])
        ->condition('privileges.status', TRUE)
        ->condition('privileges.user', $this->currentUser->id())
        ->condition('privileges.entity', $nids, 'IN');
      $or = $query_privileges->orConditionGroup();
      $or->condition('privileges.privilege', 'activity_members');
      $or->condition('privileges.privilege', 'activity_maintainers');
      $or->condition('privileges.privilege', 'activity_organizers');
      $query_privileges->condition($or);

      $rows = $query_privileges->execute()->fetchAll();

      $privileges_activity = [];
      foreach ($rows as $row) {
        $privileges_activity[$row->entity] = $row->entity;
      }

      // Merge opens activities & privileged ones to get all photos.
      $nids = array_merge($opens_activity, $privileges_activity);
    }

    if (!$nids) {
      return NULL;
    }

    $query = $this->connection->select('node_field_data', 'photo');
    $query->fields('photo', ['nid'])
      ->condition('photo.type', 'photo')
      ->condition('photo.status', TRUE);

    $query->leftJoin('node__field_event', 'field_event', 'field_event.entity_id = photo.nid');
    $query->leftJoin('node__field_activity', 'field_activity', 'field_activity.entity_id = field_event.field_event_target_id');

    $query->condition('field_activity.field_activity_target_id', $nids, 'IN');

    $query->leftJoin('node__field_end_at', 'field_end_at', 'field_end_at.entity_id = field_event.field_event_target_id');
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

  /**
   * Get Photos for given activity, in the given limit according ACL.
   *
   * @param \Drupal\node\NodeInterface $activity
   *   Base activity where photos should come from.
   * @param int $limit
   *   Maximum of photos by activity.
   *
   * @return \Drupal\node\NodeInterface[]
   *   A collection of node's Photo. Otherwise an empty array.
   */
  public function getByActivity(NodeInterface $activity, $limit = NULL) {
    if (!$this->acl->hasAccessPhoto($activity)) {
      return [];
    }

    $query = $this->connection->select('node_field_data', 'photo');
    $query->fields('photo', ['nid'])
      ->condition('photo.type', 'photo')
      ->condition('photo.status', TRUE);

    $query->leftJoin('node__field_event', 'field_event', 'field_event.entity_id = photo.nid');
    $query->leftJoin('node__field_activity', 'field_activity', 'field_activity.entity_id = field_event.field_event_target_id');
    $query->condition('field_activity.field_activity_target_id', $activity->id());

    if ($limit) {
      $query->addTag('random');
      $query->range(0, $limit);
    }

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
