<?php

namespace Drupal\qs_badge\Service;

/**
 * BadgeManager.
 */
class BadgeManager {

  /**
   * Get for the given events IDs, if they have subscriptions.
   *
   * @param integer[] $events
   *   A collection of events IDs.
   * @param bool $status
   *   The required status for the subscriptions.
   *
   * @return array[]
   *   The collection of events IDs which have subscriptions.
   */
  public function getSubscription(array $events, $status = TRUE) {
    return [];
  }

  /**
   * Count for the given events IDs, if they have subscriptions.
   *
   * @param integer[] $events
   *   A collection of $events IDs.
   * @param bool $status
   *   The required status for the subscriptions.
   *
   * @return array[]
   *   The collection of events IDs which have subscriptions.
   */
  public function countSubscriptions(array $events, $status = TRUE) {
    return [];
  }

}
