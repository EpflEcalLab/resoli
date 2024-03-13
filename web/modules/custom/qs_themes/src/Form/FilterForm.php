<?php

namespace Drupal\qs_themes\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Http\RequestStack;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A theme filter form that add the chosen theme as GET param to the route.
 */
class FilterForm extends FormBase {

  /**
   * The current active user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Request stack that controls the lifecycle of requests.
   *
   * @var \Drupal\Core\Http\RequestStack
   */
  protected $requestStack;

  /**
   * The term Storage.
   *
   * @var \Drupal\taxonomy\TermStorageInterface
   */
  private $termStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccountProxyInterface $currentUser, RequestStack $request_stack, EntityTypeManagerInterface $entity_type_manager, LanguageManagerInterface $language_manager) {
    $this->currentUser = $currentUser;
    $this->requestStack = $request_stack;
    $this->termStorage = $entity_type_manager->getStorage('taxonomy_term');
    $this->languageManager = $language_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get the current language.
    $currentLang = $this->languageManager->getCurrentLanguage();

    $form['#method'] = 'GET';

    // The request should be took at the last moment, avoid it on constructor.
    $master_request = $this->requestStack->getMainRequest();

    // Disable caching & HTML5 validation.
    $form['#cache']['max-age'] = 0;
    $form['#attributes']['novalidate'] = 'novalidate';

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

    // Get all selected themes for options.
    $filtered_themes = $master_request->query->get('themes') ?: [];
    $form['themes'] = [
      '#type' => 'checkboxes',
      '#required' => FALSE,
      '#options' => $options,
      '#default_value' => $filtered_themes,
      '#theme_wrappers' => [
        'checkboxes__buttons',
      ],
      '#attributes' => [
        'variant' => 'button',
        'data-toggle' => 'buttons',
        'no_form_group' => TRUE,
      ],
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('qs_themes.filter_form.submit'),
      '#attributes' => [
        'modal' => TRUE,
        'outline' => TRUE,
        'icon' => 'check',
        'icon_left' => TRUE,
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
      $container->get('current_user'),
      $container->get('request_stack'),
      $container->get('entity_type.manager'),
      $container->get('language_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_themes_filter_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {}

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {}

}
