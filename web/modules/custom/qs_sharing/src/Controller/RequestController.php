<?php

namespace Drupal\qs_sharing\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_sharing\Repository\RequestRepository;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Collection of Requests for Sharing.
 */
class RequestController extends ControllerBase {

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * The Request repository.
   *
   * @var \Drupal\qs_sharing\Repository\RequestRepository
   */
  private $requestRepository;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, RequestRepository $request_repository) {
    $this->acl = $acl;
    $this->requestRepository = $request_repository;
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
  public function access(AccountInterface $account, TermInterface $community): AccessResultInterface {
    $access = AccessResult::forbidden();

    if ($this->acl->isCommunityVolunteer($community)) {
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
      $container->get('qs_sharing.repository.request')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags(?array $nodes = NULL) {
    $tags = [
      // Invalidated whenever any Offer is updated, deleted or created.
      'node_list:request',
    ];

    if ($nodes) {
      foreach ($nodes as $node) {
        $tags[] = 'node:' . $node->id();
      }
    }

    return $tags;
  }

  /**
   * Requests collection page.
   */
  public function requests(Request $request, TermInterface $community) {
    $variables = [
      'community' => $community,
      'requests' => $this->requestRepository->getAllByCommunity($community),
      'can_moderate_community' => $this->acl->hasAdminAccessCommunity($community),
    ];

    return [
      '#theme' => 'qs_sharing_collection_requests_page',
      '#variables' => $variables,
      '#cache' => [
        'tags' => $this->getCacheTags(),
        'contexts' => [
          'user',
        ],
      ],
    ];
  }

}
