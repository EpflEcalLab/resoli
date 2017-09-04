<?php

/**
 * @file
 * Contains alias generation for communities.
 */

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Language\LanguageInterface;

/**
 * Implements hook_pathauto_alias_alter().
 *
 * Generate alias for lessons based on node alias.
 */
function qs_community_pathauto_alias_alter(&$alias, array &$context) {
  if (isset($context['module']) &&
  $context['module'] == 'taxonomy' && isset($context['data']['term'])) {
    qs_community_alias($context['data']['term'], $alias, $context['op']);
  }
}

/**
 * Generate alias variant for communities based on taxonomy term alias.
 */
function qs_community_alias(EntityInterface $entity, $alias, $operation = 'insert') {
  // Load services.
  $alias_storage = \Drupal::service('path.alias_storage');
  $langcode = LanguageInterface::LANGCODE_NOT_SPECIFIED;

  $aliases = [];

  switch ($entity->bundle()) {
    case 'communities':
      $aliases = [
        'communities_dashboard' => [
          'route' => '/communities/' . $entity->id() . '/dashboard',
          'alias' => '/' . trim($alias, '/') . '/dashboard',
        ],
        'communities_approval' => [
          'route' => '/communities/' . $entity->id() . '/waiting-approval',
          'alias' => '/' . trim($alias, '/') . '/waiting-approval',
        ],
        'communities_members' => [
          'route' => '/communities/' . $entity->id() . '/members',
          'alias' => '/' . trim($alias, '/') . '/members',
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
