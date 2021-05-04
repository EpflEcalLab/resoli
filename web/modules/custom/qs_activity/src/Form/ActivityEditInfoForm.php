<?php

namespace Drupal\qs_activity\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Activity form to update basics information.
 */
class ActivityEditInfoForm extends ActivityEditFormBase {

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

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
    $this->termStorage = $this->getTermStorage();
    $this->languageManager = $this->getLanguageManager();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ?NodeInterface $activity = NULL) {
    // Get the current language.
    $currentLang = $this->languageManager->getCurrentLanguage();

    $form = parent::buildForm($form, $form_state, $activity);

    $form['#theme_wrappers'] = [
      'form__modal',
    ];
    $form['#attributes'] = [
      'title' => $activity->title->value,
      'description' => $this->t('qs.activity.edit_info'),
      'theme' => 'primary',
    ];

    $form['#floating_buttons'][] = [
      'label' => $this->t('qs.activity.edit_info'),
      'icon' => 'activities',
      'active' => TRUE,
    ];

    $form['step-1'] = [
      '#type' => 'fieldset',
      '#theme_wrappers' => [
        'container__center',
      ],
    ];

    $form['step-1']['title'] = [
      '#attributes' => ['required' => TRUE],
      '#title' => $this->t('qs_activity.activities.form.edit.info.title'),
      '#placeholder' => $this->t('qs_activity.activities.form.edit.info.title.placeholder'),
      '#type' => 'textfield',
      '#required' => FALSE,
      '#default_value' => $activity->getTitle(),
    ];

    $form['step-1']['body'] = [
      '#type' => 'textarea',
      '#required' => FALSE,
      '#default_value' => $activity->field_description->value,
    ];

    $form['step-1']['quill'] = [
      '#markup' => '<div class="form-group">
        <span class="quill-label">' . $this->t('qs_activity.activities.form.edit.info.body') . '</span>
        <div
            id="editor-edit-activity"
            data-placeholder-translation="' . $this->t('qs_activity.activities.form.edit.info.body.placeholder') . '"
            class="quill-editor quill-editor-primary form-textarea form-control">
          </div>
      </div>',
    ];

    $form['#attached']['library'][] = 'quartiers_solidaires/quill';

    $form['step-2'] = [
      '#type' => 'fieldset',
      '#theme_wrappers' => [
        'container__center',
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
    $form['step-2']['theme'] = [
      '#attributes' => [
        'required' => TRUE,
        'title' => $this->t('qs_activity.activities.form.edit.info.theme'),
        'variant' => 'button_theme',
        'data-toggle' => 'buttons',
        'no_form_group' => TRUE,
      ],
      '#theme_wrappers' => [
        'radios__buttons',
      ],
      '#type' => 'radios',
      '#required' => FALSE,
      '#options' => $options,
      '#default_value' => $activity->field_theme->target_id,
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#attributes' => [
        'icon' => 'check',
        'modal' => TRUE,
        'icon_left' => TRUE,
        'outline' => TRUE,
        'class' => [
          'shadow-to-bottom',
        ],
      ],
      '#value' => $this->t('qs.form.submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_activity_edit_info_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $activity = $this->nodeStorage->load($form_state->get('activity'));

    $fields = [
      'title' => $form_state->getValue('title'),
      'field_theme' => [$form_state->getValue('theme')],
      'field_description' => $form_state->getValue('body'),
    ];

    // Update the activity.
    $activity = $this->activityManager->update($activity, $fields);

    $this->messenger()->addMessage($this->t('qs_activity.activities.form.edit.info.success @activity', [
      '@activity' => $activity->getTitle(),
    ]));

    $form_state->setRedirect('qs_activity.activities.dashboard', ['activity' => $activity->id()], []);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Assert the title is valid.
    if (!$form_state->getValue('title') || empty($form_state->getValue('title'))) {
      $form_state->setErrorByName('title', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['step-1']['title']['#title']]));
    }

    // Assert the theme is valid.
    if (!$form_state->getValue('theme') || empty($form_state->getValue('theme'))) {
      $form_state->setErrorByName('theme', $this->t('qs.form.error.empty @fieldname', ['@fieldname' => $form['step-2']['theme']['#attributes']['title']]));
    }
  }

}
