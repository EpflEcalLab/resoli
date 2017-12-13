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
 *
 * @SuppressWarnings(PHPMD)
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
    'reviewed'  => time(),
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
    'reviewed'  => time(),
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
    'reviewed'  => time(),
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
    'reviewed'  => time(),
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
    'reviewed'  => time(),
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
    'reviewed'  => time(),
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
    'reviewed'  => time(),
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
    'reviewed'  => time(),
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
    'reviewed'  => time(),
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
    'reviewed'  => time(),
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
    'reviewed'  => time(),
  ]);
  $privilege->save();

  // User 13: member+lausanne+manager+fribourg.
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 1: lausanne.
    'entity'    => 1,
  // User 13: member+lausanne+manager+fribourg.
    'user'      => 13,
  // approved.
    'status'    => 1,
    'privilege' => 'community_members',
    'reviewer'  => 1,
    'reviewed'  => time(),
  ]);
  $privilege->save();
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 2: fribourg.
    'entity'    => 2,
  // User 13: member+lausanne+manager+fribourg.
    'user'      => 13,
  // approved.
    'status'    => 1,
    'privilege' => 'community_managers',
    'reviewer'  => 1,
    'reviewed'  => time(),
  ]);
  $privilege->save();

  // User 14: member+fribourg+approval+organizer+fribourg.
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 2: fribourg.
    'entity'    => 2,
  // User 14: member+fribourg+approval+organizer+fribourg.
    'user'      => 14,
  // approved.
    'status'    => 1,
    'privilege' => 'community_members',
    'reviewer'  => 1,
    'reviewed'  => time(),
  ]);
  $privilege->save();
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 2: fribourg.
    'entity'    => 2,
  // User 14: member+fribourg+approval+organizer+fribourg.
    'user'      => 14,
  // pending.
    'status'    => NULL,
    'privilege' => 'community_organizers',
  ]);
  $privilege->save();

  // User 15: approval+member+fribourg+approval+organizer+fribourg.
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 2: fribourg.
    'entity'    => 2,
  // User 15: approval+member+fribourg+approval+organizer+fribourg.
    'user'      => 15,
  // pending.
    'status'    => NULL,
    'privilege' => 'community_members',
  ]);
  $privilege->save();
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 2: fribourg.
    'entity'    => 2,
  // User 15: approval+member+fribourg+approval+organizer+fribourg.
    'user'      => 15,
  // pending.
    'status'    => NULL,
    'privilege' => 'community_organizers',
  ]);
  $privilege->save();

  // User 16: member+fribourg+organizer+fribourg.
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 2: fribourg.
    'entity'    => 2,
  // User 16: member+fribourg+organizer+fribourg.
    'user'      => 16,
  // approved.
    'status'    => 1,
    'privilege' => 'community_members',
    'reviewer'  => 1,
    'reviewed'  => time(),
  ]);
  $privilege->save();
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 2: fribourg.
    'entity'    => 2,
  // User 16: member+fribourg+organizer+fribourg.
    'user'      => 16,
  // approved.
    'status'    => 1,
    'privilege' => 'community_organizers',
    'reviewer'  => 1,
    'reviewed'  => time(),
  ]);
  $privilege->save();

  // User 17: declined+organizer+lausanne.
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 1: lausanne.
    'entity'    => 1,
  // User 17: declined+organizer+lausanne.
    'user'      => 17,
  // declined.
    'status'    => 0,
    'privilege' => 'community_organizers',
    'reviewer'  => 1,
    'reviewed'  => time(),
  ]);
  $privilege->save();

  // User 18: member+lausanne+declined+organizer+lausanne.
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 1: lausanne.
    'entity'    => 1,
  // User 18: member+lausanne+declined+organizer+lausanne.
    'user'      => 18,
  // approved.
    'status'    => 1,
    'privilege' => 'community_members',
    'reviewer'  => 1,
    'reviewed'  => time(),
  ]);
  $privilege->save();
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 1: lausanne.
    'entity'    => 1,
  // User 18: member+lausanne+declined+organizer+lausanne.
    'user'      => 18,
  // declined.
    'status'    => 0,
    'privilege' => 'community_organizers',
    'reviewer'  => 1,
    'reviewed'  => time(),
  ]);
  $privilege->save();

  // User 19: member+fribourg+declined+member+lausanne.
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 2: fribourg.
    'entity'    => 2,
  // User 19: member+fribourg+declined+member+lausanne.
    'user'      => 19,
  // approved.
    'status'    => 1,
    'privilege' => 'community_members',
    'reviewer'  => 1,
    'reviewed'  => time(),
  ]);
  $privilege->save();
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 1: lausanne.
    'entity'    => 1,
  // User 19: member+fribourg+declined+member+lausanne.
    'user'      => 19,
  // declined.
    'status'    => 0,
    'privilege' => 'community_members',
  ]);
  $privilege->save();

  // User 20: declined+all.
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 1: lausanne.
    'entity'    => 1,
  // User 20: declined+all.
    'user'      => 20,
  // declined.
    'status'    => 0,
    'privilege' => 'community_members',
    'reviewer'  => 1,
    'reviewed'  => time(),
  ]);
  $privilege->save();
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 2: fribourg.
    'entity'    => 2,
  // User 20: declined+all.
    'user'      => 20,
  // declined.
    'status'    => 0,
    'privilege' => 'community_members',
    'reviewer'  => 1,
    'reviewed'  => time(),
  ]);
  $privilege->save();
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 3: geneve.
    'entity'    => 3,
  // User 20: declined+all.
    'user'      => 20,
  // declined.
    'status'    => 0,
    'privilege' => 'community_members',
    'reviewer'  => 1,
    'reviewed'  => time(),
  ]);
  $privilege->save();

  // User 21: approval+all.
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 1: lausanne.
    'entity'    => 1,
  // User 21: approval+all.
    'user'      => 21,
  // pending.
    'status'    => NULL,
    'privilege' => 'community_members',
  ]);
  $privilege->save();
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 2: fribourg.
    'entity'    => 2,
  // User 21: approval+all.
    'user'      => 21,
  // pending.
    'status'    => NULL,
    'privilege' => 'community_members',
  ]);
  $privilege->save();
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 3: geneve.
    'entity'    => 3,
  // User 21: approval+all.
    'user'      => 21,
  // pending.
    'status'    => NULL,
    'privilege' => 'community_members',
  ]);
  $privilege->save();

  // User 22: approved+all.
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 1: lausanne.
    'entity'    => 1,
  // User 22: approved+all.
    'user'      => 22,
  // approved.
    'status'    => 1,
    'privilege' => 'community_members',
    'reviewer'  => 1,
    'reviewed'  => time(),
  ]);
  $privilege->save();
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 2: fribourg.
    'entity'    => 2,
  // User 22: approved+all.
    'user'      => 22,
  // approved.
    'status'    => 1,
    'privilege' => 'community_members',
    'reviewer'  => 1,
    'reviewed'  => time(),
  ]);
  $privilege->save();
  $privilege = $privilegeStorage->create([
    'bundle'    => 'taxonomy_term',
  // Community 3: geneve.
    'entity'    => 3,
  // User 22: approved+all.
    'user'      => 22,
  // approved.
    'status'    => 1,
    'privilege' => 'community_members',
    'reviewer'  => 1,
    'reviewed'  => time(),
  ]);
  $privilege->save();
}

/**
 * Global function to create default privileges for activities.
 *
 * @SuppressWarnings(PHPMD)
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
    'reviewed'  => time(),
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
    'reviewed'  => time(),
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
    'reviewed'  => time(),
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
    'reviewed'  => time(),
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
    'reviewed'  => time(),
  ]);
  $privilege->save();

  // User 18: member+lausanne+declined+organizer+lausanne.
  $privilege = $privilegeStorage->create([
    'bundle'    => 'node',
  // Activity 8: Activity 8 - Lausanne - Theme N°3.
    'entity'    => 8,
  // User 18.
    'user'      => 18,
  // approved.
    'status'    => 1,
    'privilege' => 'activity_organizers',
    'reviewer'  => 1,
    'reviewed'  => time(),
  ]);
  $privilege->save();

  // User 8: member+lausanne+organizer+fribourg.
  $privilege = $privilegeStorage->create([
    'bundle'    => 'node',
    // Activity 2: Activity 2 - Lausanne - Theme N°1.
    'entity'    => 2,
    // User 8.
    'user'      => 8,
    // approved.
    'status'    => 1,
    'privilege' => 'activity_members',
    'reviewer'  => 1,
    'reviewed'  => time(),
  ]);
  $privilege->save();

  // User 9: member+fribourg+member+lausanne.
  $privilege = $privilegeStorage->create([
    'bundle'    => 'node',
    // Activity 3: Activity 3 - Lausanne - Theme N°1 - Sortie Théatre.
    'entity'    => 3,
    // User 9.
    'user'      => 9,
    // approved.
    'status'    => 1,
    'privilege' => 'activity_members',
    'reviewer'  => 1,
    'reviewed'  => time(),
  ]);
  $privilege->save();
}
