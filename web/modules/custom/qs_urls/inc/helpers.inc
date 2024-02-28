<?php

/**
 * @file
 * Contains helper functions to generate aliases & redirects.
 */

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Language\LanguageInterface;

/**
 * Check to ensure a path alias is unique and add suffix variants if necessary.
 *
 * Given an alias 'content/test' if a path alias with the exact alias already
 * exists, the function will change the alias to 'content/test-0' and will
 * increase the number suffix until it finds a unique alias.
 *
 * @param \Drupal\Core\Entity\EntityInterface $entity
 *   The source entity.
 * @param string $alias
 *   A string with the alias.
 * @param string $langcode
 *   A string with a language code.
 *
 * @return string
 *   A unique alias.
 */
function qs_alias_uniquifier(EntityInterface $entity, $alias, $langcode = LanguageInterface::LANGCODE_NOT_SPECIFIED) {
  // Get the entity URL, such as node/x or taxonomy/term/y.
  $source = $entity->toUrl()->toString();

  /** @var \Drupal\pathauto\AliasUniquifierInterface */
  $alias_uniquifier = Drupal::service('pathauto.alias_uniquifier');

  // If the alias already exists, generate a new, hopefully unique, variant.
  $alias_uniquifier->uniquify($alias, $source, $langcode);

  return $alias;
}

/**
 * Generate redirects variant for given collection.
 *
 * @param array $redirects
 *   A collection of redirect to generate.
 *   $redirects = [
 *     'redirect_key'    => [
 *       'source' => (string) the source url, should not already exists.
 *       'redirect' => (string) the redirection destination.
 *     ]
 *   ].
 * @param string $operation
 *   The operation type such as insert, update, bulkupdate or delete.
 */
function qs_urls_redirects_manager(array $redirects, $operation = 'insert') {
  if (!$redirects) {
    return;
  }

  $langcode = LanguageInterface::LANGCODE_NOT_SPECIFIED;

  /** @var \Drupal\Core\Entity\EntityTypeManagerInterface */
  $entityManager = Drupal::service('entity_type.manager');
  $redirectStorage = $entityManager->getStorage('redirect');

  // Load services.
  $redirect_repository = Drupal::service('redirect.repository');

  // @todo translation the redirect_suffix before creation/deletion,
  // on every language.
  // Generate every redirect for the given aliases.
  foreach ($redirects as $item) {
    // Generate redirect.
    if ($operation === 'insert' || $operation === 'update' || $operation === 'bulkupdate') {
      // Only generate redirect for existing alias &
      // non already redirected source.
      if (qs_urls_assert_path_alias_exists($item['source'], $langcode) === FALSE && empty($redirect_repository->findMatchingRedirect(trim($item['source'], '/'), [], $langcode))) {
        $redirect = $redirectStorage->create();
        $redirect->setSource($item['source']);
        $redirect->setRedirect($item['redirect']);
        $redirect->setLanguage($langcode);
        $redirect->setStatusCode(301);
        $redirect->save();
      }
    }

    // Delete redirect on operation delete.
    if ($operation === 'delete') {
      $redirect = $redirect_repository->findMatchingRedirect(trim($item['source'], '/'), [], $langcode);

      if (!empty($redirect)) {
        $redirect->delete();
      }
    }
  }
}

/**
 * Generate aliases variant for given collection.
 *
 * @param array $aliases
 *   A collection of aliases to generate.
 *   $aliases = [
 *     'alias_key'    => [
 *       'route' => (string) the source route.
 *       'alias' => (string) the alias.
 *     ]
 *   ].
 * @param string $operation
 *   The operation type such as insert, update, bulkupdate or delete.
 */
function qs_urls_aliases_manager(array $aliases, $operation = 'insert') {
  if (!$aliases) {
    return;
  }

  $langcode = LanguageInterface::LANGCODE_NOT_SPECIFIED;

  // Load services.
  $alias_storage = Drupal::entityTypeManager()->getStorage('path_alias');

  // @todo translation the alias_suffix before creation/deletion,
  // on every language.
  // Generate every alias for the given aliases.
  foreach ($aliases as $item) {
    // Generate alias.
    if ($operation === 'insert' || $operation === 'update' || $operation === 'bulkupdate') {
      // Only generate alias for non-existing alias &
      // route which has not already an alias.
      if (qs_urls_assert_path_alias_exists($item['alias'], $langcode) === FALSE && qs_urls_assert_path_alias_exists(NULL, $langcode, $item['route']) === FALSE) {
        $path_alias = $alias_storage->create([
          'path' => $item['route'],
          'alias' => $item['alias'],
          'langcode' => $langcode,
        ]);
        $path_alias->save();
      }
    }

    // Delete alias on operation delete.
    if ($operation === 'delete') {
      if (qs_urls_assert_path_alias_exists($item['alias'], $langcode, $item['route']) === TRUE) {
        $path_aliases = $alias_storage->loadByProperties([
          'path' => $item['route'],
          'alias' => $item['alias'],
          'langcode' => $langcode,
        ]);

        foreach ($path_aliases as $path_alias) {
          $path_alias->delete();
        }
      }
    }
  }
}

/**
 * Asserts that a path alias exists in the storage.
 *
 * @param string $alias
 *   The path alias.
 * @param string|null $langcode
 *   (optional) The language code of the path alias.
 * @param string|null $path
 *   (optional) The system path of the path alias.
 *
 * @return bool
 *   Does the path alias exists in the storage.
 */
function qs_urls_assert_path_alias_exists($alias, $langcode = NULL, $path = NULL) {
  $query = Drupal::entityTypeManager()
    ->getStorage('path_alias')
    ->getQuery()
    ->accessCheck(FALSE);

  if ($alias) {
    $query
      ->condition('alias', $alias, '=');
  }

  if ($langcode) {
    $query
      ->condition('langcode', $langcode, '=');
  }

  if ($path) {
    $query
      ->condition('path', $path, '=');
  }

  $query->count();

  return (bool) $query->execute() > 0;
}
