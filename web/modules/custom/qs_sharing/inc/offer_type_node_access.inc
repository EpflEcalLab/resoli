<?php

/**
 * @file
 * Node Access Hooks.
 */

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;

/**
 * Allow access to people of the offer' types's community only.
 */
function qs_sharing_offer_type_node_access(NodeInterface $node, $operation, AccountInterface $account) {
  $acl = \Drupal::service('qs_acl.access_control');

  if ($operation === 'view' && $node->bundle() === 'offer_type') {
    $community = $node->field_community->entity;

    if ($acl->hasAccessCommunity($community)) {
      return AccessResult::allowed();
    }

    return AccessResult::forbidden();
  }
}
