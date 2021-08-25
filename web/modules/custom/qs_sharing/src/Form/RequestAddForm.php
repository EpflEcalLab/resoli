<?php

namespace Drupal\qs_sharing\Form;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form to create a new sharing request in a community.
 */
class RequestAddForm extends FormBasic {
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
  public function __construct(ContainerInterface $container) {
    // Initialize the container.
    parent::__construct($container);

    // From the container, inject services.
    $this->acl = $this->getAcl();
    $this->termStorage = $this->getTermStorage();
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

    // Save the community for submission.
    $form_state->set('community', $community->id());

    // @TODO Create form with right fields
    $form['request']['step-1'] = [
      '#type' => 'fieldset',
      '#description' => $this->t('qs_sharing.requests.form.step1.description'),
      '#attributes' => [
        'data-step' => $this->t('qs_sharing.requests.form.step1'),
      ],
      '#theme_wrappers' => [
        'container__center',
        'fieldset__step',
      ],
    ];

    $form['request']['step-1']['title'] = [
      '#attributes' => ['required' => TRUE],
      '#title' => $this->t('qs_sharing.requests.form.add.title'),
      '#placeholder' => $this->t('qs_sharing.requests.form.add.title.placeholder'),
      '#type' => 'textfield',
      '#required' => FALSE,
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
  public function getFormId() {
    return 'qs_sharing_request_add_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // @TODO Handle logic
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // @TODO Handle logic
  }

}
