<?php

/**
 * @file
 * Contains alias generation for photos list by communities.
 */

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Language\LanguageInterface;

/**
 * Implements hook_pathauto_alias_alter().
 *
 * Generate alias for lessons based on node alias.
 */
function qs_photo_pathauto_alias_alter(&$alias, array &$context) {

  if (isset($context['module']) &&
    $context['module'] == 'taxonomy' && isset($context['data']['term'])) {
    qs_photo_alias($context['data']['term'], $alias, $context['op']);
  }

  if (isset($context['module']) &&
    $context['module'] == 'node' && isset($context['data']['node'])) {
    qs_photo_alias($context['data']['node'], $alias, $context['op']);
  }
}

/**
 * Generate alias variant for communities based on taxonomy term alias.
 */
function qs_photo_alias(EntityInterface $entity, $alias, $operation = 'insert') {
  // Load services.
  $alias_storage = \Drupal::service('path.alias_storage');
  $langcode = LanguageInterface::LANGCODE_NOT_SPECIFIED;

  $aliases = [];

  switch ($entity->bundle()) {
    case 'communities':
      $aliases = [
        'photo' => [
          'route' => '/photos/' . $entity->id() . '/theme',
          'alias' => '/' . trim($alias, '/') . '/photos',
        ],
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
