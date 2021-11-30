<?php

namespace Drupal\qs_sharing\Repository;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\taxonomy\TermInterface;

/**
 * The Request Repository.
 */
class RequestRepository {

  /**
   * Active database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The node Storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * Class constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Database\Connection $database
   *   A Database connection to use for reading and writing database data.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, Connection $database) {
    $this->nodeStorage = $entity_type_manager->getStorage('node');
    $this->database = $database;
  }

  /**
   * Get all requests bellowing to a community.
   *
   * This method will return only published requests.
   *
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community.
   *
   * @return array|\Drupal\node\NodeInterface[]
   *   A collection of published requests.
   *   Otherwise, an empty array.
   */
  public function getAllByCommunity(TermInterface $community): ?array {
    $query = $this->database
      ->select('node_field_data', 'request');
    $query->fields('request', ['nid'])
      ->condition('request.type', 'request')
      ->condition('request.status', TRUE);

    $query->leftJoin('node__field_theme', 'field_theme', 'field_theme.entity_id = request.nid');

    $query->leftJoin('node__field_community', 'field_community', 'field_community.entity_id = request.nid');
    $query->condition('field_community.field_community_target_id', [$community->id()], 'IN');

    $query->leftJoin('content_moderation_state_field_data', 'content_moderation_state', 'content_moderation_state.content_entity_id = request.nid');
    $query->orderBy('content_moderation_state.moderation_state');
    $query->orderBy('changed', 'DESC');

    $ids = array_map(static function ($tuple) {
      return $tuple->nid;
    }, $query->execute()->fetchAll());

    if (empty($ids) || !\is_array($ids)) {
      return NULL;
    }

    return $this->nodeStorage->loadMultiple($ids);
  }

}
