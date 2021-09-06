<?php

namespace Drupal\qs_sharing\Form;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManager;
use Drupal\Core\Session\AccountInterface;
use Drupal\qs_acl\Service\AccessControl;
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
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * The term Storage.
   *
   * @var \Drupal\taxonomy\TermStorageInterface
   */
  private $termStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, LanguageManager $language_manager, TermStorageInterface $termStorage) {
    $this->acl = $acl;
    $this->languageManager = $language_manager;
    $this->termStorage = $termStorage;
  }

  /**
   * Checks access.
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

    if ($this->acl->hasAccessCommunity($community)) {
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

    // Disable caching.
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

    $form['#top_lead'] = $this->t('qs.sharing.add.request @community', ['@community' => $community->getName()]);
    $form['#bottom_text'] = $this->t('qs.sharing.add.request.info');

    // Apply custom styles to wrapper.
    $form['#theme_wrappers'] = [
      'form__modal__multistep__narrow',
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
      $container->get('qs_acl.access_control'),
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
