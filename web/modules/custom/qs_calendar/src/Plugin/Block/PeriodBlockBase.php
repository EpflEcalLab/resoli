<?php

namespace Drupal\qs_calendar\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\qs_calendar\Service\CalendarBuilder;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\qs_badge\Service\BadgeManager;

/**
 * Period Block Base.
 */
abstract class PeriodBlockBase extends BlockBase implements ContainerFactoryPluginInterface {

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
   * The QS Badge Manager.
   *
   * @var \Drupal\qs_badge\Service\BadgeManager
   */
  protected $badgeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CalendarBuilder $calendar_builder, RequestStack $request_stack, BadgeManager $badge_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->calendarBuilder = $calendar_builder;
    $this->requestStack    = $request_stack;
    $this->badgeManager    = $badge_manager;
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

}
