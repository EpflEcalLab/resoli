<?php

namespace Drupal\qs_activity\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_acl\Service\PrivilegeManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Activity Floating actions buttons Block.
 *
 * Expose the Floating actions buttons of Activity detail page.
 *
 * @codingStandardsIgnoreFile
 * @Block(
 *     id="qs_activity_floating_actions_buttons_block",
 *     admin_label=@Translation("Activity Floating actions buttons"),
 * )
 */
class ActivityFloatingBtnBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The Privilege Manager.
   *
   * @var \Drupal\qs_acl\Service\PrivilegeManager
   */
  protected $privilegeManager;

  /**
   * The current route.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $route;

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccessControl $acl, CurrentRouteMatch $route, PrivilegeManager $privilege_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->acl = $acl;
    $this->route = $route;
    $this->privilegeManager = $privilege_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function build($params = []) {
    $node = $this->route->getParameter('node');
    $activity = $this->route->getParameter('activity');
    $entity = $node ? $node : $activity;
    $variables = [];

    // "Contact Organizer(s) & Maintainer(s)" floating button.
    $action = $this->contactAction($entity);

    if ($action) {
      $variables['floating_buttons']['action'] = $action;
    }

    // "Add Event" floating button.
    // When the user has write access on the activity, replace the
    // "Contact Organizer(s) & Maintainer(s)" action by the "Add Event" action.
    if ($this->acl->hasWriteAccessEvent($entity)) {
      $variables['floating_buttons']['action'] = [
        'url' => Url::fromRoute('qs_activity.events.form.add', [
          'activity' => $entity->id(),
        ]),
        'label' => $this->t('qs_activity.floating.add.event'),
        'theme' => 'secondary',
        'icon' => 'plus',
      ];
    }

    // "Activity Dashboard" floating button.
    // When the user has admin access on the activity, replace the
    // action by the "Activity Dashboard" action.
    if ($this->acl->hasAdminAccessActivity($entity)) {
      $variables['floating_buttons']['action'] = [
        'url' => Url::fromRoute('qs_activity.activities.dashboard', [
          'activity' => $entity->id(),
        ]),
        'label' => $this->t('qs_activity.floating.dashboard.activity'),
        'theme' => 'primary',
        'icon' => 'activities',
      ];
    }

    return [
      '#theme' => 'qs_activity_floating_actions_buttons_block',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
          'url',
        ],
        'tags' => [
          // Invalidated whenever any Privilege is updated, deleted or created.
          'privilege_list:privilege',
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    // Instantiates this form class.
    return new static(
    // Load the service required to construct this class.
      $configuration,
      $plugin_id,
      $plugin_definition,
      // Load customs services used in this class.
      $container->get('qs_acl.access_control'),
      $container->get('current_route_match'),
      $container->get('qs_acl.privilege_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    $node = $this->route->getParameter('node');
    $activity = $this->route->getParameter('activity');
    $entity = $node ? $node : $activity;

    if (!$account->isAuthenticated() || !$entity) {
      return AccessResult::forbidden();
    }

    if ($entity && $entity->bundle() !== 'activity') {
      return AccessResult::forbidden();
    }

    return AccessResult::allowed();
  }

  /**
   * Build a render array to "Contact Organizer(s) & Maintainer(s)" button.
   *
   * @param \Drupal\node\NodeInterface $activity
   *    The current activity to contact "Organizer(s) & Maintainer(s)".
   *
   * @return array|null
   *    The structure of action button. Null when nobody can be contacted.
   */
  private function contactAction(NodeInterface $activity) {
    $mails = [];

    // Get all organizers's mails of this activity.
    $query_organizers = $this->privilegeManager->queryPrivilege($activity, 'activity_organizers');
    $query_organizers->leftJoin('users_field_data', 'users', 'users.uid = privileges.user');
    $query_organizers->fields('users', ['mail']);
    $rows = $query_organizers->execute()->fetchAll();

    foreach ($rows as $row) {
      $mails[$row->user] = $row->mail;
    }

    // Get all maintainers's mails of this activity.
    $query_maintainers = $this->privilegeManager->queryPrivilege($activity, 'activity_maintainers');
    $query_maintainers->leftJoin('users_field_data', 'users', 'users.uid = privileges.user');
    $query_maintainers->fields('users', ['mail']);
    $rows = $query_maintainers->execute()->fetchAll();

    foreach ($rows as $row) {
      $mails[$row->user] = $row->mail;
    }

    if (empty($mails)) {
      return NULL;
    }

    return [
      'url' => 'mailto:' . implode(',', $mails),
      'label' => $this->t('qs_activity.floating.contact.organizers_and_maintainers'),
      'theme' => 'primary',
      'icon' => 'mail',
    ];
  }

}
