<?php

namespace Drupal\qs_acl\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * PrivilegeGodController.
 */
class PrivilegeGodController extends ControllerBase {

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * The Privilege Manager.
   *
   * @var \Drupal\qs_acl\Service\PrivilegeManger
   */
  private $privilegeManger;


  /**
   * Request stack that controls the lifecycle of requests.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, EntityTypeManagerInterface $entity_type_manager, PrivilegeManger $privilege_manager, RequestStack $request_stack) {
    $this->acl             = $acl;
    $this->privilegeManger = $privilege_manager;
    $this->requestStack    = $request_stack;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
    // Load customs services used in this class.
    $container->get('qs_acl.access_control'),
    $container->get('qs_acl.privilege_manger'),
    $container->get('request_stack')
    );
  }

  /**
   * Checks access.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   Run access checks for this entity (communities of activity).
   *
   * @return bool
   *   Access allowed or rejected.
   */
  public function access(AccountInterface $account, EntityInterface $entity) {
    $access = AccessResult::forbidden();
    if ($entity->bundle() == 'communities' && $this->acl->hasAdminAccessCommunity($community)) {
      $access = AccessResult::allowed();
    }
    return $access;
  }

  /**
   * Toggle (create if not exists) a privilege for a give entity & account.
   *
   * This AJAX call is called from the members dashboard only.
   */
  public function toggle(AccountInterface $account, EntityInterface $entity, $privilege) {

  }

}
