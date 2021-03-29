<?php

namespace Drupal\qs_auth\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\qs_acl\Service\PrivilegeManager;
use Drupal\taxonomy\TermInterface;
use Drupal\user\UserAuthInterface;
use Drupal\user\UserInterface;

/**
 * Service Account.
 */
class Account {

  /**
   * Composes and optionally sends an email message.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mail;

  /**
   * The entity query factory.
   *
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $queryFactory;

  /**
   * The user authentication.
   *
   * @var \Drupal\user\UserAuthInterface
   */
  protected $userAuth;

  /**
   * The user Storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * The Privilege Manager.
   *
   * @var \Drupal\qs_acl\Service\PrivilegeManager
   */
  private $privilegeManager;

  /**
   * The term Storage.
   *
   * @var \Drupal\taxonomy\TermStorageInterface
   */
  private $termStorage;

  /**
   * Class constructor.
   */
  public function __construct(MailManagerInterface $mail, EntityTypeManagerInterface $entity_type_manager, UserAuthInterface $user_auth, QueryFactory $query_factory, PrivilegeManager $privilege_manager) {
    $this->mail = $mail;
    $this->userStorage = $entity_type_manager->getStorage('user');
    $this->termStorage = $entity_type_manager->getStorage('taxonomy_term');
    $this->userAuth = $user_auth;
    $this->queryFactory = $query_factory;
    $this->privilegeManager = $privilege_manager;
  }

  /**
   * Account creation using Array to fill data.
   *
   * @param array $data
   *   Data used to create the user.
   *
   * @return \Drupal\Core\Session\AccountProxyInterface
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
    $user->setUsername($data['mail']);

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
      $this->privilegeManager->request('community_members', $community, $user);
    }

    // Login the created user.
    user_login_finalize($user);

    return $user;
  }

  /**
   * Send mail to all community managers of $community with the new request.
   *
   * @param \Drupal\user\UserInterface $account
   *   The new applier user.
   * @param \Drupal\taxonomy\TermInterface $community
   *   The impacted community.
   */
  public function sendCommunityManagersApplyReq(UserInterface $account, TermInterface $community) {
    // Get all managers of this community.
    $query = $this->privilegeManager->queryPrivilege($community, 'community_managers');
    $rows = $query->execute()->fetchAll();

    $ids = [];

    foreach ($rows as $row) {
      $ids[] = $row->user;
    }

    // Load user with community_managers privilege & send them mail.
    $users = NULL;

    if ($ids) {
      $users = $this->userStorage->loadMultiple($ids);

      foreach ($users as $user) {
        $this->mail->mail('qs_auth', 'auth_community_apply', $user->getEmail(), $user->getPreferredLangcode(), [
          'account' => $account,
          'community' => $community,
        ]);
      }
    }
  }

  /**
   * Send mail to new account mail to confirm itsMembersControll identity.
   *
   * @param \Drupal\user\UserInterface $user
   *   The user to send register mail.
   */
  public function sendRegisterEmail(UserInterface $user) {
    $params = ['user' => $user];
    $this->mail->mail('qs_auth', 'register', $user->getEmail(), $user->getPreferredLangcode(), $params);
  }

  /**
   * Update a User.
   *
   * Only update given fields.
   *
   * @param \Drupal\user\UserInterface $user
   *   The user to update.
   * @param array $fields
   *   The fields to update with the new value.
   *
   * @return \Drupal\user\UserInterface
   *   The updated user.
   */
  public function update(UserInterface $user, array $fields) {
    foreach ($fields as $key => $value) {
      if ($key === 'password') {
        $user->setPassword($value);
      }
      elseif ($key === 'username') {
        $user->setUsername($value);
      }
      elseif ($key === 'mail') {
        $user->setEmail($value);
      }
      elseif ($user->hasField($key)) {
        $user->set($key, $value);
      }
    }

    $user->save();

    return $user;
  }

}
