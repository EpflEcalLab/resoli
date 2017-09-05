<?php

namespace Drupal\qs_acl\Controller;

use Drupal\Core\Entity\EntityInterface;
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
   * @return bool
   *   Access allowed or rejected.
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
    if ($community && $community->bundle() == 'communities' && $this->acl->hasAdminAccessCommunity($community)) {
      $access = AccessResult::allowed();
    }

    // TOOD: Check access for activity.
    // $activity_id = $request->request->get('activity');.

    return $access;
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
   * @return Symfony\Component\HttpFoundation\JsonResponse
   *   JSON formated response. Contains status & toggled/created privilege.
   */
  public function toggle(Request $request) {
    $privileges = $request->request->get('privileges');

    if (!$privileges) {
      return new JsonResponse(['status'=> FALSE]);
    }

    $user_id = $request->request->get('user');
    $user    = $this->userStorage->load($user_id);

    $community_id = $request->request->get('community');
    $community = $this->termStorage->load($community_id);

    if (!$community) {
      return new JsonResponse(['status'=> FALSE]);
    }

    $roles = [
      'community_members'    => 0,
      'community_organizers' => 0,
      'community_managers'   => 0,
    ];

    foreach ($privileges as $privilege) {
      if (isset($roles[$privilege])) {
        $roles[$privilege] = 1;
      }
    }

    $updated = [];
    foreach ($roles as $role => $status) {
      $privileges = $this->privilegeStorage->loadByProperties([
        'privilege' => $role,
        'bundle'    => $community->getEntityTypeId(),
        'entity'    => $community->id(),
        'user'      => $user->id(),
      ]);
      $privilege = reset($privileges);

      if ($privilege) {
        $privilege->setStatus($status);
        $privilege->save();
        $updated[] = $privilege->toArray();
      } elseif ($status === 1) {
        $updated[] = $this->privilegeManger->create($role, $community, $user)->toArray();
      }
    }

    return new JsonResponse([
      'status'    => TRUE,
      'privilege' => $updated,
    ]);
  }

    /**
    * Decline all the privilege for the entity & the account.
    *
    * This AJAX call is called from the members dashboard only.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return Symfony\Component\HttpFoundation\JsonResponse
   *   JSON formated response. Contains status & declined privileges.
   */
  public function ban(Request $request) {
    $user_id = $request->request->get('user');
    $user    = $this->userStorage->load($user_id);

    $community_id = $request->request->get('community');
    $community = $this->termStorage->load($community_id);

    if (!$community) {
      return new JsonResponse(['status'=> FALSE]);
    }

    // Check if a privilege already exists
    $privileges = $this->privilegeStorage->loadByProperties([
      'bundle' => $community->getEntityTypeId(),
      'entity' => $community->id(),
      'user'   => $user->id(),
    ]);

    if (!$privileges) {
      return new JsonResponse([
        'status'    => TRUE,
        'privilege' => [],
      ]);
    }

    $declined = [];
    foreach ($privileges as $privilege) {
      $declined[] = $this->privilegeManger->decline($privilege)->toArray();
    }

    return new JsonResponse([
      'status'     => TRUE,
      'privileges' => $declined,
    ]);
  }

}
