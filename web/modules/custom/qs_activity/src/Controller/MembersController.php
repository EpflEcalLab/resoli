<?php

namespace Drupal\qs_activity\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\NodeInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_acl\Service\PrivilegeManager;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;

/**
 * MembersController.
 */
class MembersController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  private $configuration = ['limit' => 50];

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * The Privilege Manager.
   *
   * @var \Drupal\qs_acl\Service\PrivilegeManager
   */
  private $privilegeManager;

  /**
   * The user Storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(PrivilegeManager $privilege_manager, AccessControl $acl) {
    $this->privilegeManager = $privilege_manager;
    $this->acl              = $acl;
    $this->userStorage      = $this->entityTypeManager()->getStorage('user');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
    // Load customs services used in this class.
    $container->get('qs_acl.privilege_manager'),
    $container->get('qs_acl.access_control')
    );
  }

  /**
   * Checks access.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\node\NodeInterface $activity
   *   Run access checks for this node.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account, NodeInterface $activity) {
    $access = AccessResult::forbidden();

    if ($activity && $this->acl->hasAdminAccessActivity($activity)) {
      $access = AccessResult::allowed();
    }
    return $access;
  }

  /**
   * Members page.
   */
  public function members(NodeInterface $activity) {
    $variables['activity'] = $activity;

    $query = $this->privilegeManager->queryMembersWithPrivileges($activity);
    $ids = $query->execute()->fetchAll();
    pager_default_initialize(count($ids), $this->configuration['limit']);
    $variables['pager'] = [
      '#type'     => 'pager',
      '#quantity' => '3',
    ];
    $page = pager_find_page();
    $query->range($page * $this->configuration['limit'], $this->configuration['limit']);
    $rows = $query->execute()->fetchAll();

    $uids = [];
    $privileges = [];
    foreach ($rows as $row) {
      $uids[] = $row->user;
      $privileges[$row->user][] = $row->privilege;
    }

    // Load user entities without privileges.
    $activity_members = $this->userStorage->loadMultiple($uids);

    // Add privileges to users.
    foreach ($activity_members as $activity_member) {
      $activity_member->privileges = $privileges[$activity_member->id()];
    }

    $variables['members'] = $activity_members;

    return [
      '#theme'     => 'qs_activity_members_page',
      '#variables' => $variables,
      '#cache' => [
        'tags' => [
          // Invalidated whenever any community is updated, deleted or created.
          'user_list:user',
          // Invalidated whenever any Privilege is updated, deleted or created.
          'privilege_list:privilege',
        ],
      ],
    ];
  }

}
