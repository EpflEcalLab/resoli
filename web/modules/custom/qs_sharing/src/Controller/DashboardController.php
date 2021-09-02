<?php

namespace Drupal\qs_sharing\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\qs_acl\Service\AccessControl;
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
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl) {
    $this->acl = $acl;
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
      $container->get('qs_acl.access_control')
    );
  }

  /**
   * Dashboard page.
   */
  public function dashboard(Request $request, TermInterface $community, UserInterface $user) {
    $variables = [
      'community' => $community,
      'user' => $user->id(),
      // @todo get right value
      'isVolunteer' => TRUE,
      'hasOffers' => TRUE,
      'hasRequests' => TRUE,
    ];

    return [
      '#theme' => 'qs_sharing_dashboard_page',
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
