<?php

/**
 * @file
 * Contains alias generation for communities.
 */

use Drupal\Core\Entity\EntityInterface;

/**
 * Generate alias variant for communities based on taxonomy term alias.
 */
function qs_subscription_alias(EntityInterface $entity, $alias, $operation = 'insert') {
  $aliases = [];

  switch ($entity->bundle()) {
    case 'event':
      $aliases = [
        'events_approval' => [
          'route' => '/events/' . $entity->id() . '/dashboard/waiting-approval',
          'alias' => '/' . trim($alias, '/') . '/dashboard/waiting-approval',
        ],
        'events_subscribers' => [
          'route' => '/events/' . $entity->id() . '/dashboard/subscribers',
          'alias' => '/' . trim($alias, '/') . '/dashboard/subscribers',
        ],
      ];
      break;
  }

  if (!$aliases) {
    return;
  }

  qs_urls_aliases_manager($aliases, $operation);
}
