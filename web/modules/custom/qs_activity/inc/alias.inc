<?php

/**
 * @file
 * Contains alias generation for activities list by communities.
 */

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Language\LanguageInterface;

/**
 * Implements hook_pathauto_alias_alter().
 *
 * Generate alias for lessons based on node alias.
 */
function qs_activity_pathauto_alias_alter(&$alias, array &$context) {

  if (isset($context['module']) &&
  $context['module'] == 'taxonomy' && isset($context['data']['term'])) {
    qs_activity_alias($context['data']['term'], $alias, $context['op']);
  }

  if (isset($context['module']) &&
  $context['module'] == 'node' && isset($context['data']['node'])) {
    qs_activity_alias($context['data']['node'], $alias, $context['op']);
  }
}

/**
 * Generate alias variant for communities based on taxonomy term alias.
 */
function qs_activity_alias(EntityInterface $entity, $alias, $operation = 'insert') {
  // Load services.
  $alias_storage = \Drupal::service('path.alias_storage');
  $langcode = LanguageInterface::LANGCODE_NOT_SPECIFIED;

  $aliases = [];

  switch ($entity->bundle()) {
    case 'communities':
      $aliases = [
        'activities' => [
          'route' => '/activities/' . $entity->id() . '/theme',
          'alias' => '/' . trim($alias, '/') . '/activities',
        ],
        'activities_by_theme' => [
          'route' => '/activities/' . $entity->id() . '/theme',
          'alias' => '/' . trim($alias, '/') . '/activities/theme',
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
      ];
      break;
  }

  if (!$aliases) {
    return;
  }

  // TODO: translation the alias_suffix before creation/deletion,
  // on every language.
  // Generate every alias for the current community alias.
  foreach ($aliases as $item) {
    // Generate alias.
    if ($operation == 'insert' || $operation == 'update' || $operation == 'bulkupdate') {
      if ($alias_storage->aliasExists($item['alias'], $langcode) === FALSE) {
        $alias_storage->save($item['route'], $item['alias'], $langcode);
      }
    }

    // Delete alias on operation delete.
    if ($operation == 'delete') {
      if ($alias_storage->aliasExists($item['alias'], $langcode) === FALSE) {
        $alias_storage->delete([
          'source'   => $item['route'],
          'alias'    => $item['alias'],
          'langcode' => $langcode,
        ]);
      }
    }
  }
}
