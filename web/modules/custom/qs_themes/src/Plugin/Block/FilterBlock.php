<?php

namespace Drupal\qs_themes\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Filter Theme Block.
 *
 * Expose a filter component that open the FilterForm.
 * The form provides all the themes to be selected.
 * It will then reload the page & add the GET parameter themes[].
 *
 * @Block(
 *     id="qs_themes_filter_block",
 *     admin_label=@Translation("Themes Filter"),
 * )
 */
class FilterBlock extends BlockBase implements ContainerFactoryPluginInterface {

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
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RequestStack $request_stack, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->requestStack = $request_stack;
    $this->termStorage = $entity_type_manager->getStorage('taxonomy_term');
  }

  /**
   * {@inheritdoc}
   */
  public function build($params = []) {
    $variables = ['filtered' => NULL];

    // The request should be took at the latest moment, avoid it on constructor.
    $master_request = $this->requestStack->getMainRequest();

    $filtered_themes = $master_request->query->all('themes');

    if ($filtered_themes) {
      $variables['filtered'] = $this->termStorage->loadMultiple($filtered_themes);
    }

    return [
      '#theme' => 'qs_themes_filter_block',
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

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    // Instantiates this form class.
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('request_stack'),
      $container->get('entity_type.manager')
    );
  }

}
