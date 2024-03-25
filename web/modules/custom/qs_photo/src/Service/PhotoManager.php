<?php

namespace Drupal\qs_photo\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\node\NodeInterface;
use Drupal\qs_acl\Service\AccessControl;

/**
 * The Photo Manager.
 */
class PhotoManager {

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
   * The node Storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * The Privilege Storage.
   *
   * @var \Drupal\Core\Entity\ContentEntityStorageInterface
   */
  private $privilegeStorage;

  /**
   * Class constructor.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, Connection $connection, AccountProxyInterface $current_user, AccessControl $acl) {
    $this->nodeStorage = $entity_type_manager->getStorage('node');
    $this->privilegeStorage = $entity_type_manager->getStorage('privilege');
    $this->connection = $connection;
    $this->currentUser = $current_user;
    $this->acl = $acl;
  }

  /**
   * Create a Photo.
   *
   * @param \Drupal\node\NodeInterface $event
   *   The event.
   * @param mixed $file
   *   Uploaded file.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *
   * @return \Drupal\node\NodeInterface
   *   The created photo.
   */
  public function create(NodeInterface $event, $file) {
    $photo = $this->nodeStorage->create([
      'type' => 'photo',
      'status' => TRUE,
      'title' => $file->get('filename')->value . ' - ' . $event->getTitle(),
      'field_event' => $event->id(),
      'field_image' => $file,
      'uid' => $this->currentUser->id(),
    ]);
    $photo->save();

    return $photo;
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
      // Where the current user has access to.
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

    $query->leftJoin('node__field_end_at', 'field_end_at', 'field_end_at.entity_id = field_event.field_event_target_id');
    $query->orderBy('field_end_at.field_end_at_value', 'DESC');
    $query->orderBy('photo.created', 'ASC');
    $query->orderBy('photo.nid', 'ASC');
    $query->orderBy('field_event.field_event_target_id', 'DESC');

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

  /**
   * From given events, get photos according ACL.
   *
   * The photos will be ordered from present to past.
   *
   * @param \Drupal\node\NodeInterface[] $events
   *   Events collection to filter by ACL.
   *
   * @return \Drupal\node\NodeInterface[]
   *   A collection of node's Photo. Otherwise an empty array.
   */
  public function getByEvents(array $events) {
    $nids = [];

    foreach ($events as $event) {
      $nids[$event->nid->value] = $event->nid->value;
    }

    // If the user has access bypass, display all photos, don't check access.
    if (!$this->acl->hasBypass()) {
      // From given $events
      // Get only the ones which are Photos open to community.
      $query_open = $this->connection->select('node_field_data', 'event');
      $query_open->fields('event', ['nid']);
      $query_open->condition('event.nid', $nids, 'IN');

      // Get events which belongs to activities with opened access of photos
      // to the community.
      $query_open->leftJoin('node__field_activity', 'field_activity', 'field_activity.entity_id = event.nid');
      $query_open->leftJoin('node__field_community_access_gallery', 'access_gallery', 'access_gallery.entity_id = field_activity.field_activity_target_id');
      $query_open->condition('access_gallery.field_community_access_gallery_value', TRUE);

      $rows = $query_open->execute()->fetchAll();

      $opens_event = [];

      foreach ($rows as $row) {
        $opens_event[$row->nid] = $row->nid;
      }

      // From given $events
      // Get only the ones where user has at least one privilege.
      $query_privileges = $this->connection->select('node_field_data', 'event');
      $query_privileges->fields('event', ['nid']);
      $query_privileges->condition('event.nid', $nids, 'IN');

      // Get events where the use has activity privilege on it.
      $query_privileges->leftJoin('node__field_activity', 'field_activity', 'field_activity.entity_id = event.nid');
      $query_privileges->leftJoin('privileges', 'privileges', 'privileges.entity = field_activity.field_activity_target_id');
      $query_privileges->condition('privileges.status', TRUE)
        ->condition('privileges.user', $this->currentUser->id())
        ->condition('privileges.status', 1);

      $or = $query_privileges->orConditionGroup();
      $or->condition('privileges.privilege', 'activity_members');
      $or->condition('privileges.privilege', 'activity_maintainers');
      $or->condition('privileges.privilege', 'activity_organizers');
      $query_privileges->condition($or);

      $rows = $query_privileges->execute()->fetchAll();

      $privileges_event = [];

      foreach ($rows as $row) {
        $privileges_event[$row->nid] = $row->nid;
      }

      // Merge opens activities event & privileged ones to get all photos.
      // Where the current user has access to.
      $nids = array_merge($opens_event, $privileges_event);
    }

    if (!$nids) {
      return NULL;
    }

    $query = $this->connection->select('node_field_data', 'photo');
    $query->fields('photo', ['nid'])
      ->condition('photo.type', 'photo')
      ->condition('photo.status', TRUE);

    $query->leftJoin('node__field_event', 'field_event', 'field_event.entity_id = photo.nid');
    $query->condition('field_event.field_event_target_id', $nids, 'IN');

    $query->leftJoin('node__field_end_at', 'field_end_at', 'field_end_at.entity_id = field_event.field_event_target_id');
    $query->orderBy('field_end_at.field_end_at_value', 'DESC');
    $query->orderBy('photo.created', 'ASC');
    $query->orderBy('photo.nid', 'ASC');
    $query->orderBy('field_event.field_event_target_id', 'DESC');

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
   * Get all owned photos of the $user in the given $activity.
   *
   * @param \Drupal\node\NodeInterface $activity
   *   Base activity where photos should come from.
   * @param \Drupal\Core\Session\AccountInterface $user
   *   The user entity.
   *
   * @return \Drupal\node\NodeInterface[]
   *   A collection of node's Photo. Otherwise an empty array.
   */
  public function getByUser(NodeInterface $activity, AccountInterface $user) {
    $query = $this->connection->select('node_field_data', 'photo');
    $query->fields('photo', ['nid'])
      ->condition('photo.type', 'photo')
      ->condition('photo.status', TRUE);

    // List all photos if user has bypass access.
    if (!$this->acl->hasBypass()) {
      $query->condition('photo.uid', $user->id());
    }

    $query->leftJoin('node__field_event', 'field_event', 'field_event.entity_id = photo.nid');
    $query->leftJoin('node__field_activity', 'field_activity', 'field_activity.entity_id = field_event.field_event_target_id');
    $query->condition('field_activity.field_activity_target_id', $activity->id());

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
   * Get all writable photos for the $user in the given $activity.
   *
   * @param \Drupal\node\NodeInterface $activity
   *   Base activity where photos should come from.
   * @param \Drupal\Core\Session\AccountInterface $user
   *   The user entity.
   *
   * @return \Drupal\node\NodeInterface[]
   *   A collection of node's Photo. Otherwise an empty array.
   */
  public function getWritablePhotoByUser(NodeInterface $activity, AccountInterface $user) {
    // List all photos if user has bypass access.
    if ($this->acl->hasBypass()) {
      return $this->getByActivity($activity);
    }

    // When user has members+ access, list all photos of this activity.
    $query = $this->privilegeStorage->getAggregateQuery()
      ->accessCheck()
      ->condition('status', 1)
      ->condition('bundle', 'node')
      ->condition('entity', $activity->id())
      ->condition('user', $user->id());
    $or = $query->orConditionGroup();
    $or->condition('privilege', 'activity_organizers');
    $or->condition('privilege', 'activity_maintainers');
    $query->condition($or);

    if ($query->count()->execute()) {
      return $this->getByActivity($activity);
    }

    // If the user has only member access, list only its photos.
    $query = $this->privilegeStorage->getAggregateQuery()
      ->accessCheck()
      ->condition('status', 1)
      ->condition('bundle', 'node')
      ->condition('entity', $activity->id())
      ->condition('user', $user->id())
      ->condition('privilege', 'activity_members');

    if ($query->count()->execute() > 0) {
      return $this->getByUser($activity, $user);
    }

    return [];
  }

}
