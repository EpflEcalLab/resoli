<?php

namespace Drupal\qs_menu\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;

/**
 * Navigation Block.
 *
 * @Block(
 *   id = "qs_menu_navigation_block",
 *   admin_label = @Translation("Accordion Main Navigation"),
 * )
 */
class NavigationBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * Current Route.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  private $route;

  /**
   * The current active user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The url generator service.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccessControl $acl, CurrentRouteMatch $route, AccountProxyInterface $currentUser, UrlGeneratorInterface $url_generator) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->acl          = $acl;
    $this->route        = $route;
    $this->currentUser  = $currentUser;
    $this->urlGenerator = $url_generator;
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
        $container->get('current_user'),
        $container->get('url_generator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build($params = []) {
    $variables['route_name'] = $this->route->getRouteName();
    $variables['current_user'] = $this->currentUser;

    $render = [
      '#theme'     => 'qs_menu_navigation_block',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
          'url',
        ],
      ],
    ];

    $community = $this->route->getParameter('community');
    $node = $this->route->getParameter('node');
    if (!$community && $node && $node->bundle() == 'activity') {
      $community = $node->field_community->entity;
    }

    $activity = $this->route->getParameter('activity');
    if (!$community && $activity && $activity->bundle() == 'activity') {
      $community = $activity->field_community->entity;
    }

    $event = $this->route->getParameter('event');
    if (!$community && $event && $event->bundle() == 'event') {
      $community = $event->field_activity->entity->field_community->entity;
    }

    // When the community doesn't exists, it's impossible build the menu.
    if (!$community) {
      return $render;
    }

    $render['#variables']['community'] = $community;
    $render['#variables']['menu'] = [
      'activities' => [
        'label' => $this->t('qs_menu.links.activities'),
        'url' => $this->urlGenerator->generate('qs_activity.collection.themes', [
          'community' => $community->id(),
        ]),
        'icon' => 'activities',
        'links' => [
          'qs_activity.collection.themes' => [
            'url' => $this->urlGenerator->generate('qs_activity.collection.themes', [
              'community' => $community->id(),
            ]),
            'label' => $this->t('qs_menu.links.activities.themes'),
          ],
          'qs_activity.collection.dates' => [
            'url' => $this->urlGenerator->generate('qs_activity.collection.dates', [
              'community' => $community->id(),
            ]),
            'label' => $this->t('qs_menu.links.activities.date'),
          ],
        ],
        'activated_by' => [
          'qs_activity.collection.themes',
          'qs_activity.collection.dates',
        ],
      ],
      'calendar' => [
        'label' => $this->t('qs_menu.links.calendar'),
        'url' => $this->urlGenerator->generate('qs_calendar.collection.monthly', [
          'community' => $community->id(),
        ]),
        'icon' => 'calendar',
        'links' => [
          'qs_calendar.collection.weekly' => [
            'url' => $this->urlGenerator->generate('qs_calendar.collection.weekly', [
              'community' => $community->id(),
            ]),
            'label' => $this->t('qs_menu.links.calendar.weekly'),
          ],
          'qs_calendar.collection.monthly' => [
            'url' => $this->urlGenerator->generate('qs_calendar.collection.monthly', [
              'community' => $community->id(),
            ]),
            'label' => $this->t('qs_menu.links.calendar.monthly'),
          ],
        ],
        'activated_by' => [
          'qs_calendar.collection.weekly',
          'qs_calendar.collection.monthly',
        ],
      ],
      'stories' => [
        'label' => $this->t('qs_menu.links.stories'),
        'url' => "javascript:alert('This feature is not yet availaible.')",
        'icon' => 'stories',
        'links' => [
          'qs_activity.collection.themes' => [
            'url' => $this->urlGenerator->generate('<front>'),
            'label' => $this->t('qs_menu.links.stories.themes'),
          ],
          // @TODO temp link to not have a broken nav:
          'qs_activity.collection.date' => [
            'url' => $this->urlGenerator->generate('<front>'),
            'label' => $this->t('qs_menu.links.stories.date'),
          ],
        ],
        'activated_by' => [
          '<front>',
        ],
      ],
      'photos' => [
        'label' => $this->t('qs_menu.links.photos'),
        'url' => $this->urlGenerator->generate('qs_photo.collection.month', [
          'community' => $community->id(),
        ]),
        'icon' => 'pictures',
        'links' => [
          'qs_photo.collection.month' => [
            'url' => $this->urlGenerator->generate('qs_photo.collection.month', [
              'community' => $community->id(),
            ]),
            'label' => $this->t('qs_menu.links.photos.months'),
          ],
          'qs_photo.collection.theme' => [
            'url' => $this->urlGenerator->generate('qs_photo.collection.theme', [
              'community' => $community->id(),
            ]),
            'label' => $this->t('qs_menu.links.photos.themes'),
          ],
        ],
        'activated_by' => [
          'qs_photo.collection.month',
          'qs_photo.collection.theme',
        ],
      ],
      'settings'   => [
        'label' => $this->t('qs_menu.links.account.dashboard'),
        'url' => $this->urlGenerator->generate('qs_supervisor.account.dashboard', ['user' => $this->currentUser->id()]),
        'icon' => 'settings',
        'links' => [
          'qs_menu.links.account.dashboard' => [
            'url' => $this->urlGenerator->generate('qs_supervisor.account.dashboard', ['user' => $this->currentUser->id()]),
            'label' => $this->t('qs_menu.links.account.dashboard'),
          ],
        ],
        'activated_by' => [
          'qs_menu.links.account.dashboard',
        ],
      ],
    ];

    $current_item = NULL;
    foreach ($render['#variables']['menu'] as $key => $item) {
      if (in_array($variables['route_name'], $item['activated_by'])) {
        $current_item = $item;
        $render['#variables']['menu'][$key]['current'] = TRUE;
      }
    }

    $render['#variables']['current_menu_item'] = $current_item;

    return $render;
  }

}
