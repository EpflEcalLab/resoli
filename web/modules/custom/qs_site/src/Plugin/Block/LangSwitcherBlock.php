<?php

namespace Drupal\qs_site\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Language\LanguageManager;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an 'Header Lang Block' block.
 *
 * @Block(
 *     id="qs_site_langswitcher_block",
 *     admin_label=@Translation("Language Switcher Block"),
 * )
 */
class LangSwitcherBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The language manager.
   *
   * @var \Drupal\language\ConfigurableLanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The Current Route.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $route;

  /**
   * RelatedVideosBlock constructor.
   *
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CurrentRouteMatch $route, LanguageManager $language_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->route = $route;
    $this->languageManager = $language_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $variables = [];

    // Lang menu.
    $variables['language'] = $this->languageManager->getCurrentLanguage();
    $variables['languages'] = $this->languageManager->getLanguages();

    return [
      '#theme' => 'qs_site_langswitcher_block',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'url',
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
      $container->get('current_route_match'),
      $container->get('language_manager')
    );
  }

}
