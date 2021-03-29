<?php

namespace Drupal\qs_activity\Form;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * ActivityAddForm class.
 */
class ActivityAddForm extends FormBasic {

  /**
   * The entity QS Activity Manager.
   *
   * @var \Drupal\qs_activity\Service\ActivityManager
   */
  protected $activityManager;

  /**
   * The current user account proxy.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Manages the discovery of entity fields.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The user Storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;
  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

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
   * {@inheritdoc}
   */
  public function __construct(ContainerInterface $container) {
    // Initialize the container.
    parent::__construct($container);

    // From the container, inject services.
    $this->acl = $this->getAcl();
    $this->termStorage = $this->getTermStorage();
    $this->userStorage = $this->getUserStorage();
    $this->activityManager = $this->getActivityManager();
    $this->privilegeManager = $this->getPrivilegeManager();
    $this->currentUser = $this->getCurrentUser();
    $this->entityFieldManager = $this->getEntityFieldManager();
    $this->languageManager = $this->getLanguageManager();
  }

  /**
   * Checks access for creating file in the given community.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\taxonomy\TermInterface $community
   *   Run access checks for this taxonomy.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account, TermInterface $community) {
    $access = AccessResult::forbidden();

    if ($this->acl->hasWriteAccessCommunity($community)) {
      $access = AccessResult::allowed();
    }

    return $access;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ?TermInterface $community = NULL) {
    // Get the current language.
    $currentLang = $this->languageManager->getCurrentLanguage();

    $form = parent::buildForm($form, $form_state);

    // Disable caching & HTML5 validation.
    $form['#cache']['max-age'] = 0;
    $form['#attributes'] = [
      'novalidate' => 'novalidate',
      'theme' => 'primary',
    ];
    $form['#attached']['library'][] = 'qs_site/unload';

    $form['#floating_buttons'][] = [
      'icon' => 'happy',
      'label' => $this->t('qs_activity.floating.my_activities'),
      'active' => TRUE,
    ];

    // Apply custom styles to wrapper.
    $form['#theme_wrappers'] = [
      'form__modal__multistep',
    ];

    // Save the community for submission.
    $form_state->set('community', $community->id());

    $form['activity']['step-1'] = [
      '#type' => 'fieldset',
      '#description' => $this->t('qs_activity.activities.form.step1.description'),
      '#attributes' => [
        'data-step' => $this->t('qs_activity.activities.form.step1'),
      ],
      '#theme_wrappers' => [
        'container__center',
        'fieldset__step',
      ],
    ];

    $form['activity']['step-1']['title'] = [
      '#attributes' => ['required' => TRUE],
      '#title' => $this->t('qs_activity.activities.form.add.title'),
      '#placeholder' => $this->t('qs_activity.activities.form.add.title.placeholder'),
      '#type' => 'textfield',
      '#required' => FALSE,
    ];

    $form['activity']['step-2'] = [
      '#type' => 'fieldset',
      '#description' => $this->t('qs_activity.activities.form.step2.description'),
      '#attributes' => [
        'data-step' => $this->t('qs_activity.activities.form.step2'),
      ],
      '#theme_wrappers' => [
        'container__center__wide',
        'fieldset__step',
      ],
    ];

    // Get all themes for options.
    $themes = $this->termStorage->loadTree('themes', 0, NULL, TRUE);
    $options = [];

    foreach ($themes as $theme) {
      // Check if has translation.
      if ($theme->hasTranslation($currentLang->getId())) {
        $theme = $theme->getTranslation($currentLang->getId());
      }
      $options[$theme->id()] = $theme->getName() . '|' . $theme->field_icon->value;
    }

    $form['activity']['step-2']['theme'] = [
      '#attributes' => [
        'required' => TRUE,
        'title' => $this->t('qs_activity.activities.form.add.theme'),
        'variant' => 'button_theme',
        'data-toggle' => 'buttons',
        'no_form_group' => TRUE,
        'class' => [
          'btn-grid',
        ],
      ],
      '#theme_wrappers' => [
        'radios__buttons',
      ],
      '#type' => 'radios',
      '#required' => FALSE,
      '#options' => $options,
    ];

    $form['activity']['step-3'] = [
      '#type' => 'fieldset',
      '#description' => $this->t('qs_activity.activities.form.step3.description'),
      '#attributes' => [
        'data-step' => $this->t('qs_activity.activities.form.step3'),
        'class' => [
          'mb-4',
        ],
      ],
      '#theme_wrappers' => [
        'container__center',
        'fieldset__step',
      ],
    ];

    // Load the fields definitions.
    $activity_fields = $this->entityFieldManager->getFieldDefinitions('node', 'activity');
    $field_definition = $activity_fields['field_community_can_subscribe'];
    $form['activity']['step-3']['community_can_subscribe'] = [
      '#title' => $this->t('qs_activity.activities.form.add.community_can_subscribe'),
      '#type' => 'checkbox',
      '#required' => FALSE,
      '#default_value' => $field_definition->getDefaultValueLiteral()[0]['value'],
      '#attributes' => [
        'variant' => 'toggle',
      ],
      '#theme_wrappers' => [
        'input__checkbox__toggle',
      ],
    ];

    $field_definition = $activity_fields['field_community_access_contact'];
    $form['activity']['step-3']['community_access_contact'] = [
      '#title' => $this->t('qs_activity.activities.form.add.community_access_contact'),
      '#type' => 'checkbox',
      '#required' => FALSE,
      '#default_value' => $field_definition->getDefaultValueLiteral()[0]['value'],
      '#attributes' => [
        'variant' => 'toggle',
      ],
      '#theme_wrappers' => [
        'input__checkbox__toggle',
      ],
    ];

    $field_definition = $activity_fields['field_community_access_detail'];
    $form['activity']['step-3']['community_access_detail'] = [
      '#title' => $this->t('qs_activity.activities.form.add.community_access_detail'),
      '#type' => 'checkbox',
      '#required' => FALSE,
      '#default_value' => $field_definition->getDefaultValueLiteral()[0]['value'],
      '#attributes' => [
        'variant' => 'toggle',
      ],
      '#theme_wrappers' => [
        'input__checkbox__toggle',
      ],
    ];

    $field_definition = $activity_fields['field_community_access_story'];
    $form['activity']['step-3']['community_access_story'] = [
      '#title' => $this->t('qs_activity.activities.form.add.community_access_story'),
      '#type' => 'checkbox',
      '#required' => FALSE,
      '#default_value' => $field_definition->getDefaultValueLiteral()[0]['value'],
      '#attributes' => [
        'variant' => 'toggle',
      ],
      '#theme_wrappers' => [
        'input__checkbox__toggle',
      ],
    ];

    $field_definition = $activity_fields['field_member_create_story'];
    $form['activity']['step-3']['member_create_story'] = [
      '#title' => $this->t('qs_activity.activities.form.add.member_create_story'),
      '#type' => 'checkbox',
      '#required' => FALSE,
      '#default_value' => $field_definition->getDefaultValueLiteral()[0]['value'],
      '#attributes' => [
        'variant' => 'toggle',
      ],
      '#theme_wrappers' => [
        'input__checkbox__toggle',
      ],
    ];

    $field_definition = $activity_fields['field_community_access_gallery'];
    $form['activity']['step-3']['community_access_gallery'] = [
      '#title' => $this->t('qs_activity.activities.form.add.community_access_gallery'),
      '#type' => 'checkbox',
      '#required' => FALSE,
      '#default_value' => $field_definition->getDefaultValueLiteral()[0]['value'],
      '#attributes' => [
        'variant' => 'toggle',
      ],
      '#theme_wrappers' => [
        'input__checkbox__toggle',
      ],
    ];

    $field_definition = $activity_fields['field_member_create_gallery'];
    $form['activity']['step-3']['member_create_gallery'] = [
      '#title' => $this->t('qs_activity.activities.form.add.member_create_gallery'),
      '#type' => 'checkbox',
      '#required' => FALSE,
      '#default_value' => $field_definition->getDefaultValueLiteral()[0]['value'],
      '#attributes' => [
        'variant' => 'toggle',
      ],
      '#theme_wrappers' => [
        'input__checkbox__toggle',
      ],
    ];

    $form['activity']['step-4'] = [
      '#type' => 'fieldset',
      '#description' => $this->t('qs_activity.activities.form.step4.description'),
      '#attributes' => [
        'data-step' => $this->t('qs_activity.activities.form.step4'),
        'class' => [
          'text-center',
          'pb-4',
        ],
      ],
      '#theme_wrappers' => [
        'container__center',
        'fieldset__step',
      ],
    ];

    $form['activity']['step-4']['save'] = [
      '#type' => 'submit',
      '#name' => 'save',
      '#value' => $this->t('qs_activity.activities.form.add.save'),
      '#attributes' => [
        'outline' => TRUE,
        'icon' => 'activities',
        'icon_left' => TRUE,
        'class' => [
          'js-form-normal',
          'col-md-8',
          'mx-auto',
          'mb-3',
        ],
      ],
    ];

    $form['activity']['step-4']['save_and_set_default_values'] = [
      '#type' => 'submit',
      '#name' => 'save_and_set_default_values',
      '#value' => $this->t('qs_activity.activities.form.add.save_and_set_default_values'),
      '#attributes' => [
        'outline' => TRUE,
        'icon' => 'pencil',
        'icon_left' => TRUE,
        'class' => [
          'js-form-normal',
          'col-md-8',
          'mx-auto',
          'mb-3',
        ],
      ],
    ];

    $form['activity']['step-4']['save_and_new_event'] = [
      '#type' => 'submit',
      '#name' => 'save_and_new_event',
      '#value' => $this->t('qs_activity.activities.form.add.save_and_new_event'),
      '#attributes' => [
        // 'outline'   => TRUE,.
        'icon' => 'plus',
        'icon_left' => TRUE,
        'class' => [
          'btn-secondary',
          'js-form-normal',
          'col-md-8',
          'mx-auto',
          'mt-5',
        ],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_activity_add_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $community = $this->termStorage->load($form_state->get('community'));
    $user = $this->userStorage->load($this->currentUser->id());

    // Format authorizations for creations.
    $authorizations = [
      'field_community_can_subscribe' => (bool) $form_state->getValue('community_can_subscribe'),
      'field_community_access_contact' => (bool) $form_state->getValue('community_access_contact'),
      'field_community_access_detail' => (bool) $form_state->getValue('community_access_detail'),
      'field_community_access_story' => (bool) $form_state->getValue('community_access_story'),
      'field_member_create_story' => (bool) $form_state->getValue('member_create_story'),
      'field_community_access_gallery' => (bool) $form_state->getValue('community_access_gallery'),
      'field_member_create_gallery' => (bool) $form_state->getValue('member_create_gallery'),
    ];

    // Format themes for creations.
    // Use an array now for future proof (multiple themes).
    $themes = [$form_state->getValue('theme')];

    // Create the new activity.
    $activity = $this->activityManager->create($form_state->getValue('title'), $themes, $authorizations, $community, $user);

    // Add the current user as the first organizer of this activity.
    $this->privilegeManager->create('activity_organizers', $activity, $this->currentUser);

    drupal_set_message($this->t('qs_activity.activities.form.add.success @activity', [
      '@activity' => $activity->getTitle(),
    ]));

    // Handle redirection.
    $trigger = $form_state->getTriggeringElement();

    switch ($trigger['#name']) {
      case 'save_and_new_event':
        $form_state->setRedirect('qs_activity.events.form.add', ['activity' => $activity->id()], []);

        break;

      case 'save_and_set_default_values':
        $form_state->setRedirect('qs_activity.activities.form.edit.defaults', ['activity' => $activity->id()], []);

        break;

      case 'save':
      default:
        $form_state->setRedirect('entity.node.canonical', ['node' => $activity->id()], []);

        break;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Assert the title is valid.
    if (!$form_state->getValue('title') || empty($form_state->getValue('title'))) {
      $form_state->setErrorByName('title', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['activity']['step-1']['title']['#title']]));
    }

    // Assert the theme is valid.
    if (!$form_state->getValue('theme') || empty($form_state->getValue('theme'))) {
      $form_state->setErrorByName('form', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['activity']['step-2']['theme']['#attributes']['title']]));
    }
  }

}
