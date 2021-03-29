<?php

namespace Drupal\qs_acl\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_acl\Service\PrivilegeManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Base class for Ajax Controllers.
 */
class AjaxControllerBase extends ControllerBase {

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  protected $acl;

  /**
   * Composes and optionally sends an email message.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mail;

  /**
   * The node Storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * The Privilege Manager.
   *
   * @var \Drupal\qs_acl\Service\PrivilegeManager
   */
  protected $privilegeManager;

  /**
   * The Privilege Storage.
   *
   * @var \Drupal\Core\Entity\ContentEntityStorageInterface
   */
  protected $privilegeStorage;
  /**
   * The request stack (get the URL argument(s) and combined it with the path).
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

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
   * {@inheritdoc}
   */
  public function __construct(RequestStack $request_stack, AccessControl $acl, PrivilegeManager $privilege_manager, MailManagerInterface $mail) {
    $this->requestStack = $request_stack;
    $this->acl = $acl;
    $this->privilegeManager = $privilege_manager;
    $this->privilegeStorage = $this->entityTypeManager()->getStorage('privilege');
    $this->termStorage = $this->entityTypeManager()->getStorage('taxonomy_term');
    $this->nodeStorage = $this->entityTypeManager()->getStorage('node');
    $this->userStorage = $this->entityTypeManager()->getStorage('user');
    $this->mail = $mail;
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
    $container->get('qs_acl.privilege_manager'),
    $container->get('plugin.manager.mail')
    );
  }

}
