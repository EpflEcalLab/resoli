<?php

namespace Drupal\qs_sharing\Form;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManager;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_sharing\Repository\OfferRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form to reactivate an offer.
 */
class OfferReactivateForm extends FormBase {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

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
   * The entity QS Offer repository.
   *
   * @var \Drupal\qs_sharing\Repository\OfferRepository
   */
  protected $offerRepository;

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * {@inheritdoc}
   */
  public function __construct(OfferRepository $offer_repository, LanguageManager $language_manager, AccessControl $acl, AccountInterface $current_user, EntityTypeManagerInterface $entity_type_manager) {
    $this->offerRepository = $offer_repository;
    $this->languageManager = $language_manager;
    $this->acl = $acl;
    $this->currentUser = $current_user;
    $this->nodeStorage = $entity_type_manager->getStorage('node');
  }

  /**
   * Checks access.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\node\NodeInterface $offer
   *   Run access checks for this node.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account, NodeInterface $offer) {
    $access = AccessResult::forbidden();

    if ($this->acl->hasAccessOffer($offer)) {
      $access = AccessResult::allowed();
    }

    return $access;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ?NodeInterface $offer = NULL) {
    $form_state->set('offer', $offer->id());

    // Disable caching & HTML5 validation.
    $form['#cache']['max-age'] = 0;

    $form['#theme_wrappers'] = [
      'form__modal',
    ];

    $form['#attached']['library'][] = 'qs_site/unload';

    $form['#attributes'] = [
      'title' => $offer->title->value,
      'description' => $this->t('qs_sharing.offers.form.reactivate.warning'),
      'icon' => 'clockwise',
      'theme' => 'danger',
    ];

    $form['#floating_buttons'][] = [
      'label' => $this->t('qs.sharing.reactivate'),
      'icon' => 'trash',
      'active' => TRUE,
    ];

    $form['actions'] = [
      '#type' => 'fieldset',
      '#theme_wrappers' => [
        'container__center',
      ],
      '#attributes' => [
        'class' => [
          'text-center',
        ],
      ],
    ];

    $form['actions']['cancel'] = [
      '#type' => 'link',
      '#title' => $this->t('qs.form.cancel'),
      '#url' => Url::fromRoute('qs_sharing.collection.user.offers', [
        'community' => $offer->field_offer_type->entity->field_community->entity->id(),
        'user' => $this->currentUser->id(),
      ]),
      '#attributes' => [
        'class' => [
          'btn btn-outline-danger btn-outline-invert',
        ],
      ],
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#attributes' => [
        'class' => [
          'text-danger',
        ],
        'icon' => 'clockwise',
        'icon_left' => TRUE,
        'white' => TRUE,
      ],
      '#value' => $this->t('qs.form.reactivate'),
    ];

    // Remove unload script.
    $form['#attached']['library'] = [];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('qs_sharing.repository.offer'),
      $container->get('language_manager'),
      $container->get('qs_acl.access_control'),
      $container->get('current_user'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_sharing_reactivate_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $offer = $this->nodeStorage->load($form_state->get('offer'));
    $community = $offer->field_offer_type->entity->field_community->entity;

    $this->messenger()->addMessage($this->t('qs_sharing.offers.form.reactivate.success @offer', [
      '@offer' => $offer->getTitle(),
    ]));

    $form_state->setRedirect('qs_sharing.collection.user.offers', [
      'community' => $community->id(),
      'user' => $this->currentUser->id(),
    ], []);

    // Reactivate the offer.
    $offer->setPublished(TRUE);
    $offer->save();
  }

}
