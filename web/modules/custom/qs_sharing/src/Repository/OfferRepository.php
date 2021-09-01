<?php

namespace Drupal\qs_sharing\Repository;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\user\UserInterface;

/**
 * The Offer Repository.
 */
class OfferRepository {

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
   * Get all offers bellowing to a community.
   *
   * This method will return only published offers.
   *
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community.
   *
   * @return array|\Drupal\node\NodeInterface[]
   *   A collection of published offers.
   *   Otherwise, an empty array.
   */
  public function getAllByCommunity(TermInterface $community): ?array {
    $query = $this->database->select('node_field_data', 'offer');
    $query->fields('offer', ['nid'])
      ->condition('offer.type', 'offer')
      ->condition('offer.status', TRUE);

    $query->leftJoin('node__field_theme', 'field_theme', 'field_theme.entity_id = offer.nid');

    $query->leftJoin('node__field_offer_type', 'field_offer_type', 'field_offer_type.entity_id = offer.nid');
    $query->fields('field_offer_type', ['field_offer_type_target_id']);

    $query->leftJoin('node__field_community', 'field_community', 'field_community.entity_id = field_offer_type.field_offer_type_target_id');
    $query->condition('field_community.field_community_target_id', [$community->id()], 'IN');

    $query->orderBy('field_offer_type.field_offer_type_target_id', 'DESC');
    $query->orderBy('field_theme.field_theme_target_id', 'DESC');

    $query->execute()->fetchAll();

    $ids = array_map(static function ($tuple) {
      return $tuple->nid;
    }, $query->execute()->fetchAll());

    if (empty($ids) || !\is_array($ids)) {
      return NULL;
    }

    return $this->nodeStorage->loadMultiple($ids);
  }

  /**
   * Get all offers belonging to an offer type in a specific theme.
   *
   * This method will return only published offers.
   *
   * @param \Drupal\node\NodeInterface $offer_type
   *   The offer type theme.
   * @param \Drupal\taxonomy\TermInterface $theme
   *   The sharing theme.
   *
   * @return array|\Drupal\node\NodeInterface[]|null
   *   A collection of published offers.
   *   Otherwise, an empty array or NULL.
   */
  public function getAllByOffersByTypeByTheme(NodeInterface $offer_type, TermInterface $theme): ?array {
    $query = $this->nodeStorage->getQuery()
      ->accessCheck(TRUE)
      ->condition('type', 'offer')
      ->condition('status', TRUE)
      ->condition('field_offer_type', $offer_type->id())
      ->condition('field_theme', $theme->id());
    $ids = $query->execute();

    if (empty($ids) || !\is_array($ids)) {
      return NULL;
    }

    return $this->nodeStorage->loadMultiple($ids);
  }

  /**
   * Get all offers for a given user.
   *
   * @param \Drupal\user\UserInterface $user
   *   The user for which we want to retrieve the related offers.
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community entity.
   *
   * @return array|\Drupal\node\NodeInterface[]
   *   A collection of node's Offer. Otherwise an empty array.
   */
  public function getAllOffersByUser(UserInterface $user, TermInterface $community) {
    // Get every activity that belongs to the current community.
    $query = $this->database->select('node_field_data', 'offer');
    $query->fields('offer', ['nid'])
      ->condition('offer.uid', $user->id());

    $query->leftJoin('node__field_offer_type', 'field_offer_type', 'field_offer_type.entity_id = offer.nid');
    $query->leftJoin('node__field_community', 'field_community', 'field_community.entity_id = field_offer_type.field_offer_type_target_id');
    $query->condition('field_community.field_community_target_id', [$community->id()], 'IN');

    // Order first the published, then the archived entites.
    $query->leftJoin('content_moderation_state_field_data', 'content_moderation_state', 'content_moderation_state.content_entity_id = offer.nid');
    $query->condition(
      'content_moderation_state.moderation_state',
      ['published', 'archived'],
      'IN'
    );
    $query->orderBy('content_moderation_state.moderation_state', 'DESC');

    $tuples = $query->execute()->fetchAll();

    $offers = [];

    foreach ($tuples as $tuple) {
      /** @var \Drupal\node\NodeInterface $offer */
      $offers[] = $this->nodeStorage->load($tuple->nid);
    }

    return $offers;
  }

}
