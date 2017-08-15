<?php

/**
 * @file
 * Contains alias generation for activities list by communities.
 */

use \Drupal\taxonomy\TermInterface;
use \Drupal\Core\Language\LanguageInterface;

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
}

/**
 * Generate alias variant for communities based on taxonomy term alias.
 */
function qs_activity_alias(TermInterface $entity, $alias, $operation = 'insert') {
  // Load services.
  $alias_storage = \Drupal::service('path.alias_storage');
  $langcode = LanguageInterface::LANGCODE_NOT_SPECIFIED;

  if ($entity->bundle() == 'communities') {
    $aliases = [
      'activities_by_theme' => [
        'route' => '/activities/' . $entity->id() . '/theme',
        'alias' => '/activities/' . trim($alias, '/') . '/theme',
      ],
    ];

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
}
