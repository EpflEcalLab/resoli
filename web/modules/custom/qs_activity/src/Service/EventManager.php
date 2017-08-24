<?php

namespace Drupal\qs_activity\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * EventManager.
 */
class EventManager {
  /**
   * EntityTypeManagerInterface to load Node(s)
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $nodeStorage;

  /**
   * The entity query factory.
   *
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $queryFactory;

  /**
   * Class constructor.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, QueryFactory $query_factory) {
    $this->nodeStorage  = $entity_type_manager->getStorage('node');
    $this->queryFactory = $query_factory;
  }

  /**
   * TODO: comment.
   */
  public function getNextEventByActivities(array $activities_nids) {
    $now = new DrupalDateTime();

    if (!$activities_nids) {
      return NULL;
    }

    // Get every activity that belongs to the current community.
    $query = $this->queryFactory->get('node')
      ->condition('type', 'event')
      ->condition('field_start_at', $now, '>=')
      ->condition('field_end_at', $now, '>=')
      ->condition('status', TRUE)
      ->condition('field_activity', $activities_nids, 'IN')
      ->sort('field_start_at', 'ASC')
      ->groupBy('field_activity');

    $nids = $query->execute();
    $events = NULL;
    if ($nids) {
      $events = $this->nodeStorage->loadMultiple($nids);
    }

    return $events;
  }

}
