<?php

namespace Drupal\qs_sharing\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\user\UserInterface;

/**
 * The Offer Manager.
 */
class OfferManager {

  /**
   * The database connection to use.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * The node Storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * Class constructor.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, Connection $connection) {
    $this->nodeStorage = $entity_type_manager->getStorage('node');
    $this->connection = $connection;
  }

  /**
   * Get all offers for a given user.
   *
   * @param \Drupal\user\UserInterface $user
   *   The user for which we want to retrieve the related offers.
   *
   * @return \Drupal\node\NodeInterface[]
   *   A collection of node's Offer. Otherwise an empty array.
   */
  public function getAllByUser(UserInterface $user) {
    // Get every activity that belongs to the current community.
    $query = $this->nodeStorage->getQuery()
      ->condition('type', 'offer')
      ->condition('uid', $user->id());

    $nids = $query->execute();
    $offers = NULL;

    if ($nids) {
      $offers = $this->nodeStorage->loadMultiple($nids);
    }

    return $offers;
  }

}
