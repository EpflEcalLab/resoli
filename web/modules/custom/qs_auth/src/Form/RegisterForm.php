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
   * The QS account service.
   *
   * @var \Drupal\qs_auth\Service\Account
   */
  protected $account;

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
   * {@inheritdoc}
   */
  public function __construct(Account $account, EntityTypeManagerInterface $entity_type_manager) {
    $this->account     = $account;
    $this->termStorage = $entity_type_manager->getStorage('taxonomy_term');
    $this->userStorage = $entity_type_manager->getStorage('user');
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
    // Honeypot.
    honeypot_add_form_protection($form, $form_state, ['honeypot', 'time_restriction']);

    // Disable caching & HTML5 validation.
    $form['#cache']['max-age'] = 0;
    $form['#attributes'] = [
      'novalidate' => 'novalidate',
    ];

    // Apply custom styles to wrapper.
    $form['#theme_wrappers'] = [
      'form__fullpage__multistep',
    ];

    $form['register']['step-1'] = [
      '#type'  => 'fieldset',
      '#description' => $this->t('qs_auth.register_form.step1.description'),
      '#attributes' => [
        'data-step' => $this->t('qs_auth.register_form.step1'),
        'sub_description' => $this->t('qs_auth.register_form.step1.sub_description'),
      ],
      '#theme_wrappers' => [
        'fieldset__step',
      ],
    ];

    $communities = $this->termStorage->loadTree('communities', 0, NULL, TRUE);
    $options = [];
    foreach ($communities as $community) {
      $options[$community->tid->value] = $community->name->value;
    }
    $form['register']['step-1']['community'] = [
      '#attributes' => [
        'required' => TRUE,
        'title'    => $this->t('qs_auth.form.register.community'),
        'variant' => 'button',
      ],
      '#theme_wrappers' => [
        'radios__buttons',
        'container__center',
      ],
      '#type'       => 'radios',
      '#required'   => FALSE,
      '#options'    => $options,
    ];

    $form['register']['step-2'] = [
      '#type'  => 'fieldset',
      '#description' => $this->t('qs_auth.register_form.step2.description'),
      '#attributes' => [
        'data-step' => $this->t('qs_auth.register_form.step2'),
        'sub_description' => $this->t('qs_auth.register_form.step2.sub_description'),
      ],
      '#theme_wrappers' => [
        'container__center',
        'fieldset__step',
      ],
    ];

    $form['register']['step-2']['firstname'] = [
      '#attributes'  => ['required' => TRUE],
      '#title'       => $this->t('qs_auth.form.register.firstname'),
      '#placeholder' => $this->t('qs_auth.form.register.firstname.placeholder'),
      '#type'        => 'textfield',
      '#required'    => FALSE,
    ];

    $form['register']['step-2']['lastname'] = [
      '#attributes'  => ['required' => TRUE],
      '#title'       => $this->t('qs_auth.form.register.lastname'),
      '#placeholder' => $this->t('qs_auth.form.register.lastname.placeholder'),
      '#type'        => 'textfield',
      '#required'    => FALSE,
    ];

    $form['register']['step-3'] = [
      '#type'  => 'fieldset',
      '#description' => $this->t('qs_auth.register_form.step3.description'),
      '#attributes' => [
        'data-step' => $this->t('qs_auth.register_form.step3'),
        'sub_description' => $this->t('qs_auth.register_form.step3.sub_description'),
      ],
      '#theme_wrappers' => [
        'container__center',
        'fieldset__step',
      ],
    ];

    $form['register']['step-3']['mail'] = [
      '#type'     => 'email',
      '#attributes'  => ['required' => TRUE],
      '#title'       => $this->t('qs_auth.register_form.mail'),
      '#placeholder' => $this->t('qs_auth.register_form.mail.placeholder'),
      '#required' => FALSE,
    ];

    $form['register']['step-3']['phone'] = [
      '#type'     => 'tel',
      '#title'       => $this->t('qs_auth.register_form.phone'),
      '#placeholder' => $this->t('qs_auth.register_form.phone.placeholder'),
      '#required' => FALSE,
    ];

    $form['register']['step-4'] = [
      '#type'  => 'fieldset',
      '#description' => $this->t('qs_auth.register_form.step4.description'),
      '#attributes' => [
        'data-step' => $this->t('qs_auth.register_form.step4'),
        'sub_description' => $this->t('qs_auth.register_form.step4.sub_description'),
      ],
      '#theme_wrappers' => [
        'container__center',
        'fieldset__step',
      ],
    ];

    $form['register']['step-4']['password'] = [
      '#attributes'  => ['required' => TRUE],
      '#title'    => $this->t('qs_auth.form.register.password'),
      '#placeholder' => $this->t('qs_auth.register_form.password.placeholder'),
      '#type'     => 'password',
      '#required' => FALSE,
    ];

    $form['register']['step-4']['password_verification'] = [
      '#attributes'  => ['required' => TRUE],
      '#title'    => $this->t('qs_auth.form.register.password_verification'),
      '#placeholder'    => $this->t('qs_auth.register_form.password_verification.placeholder'),
      '#type'     => 'password',
      '#required' => FALSE,
    ];

    $form['actions']['submit'] = [
      '#type'  => 'submit',
      '#attributes' => [
        'class' => [
          'align-self-center',
        ],
        'icon' => 'check',
        'outline' => TRUE,
      ],
      '#value' => $this->t('qs.form.submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Assert the community is valid.
    if (!$form_state->getValue('community') || empty($form_state->getValue('community'))) {
      $form_state->setErrorByName('[register][step-1][community]', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['register']['step-1']['community']['#attributes']['title']]));
    }

    // Assert the firstname is valid.
    if (!$form_state->getValue('firstname') || empty($form_state->getValue('firstname'))) {
      $form_state->setErrorByName('[register][step-2][firstname]', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['register']['step-2']['firstname']['#title']]));
    }

    // Assert the lastname is valid.
    if (!$form_state->getValue('lastname') || empty($form_state->getValue('lastname'))) {
      $form_state->setErrorByName('[register][step-2][lastname]', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['register']['step-2']['lastname']['#title']]));
    }

    // Assert the password is valid.
    if (!$form_state->getValue('password') || empty($form_state->getValue('password'))) {
      $form_state->setErrorByName('[register][step-4][password]', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['register']['step-4']['password']['#title']]));
    }

    // Assert the mail is valid.
    if (!$form_state->getValue('mail') || !filter_var($form_state->getValue('mail'), FILTER_VALIDATE_EMAIL)) {
      $form_state->setErrorByName('[register][step-3][mail]', $this->t('qs.form.error.mail.malformed'));
    }

    // Check email is uniq. as mail.
    $account = $this->userStorage->loadByProperties(['mail' => $form_state->getValue('mail')]);
    if ($account) {
      $form_state->setErrorByName('[register][step-3][mail]', $this->t('qs.form.error.mail.used'));
    }

    // Check email is uniq. as username.
    $account = $this->userStorage->loadByProperties(['name' => $form_state->getValue('mail')]);
    if ($account) {
      $form_state->setErrorByName('[register][step-3][mail]', $this->t('qs.form.error.username.used'));
    }

    // Check username is Drupal compliant.
    if ($violation = user_validate_name($form_state->getValue('mail'))) {
      $form_state->setErrorByName('[register][step-3][mail]', $violation);
    }

    // Assert the password is valid.
    if (!$form_state->getValue('password') || empty($form_state->getValue('password'))) {
      $form_state->setErrorByName('[register][step-4][password]', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['register']['step-4']['password']['#title']]));
    }

    // Assert the password_verification is valid.
    if (!$form_state->getValue('password_verification') || empty($form_state->getValue('password_verification'))) {
      $form_state->setErrorByName('[register][step-4][password_verification]', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['register']['step-4']['password_verification']['#title']]));
    }

    // Assert the password_verification is equal to password.
    if ($form_state->getValue('password') !== $form_state->getValue('password_verification')) {
      $form_state->setErrorByName('[register][step-4][password_verification]', $this->t('qs_auth.form.register.error.password_verification.invalid'));
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

    drupal_set_message($this->t('qs_auth.form.register.success @firstname, @lastname, @mail', [
      '@firstname' => $user->field_firstname->value,
      '@lastname'  => $user->field_lastname->value,
      '@mail'      => $user->field_email->value,
    ]));

    $community = $this->termStorage->load($form_state->getValue('community'));
    $form_state->setRedirect('qs_auth.approval', ['community' => $community->id()], []);
  }

}
