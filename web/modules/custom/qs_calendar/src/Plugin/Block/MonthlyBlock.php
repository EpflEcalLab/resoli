<?php

namespace Drupal\qs_calendar\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\qs_calendar\Service\CalendarBuilder;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\qs_acl\Service\PrivilegeManager;
use Drupal\qs_activity\Service\EventManager;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Calendar Monthly.
 *
 * @Block(
 *   id = "qs_calendar_monthly_block",
 *   admin_label = @Translation("Calendar Monthly"),
 * )
 */
class MonthlyBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The Calendar builder.
   *
   * @var \Drupal\qs_calendar\Service\CalendarBuilder
   */
  protected $calendarBuilder;

  /**
   * Current Route.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $route;

  /**
   * The entity QS Event Manager.
   *
   * @var \Drupal\qs_activity\Service\EventManager
   */
  protected $eventManager;

  /**
   * The Privilege Manager.
   *
   * @var \Drupal\qs_acl\Service\PrivilegeManager
   */
  protected $privilegeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CalendarBuilder $calendar_builder, CurrentRouteMatch $route, EventManager $event_manager, PrivilegeManager $privilege_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->calendarBuilder  = $calendar_builder;
    $this->route            = $route;
    $this->eventManager     = $event_manager;
    $this->privilegeManager = $privilege_manager;
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
        $container->get('qs_calendar.calendar_builder'),
        $container->get('current_route_match'),
        $container->get('qs_activity.event_manager'),
        $container->get('qs_acl.privilege_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build($params = []) {
    $variables = [];

    // Get pagination day.
    $pagination_day = $this->route->getParameter('day');
    $day = new DrupalDateTime();
    if ($pagination_day) {
      try {
        $day = DrupalDateTime::createFromFormat('Y-m-d', $pagination_day);
      }
      catch (\Exception $e) {
        $day = new DrupalDateTime();
      }
    }

    $next_month = clone $day;
    $next_month->modify('first day of next month');
    $next_month->setTime(0, 0);

    $prev_month = clone $day;
    $prev_month->modify('first day of previous month');
    $prev_month->setTime(0, 0);

    $variables['prev_month'] = $prev_month;
    $variables['next_month'] = $next_month;

    $date_start         = $this->calendarBuilder->getFirstMondayMonthFullWeek($day);
    $date_end           = $this->calendarBuilder->getLastSundayMonthFullWeek($day);
    $variables['dates'] = $this->calendarBuilder->build($date_start, $date_end);

    return [
      '#theme'     => 'qs_calendar_monthly_block',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
          'url.query_args',
        ],
        'tags' => [
          // Invalidated whenever any Event is updated, deleted or created.
          'node_list:event',
          // Invalidated whenever any Privilege is updated, deleted or created.
          'privilege_list:privilege',
        ],
      ],
    ];
  }

}
