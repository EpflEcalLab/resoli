<?php

namespace Drupal\qs_community\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_acl\Service\PrivilegeManager;
use Drupal\qs_export\Excel;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Datetime\DrupalDateTime;

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
   * The QS Excel exporter.
   *
   * @var \Drupal\qs_export\Excel
   */
  protected $excelExporter;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, PrivilegeManager $privilege_manager, Excel $excel_exporter) {
    $this->acl = $acl;
    $this->privilegeManager = $privilege_manager;
    $this->userStorage = $this->entityTypeManager()->getStorage('user');
    $this->excelExporter = $excel_exporter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
    // Load customs services used in this class.
    $container->get('qs_acl.access_control'),
    $container->get('qs_acl.privilege_manager'),
    $container->get('qs_export.excel')
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
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
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
    $render = [
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

    $query = $this->privilegeManager->queryMembersWithPrivileges($community, $this->configuration['limit']);
    if (!$query) {
      return $render;
    }
    $render['#variables']['pager'] = [
      '#type'     => 'pager',
      '#quantity' => '3',
    ];

    $rows = $query->execute()->fetchAll();
    $uids = [];
    $privileges = [];
    foreach ($rows as $row) {
      $uids[] = $row->user;
      $privileges[$row->user][] = $row->privilege;
    }

    // Load user entities without privileges.
    $community_members = $this->userStorage->loadMultiple($uids);

    // Add privileges to users.
    foreach ($community_members as $community_member) {
      $community_member->privileges = $privileges[$community_member->id()];
    }

    $render['#variables']['members'] = $community_members;

    return $render;
  }

  /**
   * Export the complete list of members by community.
   *
   * A member may appear multiple time, as they may have more than 1 privilege.
   */
  public function export(TermInterface $community) {
    $now = new DrupalDateTime();

    $query = $this->privilegeManager->queryMembersWithPrivileges($community);
    $rows = $query->execute()->fetchAll();

    $uids = [];
    $privileges = [];
    foreach ($rows as $row) {
      $uids[] = $row->user;
      $privileges[$row->user][] = $row->privilege;
    }

    // Load user entities without privileges.
    $community_members = $this->userStorage->loadMultiple($uids);

    // Add privileges to users.
    foreach ($community_members as $community_member) {
      $community_member->privileges = $privileges[$community_member->id()];
    }

    $this->excelExporter->init();
    $this->excelExporter->normalize();

    $title = $this->t('qs_community.members.export.title @community @date', [
      '@community' => $community->getName(),
      '@date' => $now->format('d-m-Y'),
    ]);

    $this->excelExporter->setTitle($title->render());
    $this->excelExporter->addHeader([
      $this->t('qs_community.members.export.header.privilege.label')->render(),
      $this->t('qs_community.members.export.header.firstname.label')->render(),
      $this->t('qs_community.members.export.header.lastname.label')->render(),
      $this->t('qs_community.members.export.header.mail.label')->render(),
      $this->t('qs_community.members.export.header.phone.label')->render(),
    ]);

    foreach ($community_members as $member) {
      foreach ($member->privileges as $privilege) {
        $acl = '';
        switch ($privilege) {
          case 'community_managers':
            $acl = $this->t('qs.roles.community_manager');
            break;

          case 'community_organizers':
            $acl = $this->t('qs.roles.community_organizer');
            break;

          default:
          case 'community_members':
            $acl = $this->t('qs.roles.community_member');
            break;
        }

        $this->excelExporter->addRow([
          $acl,
          $member->field_firstname->value,
          $member->field_lastname->value,
          $member->getEmail(),
          $member->field_phone->value,
        ]);
      }

    }
    $this->excelExporter->finalize();

    return $this->excelExporter->download();
  }

}
