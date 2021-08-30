<?php

namespace Drupal\qs_sharing\Repository;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\taxonomy\TermInterface;

/**
 * The Offer's Type Repository.
 */
class OfferTypeRepository {

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
  private $nodeStorage;

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
   * Get all offer type in a community for a specific sharing theme.
   *
   * This method will return only offer type with at least one published offer.
   *
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community entity.
   * @param \Drupal\taxonomy\TermInterface $theme
   *   The sharing theme.
   *
   * @return array|\Drupal\node\NodeInterface[]
   *   A collection of sharing offers with at least one offer, count of offers.
   *   Otherwise, an empty array.
   */
  public function getAllByCommunityByThemeWithOffersCount(TermInterface $community, TermInterface $theme): ?array {
    $query = $this->database->select('node_field_data', 'offer');
    $query->fields('offer', ['nid'])
      ->condition('offer.type', 'offer')
      ->condition('offer.status', TRUE);

    $query->leftJoin('node__field_theme', 'field_theme', 'field_theme.entity_id = offer.nid');
    $query->condition('field_theme.field_theme_target_id', [$theme->id()], 'IN');

    $query->leftJoin('node__field_offer_type', 'field_offer_type', 'field_offer_type.entity_id = offer.nid');
    $query->leftJoin('node__field_community', 'field_community', 'field_community.entity_id = field_offer_type.field_offer_type_target_id');
    $query->condition('field_community.field_community_target_id', [$community->id()], 'IN');

    $query->addExpression('COUNT(*)', 'offersByType');

    $query->groupBy('field_offer_type.field_offer_type_target_id');
    $query->orderBy('offersByType', 'DESC');

    $tuples = $query->execute()->fetchAll();

    $offerTypes = [];

    foreach ($tuples as $tuple) {
      /** @var \Drupal\node\NodeInterface $offerType */
      $offerType = $this->nodeStorage->load($tuple->nid);
      $offerType->offersCount = $tuple->offersByType;
      $offerTypes[] = $offerType;
    }

    return $offerTypes;
  }

}
