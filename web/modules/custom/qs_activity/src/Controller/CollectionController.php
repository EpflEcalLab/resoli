<?php

namespace Drupal\qs_activity\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\qs_activity\Service\ActivityManager;
use Drupal\qs_activity\Service\EventManager;

/**
 * CollectionController.
 */
class CollectionController extends ControllerBase {

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * The node Storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * The entity QS Activity Manager.
   *
   * @var \Drupal\qs_activity\Service\ActivityManager
   */
  protected $activityManager;

  /**
   * The entity QS Event Manager.
   *
   * @var \Drupal\qs_activity\Service\EventManager
   */
  protected $eventManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, EntityTypeManagerInterface $entity_type_manager, ActivityManager $activity_manager, EventManager $event_manager) {
    $this->acl             = $acl;
    $this->nodeStorage     = $entity_type_manager->getStorage('node');
    $this->activityManager = $activity_manager;
    $this->eventManager    = $event_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
    // Load customs services used in this class.
    $container->get('qs_acl.access_control'),
    $container->get('entity_type.manager'),
    $container->get('qs_activity.activity_manager'),
    $container->get('qs_activity.event_manager')
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

    if ($this->acl->hasAccessCommunity($community)) {
      $access = AccessResult::allowed();
    }

    return $access;
  }

  /**
   * Collection by themes.
   */
  public function themes(TermInterface $community) {
    // Query to retreive all activities by theme.
    $activities_nids = $this->activityManager->getThemed($community);
    $variables = [];

    // From the activity before, get the only next events of each ones.
    $events = $this->eventManager->getNext($activities_nids);

    if (!empty($activities_nids)) {
      $variables['activities'] = $this->nodeStorage->loadMultiple($activities_nids);
    }

    if (!empty($events)) {
      foreach ($events as $event) {
        $variables['events'][$event->field_activity->target_id] = $event;
      }
    }

    return [
      '#theme'     => 'qs_activity_collection_by_theme_page',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
          'url.query_args',
        ],
        'tags' => [
          // Invalidated whenever any Community is updated, deleted or created.
          'taxonomy_term_list:communities',
          // Invalidated whenever any Privilege is updated, deleted or created.
          'privilege_list:privilege',
        ],
      ],
    ];
  }

}
