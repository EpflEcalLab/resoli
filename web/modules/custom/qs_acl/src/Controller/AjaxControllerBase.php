<?php

namespace Drupal\qs_acl\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_acl\Service\PrivilegeManger;
use Drupal\qs_acl\Entity\Privilege;

/**
 * AjaxControllerBase.
 */
class AjaxControllerBase extends ControllerBase {
  /**
   * The request stack (get the URL argument(s) and combined it with the path).
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

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
   * The term Storage.
   *
   * @var \Drupal\taxonomy\TermStorageInterface
   */
  protected $termStorage;

  /**
   * The user Storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * The Privilege Storage.
   *
   * @var \Drupal\Core\Entity\ContentEntityStorageInterface
   */
  protected $privilegeStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(RequestStack $request_stack, AccessControl $acl, PrivilegeManger $privilege_manager) {
    $this->requestStack     = $request_stack;
    $this->acl              = $acl;
    $this->privilegeManger  = $privilege_manager;
    $this->privilegeStorage = $this->entityTypeManager()->getStorage('privilege');
    $this->termStorage      = $this->entityTypeManager()->getStorage('taxonomy_term');
    $this->userStorage      = $this->entityTypeManager()->getStorage('user');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
    // Load customs services used in this class.
    $container->get('request_stack'),
    $container->get('qs_acl.access_control'),
    $container->get('qs_acl.privilege_manger')
    );
  }
}
