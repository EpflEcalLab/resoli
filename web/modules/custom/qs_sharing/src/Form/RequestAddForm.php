<?php

namespace Drupal\qs_sharing\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManager;
use Drupal\taxonomy\TermInterface;
use Drupal\taxonomy\TermStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form to create a new sharing request in a community.
 */
class RequestAddForm extends FormBase {
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
  public function __construct(LanguageManager $language_manager, TermStorageInterface $termStorage) {
    $this->languageManager = $language_manager;
    $this->termStorage = $termStorage;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ?TermInterface $community = NULL) {
    // Get the current language.
    $currentLang = $this->languageManager->getCurrentLanguage();

    // Disable caching & HTML5 validation.
    $form['#cache']['max-age'] = 0;

    $form['#attributes'] = [
      'novalidate' => 'novalidate',
      'theme' => 'pink',
      'class' => ['bg-pink'],
    ];
    $form['#attached']['library'][] = 'qs_site/unload';

    $form['#floating_buttons'][] = [
      'icon' => 'sharing',
      'label' => $this->t('qs_sharing.floating.my_requests'),
      'active' => TRUE,
      'theme' => 'primary',
    ];

    // Apply custom styles to wrapper.
    $form['#theme_wrappers'] = [
      'form__multistep',
    ];

    // Save the community for submission.
    $form_state->set('community', $community->id());

    // @todo Create form with right fields
    $form['request']['step-1'] = [
      '#type' => 'fieldset',
      '#description' => $this->t('qs_sharing.requests.form.step1.description'),
      '#attributes' => [
        'data-step' => $this->t('qs_sharing.requests.form.step1'),

      ],
      '#theme_wrappers' => [
        'container__center__wide',
        'fieldset__step',
      ],
    ];

    $form['request']['step-1']['body'] = [
      '#title' => $this->t('qs_sharing.requests.form.add.body'),
      '#placeholder' => $this->t('qs_sharing.requests.form.add.body.placeholder'),
      '#type' => 'textarea',
      '#required' => FALSE,
    ];


    $form['request']['step-2'] = [
      '#type' => 'fieldset',
      '#description' => $this->t('qs_sharing.requests.form.step2.description'),
      '#attributes' => [
        'data-step' => $this->t('qs_sharing.requests.form.step2'),
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

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('language_manager'),
      $container->get('entity_type.manager')->getStorage('taxonomy_term')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_sharing_request_add_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // @todo Handle logic
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // @todo Handle logic
  }

}
