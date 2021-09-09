<?php

namespace Drupal\qs_sharing\Form;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_sharing\Manager\OfferManager;
use Drupal\qs_sharing\Manager\OfferTypeManager;
use Drupal\qs_sharing\Repository\OfferTypeRepository;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form to create new offer on a community.
 */
class OfferAddForm extends FormBase {

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
   * The offer manager.
   *
   * @var \Drupal\qs_sharing\Manager\OfferManager
   */
  private $offerManager;

  /**
   * The offer's type manager.
   *
   * @var \Drupal\qs_sharing\Manager\OfferTypeManager
   */
  private $offerTypeManager;

  /**
   * The offer's type repository.
   *
   * @var \Drupal\qs_sharing\Repository\OfferTypeRepository
   */
  private $offerTypeRepository;

  /**
   * Construct a new form allowing submission of Offers from Volunteers.
   *
   * @param \Drupal\qs_acl\Service\AccessControl $acl
   *   The access controls.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\qs_sharing\Repository\OfferTypeRepository $offer_type_repository
   *   The offer's type repository.
   * @param \Drupal\qs_sharing\Manager\OfferTypeManager $offer_type_manager
   *   The offer's type manager.
   * @param \Drupal\qs_sharing\Manager\OfferManager $offer_manager
   *   The offer manager.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(AccessControl $acl, EntityTypeManagerInterface $entity_type_manager, LanguageManagerInterface $language_manager, OfferTypeRepository $offer_type_repository, OfferTypeManager $offer_type_manager, OfferManager $offer_manager) {
    // From the container, inject services.
    $this->acl = $acl;
    $this->termStorage = $entity_type_manager->getStorage('taxonomy_term');
    $this->userStorage = $entity_type_manager->getStorage('user');
    $this->nodeStorage = $entity_type_manager->getStorage('node');
    $this->languageManager = $language_manager;
    $this->offerTypeRepository = $offer_type_repository;
    $this->offerTypeManager = $offer_type_manager;
    $this->offerManager = $offer_manager;
  }

  /**
   * Checks access for creating offer in the given community.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\taxonomy\TermInterface $community
   *   Run access checks for this taxonomy.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access results.
   */
  public function access(AccountInterface $account, TermInterface $community) {
    $access = AccessResult::forbidden();

    if ($this->acl->isCommunityVolunteer($community)) {
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

    // Disable caching & HTML5 validation.
    $form['#cache']['max-age'] = 0;
    $form['#attributes'] = [
      'novalidate' => 'novalidate',
      'theme' => 'info',
    ];
    $form['#attached']['library'][] = 'qs_site/unload';

    $form['#floating_buttons'][] = [
      'icon' => 'plus',
      'label' => $this->t('qs_sharing.add_offer'),
      'active' => TRUE,
    ];

    // Apply custom styles to wrapper.
    $form['#theme_wrappers'] = [
      'form__modal__multistep',
    ];

    // Save the community for later usage on submission.
    $form_state->set('community', $community->id());

    $form['offer']['step-1'] = [
      '#type' => 'fieldset',
      '#description' => $this->t('qs_sharing.offers.form.step1.description'),
      '#attributes' => [
        'data-step' => $this->t('qs_sharing.offers.form.step1'),
      ],
      '#theme_wrappers' => [
        'container__center',
        'fieldset__step',
      ],
    ];

    $community_offer_types = $this->offerTypeRepository->getAllByCommunity($community);
    $select_options = [];
    $this->fallback = [];

    if (!empty($community_offer_types)) {
      foreach ($community_offer_types as $community_offer_type) {
        $select_options[] = [
          'nid' => $community_offer_type->id(),
          'name' => $community_offer_type->getTitle(),
          'theme' => $community_offer_type->get('field_theme')->entity->getName(),
        ];
        $this->fallback[$community_offer_type->id()] = !empty($select_options[$community_offer_type->id()]['name']) ? $select_options[$community_offer_type->id()]['name'] : $community_offer_type->getTitle();
      }
    }

    $form['offer']['step-1']['offer_type'] = [
      '#title' => $this->t('qs_sharing.offers.form.add.offer_type'),
      '#type' => 'select',
      '#multiple' => FALSE,
      '#required' => FALSE,
      '#options' => $this->fallback,
      '#attributes' => [
        'icon' => 'sharing',
        'placeholder' => $this->t('qs_sharing.offers.form.add.offer_type.placeholder'),
        'selectize' => TRUE,
        'class' => ['selectize-members'],
        'data-options' => json_encode($select_options),
      ],
      '#validated' => TRUE,
      '#theme_wrappers' => [
        'form_element',
      ],
    ];

    $form['offer']['step-1']['offer_type_new'] = [
      '#type' => 'radios',
      '#options' => [0 => $this->t('qs.form.no'), 1 => $this->t('qs.form.yes')],
      '#required' => FALSE,
      '#default_value' => 0,
      '#attributes' => [
        'title' => $this->t('qs_sharing.offers.form.add.offer_type_new'),
        'no_form_group' => TRUE,
        'data-toggle' => 'buttons',
        'color' => 'danger',
        'variant' => 'button',
        'no_block' => TRUE,
        'class' => [
          'mb-2',
        ],
      ],
      '#theme_wrappers' => [
        'input__button_group',
      ],
    ];

    $form['offer']['step-1']['offer_type_title'] = [
      '#attributes' => [
        'icon' => 'plus',
      ],
      '#title' => $this->t('qs_sharing.offers.form.add.offer_type_title'),
      '#placeholder' => $this->t('qs_sharing.offers.form.add.offer_type_title.placeholder'),
      '#type' => 'textfield',
      '#required' => FALSE,
      '#theme_wrappers' => [
        'form_element',
      ],
      '#states' => [
        'visible' => [
          ':input[name="offer_type_new"]' => ['value' => '1'],
        ],
      ],
    ];

    $form['offer']['step-2'] = [
      '#type' => 'fieldset',
      '#description' => $this->t('qs_sharing.offers.form.step2.description'),
      '#attributes' => [
        'data-step' => $this->t('qs_sharing.offers.form.step2'),
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
      $options[$theme->id()] = $theme->getName() . '|' . $theme->field_sharing_icon->value;
    }

    $form['offer']['step-2']['theme'] = [
      '#attributes' => [
        'required' => TRUE,
        'variant' => 'button_theme',
        'data-toggle' => 'buttons',
        'no_form_group' => TRUE,
        'class' => [
          'btn-grid',
        ],
        'is_offer' => TRUE,
        // Create a translation string for each of the sharing themes in the foreach loop
        // phpcs:disable
        'description' => $this->t(sprintf('qs_sharing.volunteerism.form.description.theme.%s', $theme->field_sharing_icon->value)),
        // phpcs:enable
      ],
      '#theme_wrappers' => [
        'radios__buttons',
      ],
      '#type' => 'radios',
      '#required' => FALSE,
      '#options' => $options,
    ];

    $form['offer']['step-3'] = [
      '#type' => 'fieldset',
      '#description' => $this->t('qs_sharing.offers.form.step3.description'),
      '#attributes' => [
        'data-step' => $this->t('qs_sharing.offers.form.step3'),
        'class' => [
          'mb-4',
        ],
      ],
      '#theme_wrappers' => [
        'container__center',
        'fieldset__step',
      ],
    ];

    $form['offer']['step-3']['body'] = [
      '#type' => 'textarea',
      '#required' => FALSE,
    ];
    $form['offer']['step-3']['quill_body'] = [
      '#markup' => '<div class="form-group">
        <span class="quill-label">' . $this->t('qs_sharing.offers.form.add.body') . '</span>
        <div
          id="editor-add-offer-body"
          data-placeholder-translation="' . $this->t('qs_sharing.offers.form.add.body.placeholder') . '"
          class="quill-editor quill-editor-primary form-textarea form-control">
        </div>
      </div>',
    ];

    $form['offer']['step-3']['availability'] = [
      '#type' => 'textarea',
      '#required' => FALSE,
    ];
    $form['offer']['step-3']['quill_availability'] = [
      '#markup' => '<div class="form-group">
        <span class="quill-label">' . $this->t('qs_sharing.offers.form.add.availability') . '</span>
        <div
          id="editor-add-offer-availability"
          data-placeholder-translation="' . $this->t('qs_sharing.offers.form.add.availability.placeholder') . '"
          class="quill-editor quill-editor-primary form-textarea form-control">
        </div>
      </div>',
    ];

    $form['#attached']['library'][] = 'quartiers_solidaires/quill';

    $form['offer']['step-4'] = [
      '#type' => 'fieldset',
      '#description' => $this->t('qs_sharing.offers.form.step4.description'),
      '#attributes' => [
        'data-step' => $this->t('qs_sharing.offers.form.step4'),
        'class' => [
          'pb-4',
        ],
      ],
      '#theme_wrappers' => [
        'container__center',
        'fieldset__step',
      ],
    ];

    $form['offer']['step-4']['contact_firstname'] = [
      '#attributes' => [
        'icon' => 'user',
      ],
      '#title' => $this->t('qs_sharing.offers.form.add.contact_firstname'),
      '#placeholder' => $this->t('qs_sharing.offers.form.add.contact_firstname.placeholder'),
      '#default_value' => $account->field_firstname->value,
      '#type' => 'textfield',
      '#required' => FALSE,
      '#theme_wrappers' => [
        'form_element',
      ],
    ];

    $form['offer']['step-4']['contact_lastname'] = [
      '#attributes' => [
        'icon' => '_',
      ],
      '#placeholder' => $this->t('qs_sharing.offers.form.add.contact_lastname.placeholder'),
      '#type' => 'textfield',
      '#default_value' => $account->field_lastname->value,
      '#required' => FALSE,
      '#theme_wrappers' => [
        'form_element',
      ],
    ];

    $form['offer']['step-4']['contact_mail'] = [
      '#attributes' => [
        'icon' => 'mail',
      ],
      '#title' => $this->t('qs_sharing.offers.form.add.contact_mail'),
      '#placeholder' => $this->t('qs_sharing.offers.form.add.contact_mail.placeholder'),
      '#default_value' => $account->mail->value,
      '#type' => 'textfield',
      '#required' => FALSE,
      '#theme_wrappers' => [
        'form_element',
      ],
    ];

    $form['offer']['step-4']['contact_phone'] = [
      '#attributes' => [
        'icon' => 'phone',
      ],
      '#placeholder' => $this->t('qs_sharing.offers.form.add.contact_phone.placeholder'),
      '#default_value' => $account->field_phone->value,
      '#type' => 'textfield',
      '#required' => FALSE,
      '#theme_wrappers' => [
        'form_element',
      ],
    ];

    $form['offer']['step-4']['actions']['submit'] = [
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
      '#value' => $this->t('qs_sharing.offers.form.add.save'),
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
      $container->get('qs_sharing.repository.offer_type'),
      $container->get('qs_sharing.manager.offer_type'),
      $container->get('qs_sharing.manager.offer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_sharing_add_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $comunity = $this->termStorage->load($form_state->get('community'));
    $account = $this->userStorage->load($this->currentUser()->id());
    $theme = $this->termStorage->load($form_state->getValue('theme'));

    // Create the new type offer if necessary.
    if ($form_state->getValue('offer_type_new') === '1') {
      $offer_type = $this->offerTypeManager->create(
        $form_state->getValue('offer_type_title'),
        $theme,
        $comunity,
        $account
      );
    }
    else {
      $offer_type = $this->nodeStorage->load($form_state->getValue('offer_type'));
    }

    if (!$offer_type instanceof NodeInterface || $offer_type->bundle() !== 'offer_type') {
      throw new \Exception('Oops something went wrong.');
    }

    $offer = $this->offerManager->create(
      $offer_type,
      $theme,
      $account,
      $form_state->getValue('body'),
      $form_state->getValue('availability'),
      $form_state->getValue('contact_firstname'),
      $form_state->getValue('contact_lastname'),
      $form_state->getValue('contact_phone'),
      $form_state->getValue('contact_mail'),
    );

    $this->messenger()->addMessage($this->t('qs_sharing.offers.form.add.success @offer @offer_type', [
      '@offer' => $offer->getTitle(),
      '@offer_type' => $offer_type->getTitle(),
    ]));
    $form_state->setRedirect('entity.node.canonical', [
      'node' => $offer_type->id(),
      'theme' => $theme->id(),
    ], ['fragment' => "card{$offer->id()}"]);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // When custom offer type is checked, then offer type title is mandatory.
    if ($form_state->getValue('offer_type_new') === '1'
      && (!$form_state->getValue('offer_type_title') || empty($form_state->getValue('offer_type_title')))
    ) {
      $form_state->setErrorByName('offer_type_title', $this->t('qs.form.error.empty @fieldname', [
        '@fieldname' => $form['offer']['step-1']['offer_type_title']['#title'],
      ]));
    }

    // When custom offer type is unchecked, then offer type select is mandatory.
    if ($form_state->getValue('offer_type_new') === '0'
      && (!$form_state->getValue('offer_type') || empty($form_state->getValue('offer_type')))
    ) {
      $form_state->setErrorByName('offer_type', $this->t('qs.form.error.empty @fieldname', [
        '@fieldname' => $form['offer']['step-1']['offer_type']['#title'],
      ]));
    }

    // Assert the theme is valid.
    if (!$form_state->getValue('theme') || empty($form_state->getValue('theme'))) {
      $form_state->setErrorByName('form', $this->t('qs_sharing.offers.form.add.error.empty.theme'));
    }

    // Assert the body is valid.
    if (!$form_state->getValue('body') || empty($form_state->getValue('body'))) {
      $form_state->setErrorByName('body', $this->t('qs.form.error.empty @fieldname', [
        '@fieldname' => $this->t('qs_sharing.offers.form.add.body'),
      ]));
    }

    // Assert the availability is valid.
    if (!$form_state->getValue('availability') || empty($form_state->getValue('availability'))) {
      $form_state->setErrorByName('availability', $this->t('qs.form.error.empty @fieldname', [
        '@fieldname' => $this->t('qs_sharing.offers.form.add.availability'),
      ]));
    }
  }

}
