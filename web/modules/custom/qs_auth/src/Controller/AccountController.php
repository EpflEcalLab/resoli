<?php

namespace Drupal\qs_auth\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\taxonomy\TermInterface;

/**
 * AccountController.
 */
class AccountController extends ControllerBase {

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  protected $acl;

  /**
   * The term Storage.
   *
   * @var \Drupal\taxonomy\TermStorageInterface
   */
  private $termStorage;

  /**
   * The entity query factory.
   *
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $queryFactory;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, EntityTypeManagerInterface $entity_type_manager, QueryFactory $query_factory) {
    $this->acl          = $acl;
    $this->termStorage  = $entity_type_manager->getStorage('taxonomy_term');
    $this->queryFactory = $query_factory;
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
    $container->get('entity.query')
    );
  }

  /**
   * Approval page.
   *
   * This page is shown when the user access to a community which he previously
   * applied but which he's not a certified member.
   * He must be reviewed by a Manager of this community.
   */
  public function approval(TermInterface $community) {
    $variables['community'] = $community;

    return [
      '#theme' => 'qs_auth_approval_page',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
        ],
        'tags' => [
          // Invalidated whenever any Privilege is updated, deleted or created.
          'privilege_list:privilege',
        ],
      ],
    ];
  }

  /**
   * Communities page.
   *
   * This page is shown when the user has more than 1 community where he's a
   * certified member.
   *
   * @TODO: Code the page with link of community, appliance link,
   * status of pending appliance & membership.
   */
  public function communities() {
    $variables['communities'] = $this->acl->getCommunities();
    $variables['pending'] = $this->acl->getPendingApprovalCommunities();

    return [
      '#theme'     => 'qs_auth_communities_page',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
        ],
        'tags' => [
          // Invalidated whenever any Community is updated, deleted or created.
          'taxonomy_term_list:communities',
          // Invalidated whenever any Privilege is updated, deleted or created.
          'privilege_list:privilege',
        ],
      ],
    ];
  }

}
