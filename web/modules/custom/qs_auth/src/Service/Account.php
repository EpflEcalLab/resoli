<?php

namespace Drupal\qs_auth\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\user\UserAuthInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\qs_acl\Service\PrivilegeManger;

/**
 * Service Account.
 */
class Account {

  /**
   * Composes and optionally sends an email message.
   *
   * @var Drupal\Core\Mail\MailManagerInterface
   */
  protected $mail;

  /**
   * The user Storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * The user authentication.
   *
   * @var \Drupal\user\UserAuthInterface
   */
  protected $userAuth;

  /**
   * The entity query factory.
   *
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $queryFactory;

  /**
   * The term Storage.
   *
   * @var \Drupal\taxonomy\TermStorageInterface
   */
  private $termStorage;

  /**
   * The Privilege Manager.
   *
   * @var \Drupal\qs_acl\Service\PrivilegeManger
   */
  private $privilegeManger;

  /**
   * Class constructor.
   */
  public function __construct(MailManagerInterface $mail, EntityTypeManagerInterface $entity_type_manager, UserAuthInterface $user_auth, QueryFactory $query_factory, PrivilegeManger $privilege_manager) {
    $this->mail            = $mail;
    $this->userStorage     = $entity_type_manager->getStorage('user');
    $this->termStorage     = $entity_type_manager->getStorage('taxonomy_term');
    $this->userAuth        = $user_auth;
    $this->queryFactory    = $query_factory;
    $this->privilegeManger = $privilege_manager;
  }

  /**
   * Account creation using Array to fill data.
   *
   * @param array $data
   *   Data used to create the user.
   *
   * @return Drupal\Core\Session\AccountProxyInterface
   *   Created user object.
   */
  public function create(array $data) {
    $user = $this->userStorage->create();

    // Mandatory settings.
    $user->setPassword($data['password']);
    $user->enforceIsNew();
    $user->setEmail($data['mail']);
    // This username must be unique and accept only a-Z,0-9, - _ @
    // We use the email address as Username.
    $user->setUsername($data['username']);

    // Account settings.
    $user->set('field_firstname', $data['firstname']);
    $user->set('field_lastname', $data['lastname']);
    $user->set('field_phone', $data['phone']);

    // Add default role beginner.
    $user->addRole('beginner');
    $user->activate();
    $user->save();

    // Create a Request Privilege as Member for this community.
    $community = $this->termStorage->load($data['community']);
    if ($community) {
      $this->privilegeManger->request('community_members', $community, $user);
    }

    // Login the created user.
    user_login_finalize($user);

    return $user;
  }

  /**
   * Account update using uid & Array to fill data.
   *
   * @param int $uid
   *   User id to update.
   * @param array $data
   *   Data used to create the user.
   *
   * @return Drupal\Core\Session\AccountProxyInterface
   *   Created user object.
   */
  public function update($uid, array $data) {
    $user = $this->userStorage->load($uid);

    if (isset($data['password']) && !empty($data['password'])) {
      $user->setPassword($data['password']);
    }
    $user->setEmail($data['username']);
    // This username must be unique and accept only a-Z,0-9, - _ @
    // We use the email address as Username.
    $user->setUsername($data['username']);

    // Account settings.
    $user->set('field_firstname', $data['firstname']);
    $user->set('field_lastname', $data['lastname']);
    $user->set('field_phone', $data['phone']);

    $user->save();

    return $user;
  }

  /**
   * Send mail to new account mail to confirme his identity.
   *
   * @param \Drupal\user\UserInterface $user
   *   The user to send register mail.
   */
  public function sendRegisterEmail(UserInterface $user) {
    $params = ['user' => $user];
    $this->mail->mail('qs_auth', 'register', $user->getEmail(), $user->getPreferredLangcode(), $params);
  }

}
