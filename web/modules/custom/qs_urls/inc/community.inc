<?php

/**
 * @file
 * Contains alias generation for communities.
 */

use Drupal\Core\Entity\EntityInterface;

/**
 * Generate alias variant for communities based on taxonomy term alias.
 */
function qs_community_alias(EntityInterface $entity, $alias, $operation = 'insert') {
  $aliases = [];

  switch ($entity->bundle()) {
    case 'communities':
      $aliases = [
        'communities_dashboard' => [
          'route' => '/communities/' . $entity->id() . '/dashboard',
          'alias' => '/' . trim($alias, '/') . '/dashboard',
        ],
        'communities_welcome' => [
          'route' => '/communities/' . $entity->id() . '/welcome',
          'alias' => '/' . trim($alias, '/') . '/welcome',
        ],
        'communities_approval' => [
          'route' => '/communities/' . $entity->id() . '/dashboard/waiting-approval',
          'alias' => '/' . trim($alias, '/') . '/dashboard/waiting-approval',
        ],
        'communities_members' => [
          'route' => '/communities/' . $entity->id() . '/dashboard/members',
          'alias' => '/' . trim($alias, '/') . '/dashboard/members',
        ],
      ];
      break;
  }

  if (!$aliases) {
    return;
  }

  qs_urls_aliases_manager($aliases, $operation);
}
