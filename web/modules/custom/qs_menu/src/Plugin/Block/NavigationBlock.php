<?php

namespace Drupal\qs_menu\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\masquerade\Masquerade;
use Drupal\qs_acl\Service\AccessControl;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Navigation Block.
 *
 * @Block(
 *     id="qs_menu_navigation_block",
 *     admin_label=@Translation("Accordion Main Navigation"),
 * )
 */
class NavigationBlock extends BlockBase implements ContainerFactoryPluginInterface {

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
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * The Masquerade Service.
   *
   * @var \Drupal\masquerade\Masquerade
   */
  private $masquerade;

  /**
   * Current Route.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  private $route;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccessControl $acl, CurrentRouteMatch $route, AccountProxyInterface $currentUser, UrlGeneratorInterface $url_generator, Masquerade $masquerade) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->acl = $acl;
    $this->route = $route;
    $this->currentUser = $currentUser;
    $this->urlGenerator = $url_generator;
    $this->masquerade = $masquerade;
  }

  /**
   * {@inheritdoc}
   */
  public function build($params = []) {
    $variables['route_name'] = $this->route->getRouteName();
    $variables['current_user'] = $this->currentUser;

    $render = [
      '#theme' => 'qs_menu_navigation_block',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
          'url',
          'session.is_masquerading',
        ],
        'tags' => [
          // Invalidated whenever any Privilege is updated, deleted or created.
          'privilege_list:privilege',
        ],
      ],
    ];

    $community = $this->route->getParameter('community');
    $node = $this->route->getParameter('node');

    if (!$community && $node && $node->bundle() === 'activity') {
      $community = $node->field_community->entity;
    }

    $activity = $this->route->getParameter('activity');

    if (!$community && $activity && $activity->bundle() === 'activity') {
      $community = $activity->field_community->entity;
    }

    $event = $this->route->getParameter('event');

    if (!$community && $event && $event->bundle() === 'event') {
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
        'url' => $this->urlGenerator->generate('qs_activity.collection.dates', [
          'community' => $community->id(),
        ]),
        'icon' => 'activities',
        'links' => [
          'qs_activity.collection.dates' => [
            'url' => $this->urlGenerator->generate('qs_activity.collection.dates', [
              'community' => $community->id(),
            ]),
            'label' => $this->t('qs_menu.links.activities.date'),
          ],
          'qs_activity.collection.themes' => [
            'url' => $this->urlGenerator->generate('qs_activity.collection.themes', [
              'community' => $community->id(),
            ]),
            'label' => $this->t('qs_menu.links.activities.themes'),
          ],
        ],
        'activated_by' => [
          'qs_activity.collection.themes',
          'qs_activity.collection.dates',
        ],
      ],
      'calendar' => [
        'label' => $this->t('qs_menu.links.calendar'),
        'url' => $this->urlGenerator->generate('qs_calendar.collection.weekly', [
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
      'sharing' => [
        'label' => $this->t('qs_menu.links.sharing'),
        'url' => $this->urlGenerator->generate('qs_sharing.collection.offer', [
          'community' => $community->id(),
        ]),
        'icon' => 'sharing',
        'links' => [
          'qs_sharing.collection.offer' => [
            'url' => $this->urlGenerator->generate('qs_sharing.collection.offer', [
              'community' => $community->id(),
            ]),
            'label' => $this->t('qs_menu.links.sharing.offer'),
          ],
          'qs_sharing.requests.form.add' => [
            'url' => $this->urlGenerator->generate('qs_sharing.requests.form.add', [
              'community' => $community->id(),
            ]),
            'label' => $this->t('qs_menu.links.sharing.request'),
          ],
        ],
        'activated_by' => [
          'qs_sharing.collection.offer',
          'qs_sharing.requests.form.add',
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
    ];

    $render['#variables']['settings']['account'] = [
      'label' => $this->t('qs_menu.links.account.dashboard'),
      'url' => $this->urlGenerator->generate('qs_supervisor.account.dashboard', ['user' => $this->currentUser->id()]),
      'icon' => 'user',
      'activated_by' => [
        'qs_menu.links.account.dashboard',
      ],
    ];

    if ($this->acl->hasAdminAccessCommunity($community)) {
      $render['#variables']['settings']['community'] = [
        'label' => $this->t('qs_menu.links.account.communities'),
        'url' => $this->urlGenerator->generate('qs_community.dashboard', ['community' => $community->id()]),
        'icon' => 'communities-sm',
        'activated_by' => [
          'qs_menu.links.account.dashboard',
        ],
      ];
    }

    if ($this->masquerade->isMasquerading()) {
      $render['#variables']['settings']['unmasquerade'] = [
        'label' => $this->t('qs.masquerade.unmasquerade'),
        'url' => $this->urlGenerator->generate('masquerade.unmasquerade'),
        'icon' => 'power',
      ];
    }

    $current_item = NULL;

    foreach ($render['#variables']['menu'] as $key => $item) {
      if (\in_array($variables['route_name'], $item['activated_by'], TRUE)) {
        $current_item = $item;
        $render['#variables']['menu'][$key]['current'] = TRUE;
      }
    }

    $render['#variables']['current_menu_item'] = $current_item;

    return $render;
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
        $container->get('url_generator'),
        $container->get('masquerade')
    );
  }

}
