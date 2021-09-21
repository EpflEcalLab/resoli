<?php

namespace Drupal\qs_sharing\Form;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\node\NodeInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_sharing\Manager\OfferManager;
use Drupal\qs_sharing\Manager\OfferTypeManager;
use Drupal\qs_sharing\Repository\OfferTypeRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form to edit an existing offer.
 */
class OfferEditForm extends FormBase {

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  protected $acl;

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

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
   * Construct a new form allowing edition of Offers by author volunteer.
   *
   * @param \Drupal\qs_acl\Service\AccessControl $acl
   *   The access controls.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Core\Datetime\DateFormatter $date_formatter
   *   The date formatter service.
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
  public function __construct(AccessControl $acl, EntityTypeManagerInterface $entity_type_manager, LanguageManagerInterface $language_manager, DateFormatter $date_formatter, OfferTypeRepository $offer_type_repository, OfferTypeManager $offer_type_manager, OfferManager $offer_manager) {
    // From the container, inject services.
    $this->acl = $acl;
    $this->termStorage = $entity_type_manager->getStorage('taxonomy_term');
    $this->userStorage = $entity_type_manager->getStorage('user');
    $this->nodeStorage = $entity_type_manager->getStorage('node');
    $this->languageManager = $language_manager;
    $this->dateFormatter = $date_formatter;
    $this->offerTypeRepository = $offer_type_repository;
    $this->offerTypeManager = $offer_type_manager;
    $this->offerManager = $offer_manager;
  }

  /**
   * Checks access for creating offer in the given community.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\node\NodeInterface $offer
   *   Run access checks for this offer.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access results.
   */
  public function access(AccountInterface $account, NodeInterface $offer) {
    $access = AccessResult::forbidden();

    if ($this->acl->hasAdminAccessOffer($offer)) {
      $access = AccessResult::allowed();
    }

    return $access;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ?NodeInterface $offer = NULL) {
    // Save the offer for later usage on submission.
    $form_state->set('offer', $offer->id());

    // Get the current language.
    $currentLang = $this->languageManager->getCurrentLanguage();

    $form['#theme_wrappers'] = [
      'form__modal',
    ];

    // Disable caching & HTML5 validation.
    $form['#cache']['max-age'] = 0;
    $form['#attached']['library'][] = 'qs_site/unload';
    $form['#attributes'] = [
      'novalidate' => 'novalidate',
      'title' => $this->getTitle($offer),
      'description' => $this->t('qs_sharing.offers.form.edit.description'),
      'theme' => 'info',
    ];

    $form['#floating_buttons'][] = [
      'label' => $this->t('qs_sharing.edit_offer'),
      'icon' => 'pencil',
      'active' => TRUE,
    ];

    $community_offer_types = $this->offerTypeRepository->getAllByCommunity($offer->get('field_offer_type')->entity->get('field_community')->entity);
    $options = [];

    if (!empty($community_offer_types)) {
      foreach ($community_offer_types as $community_offer_type) {
        $options[$community_offer_type->id()] = $community_offer_type->getTitle();
      }
    }

    $form['group'] = [
      '#type' => 'fieldset',
      '#attributes' => [
        'class' => [
          'mb-5',
        ],
      ],
      '#theme_wrappers' => [
        'container__center',
      ],
    ];

    $form['group']['offer_type'] = [
      '#title' => $this->t('qs_sharing.offers.form.edit.offer_type'),
      '#type' => 'select',
      '#multiple' => FALSE,
      '#required' => FALSE,
      '#default_value' => $offer->field_offer_type->target_id,
      '#options' => $options,
      '#attributes' => [
        'icon' => 'sharing',
        'placeholder' => $this->t('qs_sharing.offers.form.edit.offer_type.placeholder'),
      ],
      '#validated' => TRUE,
      '#theme_wrappers' => [
        'form_element',
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

    $form['group']['theme'] = [
      '#title' => $this->t('qs_sharing.offers.form.edit.theme'),
      '#type' => 'select',
      '#required' => FALSE,
      '#options' => $options,
      '#default_value' => $offer->field_theme->target_id,
      '#attributes' => [
        'icon' => 'sharing',
        'placeholder' => $this->t('qs_sharing.offers.form.edit.theme.placeholder'),
      ],
    ];

    $form['group']['body'] = [
      '#type' => 'textarea',
      '#required' => FALSE,
      '#default_value' => $offer->body->value,
    ];
    $form['group']['quill_body'] = [
      '#markup' => '<div class="form-group">
        <span class="quill-label">' . $this->t('qs_sharing.offers.form.edit.body') . '</span>
        <div
          id="editor-add-offer-body"
          data-placeholder-translation="' . $this->t('qs_sharing.offers.form.edit.body.placeholder') . '"
          class="quill-editor quill-editor-primary form-textarea form-control">
        </div>
      </div>',
    ];

    $form['group']['availability'] = [
      '#type' => 'textarea',
      '#required' => FALSE,
      '#default_value' => $offer->field_description->value,
    ];
    $form['group']['quill_availability'] = [
      '#markup' => '<div class="form-group">
        <span class="quill-label">' . $this->t('qs_sharing.offers.form.edit.availability') . '</span>
        <div
          id="editor-add-offer-availability"
          data-placeholder-translation="' . $this->t('qs_sharing.offers.form.edit.availability.placeholder') . '"
          class="quill-editor quill-editor-primary form-textarea form-control">
        </div>
      </div>',
    ];

    $form['group']['#attached']['library'][] = 'quartiers_solidaires/quill';

    $form['group']['contact_name'] = [
      '#attributes' => [
        'icon' => 'user',
      ],
      '#title' => $this->t('qs_sharing.offers.form.edit.contact_name'),
      '#placeholder' => $this->t('qs_sharing.offers.form.edit.contact_name.placeholder'),
      '#default_value' => $offer->field_contact_name->value,
      '#type' => 'textfield',
      '#required' => FALSE,
      '#theme_wrappers' => [
        'form_element',
      ],
    ];

    $form['group']['contact_mail'] = [
      '#attributes' => [
        'icon' => 'mail',
      ],
      '#title' => $this->t('qs_sharing.offers.form.edit.contact_mail'),
      '#placeholder' => $this->t('qs_sharing.offers.form.edit.contact_mail.placeholder'),
      '#default_value' => $offer->get('field_contact_mail')->value,
      '#type' => 'textfield',
      '#required' => FALSE,
      '#theme_wrappers' => [
        'form_element',
      ],
    ];

    $form['group']['contact_phone'] = [
      '#attributes' => [
        'icon' => 'phone',
      ],
      '#placeholder' => $this->t('qs_sharing.offers.form.edit.contact_phone.placeholder'),
      '#default_value' => $offer->get('field_contact_phone')->value,
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
        'icon_left' => TRUE,
        'outline' => TRUE,
        'class' => [
          'shadow-to-bottom',
        ],
      ],
      '#value' => $this->t('qs_sharing.offers.form.edit.save'),
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
      $container->get('date.formatter'),
      $container->get('qs_sharing.repository.offer_type'),
      $container->get('qs_sharing.manager.offer_type'),
      $container->get('qs_sharing.manager.offer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_sharing_edit_form';
  }

  /**
   * Generate a dynamic form title using the offer created date.
   *
   * @param \Drupal\node\NodeInterface $offer
   *   The offer to be edited.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The translatable dynamic form title.
   */
  public function getTitle(NodeInterface $offer): TranslatableMarkup {
    return $this->t('qs_sharing.offers.form.edit.title @offer_date', [
      '@offer_date' => $this->dateFormatter->format($offer->getChangedTime(), 'default_medium_date_only'),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $theme = $this->termStorage->load($form_state->getValue('theme'));
    $offer = $this->nodeStorage->load($form_state->get('offer'));
    $offer_type = $this->nodeStorage->load($form_state->getValue('offer_type'));

    $offer = $this->offerManager->update(
      $offer,
      [
        'field_offer_type' => $offer_type->id(),
        'field_theme' => $theme->id(),
        'body' => [
          'format' => 'light_html',
          'value' => $form_state->getValue('body'),
        ],
        'field_description' => [
          'format' => 'light_html',
          'value' => $form_state->getValue('availability'),
        ],
        'field_contact_name' => $form_state->getValue('contact_name'),
        'field_contact_mail' => $form_state->getValue('contact_phone'),
        'field_contact_phone' => $form_state->getValue('contact_mail'),
      ]
    );

    $this->messenger()->addMessage($this->t('qs_sharing.offers.form.edit.success @offer_date', [
      '@offer_date' => $this->dateFormatter->format($offer->getChangedTime(), 'default_medium_date_only'),
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
    // When custom offer type is unchecked, then offer type select is mandatory.
    if (!$form_state->getValue('offer_type') || empty($form_state->getValue('offer_type'))) {
      $form_state->setErrorByName('offer_type', $this->t('qs.form.error.empty @fieldname', [
        '@fieldname' => $form['offer_type']['#title'],
      ]));
    }

    // Assert the theme is valid.
    if (!$form_state->getValue('theme') || empty($form_state->getValue('theme'))) {
      $form_state->setErrorByName('form', $this->t('qs_sharing.offers.form.edit.error.empty.theme'));
    }

    // Assert the body is valid.
    if (!$form_state->getValue('body') || empty($form_state->getValue('body'))) {
      $form_state->setErrorByName('body', $this->t('qs.form.error.empty @fieldname', [
        '@fieldname' => $this->t('qs_sharing.offers.form.edit.body'),
      ]));
    }

    // Assert the availability is valid.
    if (!$form_state->getValue('availability') || empty($form_state->getValue('availability'))) {
      $form_state->setErrorByName('availability', $this->t('qs.form.error.empty @fieldname', [
        '@fieldname' => $this->t('qs_sharing.offers.form.edit.availability'),
      ]));
    }
  }

}
