<?php

namespace Drupal\qs_calendar\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\qs_activity\Service\EventManager;
use Drupal\Core\Datetime\DrupalDateTime;
use Symfony\Component\HttpFoundation\Request;
use Drupal\taxonomy\TermInterface;

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
   * The entity QS Event Manager.
   *
   * @var \Drupal\qs_activity\Service\EventManager
   */
  protected $eventManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, EventManager $event_manager) {
    $this->acl          = $acl;
    $this->eventManager = $event_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
    // Load customs services used in this class.
    $container->get('qs_acl.access_control'),
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
   * Collection by week.
   */
  public function weekly(Request $request, TermInterface $community) {
    $variables = ['community' => $community];
    $variables['events'] = $this->getEventsByDay($request, $community);

    // Get day from parameter.
    $current_day = $request->query->get('day');
    $variables['current_day'] = $current_day ?: new DrupalDateTime();

    // Get badges.
    if (!empty($variables['events'])) {
    }

    return [
      '#theme'     => 'qs_calendar_collection_weekly_page',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
          'url.query_args',
        ],
        'tags' => [
          // Invalidated whenever any Event is updated, deleted or created.
          'node_list:event',
          // Invalidated whenever any Community is updated, deleted or created.
          'taxonomy_term_list:communities',
          // Invalidated whenever any Privilege is updated, deleted or created.
          'privilege_list:privilege',
        ],
      ],
    ];
  }

  /**
   * Collection by month.
   */
  public function monthly(Request $request, TermInterface $community) {
    $variables = ['community' => $community];
    $variables['events'] = $this->getEventsByDay($request, $community);

    // Get day from parameter.
    $current_day = $request->query->get('day');
    $variables['current_day'] = $current_day ?: new DrupalDateTime();

    // Get badges.
    if (!empty($variables['events'])) {
    }

    return [
      '#theme'     => 'qs_calendar_collection_monthly_page',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
          'url.query_args',
        ],
        'tags' => [
          // Invalidated whenever any Event is updated, deleted or created.
          'node_list:event',
          // Invalidated whenever any Community is updated, deleted or created.
          'taxonomy_term_list:communities',
          // Invalidated whenever any Privilege is updated, deleted or created.
          'privilege_list:privilege',
        ],
      ],
    ];
  }

  /**
   * Get every events for the given community & day in GET parameter.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   * @param Drupal\taxonomy\TermInterface $community
   *   The community entity.
   *
   * @return Drupal\node\NodeInterface[]
   *   A collection of node's Event. Oterwhise an empty array.
   */
  protected function getEventsByDay(Request $request, TermInterface $community) {
    // Get pagination day.
    $pagination_day = $request->query->get('day');
    $day = new DrupalDateTime();
    if ($pagination_day) {
      try {
        $day = DrupalDateTime::createFromFormat('Y-m-d', $pagination_day);
      }
      catch (\Exception $e) {
        $day = new DrupalDateTime();
      }
    }

    $day_start = clone $day;
    $day_start->setTime(0, 0);

    $day_end = clone $day;
    $day_end->setTime(23, 59, 59);

    // Get the only next events of each ones.
    return $this->eventManager->getByDate($community, $day_start, $day_end);
  }

}
