<?php

namespace Drupal\qs_sharing\Repository;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;
use Drupal\taxonomy\TermInterface;

/**
 * The Offer Repository.
 */
class OfferRepository {

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
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->nodeStorage = $entity_type_manager->getStorage('node');
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
   * @return array|\Drupal\node\NodeInterface[]
   *   A collection of published offers.
   *   Otherwise, an empty array.
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

}
