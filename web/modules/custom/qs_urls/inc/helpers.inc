<?php

/**
 * @file
 * Contains helper functions to generate aliases & redirects.
 */

use Drupal\Core\Language\LanguageInterface;

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

  // @var \Drupal\Core\Entity\EntityTypeManagerInterface
  $entityManager = \Drupal::service('entity_type.manager');
  $redirectStorage = $entityManager->getStorage('redirect');

  // Load services.
  $redirect_repository = \Drupal::service('redirect.repository');
  $alias_storage = \Drupal::service('path.alias_storage');

  // TODO: translation the redirect_suffix before creation/deletion,
  // on every language.
  // Generate every redirect for the given aliases.
  foreach ($redirects as $item) {
    // Generate redirect.
    if ($operation == 'insert' || $operation == 'update' || $operation == 'bulkupdate') {

      // Only generate redirect for existing alias &
      // non already redirected source.
      if ($alias_storage->aliasExists($item['source'], $langcode) === FALSE && empty($redirect_repository->findBySourcePath($item['source'], $langcode))) {
        $redirect = $redirectStorage->create();
        $redirect->setSource($item['source']);
        $redirect->setRedirect($item['redirect']);
        $redirect->setLanguage($langcode);
        $redirect->setStatusCode(301);
        $redirect->save();
      }
    }

    // Delete redirect on operation delete.
    if ($operation == 'delete') {
      $redirect = $redirect_repository->findBySourcePath($item['source'], $langcode);
      if ($redirect !== FALSE) {
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
  $alias_storage = \Drupal::service('path.alias_storage');

  // TODO: translation the alias_suffix before creation/deletion,
  // on every language.
  // Generate every alias for the given aliases.
  foreach ($aliases as $item) {
    // Generate alias.
    if ($operation == 'insert' || $operation == 'update' || $operation == 'bulkupdate') {
      // Only generate alias for non-existing alias &
      // route which has not already an alias.
      if ($alias_storage->aliasExists($item['alias'], $langcode) === FALSE && $alias_storage->lookupPathAlias($item['route'], $langcode) === FALSE) {
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
