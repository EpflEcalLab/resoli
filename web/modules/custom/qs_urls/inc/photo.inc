<?php

/**
 * @file
 * Contains alias generation for photos list by communities.
 */

use Drupal\Core\Entity\EntityInterface;

/**
 * Generate alias variant for photo based on given entity alias.
 */
function qs_photo_alias(EntityInterface $entity, $alias, $operation = 'insert') {
  $aliases = [];

  switch ($entity->bundle()) {
    case 'communities':
      $aliases = [
        'photo_by_event' => [
          'route' => '/photos/' . $entity->id() . '/theme',
          'alias' => '/' . trim($alias, '/') . '/photos/theme',
        ],
        'photo_by_month' => [
          'route' => '/photos/' . $entity->id() . '/month',
          'alias' => '/' . trim($alias, '/') . '/photos/month',
        ],
      ];

      break;

    case 'activity':
      $aliases = [
        'activity' => [
          'route' => '/photos/activity/' . $entity->id(),
          'alias' => '/' . trim($alias, '/') . '/photos',
        ],
      ];

      break;
  }

  if (!$aliases) {
    return;
  }

  qs_urls_aliases_manager($aliases, $operation);
}

/**
 * Generate redirect variant for given entity.
 */
function qs_photo_redirects(EntityInterface $entity, $alias, $operation = 'insert') {
  $redirects = [];

  switch ($entity->bundle()) {
    case 'communities':
      $redirects = [
        'photo' => [
          'source' => '/' . trim($alias, '/') . '/photos',
          'redirect' => '/' . trim($alias, '/') . '/photos/theme',
        ],
      ];

      break;
  }

  if (!$redirects) {
    return;
  }

  qs_urls_redirects_manager($redirects, $operation);
}
