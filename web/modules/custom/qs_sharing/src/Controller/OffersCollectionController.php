<?php

namespace Drupal\qs_sharing\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Collection of offers for Sharing.
 */
class OffersCollectionController extends ControllerBase {
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
   * {@inheritdoc}
   */
  public function getCacheTags(?array $nodes = NULL) {
    $tags = [
      // Invalidated whenever any Community is updated, deleted or created.
      'taxonomy_term_list:communities',
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
   * Collection by offers.
   */
  public function offer(Request $request, TermInterface $community) {
    $variables = ['community' => $community];

    return [
      '#theme' => 'qs_sharing_collection_offer_page',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
          'url.query_args',
        ],
      ],
    ];
  }

}
