<?php

namespace Drupal\qs_acl\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_acl\Service\PrivilegeManger;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\qs_acl\Entity\Privilege;

/**
 * AjaxControllerBase.
 */
class AjaxControllerBase extends ControllerBase {

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  protected $acl;

  /**
   * The Privilege Manager.
   *
   * @var \Drupal\qs_acl\Service\PrivilegeManger
   */
  protected $privilegeManger;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, PrivilegeManger $privilege_manager) {
    $this->acl             = $acl;
    $this->privilegeManger = $privilege_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
    // Load customs services used in this class.
    $container->get('qs_acl.access_control'),
    $container->get('qs_acl.privilege_manger')
    );
  }

  /**
   * Checks access.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\qs_acl\Entity\Privilege $privilege
   *   Run access checks for this privilege (communities privileges).
   *
   * @return bool
   *   Access allowed or rejected.
   */
  public function access(AccountInterface $account, Privilege $privilege) {
    $access = AccessResult::forbidden();

    $entity = $privilege->getEntity();
    if ($entity->bundle() == 'communities' && $this->acl->hasAdminAccessCommunity($entity)) {
      $access = AccessResult::allowed();
    }
    return $access;
  }

}
