<?php

namespace Drupal\qs_sharing\Repository;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\user\UserInterface;

/**
 * The Offer repository.
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
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, Connection $database) {
    $this->nodeStorage = $entity_type_manager->getStorage('node');
    $this->database = $database;
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
      ->condition('uid', $user->id());

    $query->leftJoin('node__field_offer_type', 'field_offer_type', 'field_offer_type.entity_id = offer.nid');
    $query->leftJoin('node__field_community', 'field_community', 'field_community.entity_id = field_offer_type.field_offer_type_target_id');
    $query->condition('field_community.field_community_target_id', [$community->id()], 'IN');

    $tuples = $query->execute()->fetchAll();

    $offers = [];

    foreach ($tuples as $tuple) {
      /** @var \Drupal\node\NodeInterface $offer */
      $offers[] = $this->nodeStorage->load($tuple->nid);
    }

    return $offers;
  }

}
