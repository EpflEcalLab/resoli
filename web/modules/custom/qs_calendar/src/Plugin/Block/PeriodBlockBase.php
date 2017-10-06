<?php

namespace Drupal\qs_calendar\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\qs_calendar\Service\CalendarBuilder;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\qs_acl\Service\PrivilegeManager;
use Drupal\qs_activity\Service\EventManager;

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
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CalendarBuilder $calendar_builder, RequestStack $request_stack, EventManager $event_manager, PrivilegeManager $privilege_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->calendarBuilder  = $calendar_builder;
    $this->requestStack     = $request_stack;
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
        $container->get('request_stack'),
        $container->get('qs_activity.event_manager'),
        $container->get('qs_acl.privilege_manager')
    );
  }

}
