<?php

/**
 * @file
 * Contains alias generation for activities list by communities.
 */

use Drupal\Core\Entity\EntityInterface;

/**
 * Generate alias variant for activities based on given entity alias.
 */
function qs_activity_alias(EntityInterface $entity, $alias, $operation = 'insert') {
  $aliases = [];

  switch ($entity->bundle()) {
    case 'communities':
      $aliases = [
        'activities_by_theme' => [
          'route' => '/activities/' . $entity->id() . '/theme',
          'alias' => '/' . trim($alias, '/') . '/activities/theme',
        ],
        'activities_by_date' => [
          'route' => '/activities/' . $entity->id() . '/date',
          'alias' => '/' . trim($alias, '/') . '/activities/date',
        ],
        'activities_form_add' => [
          'route' => '/activities/' . $entity->id() . '/add',
          'alias' => '/' . trim($alias, '/') . '/activities/add',
        ],
      ];
      break;

    case 'activity':
      $aliases = [
        'events_form_add' => [
          'route' => '/events/' . $entity->id() . '/add',
          'alias' => '/' . trim($alias, '/') . '/events/add',
        ],
        'activities_dashboard' => [
          'route' => '/activities/' . $entity->id() . '/dashboard',
          'alias' => '/' . trim($alias, '/') . '/dashboard',
        ],
        'activities_form_edit_info' => [
          'route' => '/activities/' . $entity->id() . '/edit/info',
          'alias' => '/' . trim($alias, '/') . '/edit/info',
        ],
        'activities_form_edit_visibility' => [
          'route' => '/activities/' . $entity->id() . '/edit/visibility',
          'alias' => '/' . trim($alias, '/') . '/edit/visibility',
        ],
        'activities_form_edit_defaults' => [
          'route' => '/activities/' . $entity->id() . '/edit/defaults',
          'alias' => '/' . trim($alias, '/') . '/edit/defaults',
        ],
        'activities_form_delete' => [
          'route' => '/activities/' . $entity->id() . '/delete',
          'alias' => '/' . trim($alias, '/') . '/delete',
        ],
        'activities_members' => [
          'route' => '/activities/' . $entity->id() . '/dashboard/members',
          'alias' => '/' . trim($alias, '/') . '/dashboard/members',
        ],
      ];
      break;

    case 'event':
      $aliases = [
        'events_dashboard' => [
          'route' => '/events/' . $entity->id() . '/dashboard',
          'alias' => '/' . trim($alias, '/') . '/dashboard',
        ],
        'events_form_edit' => [
          'route' => '/events/' . $entity->id() . '/edit',
          'alias' => '/' . trim($alias, '/') . '/edit',
        ],
        'events_form_delete' => [
          'route' => '/events/' . $entity->id() . '/delete',
          'alias' => '/' . trim($alias, '/') . '/delete',
        ],
        'events_subscribers' => [
          'route' => '/events/' . $entity->id() . '/dashboard/subscribers',
          'alias' => '/' . trim($alias, '/') . '/dashboard/subscribers',
        ],
        'events_approval' => [
          'route' => '/events/' . $entity->id() . '/dashboard/waiting-approval',
          'alias' => '/' . trim($alias, '/') . '/dashboard/waiting-approval',
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
function qs_activity_redirects(EntityInterface $entity, $alias, $operation = 'insert') {
  $redirects = [];

  switch ($entity->bundle()) {
    case 'communities':
      $redirects = [
        'activities_by_theme' => [
          'source'   => '/' . trim($alias, '/') . '/activities',
          'redirect' => '/' . trim($alias, '/') . '/activities/theme',
        ],
      ];
      break;

    case 'activity':
      $redirects = [
        'events' => [
          'source'   => '/' . trim($alias, '/') . '/events',
          'redirect' => '/' . trim($alias, '/'),
        ],
      ];
      break;
  }

  if (!$redirects) {
    return;
  }

  qs_urls_redirects_manager($redirects, $operation);
}
