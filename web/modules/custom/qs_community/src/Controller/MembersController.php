<?php

namespace Drupal\qs_community\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\taxonomy\TermInterface;
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
  public function __construct(AccessControl $acl, PrivilegeManager $privilege_manager) {
    $this->acl              = $acl;
    $this->privilegeManager = $privilege_manager;
    $this->userStorage      = $this->entityTypeManager()->getStorage('user');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
    // Load customs services used in this class.
    $container->get('qs_acl.access_control'),
    $container->get('qs_acl.privilege_manager')
    );
  }

  /**
   * Checks access.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\taxonomy\TermInterface $community
   *   Run access checks for this taxonomy.
   *
   * @return bool
   *   Access allowed or rejected.
   */
  public function access(AccountInterface $account, TermInterface $community) {
    $access = AccessResult::forbidden();
    if ($this->acl->hasAdminAccessCommunity($community)) {
      $access = AccessResult::allowed();
    }
    return $access;
  }

  /**
   * Members page.
   */
  public function members(TermInterface $community) {
    $variables['community'] = $community;

    $query = $this->privilegeManager->queryMembersWithPrivileges($community);
    $ids = $query->execute()->fetchAll();
    pager_default_initialize(count($ids), $this->configuration['limit']);
    $variables['pager'] = [
      '#type'     => 'pager',
      '#quantity' => '3',
    ];
    $page = pager_find_page();
    $query->range($page, $this->configuration['limit']);
    $rows = $query->execute()->fetchAll();

    $uids = [];
    $privileges = [];
    foreach ($rows as $row) {
      $uids[] = $row->user;
      $privileges[$row->user][] = $row->privilege;
    }

    // Load user entities whitout privileges.
    $community_members = $this->userStorage->loadMultiple($uids);

    // Add privileges to users.
    foreach ($community_members as $community_member) {
      $community_member->privileges = $privileges[$community_member->id()];
    }

    $variables['members'] = $community_members;

    return [
      '#theme'     => 'qs_community_members_page',
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
