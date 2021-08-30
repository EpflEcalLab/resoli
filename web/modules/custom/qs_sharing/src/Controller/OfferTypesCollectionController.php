<?php

namespace Drupal\qs_sharing\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_sharing\Repository\OfferTypeRepository;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Collection of Offer's Type for Sharing.
 */
class OfferTypesCollectionController extends ControllerBase {
  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * The offer's type repository.
   *
   * @var \Drupal\qs_sharing\Repository\OfferTypeRepository
   */
  private $offerTypeRepository;

  /**
   * The term Storage.
   *
   * @var \Drupal\taxonomy\TermStorageInterface
   */
  private $termStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, EntityTypeManagerInterface $entity_type_manager, OfferTypeRepository $offer_type_repository) {
    $this->acl = $acl;
    $this->termStorage = $entity_type_manager->getStorage('taxonomy_term');
    $this->offerTypeRepository = $offer_type_repository;
  }

  /**
   * Checks access.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\taxonomy\TermInterface $community
   *   Run access checks for this taxonomy.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account, TermInterface $community) {
    $access = AccessResult::forbidden();

    if ($this->acl->hasAccessCommunity($community)) {
      $access = AccessResult::allowed();
    }

    return $access;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
    // Load customs services used in this class.
      $container->get('qs_acl.access_control'),
      $container->get('entity_type.manager'),
      $container->get('qs_sharing.repository.offer_type')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags(?array $nodes = NULL) {
    $tags = [
      // Invalidated whenever any Community is updated, deleted or created.
      'taxonomy_term_list:communities',
      // Invalidated whenever any Sharing Theme is updated, deleted or created.
      'taxonomy_term_list:sharing_themes',
      // Invalidated whenever any Privilege is updated, deleted or created.
      'privilege_list:privilege',
    ];

    if ($nodes) {
      foreach ($nodes as $node) {
        $tags[] = 'node:' . $node->id();
      }
    }

    return $tags;
  }

  /**
   * Collection of Offer's Type by sharing theme.
   */
  public function offersTypeByTheme(Request $request, TermInterface $community) {
    $variables = ['community' => $community];

    // Get all sharing themes.
    $themes = $this->termStorage->loadTree('sharing_themes', 0, NULL, TRUE);

    $offerTypesByTheme = [];

    foreach ($themes as $theme) {
      $offerTypesByTheme[] = [
        'theme' => $theme,
        'offersTypes' => $this->offerTypeRepository->getAllByCommunityByThemeWithOffersCount($community, $theme),
      ];
    }

    $variables['offerTypesByThemes'] = $offerTypesByTheme;

    return [
      '#theme' => 'qs_sharing_collection_offer_types_page',
      '#variables' => $variables,
      '#cache' => [
        'tags' => $this->getCacheTags(),
        'contexts' => [
          'user',
          'url.query_args',
        ],
      ],
    ];
  }

}
