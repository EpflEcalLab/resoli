<?php

namespace Drupal\qs_auth\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\user\UserAuthInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Entity\Query\QueryFactory;

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
   * EntityTypeManagerInterface to load user.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
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
   * EntityTypeManagerInterface to load Term(s)
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  private $termStorage;

  /**
   * Class constructor.
   */
  public function __construct(MailManagerInterface $mail, EntityTypeManagerInterface $entity_type_manager, UserAuthInterface $user_auth, QueryFactory $query_factory) {
    $this->mail         = $mail;
    $this->userStorage  = $entity_type_manager->getStorage('user');
    $this->termStorage  = $entity_type_manager->getStorage('taxonomy_term');
    $this->userAuth     = $user_auth;
    $this->queryFactory = $query_factory;
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

    // Add default role need-approval.
    // $user->addRole('need-approval');.
    $user->activate();
    $user->save();

    // Add this user to the community.
    $community = $this->termStorage->load($data['community']);
    if ($community) {
      $community->get('field_community_members')->appendItem($user);
      $community->save();
    }

    // Login the created user.
    $this->login($user->mail->value, $data['password']);

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

    // Add default role need-approval.
    // $user->addRole('need-approval');.
    $user->save();

    return $user;
  }

  /**
   * Login the given user/password.
   *
   * @param string $login
   *   Username.
   * @param string $password
   *   Password uncrypted.
   *
   * @return \Drupal\user\UserInterface
   *   The loggedin user.
   */
  protected function login($login, $password) {
    $user_uid = $this->userAuth->authenticate($login, $password);
    $account = $this->userStorage->load($user_uid);
    if (!empty($user_uid) && $account != NULL) {
      // Login the user.
      user_login_finalize($account);
      return $account;
    }
    return NULL;
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
