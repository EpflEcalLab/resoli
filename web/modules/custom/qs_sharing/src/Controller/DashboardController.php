<?php

namespace Drupal\qs_sharing\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_sharing\Repository\OfferRepository;
use Drupal\qs_sharing\Repository\VolunteerismRepository;
use Drupal\taxonomy\TermInterface;
use Drupal\user\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Dashboard to manage sharing.
 *
 * The dashboard list operations the user can operate related to sharing.
 */
class DashboardController extends ControllerBase {

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * The Volunteerism repository.
   *
   * @var \Drupal\qs_sharing\Repository\OfferRepository
   */
  private $offerRepository;

  /**
   * The Volunteerism repository.
   *
   * @var \Drupal\qs_sharing\Repository\VolunteerismRepository
   */
  private $volunteerismRepository;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, VolunteerismRepository $volunteerism_repository, OfferRepository $offer_repository) {
    $this->acl = $acl;
    $this->volunteerismRepository = $volunteerism_repository;
    $this->offerRepository = $offer_repository;
  }

  /**
   * Checks access.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\taxonomy\TermInterface $community
   *   Run access checks for this taxonomy.
   * @param \Drupal\user\UserInterface $user
   *   Run access checks for this user.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account, TermInterface $community, UserInterface $user) {
    $access = AccessResult::forbidden();

    if ($this->acl->hasDashboardSharingAccess($community, $user)) {
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
      $container->get('qs_sharing.repository.volunteerism'),
      $container->get('qs_sharing.repository.offer')
    );
  }

  /**
   * Dashboard page.
   */
  public function dashboard(Request $request, TermInterface $community, UserInterface $user) {
    $variables = [
      'community' => $community,
      'user' => $user->id(),
      'isVolunteer' => $this->volunteerismRepository->getAllByCommunityUser($community, $user),
      'hasOffers' => $this->offerRepository->getAllOffersByUser($user, $community),
    ];

    return [
      '#theme' => 'qs_sharing_dashboard_page',
      '#variables' => $variables,
      '#cache' => [
        'tags' => $this->getCacheTags(),
        'contexts' => [
          'user',
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags(?array $nodes = NULL) {
    $tags = [
      // Invalidated whenever any Offer is updated, deleted or created.
      'node_list:offer',
      // Invalidated whenever any Volunteerism is updated, deleted or created.
      'volunteerism_list:volunteerism',
    ];

    if ($nodes) {
      foreach ($nodes as $node) {
        $tags[] = 'node:' . $node->id();
      }
    }

    return $tags;
  }

}
