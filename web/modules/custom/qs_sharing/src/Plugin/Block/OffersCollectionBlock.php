<?php

namespace Drupal\qs_sharing\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Http\RequestStack;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\node\NodeInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_sharing\Repository\OfferRepository;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Collection of Offers by Offer's type.
 *
 * This block must be shown into an Offer's type Node page only.
 *
 * @Block(
 *     id="qs_sharing_offers_collection_block",
 *     admin_label=@Translation("Collection of Offers by Offer's type"),
 * )
 */
class OffersCollectionBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Number of offers per page.
   *
   * @var int
   */
  private const OFFERS_PER_PAGE = 25;

  /**
   * The request stack.
   *
   * @var \Drupal\Core\Http\RequestStack
   */
  protected $requestStack;

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * The offer repository.
   *
   * @var \Drupal\qs_sharing\Repository\OfferRepository
   */
  private $offerRepository;

  /**
   * The term Storage.
   *
   * @var \Drupal\taxonomy\TermStorageInterface
   */
  private $termStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, RouteMatchInterface $route_match, RequestStack $request_stack, OfferRepository $offer_repository, AccessControl $acl) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->routeMatch = $route_match;
    $this->offerRepository = $offer_repository;
    $this->requestStack = $request_stack;
    $this->termStorage = $entity_type_manager->getStorage('taxonomy_term');
    $this->acl = $acl;
  }

  /**
   * {@inheritdoc}
   */
  public function build($params = []) {
    $renderer = [
      '#theme' => 'qs_sharing_offers_collection_block',
      '#variables' => [
        'theme' => NULL,
        'offer_type' => NULL,
        'offers' => [],
      ],
      '#cache' => [
        'contexts' => [
          'user',
          'url.query_args',
        ],
      ],
    ];

    $request = $this->requestStack->getCurrentRequest();

    /** @var \Drupal\node\NodeInterface|null $offer_type */
    $offer_type = $this->routeMatch->getParameter('node');

    if (!$offer_type instanceof NodeInterface || $offer_type->bundle() !== 'offer_type') {
      return $renderer;
    }
    $renderer['#variables']['offer_type'] = $offer_type;

    $theme_tid = $request->get('theme');

    if (!$theme_tid) {
      return $renderer;
    }

    /** @var \Drupal\taxonomy\TermInterface|null $offer_type */
    $theme = $this->termStorage->load($theme_tid);

    if (!$theme instanceof TermInterface || $theme->bundle() !== 'sharing_themes') {
      return $renderer;
    }

    $renderer['#variables']['theme'] = $theme;
    $offers = $this->offerRepository->getAllByOffersByTypeByTheme($offer_type, $theme);

    $renderer['#variables']['pager'] = [
      '#type' => 'pager',
      '#quantity' => '3',
      '#theme' => 'pager__light_bg',
    ];

    $renderer['#variables']['offers'] = $offers;
    $renderer['#cache']['tags'] = $this->getCacheTags($offers);

    $renderer['#variables']['can_moderate_community'] = $this->acl->hasAdminAccessCommunity($offer_type->field_community->entity);

    return $renderer;
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
      $container->get('entity_type.manager'),
      $container->get('current_route_match'),
      $container->get('request_stack'),
      $container->get('qs_sharing.repository.offer'),
      $container->get('qs_acl.access_control'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags(?array $nodes = NULL) {
    $tags = [
      // Invalidated whenever any Offer is updated, deleted or created.
      'node_list:offer',
    ];

    if ($nodes) {
      foreach ($nodes as $node) {
        $tags[] = 'node:' . $node->id();
      }
    }

    return $tags;
  }

}
