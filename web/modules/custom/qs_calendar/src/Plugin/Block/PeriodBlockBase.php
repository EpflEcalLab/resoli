<?php

namespace Drupal\qs_calendar\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\qs_badge\Service\BadgeManager;
use Drupal\qs_calendar\Service\CalendarBuilder;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Period Block Base.
 */
abstract class PeriodBlockBase extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The QS Badge Manager.
   *
   * @var \Drupal\qs_badge\Service\BadgeManager
   */
  protected $badgeManager;

  /**
   * The Calendar builder.
   *
   * @var \Drupal\qs_calendar\Service\CalendarBuilder
   */
  protected $calendarBuilder;

  /**
   * The request stack (get the URL argument(s) and combined it with the path).
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CalendarBuilder $calendar_builder, RequestStack $request_stack, BadgeManager $badge_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->calendarBuilder = $calendar_builder;
    $this->requestStack = $request_stack;
    $this->badgeManager = $badge_manager;
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
        $container->get('request_stack'),
        $container->get('qs_badge.badge_manager')
    );
  }

  /**
   * Get Badges of Highest Privilegies where user has confirmed subscription(s).
   *
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community entity.
   * @param \Drupal\Core\Datetime\DrupalDateTime $date_start
   *   The start date.
   * @param \Drupal\Core\Datetime\DrupalDateTime $date_end
   *   The end date.
   *
   * @return array[]
   *   collection of dates containing privilegies, where user has subscriptions.
   */
  protected function getDotesBadges(TermInterface $community, DrupalDateTime $date_start, DrupalDateTime $date_end) {
    $badges = [];

    // From 2 dates, get Events with confirmed subscription ordered by day.
    $badges['events_subscriptions']['confirmed'] = $this->badgeManager->getSubscriptionByDates($community, $date_start, $date_end, TRUE);

    // Get all confirmed events in a single array.
    $events_confirmed = [];

    foreach ($badges['events_subscriptions']['confirmed'] as $events) {
      $events_confirmed = array_merge($events_confirmed, $events);
    }

    // From confirmed events, get privileges.
    if ($events_confirmed) {
      $badges['privileges'] = $this->badgeManager->getPrivilegesByEventsByDates($events_confirmed);
    }

    return $badges;
  }

}
