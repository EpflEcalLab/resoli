<?php

namespace Drupal\qs_acl\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * PrivilegeGodController.
 *
 * This following AJAX call are made from the members dashboard only.
 */
class PrivilegeGodController extends AjaxControllerBase {

  /**
   * Checks access.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account) {
    $access = AccessResult::forbidden();
    $request = $this->requestStack->getCurrentRequest();

    $user_id = $request->request->get('user');
    $user = $this->userStorage->load($user_id);

    if (!$user) {
      return AccessResult::forbidden();
    }

    $community_id = $request->request->get('community');
    $community = $this->termStorage->load($community_id);

    if ($community && $community->bundle() === 'communities' && $this->acl->hasAdminAccessCommunity($community)) {
      $access = AccessResult::allowed();
    }

    // Check access for activity.
    $activity_id = $request->request->get('activity');
    $activity = $this->nodeStorage->load($activity_id);

    if ($activity && $activity->bundle() === 'activity' && $this->acl->hasAdminAccessActivity($activity)) {
      $access = AccessResult::allowed();
    }

    return $access;
  }

  /**
   * Decline all the privilege for the entity & the account.
   *
   * This AJAX call is called from the members dashboard only.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JSON formatted response. Contains status & declined privileges.
   */
  public function ban(Request $request) {
    $user_id = $request->request->get('user');
    $user = $this->userStorage->load($user_id);
    $entity = NULL;

    if ($community_id = $request->request->get('community')) {
      $entity = $this->termStorage->load($community_id);
    }

    if ($activity_id = $request->request->get('activity')) {
      $entity = $this->nodeStorage->load($activity_id);
    }

    if (!$entity) {
      return new JsonResponse(['status' => FALSE]);
    }

    // Check if a privilege already exists.
    $privileges = $this->privilegeStorage->loadByProperties([
      'bundle' => $entity->getEntityTypeId(),
      'entity' => $entity->id(),
      'user' => $user->id(),
    ]);

    if (!$privileges) {
      return new JsonResponse([
        'status' => TRUE,
        'privilege' => [],
      ]);
    }

    $declined = [];

    foreach ($privileges as $privilege) {
      $declined[] = $this->privilegeManager->decline($privilege)->toArray();
    }

    return new JsonResponse([
      'status' => TRUE,
      'privileges' => $declined,
    ]);
  }

  /**
   * Toggle the given privilege for the entity & the account.
   *
   * If the toggle is requested for a non existing privilege, it create it.
   * This AJAX call is called from the members dashboard only.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JSON formatted response. Contains status & toggled/created privilege.
   */
  public function toggle(Request $request) {
    $privileges = $request->request->get('privileges');
    $privileges = explode('|', $privileges);

    if (!$privileges) {
      return new JsonResponse(['status' => FALSE]);
    }

    $user_id = $request->request->get('user');
    $user = $this->userStorage->load($user_id);

    $community_id = $request->request->get('community');
    $community = $this->termStorage->load($community_id);

    $activity_id = $request->request->get('activity');
    $activity = $this->nodeStorage->load($activity_id);

    if (!$community && !$activity) {
      return new JsonResponse(['status' => FALSE]);
    }

    if ($community) {
      $entity = $community;
      $roles = [
        'community_members' => 0,
        'community_organizers' => 0,
        'community_managers' => 0,
      ];
    }

    if ($activity) {
      $entity = $activity;
      $roles = [
        'activity_members' => 0,
        'activity_maintainers' => 0,
        'activity_organizers' => 0,
      ];
    }

    foreach ($privileges as $privilege) {
      if (isset($roles[$privilege])) {
        $roles[$privilege] = 1;
      }
    }

    $updated = [];

    foreach ($roles as $role => $status) {
      $privileges = $this->privilegeStorage->loadByProperties([
        'privilege' => $role,
        'bundle' => $entity->getEntityTypeId(),
        'entity' => $entity->id(),
        'user' => $user->id(),
      ]);
      $privilege = reset($privileges);

      if ($privilege) {
        $privilege->setStatus($status);
        $privilege->save();
        $updated[] = $privilege->toArray();
      }
      elseif ($status === 1) {
        $updated[] = $this->privilegeManager->create($role, $entity, $user)->toArray();
      }
    }

    return new JsonResponse([
      'status' => TRUE,
      'privilege' => $updated,
    ]);
  }

}
