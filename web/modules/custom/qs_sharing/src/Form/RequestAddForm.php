<?php

namespace Drupal\qs_sharing\Form;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManager;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_sharing\Manager\RequestManager;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form to create a new sharing request in a community.
 */
class RequestAddForm extends FormBase {

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  protected $acl;
  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The node Storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * The Request Manager.
   *
   * @var \Drupal\qs_sharing\Manager\RequestManager
   */
  protected $requestManager;

  /**
   * The term Storage.
   *
   * @var \Drupal\taxonomy\TermStorageInterface
   */
  protected $termStorage;

  /**
   * The user Storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * Construct a new form allowing submission of Offers from Volunteers.
   *
   * @param \Drupal\qs_acl\Service\AccessControl $acl
   *   The access controls.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\qs_sharing\Manager\RequestManager $request_manager
   *   The request manager.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(AccessControl $acl, EntityTypeManagerInterface $entity_type_manager, LanguageManager $language_manager, RequestManager $request_manager) {
    $this->acl = $acl;
    $this->languageManager = $language_manager;
    $this->termStorage = $entity_type_manager->getStorage('taxonomy_term');
    $this->userStorage = $entity_type_manager->getStorage('user');
    $this->nodeStorage = $entity_type_manager->getStorage('node');
    $this->requestManager = $request_manager;
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
    $account = $this->userStorage->load($this->currentUser()->id());

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
      'label' => $this->t('qs_sharing.floating.dashboard'),
      'theme' => 'primary',
      'url' => Url::fromRoute('qs_sharing.sharing.dashboard', [
        'community' => $community->id(),
        'user' => $this->currentUser()->id(),
      ]),
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

    // Get all sharing themes for options.
    $themes = $this->termStorage->loadTree('sharing_themes', 0, NULL, TRUE);
    $options = [];

    foreach ($themes as $theme) {
      // Check if has translation.
      if ($theme->hasTranslation($currentLang->getId())) {
        $theme = $theme->getTranslation($currentLang->getId());
      }
      $options[$theme->id()] = $theme->getName();
    }

    $form['request']['step-1']['theme'] = [
      '#title' => $this->t('qs_sharing.requests.form.add.theme'),
      '#type' => 'select',
      '#required' => FALSE,
      '#options' => $options,
      '#attributes' => [
        'placeholder' => $this->t('qs_sharing.requests.form.add.theme.placeholder'),
      ],
    ];

    $form['request']['#attached']['library'][] = 'quartiers_solidaires/quill';
    $form['request']['step-1']['body'] = [
      '#title' => $this->t('qs_sharing.requests.form.add.body'),
      '#type' => 'textarea',
      '#required' => FALSE,
    ];
    $form['request']['step-1']['quill_body'] = [
      '#markup' => '<div class="form-group">
        <span class="quill-label">' . $this->t('qs_sharing.requests.form.add.body') . '</span>
        <div
          id="editor-add-offer-body"
          data-placeholder-translation="' . $this->t('qs_sharing.requests.form.add.body.placeholder') . '"
          class="quill-editor quill-editor-primary form-textarea form-control">
        </div>
      </div>',
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

    $form['request']['step-2']['contact_firstname'] = [
      '#attributes' => [
        'icon' => 'user',
      ],
      '#title' => $this->t('qs_sharing.requests.form.add.contact_firstname'),
      '#placeholder' => $this->t('qs_sharing.requests.form.add.contact_firstname.placeholder'),
      '#default_value' => $account->field_firstname->value,
      '#type' => 'textfield',
      '#required' => FALSE,
      '#theme_wrappers' => [
        'form_element',
      ],
    ];

    $form['request']['step-2']['contact_lastname'] = [
      '#attributes' => [
        'icon' => '_',
      ],
      '#placeholder' => $this->t('qs_sharing.requests.form.add.contact_lastname.placeholder'),
      '#type' => 'textfield',
      '#default_value' => $account->field_lastname->value,
      '#required' => FALSE,
      '#theme_wrappers' => [
        'form_element',
      ],
    ];

    $form['request']['step-2']['contact_mail'] = [
      '#attributes' => [
        'icon' => 'mail',
      ],
      '#title' => $this->t('qs_sharing.requests.form.add.contact_mail'),
      '#placeholder' => $this->t('qs_sharing.requests.form.add.contact_mail.placeholder'),
      '#default_value' => $account->mail->value,
      '#type' => 'textfield',
      '#required' => FALSE,
      '#theme_wrappers' => [
        'form_element',
      ],
    ];

    $form['request']['step-2']['contact_phone'] = [
      '#attributes' => [
        'icon' => 'phone',
      ],
      '#placeholder' => $this->t('qs_sharing.requests.form.add.contact_phone.placeholder'),
      '#default_value' => $account->field_phone->value,
      '#type' => 'textfield',
      '#required' => FALSE,
      '#theme_wrappers' => [
        'form_element',
      ],
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#attributes' => [
        'icon' => 'check',
        'modal' => TRUE,
        'outline' => TRUE,
        'class' => [
          'shadow-to-bottom',
        ],
      ],
      '#value' => $this->t('qs_sharing.requests.form.add.save'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('qs_acl.access_control'),
      $container->get('entity_type.manager'),
      $container->get('language_manager'),
      $container->get('qs_sharing.manager.request')
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
