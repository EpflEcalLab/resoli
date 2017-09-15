<?php

namespace Drupal\qs_supervisor\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\qs_auth\Service\Account;
use Drupal\qs_site\Form\InlineErrorFormTrait;
use Drupal\user\UserInterface;

/**
 * AccountEditForm Class.
 */
class AccountEditForm extends FormBase {
  use InlineErrorFormTrait;

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * The user Storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * The Quartiers-Solidaires account service.
   *
   * @var \Drupal\qs_auth\Service\Account
   */
  private $account;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, EntityTypeManagerInterface $entity_type_manager, Account $account) {
    $this->acl         = $acl;
    $this->userStorage = $entity_type_manager->getStorage('user');
    $this->account     = $account;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('qs_acl.access_control'),
      $container->get('entity_type.manager'),
      $container->get('qs_auth.account')
    );
  }

  /**
   * Checks access.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $account
   *   Run access checks for this account.
   * @param \Drupal\user\UserInterface $user
   *   Run access checks for this user.
   *
   * @return bool
   *   Access allowed or rejected.
   */
  public function access(AccountProxyInterface $account, UserInterface $user) {
    $access = AccessResult::forbidden();

    if ($this->acl->hasWriteAccessAccount($user, $account)) {
      $access = AccessResult::allowed();
    }

    return $access;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_supervisor_account_edit_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, UserInterface $user = NULL) {
    // Disable caching & HTML5 validation.
    $form['#cache']['max-age'] = 0;
    $form['#attributes']['novalidate'] = 'novalidate';
    $form['#title'] = $this->t('qs_supervisor.account.form.edit_title');
    $form['#top_links'] = [
      'qs_supervisor.account.dashboard' => [
        'label' => $this->t('qs_supervisor.account.form.back'),
        'params' => [
          'user' => $user->uid->value,
        ],
        'options' => [
          'icon' => 'chevron-left',
        ],
      ],
    ];
    $form['#theme_wrappers'] = [
      'form__fullpage',
    ];

    // Save the user for submisson.
    $form['user'] = [
      '#type'  => 'hidden',
      '#value' => $user->id(),
    ];

    $form['credentials'] = [
      '#type'  => 'fieldset',
      '#title'  => 'fieldset',
      '#theme_wrappers' => [
        'container__center',
      ],
    ];

    $form['credentials']['mail'] = [
      '#attributes'    => ['required' => TRUE],
      '#type'          => 'email',
      '#title'         => $this->t('qs_supervisor.account.form.edit.mail'),
      '#placeholder'   => $this->t('qs_auth.register_form.mail.placeholder'),
      '#required'      => FALSE,
      '#default_value' => $user->mail->value,
    ];

    $form['credentials']['password'] = [
      '#attributes'  => ['required' => FALSE],
      '#type'        => 'password',
      '#title'       => $this->t('qs_supervisor.account.form.edit.password'),
      '#placeholder' => $this->t('qs_auth.register_form.password.placeholder'),
      '#required'    => FALSE,
    ];

    $form['personnal'] = [
      '#type'  => 'fieldset',
      '#theme_wrappers' => [
        'container__center',
      ],
    ];

    $form['personnal']['firstname'] = [
      '#attributes'    => ['required' => TRUE],
      '#title'         => $this->t('qs_supervisor.account.form.edit.firstname'),
      '#placeholder'   => $this->t('qs_auth.form.register.firstname.placeholder'),
      '#type'          => 'textfield',
      '#required'      => FALSE,
      '#default_value' => $user->field_firstname->value,
    ];

    $form['personnal']['lastname'] = [
      '#attributes'    => ['required' => TRUE],
      '#title'         => $this->t('qs_supervisor.account.form.edit.lastname'),
      '#placeholder'   => $this->t('qs_auth.form.register.lastname.placeholder'),
      '#type'          => 'textfield',
      '#required'      => FALSE,
      '#default_value' => $user->field_lastname->value,
    ];

    $form['personnal']['phone'] = [
      '#attributes'    => ['required' => FALSE],
      '#type'          => 'textfield',
      '#title'         => $this->t('qs_supervisor.account.form.edit.phone'),
      '#placeholder'   => $this->t('qs_auth.register_form.phone.placeholder'),
      '#required'      => FALSE,
      '#default_value' => $user->field_phone->value,
    ];

    $form['actions']['submit'] = [
      '#type'  => 'submit',
      '#attributes' => [
        'class' => [
          'align-self-center',
        ],
        'icon' => 'check',
      ],
      '#value' => $this->t('qs.form.submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Assert the firstname is valid.
    if (!$form_state->getValue('firstname') || empty($form_state->getValue('firstname'))) {
      $form_state->setErrorByName('[personnal][firstname]', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['personnal']['firstname']['#title']]));
    }

    // Assert the lastname is valid.
    if (!$form_state->getValue('lastname') || empty($form_state->getValue('lastname'))) {
      $form_state->setErrorByName('[personnal][lastname]', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['personnal']['lastname']['#title']]));
    }

    // Assert the mail is valid.
    if (!$form_state->getValue('mail') || !filter_var($form_state->getValue('mail'), FILTER_VALIDATE_EMAIL)) {
      $form_state->setErrorByName('[credentials][mail]', $this->t('qs.form.error.mail.malformed'));
    }

    // Check email is uniq. as mail.
    $accounts = $this->userStorage->loadByProperties(['mail' => $form_state->getValue('mail')]);
    if ($accounts && !isset($accounts[$form_state->getValue('user')])) {
      $form_state->setErrorByName('[credentials][mail]', $this->t('qs.form.error.mail.used'));
    }

    // Check email is uniq. as username.
    $accounts = $this->userStorage->loadByProperties(['name' => $form_state->getValue('mail')]);
    if ($accounts && !isset($accounts[$form_state->getValue('user')])) {
      $form_state->setErrorByName('[credentials][mail]', $this->t('qs.form.error.mail.used'));
    }

    // Check username is Drupal compliant.
    if ($violation = user_validate_name($form_state->getValue('mail'))) {
      $form_state->setErrorByName('[credentials][mail]', $violation);
    }

    // Add inline errors.
    $this->applyErrorsInline($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $user = $this->userStorage->load($form_state->getValue('user'));

    // Prepare fields.
    $fields['mail']            = $form_state->getValue('mail');
    $fields['username']        = $form_state->getValue('mail');
    $fields['field_phone']     = $form_state->getValue('phone');
    $fields['field_firstname'] = $form_state->getValue('firstname');
    $fields['field_lastname']  = $form_state->getValue('lastname');

    if (!empty($form_state->getValue('password'))) {
      $fields['password'] = $form_state->getValue('password');
    }

    $user = $this->account->update($user, $fields);

    drupal_set_message($this->t('qs_supervisor.account.form.edit.success @firstname, @lastname, @mail', [
      '@firstname' => $user->field_firstname->value,
      '@lastname'  => $user->field_lastname->value,
      '@mail'      => $user->field_email->value,
    ]));

    $form_state->setRedirect('qs_supervisor.account.dashboard', ['user' => $user->id()], []);
  }

}
