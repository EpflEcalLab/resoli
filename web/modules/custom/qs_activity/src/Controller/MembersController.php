<?php

namespace Drupal\qs_activity\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\NodeInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_acl\Service\PrivilegeManager;
use Drupal\qs_export\Excel;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
  public function __construct(PrivilegeManager $privilege_manager, AccessControl $acl, Excel $excel_exporter) {
    $this->privilegeManager = $privilege_manager;
    $this->acl = $acl;
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
    $container->get('qs_acl.privilege_manager'),
    $container->get('qs_acl.access_control'),
    $container->get('qs_export.excel')
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
    $render = [
      '#theme'     => 'qs_activity_members_page',
      '#variables' => ['activity' => $activity],
      '#cache' => [
        'tags' => [
          // Invalidated whenever any community is updated, deleted or created.
          'user_list:user',
          // Invalidated whenever any Privilege is updated, deleted or created.
          'privilege_list:privilege',
        ],
      ],
    ];

    $query = $this->privilegeManager->queryMembersWithPrivileges($activity, $this->configuration['limit']);
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
    $activity_members = $this->userStorage->loadMultiple($uids);

    // Add privileges to users.
    foreach ($activity_members as $activity_member) {
      $activity_member->privileges = $privileges[$activity_member->id()];
    }

    $render['#variables']['members'] = $activity_members;

    // Get all members to mailto.
    $mailto = [];
    $query_members = $this->privilegeManager->queryMembersWithPrivileges($activity, NULL);
    $rows = $query_members->execute()->fetchAll();
    foreach ($rows as $row) {
      $mailto[$row->user] = $row->mail;
    }
    $render['#variables']['mailto'] = $mailto;

    return $render;
  }

  /**
   * Export the complete list of members by activity.
   *
   * A member appear only one time, its highest privilege is shown.
   */
  public function export(NodeInterface $activity) {
    $now = new DrupalDateTime();

    $query = $this->privilegeManager->queryMembersWithPrivileges($activity);

    // When nothing can be downloaded, return a 404.
    if (!$query) {
      throw new NotFoundHttpException();
    }

    $rows = $query->execute()->fetchAll();

    $uids = [];
    $privileges = [];
    foreach ($rows as $row) {
      $uids[] = $row->user;
      $privileges[$row->user][] = $row->privilege;
    }

    // Load user entities without privileges.
    $activity_members = $this->userStorage->loadMultiple($uids);

    // Add highest privilege by users.
    foreach ($activity_members as $activity_member) {
      $activity_member->privilege = end($privileges[$activity_member->id()]);
    }

    $this->excelExporter->init();
    $this->excelExporter->normalize();

    $title = $this->t('qs_activity.activities.members.export.title @activity @date', [
      '@activity' => $activity->getTitle(),
      '@date' => $now->format('d-m-Y'),
    ]);
    $summary = $this->t('qs_activity.activities.members.export.summary @total', [
      '@total' => count($activity_members),
    ]);
    $disclaimer = $this->t('qs_activity.activities.members.export.disclaimer');

    $this->excelExporter->setTitle($title->render());
    $this->excelExporter->setSummary($summary->render());
    $this->excelExporter->addHeader([
      $this->t('qs_activity.activities.members.export.header.privilege.label')->render(),
      $this->t('qs_activity.activities.members.export.header.firstname.label')->render(),
      $this->t('qs_activity.activities.members.export.header.lastname.label')->render(),
      $this->t('qs_activity.activities.members.export.header.mail.label')->render(),
      $this->t('qs_activity.activities.members.export.header.phone.label')->render(),
    ]);

    foreach ($activity_members as $member) {
      $privilege = '';
      switch ($member->privilege) {
        case 'activity_maintainers':
          $privilege = $this->t('qs.roles.activity_maintainer');
          break;

        case 'activity_organizers':
          $privilege = $this->t('qs.roles.activity_organizer');
          break;

        default:
        case 'activity_members':
          $privilege = $this->t('qs.roles.activity_member');
          break;
      }

      $this->excelExporter->addRow([
        ['value' => $privilege],
        ['value' => $member->field_firstname->value],
        ['value' => $member->field_lastname->value],
        ['value' => $member->getEmail()],
        ['value' => $member->field_phone->value],
      ], [
        'odd-even-background' => TRUE,
      ]);

    }
    $this->excelExporter->setFooter($disclaimer->render());
    $this->excelExporter->finalize();

    return $this->excelExporter->download();
  }

}
