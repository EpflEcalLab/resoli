<?php

namespace Drupal\qs_menu\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Previous Block.
 *
 * @Block(
 *   id = "qs_menu_previous_block",
 *   admin_label = @Translation("Previous Navigation"),
 * )
 */
class PreviousBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The current route.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $route;

  /**
   * The url generator service.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;

  /**
   * The current active user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  private $currentUser;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CurrentRouteMatch $route, UrlGeneratorInterface $urlGenerator, AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->route = $route;
    $this->urlGenerator = $urlGenerator;
    $this->currentUser = $current_user;
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
      $container->get('current_route_match'),
      $container->get('url_generator'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build($params = []) {
    $variables = [];

    $route_name = $this->route->getRouteName();

    $label = $this->t('qs.previous');
    $theme = 'secondary';
    $options = [];
    $url = FALSE;
    $invert = FALSE;

    $community = $this->route->getParameter('community');
    $activity = $this->route->getParameter('activity');
    $event = $this->route->getParameter('event');
    $node = $this->route->getParameter('node');

    if (!$community && $event && !$event->get('field_activity')->isEmpty()) {
      $community = $event->field_activity->entity;
    }

    if (!$activity && $event && $event->hasField('field_activity')) {
      $activity = $event->field_activity->entity;
    }

    if (!$community && $node && $node->hasField('field_community')) {
      $community = $node->field_community->entity;
    }

    if (!$community && $activity && !$activity->get('field_community')->isEmpty()) {
      $community = $activity->field_community->entity;
    }

    if ($route_name === 'entity.node.canonical') {
      switch ($node->bundle()) {
        case "activity":
          $url = $this->urlGenerator->generateFromRoute('qs_activity.collection.dates', [
            'community' => $community->id(),
          ], $options);
          $label = $this->t('qs.previous.to_activities_list');
          $theme = 'primary';
          break;

        case "event":
          $options = ['fragment' => "card{$event->id()}"];
          $url = $this->urlGenerator->generateFromRoute('entity.node.canonical', [
            'node' => $node->field_activity->target_id,
          ], $options);
          $label = $this->t('qs.previous.to_activity');
          $theme = 'primary';
          break;
      }
    }
    else {
      switch ($route_name) {
        // Go to Community Dashboard.
        case "qs_community.members":
        case "qs_community.waiting_approval":
          $url = $this->urlGenerator->generateFromRoute('qs_community.dashboard', [
            'community' => $community->id(),
          ], $options);
          $label = $this->t('qs.previous.to_community_dashboard');
          $theme = 'danger';
          break;

        // Go to Activities Listing.
        case "qs_community.dashboard":
          $url = $this->urlGenerator->generateFromRoute('qs_activity.collection.dates', [
            'community' => $community->id(),
          ], $options);
          $label = $this->t('qs.previous.to_activities_list');
          $theme = 'danger';
          break;

        case "qs_activity.user.collection":
          $url = $this->urlGenerator->generateFromRoute('qs_activity.collection.dates', [
            'community' => $community->id(),
          ], $options);
          $label = $this->t('qs.previous.to_activities_list');
          $theme = 'primary';
          break;

        // Go to Activity.
        case "qs_activity.activities.dashboard":
          $options = [];
          if ($event) {
            $options = ['fragment' => "card{$event->id()}"];
          }
          $url = $this->urlGenerator->generateFromRoute('entity.node.canonical', [
            'node' => $activity->id(),
          ], $options);
          $label = $this->t('qs.previous.to_activity');
          $theme = 'primary';
          break;

        // Go to My Activities.
        case "qs_activity.activities.form.add":
          $url = $this->urlGenerator->generateFromRoute('qs_activity.user.collection', [
            'community' => $community->id(),
            'user' => $this->currentUser->id(),
          ]);
          $label = $this->t('qs.previous.to_my_activities');
          $theme = 'primary';
          break;

        // Go to Activity Dashboard.
        case "qs_activity.activities.form.edit.info":
        case "qs_activity.activities.form.edit.visibility":
        case "qs_activity.activities.form.edit.defaults":
        case "qs_activity.events.form.add":
        case "qs_activity.activities.members":
          $options = [];
          if ($event) {
            $options = ['fragment' => "card{$event->id()}"];
          }
          $url = $this->urlGenerator->generateFromRoute('qs_activity.activities.dashboard', [
            'activity' => $activity->id(),
          ], $options);
          $label = $this->t('qs.previous.to_activity_dashboard');
          $theme = 'primary';
          break;

        // Go to Event Dashboard.
        case "qs_activity.events.form.edit":
        case "qs_subscription.subscribers":
        case "qs_subscription.waiting_approval":
          $url = $this->urlGenerator->generateFromRoute('qs_activity.events.dashboard', [
            'event' => $event->id(),
          ], $options);
          $label = $this->t('qs.previous.to_event_dashboard');
          $theme = 'secondary';
          break;

        case "qs_activity.events.dashboard":
          $options = [];
          if ($event) {
            $options = ['fragment' => "card{$event->id()}"];
          }
          $url = $this->urlGenerator->generateFromRoute('entity.node.canonical', [
            'node' => $event->id(),
          ], $options);
          $label = $this->t('qs.previous.to_event');
          $theme = 'secondary';
          break;

        // Go to Calendar.
        case "qs_subscription.user.collection":
          $url = $this->urlGenerator->generateFromRoute('qs_calendar.collection.monthly', [
            'community' => $community->id(),
          ], $options);
          $label = $this->t('qs.previous.to_calendar');
          $theme = 'primary';
          break;

        // Go to Photos.
        case "qs_photo.activity":
        case "qs_photo.user.activities.collection":
          $url = $this->urlGenerator->generateFromRoute('qs_photo.collection.theme', [
            'community' => $community->id(),
          ], $options);
          $label = $this->t('qs.previous.to_photos_list');
          $theme = 'primary';
          break;

        // Go to My Photos.
        case "qs_photo.user.form.manage":
          $url = $this->urlGenerator->generateFromRoute('qs_photo.user.activities.collection', [
            'community' => $community->id(),
            'user' => $this->currentUser->id(),
          ], $options);
          $label = $this->t('qs.previous.to_my_photos');
          $theme = 'primary';
          break;

        // Go to Homepage.
        // TODO add test for this link.
        case "qs_community.welcome":
        case "qs_supervisor.account.dashboard":
          $url = $this->urlGenerator->generateFromRoute('<front>');
          $label = $this->t('qs_auth.link.home');
          $theme = 'primary';
          $invert = TRUE;
          break;
      }
    }

    $variables['url'] = $url;
    $variables['label'] = $label;
    $variables['theme'] = $theme;
    $variables['invert'] = $invert;

    return [
      '#theme'     => 'qs_menu_previous_block',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
          'url',
        ],
      ],
    ];
  }

}
