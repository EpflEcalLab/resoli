<?php

namespace Drupal\qs_auth\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\qs_acl\Service\PrivilegeManager;
use Drupal\qs_site\Form\InlineErrorFormTrait;
use Drupal\Core\Mail\MailManagerInterface;

/**
 * CommunitiesApplyForm class.
 *
 * @TODO: Code the form for appliance.
 */
class CommunitiesApplyForm extends FormBase {
  use InlineErrorFormTrait;

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
   * The user Storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * The current active user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The Privilege Manager.
   *
   * @var \Drupal\qs_acl\Service\PrivilegeManager
   */
  private $privilegeManager;

  /**
   * Composes and optionally sends an email message.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mail;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, EntityTypeManagerInterface $entity_type_manager, PrivilegeManager $privilege_manager, AccountProxyInterface $currentUser, MailManagerInterface $mail) {
    $this->acl              = $acl;
    $this->termStorage      = $entity_type_manager->getStorage('taxonomy_term');
    $this->userStorage      = $entity_type_manager->getStorage('user');
    $this->privilegeManager = $privilege_manager;
    $this->currentUser      = $currentUser;
    $this->mail             = $mail;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
    $container->get('qs_acl.access_control'),
    $container->get('entity_type.manager'),
    $container->get('qs_acl.privilege_manager'),
    $container->get('current_user'),
    $container->get('plugin.manager.mail')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_auth_communities_apply_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $extra = NULL) {
    // Disable caching & HTML5 validation.
    $form['#cache']['max-age'] = 0;
    $form['#attributes']['novalidate'] = 'novalidate';

    $form['#title'] = $this->t('qs_supervisor.account.form.title');

    $form['#top_links'] = [
      'qs_supervisor.account.dashboard' => [
        'label' => $this->t('qs_supervisor.account.form.back'),
        'params' => [
          'user' => $this->currentUser->id(),
        ],
        'options' => [
          'icon' => 'chevron-left',
        ],
      ],
    ];
    $form['#theme_wrappers'] = [
      'form__fullpage',
    ];

    $communities = $this->termStorage->loadTree('communities', 0, NULL, TRUE);
    $options = [];
    foreach ($communities as $community) {
      $options[$community->tid->value] = $community->name->value;
    }

    // Get current user already requested communities.
    $user_communities = $this->acl->getCommunities();
    $user_pending     = $this->acl->getPendingApprovalCommunities();
    foreach ($user_communities as $community) {
      if (isset($options[$community->tid->value])) {
        unset($options[$community->tid->value]);
      }
    }
    foreach ($user_pending as $community) {
      if (isset($options[$community->tid->value])) {
        unset($options[$community->tid->value]);
      }
    }

    if (!empty($options)) {
      $form['community'] = [
        '#attributes' => [
          'required' => TRUE,
          'title' => $this->t('qs_auth.form.communities_apply.community'),
          'variant' => 'button',
        ],
        '#type'       => 'radios',
        '#required'   => FALSE,
        '#options'    => $options,
        '#theme_wrappers' => [
          'radios__buttons',
          'container__center',
        ],
      ];

      $form['actions']['submit'] = [
        '#type'  => 'submit',
        '#attributes' => [
          'class' => [
            'align-self-center',
            'mt-5',
          ],
          'icon' => 'check',
        ],
        '#value' => $this->t('qs.form.submit'),
      ];
    }
    else {
      $form['community'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('qs_auth.form.communities_apply.no_community'),
        '#attributes' => [
          'class' => [
            'mx-auto',
          ],
        ],
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Assert the community is valid.
    if (!$form_state->getValue('community') || empty($form_state->getValue('community'))) {
      $form_state->setErrorByName('[community]', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['community']['#attributes']['title']]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $account = $this->userStorage->load($this->currentUser->id());

    // Create a Request Privilege as Member for this community.
    $community = $this->termStorage->load($form_state->getValue('community'));
    $this->privilegeManager->request('community_members', $community, $this->currentUser);

    // Get all managers of one community.
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
          'account'   => $account,
          'community' => $community,
        ]);
      }
    }

    drupal_set_message($this->t('qs_auth.communities.apply.success @community', [
      '@community' => $community->getName(),
    ]));

    $form_state->setRedirect('qs_supervisor.account.dashboard', ['user' => $account->id()], []);
  }

}
