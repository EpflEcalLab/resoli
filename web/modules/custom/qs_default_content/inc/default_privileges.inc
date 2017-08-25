<?php

/**
 * @file
 * Contains creation of default privileges.
 */

/**
 * Global function to create default privileges.
 */
function qs_default_content_privileges() {
  qs_default_content_privileges_community();
  qs_default_content_privileges_activities();
}

/**
 * Global function to create default privileges for communities.
 */
function qs_default_content_privileges_community() {

  // @var \Drupal\Core\Entity\EntityTypeManagerInterface
  $entityManager = \Drupal::service('entity_type.manager');
  $privilegeStorage = $entityManager->getStorage('privilege');

  // User 2: member+lausanne.
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 1: lausanne.
    'entity'    => 1,
  // User 2: member+lausanne.
    'user'      => 2,
  // approved.
    'status'    => 1,
    'privilege' => 'community_members',
    'reviewer'  => 1,
  ]);
  $privilege->save();

  // User 3: approval+lausanne.
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 1: lausanne.
    'entity'    => 1,
  // User 3: approval+lausanne.
    'user'      => 3,
  // pending.
    'status'    => NULL,
    'privilege' => 'community_members',
  ]);
  $privilege->save();

  // User 4: member+fribourg.
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 2: fribourg.
    'entity'    => 2,
  // User 4: member+fribourg.
    'user'      => 4,
  // approved.
    'status'    => 1,
    'privilege' => 'community_members',
    'reviewer'  => 1,
  ]);
  $privilege->save();

  // User 5: manager+lausanne.
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 1: lausanne.
    'entity'    => 1,
  // User 5: manager+lausanne.
    'user'      => 5,
  // approved.
    'status'    => 1,
    'privilege' => 'community_managers',
    'reviewer'  => 1,
  ]);
  $privilege->save();

  // User 6: organizer+lausanne.
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 1: lausanne.
    'entity'    => 1,
  // User 6: organizer+lausanne.
    'user'      => 6,
  // approved.
    'status'    => 1,
    'privilege' => 'community_organizers',
    'reviewer'  => 1,
  ]);
  $privilege->save();

  // User 8: member+lausanne+organizer+fribourg.
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 1: lausanne.
    'entity'    => 1,
  // User 8: member+lausanne+organizer+fribourg.
    'user'      => 8,
  // approved.
    'status'    => 1,
    'privilege' => 'community_members',
    'reviewer'  => 1,
  ]);
  $privilege->save();
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 2: fribourg.
    'entity'    => 2,
  // User 8: member+lausanne+organizer+fribourg.
    'user'      => 8,
  // approved.
    'status'    => 1,
    'privilege' => 'community_organizers',
    'reviewer'  => 1,
  ]);
  $privilege->save();

  // User 9: member+fribourg+member+lausanne.
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 1: lausanne.
    'entity'    => 1,
  // User 9: member+fribourg+member+lausanne.
    'user'      => 9,
  // approved.
    'status'    => 1,
    'privilege' => 'community_members',
    'reviewer'  => 1,
  ]);
  $privilege->save();
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 2: fribourg.
    'entity'    => 2,
  // User 9: member+fribourg+member+lausanne.
    'user'      => 9,
  // approved.
    'status'    => 1,
    'privilege' => 'community_members',
    'reviewer'  => 1,
  ]);
  $privilege->save();

  // User 10: approval+fribourg+approval+lausanne.
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 1: lausanne.
    'entity'    => 1,
  // User 10: approval+fribourg+approval+lausanne.
    'user'      => 10,
  // pending.
    'status'    => NULL,
    'privilege' => 'community_members',
  ]);
  $privilege->save();
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 2: fribourg.
    'entity'    => 2,
  // User 10: approval+fribourg+approval+lausanne.
    'user'      => 10,
  // pending.
    'status'    => NULL,
    'privilege' => 'community_organizers',
  ]);
  $privilege->save();

  // User 11: member+fribourg+approval+lausanne.
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 1: lausanne.
    'entity'    => 1,
  // User 11: member+fribourg+approval+lausanne.
    'user'      => 11,
  // pending.
    'status'    => NULL,
    'privilege' => 'community_members',
  ]);
  $privilege->save();
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 2: fribourg.
    'entity'    => 2,
  // User 11: member+fribourg+approval+lausanne.
    'user'      => 11,
  // approved.
    'status'    => 1,
    'privilege' => 'community_members',
    'reviewer'  => 1,
  ]);
  $privilege->save();

  // User 12: member+fribourg+approval+lausanne+member+geneve.
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 2: fribourg.
    'entity'    => 2,
  // User 12: member+fribourg+approval+lausanne+member+geneve.
    'user'      => 12,
  // approved.
    'status'    => 1,
    'privilege' => 'community_members',
    'reviewer'  => 1,
  ]);
  $privilege->save();
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 1: lausanne.
    'entity'    => 1,
  // User 12: member+fribourg+approval+lausanne+member+geneve.
    'user'      => 12,
  // pending.
    'status'    => NULL,
    'privilege' => 'community_members',
  ]);
  $privilege->save();
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 3: geneve.
    'entity'    => 3,
  // User 12: member+fribourg+approval+lausanne+member+geneve.
    'user'      => 12,
  // approved.
    'status'    => 1,
    'privilege' => 'community_members',
    'reviewer'  => 1,
  ]);
  $privilege->save();
}

/**
 * Global function to create default privileges for activities.
 */
function qs_default_content_privileges_activities() {
  // @var \Drupal\Core\Entity\EntityTypeManagerInterface
  $entityManager = \Drupal::service('entity_type.manager');
  $privilegeStorage = $entityManager->getStorage('privilege');

  // User 2: member+lausanne.
  $privilege = $privilegeStorage->create([
    'bundle'    => 'node',
  // Activity 4: Activity 4 - Lausanne - Theme N°1.
    'entity'    => 4,
  // User 2: member+lausanne.
    'user'      => 2,
  // approved.
    'status'    => 1,
    'privilege' => 'activity_members',
    'reviewer'  => 1,
  ]);
  $privilege->save();
  $privilege = $privilegeStorage->create([
    'bundle'    => 'node',
  // Activity 5: Activity 5 - Lausanne - Theme N°1.
    'entity'    => 5,
  // User 2: member+lausanne.
    'user'      => 2,
  // pending.
    'status'    => NULL,
    'privilege' => 'activity_members',
  ]);
  $privilege->save();

  // User 5: manager+lausanne.
  $privilege = $privilegeStorage->create([
    'bundle'    => 'node',
  // Activity 2: Activity 2 - Lausanne - Theme N°1.
    'entity'    => 2,
  // User 5.
    'user'      => 5,
  // approved.
    'status'    => 1,
    'privilege' => 'activity_organizers',
    'reviewer'  => 1,
  ]);
  $privilege->save();
  $privilege = $privilegeStorage->create([
    'bundle'    => 'node',
  // Activity 3: Activity 3 - Lausanne - Theme N°1.
    'entity'    => 3,
  // User 5.
    'user'      => 5,
  // approved.
    'status'    => 1,
    'privilege' => 'activity_maintainers',
    'reviewer'  => 1,
  ]);
  $privilege->save();

  // User 6: organizer+lausanne.
  $privilege = $privilegeStorage->create([
    'bundle'    => 'node',
  // Activity 3: Activity 3 - Lausanne - Theme N°1.
    'entity'    => 3,
  // User 6.
    'user'      => 6,
  // approved.
    'status'    => 1,
    'privilege' => 'activity_organizers',
    'reviewer'  => 1,
  ]);
  $privilege->save();
  $privilege = $privilegeStorage->create([
    'bundle'    => 'node',
  // Activity 5: Activity 5 - Lausanne - Theme N°1.
    'entity'    => 5,
  // User 6.
    'user'      => 6,
  // approved.
    'status'    => 1,
    'privilege' => 'activity_organizers',
    'reviewer'  => 1,
  ]);
  $privilege->save();
}
