<?php

/**
 * @file
 * Contains alias generation for activities list by communities.
 */

use Drupal\Core\Entity\EntityInterface;

/**
 * Generate alias variant for calendar based on given entity alias.
 */
function qs_calendar_alias(EntityInterface $entity, $alias, $operation = 'insert') {
  $aliases = [];

  switch ($entity->bundle()) {
    case 'communities':
      $aliases = [
        'calendar_weekly' => [
          'route' => '/calendar/' . $entity->id() . '/weekly',
          'alias' => '/' . trim($alias, '/') . '/calendar/weekly',
        ],
        'calendar_monthly' => [
          'route' => '/calendar/' . $entity->id() . '/monthly',
          'alias' => '/' . trim($alias, '/') . '/calendar/monthly',
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
function qs_calendar_redirects(EntityInterface $entity, $alias, $operation = 'insert') {
  $redirects = [];

  switch ($entity->bundle()) {
    case 'communities':
      $redirects = [
        'calendar' => [
          'source' => '/' . trim($alias, '/') . '/calendar',
          'redirect' => '/' . trim($alias, '/') . '/calendar/weekly',
        ],
      ];

      break;
  }

  if (!$redirects) {
    return;
  }

  qs_urls_redirects_manager($redirects, $operation);
}
