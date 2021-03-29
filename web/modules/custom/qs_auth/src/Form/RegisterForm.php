<?php

namespace Drupal\qs_auth\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\qs_auth\Service\Account;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * RegisterForm class.
 */
class RegisterForm extends FormBase {
  /**
   * The QS account service.
   *
   * @var \Drupal\qs_auth\Service\Account
   */
  protected $account;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The user Storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * The term Storage.
   *
   * @var \Drupal\taxonomy\TermStorageInterface
   */
  private $termStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(Account $account, EntityTypeManagerInterface $entity_type_manager, ModuleHandlerInterface $module_handler) {
    $this->account = $account;
    $this->termStorage = $entity_type_manager->getStorage('taxonomy_term');
    $this->userStorage = $entity_type_manager->getStorage('user');
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $extra = NULL) {
    // Honeypot.
    if ($this->moduleHandler->moduleExists('honeypot')) {
      honeypot_add_form_protection($form, $form_state, ['honeypot', 'time_restriction']);
    }

    // Disable caching & HTML5 validation.
    $form['#cache']['max-age'] = 0;
    $form['#attributes'] = [
      'novalidate' => 'novalidate',
    ];
    $form['#attached']['library'][] = 'qs_site/unload';

    // Apply custom styles to wrapper.
    $form['#theme_wrappers'] = [
      'form__fullpage__multistep',
    ];

    // Display links to other meaningful pages.
    $form['#top_links'] = [
      '<front>' => [
        'label' => $this->t('qs_auth.link.home'),
        'options' => [
          'icon' => 'chevron-left',
          'hide_xs' => TRUE,
        ],
      ],
      'qs_auth.login' => [
        'label' => $this->t('qs_auth.link.login'),
      ],
    ];

    $form['register']['step-1'] = [
      '#type' => 'fieldset',
      '#description' => $this->t('qs_auth.register_form.step1.description'),
      '#attributes' => [
        'data-step' => $this->t('qs_auth.register_form.step1'),
        'sub_description' => $this->t('qs_auth.register_form.step1.sub_description'),
        'class' => [
          'tab-pane',
          'container-fluid',
        ],
        'role' => 'tabpanel',
      ],
      '#theme_wrappers' => [
        'container__center',
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
        'title' => $this->t('qs_auth.form.register.community'),
        'variant' => 'button',
      ],
      '#theme_wrappers' => [
        'radios__buttons',
        'container__center',
      ],
      '#type' => 'radios',
      '#required' => FALSE,
      '#options' => $options,
    ];

    $form['register']['step-2'] = [
      '#type' => 'fieldset',
      '#description' => $this->t('qs_auth.register_form.step2.description'),
      '#attributes' => [
        'data-step' => $this->t('qs_auth.register_form.step2'),
        'sub_description' => $this->t('qs_auth.register_form.step2.sub_description'),
        'class' => [
          'container-fluid',
        ],
      ],
      '#theme_wrappers' => [
        'container__center',
        'fieldset__step',
      ],
    ];

    $form['register']['step-2']['firstname'] = [
      '#attributes' => ['required' => TRUE],
      '#title' => $this->t('qs_auth.form.register.firstname'),
      '#placeholder' => $this->t('qs_auth.form.register.firstname.placeholder'),
      '#type' => 'textfield',
      '#required' => FALSE,
    ];

    $form['register']['step-2']['lastname'] = [
      '#attributes' => ['required' => TRUE],
      '#title' => $this->t('qs_auth.form.register.lastname'),
      '#placeholder' => $this->t('qs_auth.form.register.lastname.placeholder'),
      '#type' => 'textfield',
      '#required' => FALSE,
    ];

    $form['register']['step-3'] = [
      '#type' => 'fieldset',
      '#description' => $this->t('qs_auth.register_form.step3.description'),
      '#attributes' => [
        'data-step' => $this->t('qs_auth.register_form.step3'),
        'sub_description' => $this->t('qs_auth.register_form.step3.sub_description'),
        'class' => [
          'container-fluid',
        ],
      ],
      '#theme_wrappers' => [
        'container__center',
        'fieldset__step',
      ],
    ];

    $form['register']['step-3']['mail'] = [
      '#type' => 'email',
      '#attributes' => ['required' => TRUE, 'force_feedback' => TRUE],
      '#title' => $this->t('qs_auth.register_form.mail'),
      '#placeholder' => $this->t('qs_auth.register_form.mail.placeholder'),
      '#required' => FALSE,
    ];

    $form['register']['step-3']['phone'] = [
      '#type' => 'tel',
      '#title' => $this->t('qs_auth.register_form.phone'),
      '#placeholder' => $this->t('qs_auth.register_form.phone.placeholder'),
      '#required' => FALSE,
    ];

    $form['register']['step-4'] = [
      '#type' => 'fieldset',
      '#description' => $this->t('qs_auth.register_form.step4.description'),
      '#attributes' => [
        'data-step' => $this->t('qs_auth.register_form.step4'),
        'sub_description' => $this->t('qs_auth.register_form.step4.sub_description'),
        'class' => [
          'container-fluid',
        ],
      ],
      '#theme_wrappers' => [
        'container__center',
        'fieldset__step',
      ],
    ];

    $form['register']['step-4']['password'] = [
      '#attributes' => ['required' => TRUE],
      '#title' => $this->t('qs_auth.form.register.password'),
      '#placeholder' => $this->t('qs_auth.register_form.password.placeholder'),
      '#type' => 'password',
      '#required' => FALSE,
    ];

    $form['register']['step-4']['password_verification'] = [
      '#attributes' => ['required' => TRUE],
      '#title' => $this->t('qs_auth.form.register.password_verification'),
      '#placeholder' => $this->t('qs_auth.register_form.password_verification.placeholder'),
      '#type' => 'password',
      '#required' => FALSE,
    ];

    if ($this->moduleHandler->moduleExists('captcha')) {
      $form['register']['step-4']['captcha'] = [
        '#type' => 'captcha',
        '#captcha_type' => 'recaptcha/reCAPTCHA',
      ];
    }

    $form['actions']['submit'] = [
      '#type' => 'submit',
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
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('qs_auth.account'),
      $container->get('entity_type.manager'),
      $container->get('module_handler')
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
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $user = $this->account->create($form_state->getValues());
    $community = $this->termStorage->load($form_state->getValue('community'));

    $this->account->sendRegisterEmail($user);

    // Send to community managers a mail with the new request.
    $this->account->sendCommunityManagersApplyReq($user, $community);

    drupal_set_message($this->t('qs_auth.form.register.success @firstname @lastname @mail', [
      '@firstname' => $user->field_firstname->value,
      '@lastname' => $user->field_lastname->value,
      '@mail' => $user->getEmail(),
    ]));

    $form_state->setRedirect('qs_auth.approval', ['community' => $community->id()], []);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Assert the community is valid.
    if (!$form_state->getValue('community') || empty($form_state->getValue('community'))) {
      $form_state->setErrorByName('form', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['register']['step-1']['community']['#attributes']['title']]));
    }

    // Assert the firstname is valid.
    if (!$form_state->getValue('firstname') || empty($form_state->getValue('firstname'))) {
      $form_state->setErrorByName('firstname', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['register']['step-2']['firstname']['#title']]));
    }

    // Assert the lastname is valid.
    if (!$form_state->getValue('lastname') || empty($form_state->getValue('lastname'))) {
      $form_state->setErrorByName('lastname', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['register']['step-2']['lastname']['#title']]));
    }

    // Assert the password is valid.
    if (!$form_state->getValue('password') || empty($form_state->getValue('password'))) {
      $form_state->setErrorByName('password', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['register']['step-4']['password']['#title']]));
    }

    // Assert the mail is valid.
    if (!$form_state->getValue('mail') || !filter_var($form_state->getValue('mail'), \FILTER_VALIDATE_EMAIL)) {
      $form_state->setErrorByName('mail', $this->t('qs.form.error.mail.malformed'));
    }

    // Check email is uniq. as mail.
    $account = $this->userStorage->loadByProperties(['mail' => $form_state->getValue('mail')]);

    if ($account) {
      $form_state->setErrorByName('mail', $this->t('qs.form.error.mail.used'));
    }

    // Check email is uniq. as username.
    $account = $this->userStorage->loadByProperties(['name' => $form_state->getValue('mail')]);

    if ($account) {
      $form_state->setErrorByName('mail', $this->t('qs.form.error.username.used'));
    }

    // Check username is Drupal compliant.
    if ($violation = user_validate_name($form_state->getValue('mail'))) {
      $form_state->setErrorByName('mail', $violation);
    }

    // Assert the password_verification is valid.
    if (!$form_state->getValue('password_verification') || empty($form_state->getValue('password_verification'))) {
      $form_state->setErrorByName('password_verification', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['register']['step-4']['password_verification']['#title']]));
    }

    // Assert the password_verification is equal to password.
    if ($form_state->getValue('password') !== $form_state->getValue('password_verification')) {
      $form_state->setErrorByName('password_verification', $this->t('qs_auth.form.register.error.password_verification.invalid'));
    }
  }

}
