<?php

namespace Drupal\qs_auth\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\qs_auth\Service\Account;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\qs_site\Form\InlineErrorFormTrait;

/**
 * RegisterForm class.
 */
class RegisterForm extends FormBase {
  use InlineErrorFormTrait;

  /**
   * The qs account service.
   *
   * @var \Drupal\qs_auth\Service\Account
   */
  protected $account;

  /**
   * EntityTypeManagerInterface to load Term(s)
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  private $termStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(Account $account, EntityTypeManagerInterface $entity) {
    $this->account     = $account;
    $this->termStorage = $entity->getStorage('taxonomy_term');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
    $container->get('qs_auth.account'),
    $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_auth_register_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $extra = NULL) {
    $form['#attributes']['class'] = ['form-emphasis'];

    // Honeypot.
    honeypot_add_form_protection($form, $form_state, ['honeypot', 'time_restriction']);

    // Disable caching & HTML5 validation.
    $form['#cache']['max-age'] = 0;
    $form['#attributes']['novalidate'] = 'novalidate';

    $form['register']['step-1'] = [
      '#type'  => 'fieldset',
    ];

    $communities = $this->termStorage->loadTree('communities', 0, NULL, TRUE);
    $options = [];
    foreach ($communities as $community) {
      $options[$community->tid->value] = $community->name->value;
    }
    $form['register']['step-1']['community'] = [
      '#attributes' => ['title' => $this->t('qs_auth.register_form.community *')],
      '#type'       => 'radios',
      '#required'   => FALSE,
      '#options'    => $options,
    ];

    $form['register']['step-2'] = [
      '#type'  => 'fieldset',
    ];

    $form['register']['step-2']['firstname'] = [
      '#title'       => $this->t('qs_auth.register_form.firstname *'),
      '#placeholder' => $this->t('qs_auth.register_form.firstname.placeholder'),
      '#type'        => 'textfield',
      '#required'    => FALSE,
    ];

    $form['register']['step-2']['lastname'] = [
      '#title'       => $this->t('qs_auth.register_form.lastname *'),
      '#placeholder' => $this->t('qs_auth.register_form.lastname.placeholder'),
      '#type'        => 'textfield',
      '#required'    => FALSE,
    ];

    $form['register']['step-3'] = [
      '#type'  => 'fieldset',
    ];

    $form['register']['step-3']['contacts'] = [
      '#type'     => 'checkboxes',
      '#required' => FALSE,
      '#options'  => [
        'mail'  => $this->t('qs_auth.register_form.contacts.mail'),
        'phone' => $this->t('qs_auth.register_form.contacts.phone'),
      ],
    ];

    $form['register']['step-3']['mail'] = [
      '#type'     => 'textfield',
      '#required' => FALSE,
    ];

    $form['register']['step-3']['phone'] = [
      '#type'     => 'textfield',
      '#required' => FALSE,
    ];

    $form['register']['step-4'] = [
      '#type'  => 'fieldset',
    ];

    $form['register']['step-4']['username'] = [
      '#title'       => $this->t('qs_auth.register_form.username *'),
      '#placeholder' => $this->t('qs_auth.register_form.username.placeholder'),
      '#type'        => 'textfield',
      '#required'    => FALSE,
    ];

    $form['register']['step-4']['password'] = [
      '#title'    => $this->t('qs_auth.register_form.password *'),
      '#type'     => 'password',
      '#required' => FALSE,
    ];

    $form['register']['step-4']['password_verification'] = [
      '#title'    => $this->t('qs_auth.register_form.password_verification *'),
      '#type'     => 'password',
      '#required' => FALSE,
    ];

    $form['actions']['submit'] = [
      '#type'  => 'submit',
      '#value' => $this->t('qs_auth.register_form.submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Assert the community is valid.
    if (!$form_state->getValue('community') || empty($form_state->getValue('community'))) {
      $form_state->setErrorByName('[register][step-1][community]', $this->t('qs_auth.form.error.empty'));
    }

    // Assert the firstname is valid.
    if (!$form_state->getValue('firstname') || empty($form_state->getValue('firstname'))) {
      $form_state->setErrorByName('[register][step-2][firstname]', $this->t('qs_auth.form.error.empty'));
    }

    // Assert the lastname is valid.
    if (!$form_state->getValue('lastname') || empty($form_state->getValue('lastname'))) {
      $form_state->setErrorByName('[register][step-2][lastname]', $this->t('qs_auth.form.error.empty'));
    }

    // Assert the password is valid.
    if (!$form_state->getValue('password') || empty($form_state->getValue('password'))) {
      $form_state->setErrorByName('[register][step-3][password]', $this->t('qs_auth.form.error.empty'));
    }

    // Assert the mail is valid.
    if (!$form_state->getValue('username') || !filter_var($form_state->getValue('username'), FILTER_VALIDATE_EMAIL)) {
      $form_state->setErrorByName('[register][step-3][username]', $this->t('qs_auth.form.error.mail_invalid'));
    }

    // Check account is uniq.
    $account = user_load_by_mail($form_state->getValue('username'));
    if ($account) {
      $form_state->setErrorByName('[register][step-3][username]', $this->t('qs_auth.form.error.username_used.'));
    }

    // Check username is Drupal compliant.
    if ($violation = user_validate_name($form_state->getValue('username'))) {
      $form_state->setErrorByName('[register][step-3][username]', $violation);
    }

    // Assert the password is valid.
    if (!$form_state->getValue('password') || empty($form_state->getValue('password'))) {
      $form_state->setErrorByName('[register][step-3][password]', $this->t('qs_auth.form.error.empty'));
    }

    // Assert the password is valid.
    if (!$form_state->getValue('password_verification') || empty($form_state->getValue('password_verification'))) {
      $form_state->setErrorByName('[register][step-3][password]', $this->t('qs_auth.form.error.empty'));
    }

    // Assert the password_verification is equal to password.
    if ($form_state->getValue('password') !== $form_state->getValue('password_verification')) {
      $form_state->setErrorByName('[register][step-3][password_verification]', $this->t('qs_auth.form.error.password_invalid'));
    }

    // Add inline errors.
    $this->applyErrorsInline($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $user = $this->account->create($form_state->getValues());
    $this->account->sendRegisterEmail($user);

    drupal_set_message($this->t("Thank you <strong>@nickname</strong> for your subscription!", [
      '@nickname' => $user->field_nickname->value,
    ]));

    $form_state->setRedirect('<front>');
  }

}
