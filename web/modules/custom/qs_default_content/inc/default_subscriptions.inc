<?php

/**
 * @file
 * Contains creation of default subscriptions.
 */

/**
 * Global function to create default subscriptions for events.
 *
 * @SuppressWarnings(PHPMD)
 */
function qs_default_content_subscriptions() {

  /** @var \Drupal\Core\Entity\EntityTypeManagerInterface */
  $entityManager = \Drupal::service('entity_type.manager');
  $subscriptionStorage = $entityManager->getStorage('subscription');

  // Event 36: Activity (3) - In 1 month at 16h00.
  // User 5: manager+lausanne.
  $subscription = $subscriptionStorage->create([
    // Event 36.
    'entity' => 36,
    // User 5.
    'user' => 5,
    // approved.
    'status' => 1,
    'reviewer' => 1,
    'reviewed' => time(),
  ]);
  $subscription->save();

  // Event 38: Activity (3) - Yesterday at 12h00.
  // User 5: manager+lausanne.
  $subscription = $subscriptionStorage->create([
    // Event 38.
    'entity' => 38,
    // User 5.
    'user' => 5,
    // approved.
    'status' => 1,
    'reviewer' => 1,
    'reviewed' => time(),
  ]);
  $subscription->save();

  // Event 40: Activity (5) - In 8 months at 11h00.
  // User 5: manager+lausanne.
  $subscription = $subscriptionStorage->create([
    // Event 40.
    'entity' => 40,
    // User 5.
    'user' => 5,
    // cancel.
    'status' => 0,
    'reviewer' => 1,
    'reviewed' => time(),
  ]);
  $subscription->save();

  // Event 37: Activity (3) - today at 23:00.
  // User 5: manager+lausanne.
  $subscription = $subscriptionStorage->create([
    // Event 37.
    'entity' => 37,
    // User 5.
    'user' => 5,
    // pending.
    'status' => NULL,
  ]);
  $subscription->save();

  // Event 37: Activity (3) - today at 23:00.
  // User 6: organizer+lausanne.
  $subscription = $subscriptionStorage->create([
    // Event 37.
    'entity' => 37,
    // User 6.
    'user' => 6,
    // approved.
    'status' => 1,
    'reviewer' => 1,
    'reviewed' => time(),
  ]);
  $subscription->save();

  // Event 40: Activity (5) - In 8 months at 11h00.
  // User 6: organizer+lausanne.
  $subscription = $subscriptionStorage->create([
    // Event 40.
    'entity' => 40,
    // User 6.
    'user' => 6,
    // approved.
    'status' => 1,
    'reviewer' => 1,
    'reviewed' => time(),
  ]);
  $subscription->save();

  // Event 29: Activity (11) -Today in 2 hours.
  // User 13: member+lausanne+manager+fribourg.
  $subscription = $subscriptionStorage->create([
    // Event 29.
    'entity' => 29,
    // User 13.
    'user' => 13,
    // approved.
    'status' => 1,
    'reviewer' => 1,
    'reviewed' => time(),
  ]);
  $subscription->save();

  // Event 54: Activity (3) - in 2 days.
  // User 5: manager+lausanne.
  $subscription = $subscriptionStorage->create([
    // Event 54.
    'entity' => 54,
    // User 5.
    'user' => 5,
    // approved.
    'status' => 1,
    'reviewer' => 5,
    'reviewed' => time(),
  ]);
  $subscription->save();

  // Event 54: Activity (3) - in 2 days.
  // User 13: member+lausanne+manager+fribourg.
  $subscription = $subscriptionStorage->create([
    // Event 54.
    'entity' => 54,
    // User 13.
    'user' => 13,
    // pending.
    'status' => NULL,
  ]);
  $subscription->save();

  // Event 55: Activity (13) - 3 months ago at 15h15.
  // User 13: member+lausanne+manager+fribourg.
  $subscription = $subscriptionStorage->create([
    // Event 55.
    'entity' => 55,
    // User 13.
    'user' => 13,
    // pending.
    'status' => NULL,
  ]);
  $subscription->save();

  // Event 58: Activity (56) - 3 months ago at 18h12.
  // User 23: member2+fribourg.
  $subscription = $subscriptionStorage->create([
    // Event 58.
    'entity' => 58,
    // User 23.
    'user' => 23,
    // approved.
    'status' => 1,
    'reviewer' => 5,
    'reviewed' => time(),
  ]);
  $subscription->save();

  // Event 59: Activity (56) - Today in 2 hour.
  // User 23: member2+fribourg.
  $subscription = $subscriptionStorage->create([
    // Event 59.
    'entity' => 59,
    // User 23.
    'user' => 23,
    // approved.
    'status' => 1,
    'reviewer' => 5,
    'reviewed' => time(),
  ]);
  $subscription->save();

  // Event 60: Activity (57) - 3 months ago at 16h19.
  // User 23: member2+fribourg.
  $subscription = $subscriptionStorage->create([
    // Event 60.
    'entity' => 60,
    // User 23.
    'user' => 23,
    // approved.
    'status' => 1,
    'reviewer' => 5,
    'reviewed' => time(),
  ]);
  $subscription->save();

  // Event 61: Activity (57) - Today in 30 minutes.
  // User 23: member2+fribourg.
  $subscription = $subscriptionStorage->create([
    // Event 61.
    'entity' => 61,
    // User 23.
    'user' => 23,
    // approved.
    'status' => 1,
    'reviewer' => 5,
    'reviewed' => time(),
  ]);
  $subscription->save();

  // Event 63: Activity (62) - Today in 44 minutes.
  // User 23: member2+fribourg.
  $subscription = $subscriptionStorage->create([
    // Event 63.
    'entity' => 63,
    // User 23.
    'user' => 23,
    // approved.
    'status' => 1,
    'reviewer' => 5,
    'reviewed' => time(),
  ]);
  $subscription->save();
}
