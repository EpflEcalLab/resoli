<?php

namespace Drupal\qs_themes\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Filter Theme Block.
 *
 * Expose a filter component that open the FilterForm.
 * The form provides all the themes to be selected.
 * It will then reload the page & add the GET parameter themes[].
 *
 * @Block(
 *   id = "qs_themes_filter_block",
 *   admin_label = @Translation("Themes Filter block"),
 * )
 */
class FilterBlock extends BlockBase implements ContainerFactoryPluginInterface {
  /**
   * The current active user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Request stack that controls the lifecycle of requests.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The term Storage.
   *
   * @var \Drupal\taxonomy\TermStorageInterface
   */
  private $termStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccountProxyInterface $currentUser, RequestStack $request_stack, EntityTypeManagerInterface $entity_type_manager) {
    $this->currentUser = $currentUser;
    $this->requestStack = $request_stack;
    $this->termStorage = $entity_type_manager->getStorage('taxonomy_term');
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
        $container->get('current_user'),
        $container->get('request_stack'),
        $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build($params = []) {
    $variables = ['filtred' => NULL];

    // The request should be took at the latest moment, avoid it on constructor.
    $master_request = $this->requestStack->getMasterRequest();

    $filtred_themes = $master_request->query->get('themes');

    if ($filtred_themes) {
      $variables['filtred'] = $this->termStorage->loadMultiple($filtred_themes);
    }

    return [
      '#theme'     => 'qs_themes_filter_block',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
          'url',
          'url.query_args',
        ],
        'tags' => [
          // Invalidated whenever any Themes is updated, deleted or created.
          'taxonomy_term_list:themes',
        ],
      ],
    ];
  }

}
