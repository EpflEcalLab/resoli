<?php

namespace Drupal\qs_sharing\Form;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_sharing\Manager\OfferManager;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form to moderate an offer.
 */
class OfferModerateForm extends FormBase {

  /**
   * The node Storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * The Offer Manager.
   *
   * @var \Drupal\qs_sharing\Manager\OfferManager
   */
  private $offerManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, EntityTypeManagerInterface $entity_type_manager, OfferManager $offer_manager) {
    $this->acl = $acl;
    $this->nodeStorage = $entity_type_manager->getStorage('node');
    $this->offerManager = $offer_manager;
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

    if ($this->acl->hasAdminAccessCommunity($community)) {
      $access = AccessResult::allowed();
    }

    return $access;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $options = NULL) {
    if (!isset($options) || !$options['offer']) {
      return $form;
    }

    /** @var \Drupal\node\NodeInterface $offer */
    $offer = $options['offer'];
    // Save the offer for later usage on submission.
    $form_state->set('offer', $offer->id());

    // Disable caching.
    $form['#cache']['max-age'] = 0;

    // Needed to ensure the right offer is linked to the right form
    // https://drupal.stackexchange.com/a/276999
    $form_state->setRequestMethod('POST');
    $form_state->setCached(TRUE);

    $form['#attributes'] = [
      'data-ajax' => 'true',
      'data-parent' => 'card' . $offer->id(),
      'class' => [
        'offer',
        'offer' . $offer->id(),
        'moderate',
        'mx-auto',
        'mb-3',
      ],
    ];
    $form['action']['submit'] = [
      '#type' => 'submit',
      '#name' => 'submit',
      '#value' => $this->t('qs_sharing.user.offers.collection.moderate'),
      '#attributes' => [
        'icon' => 'cross',
        'icon_left' => TRUE,
        'class' => [
          'btn',
          'btn-outline-invert',
          'btn-icon',
          'shadow-to-bottom',
          'btn-block',
          'bg-danger',
        ],
        'data-confirm' => $this->t('qs_sharing.user.offers.collection.moderate.confirmed'),
      ],
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
      $container->get('qs_sharing.manager.offer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_sharing_offer_moderate_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\node\NodeInterface $offer */
    $offer = $this->nodeStorage->load($form_state->get('offer'));

    // Deactivate the offer and send an email to its author.
    $this->offerManager->deactivate($offer);
    $this->offerManager->sendModeratedMail($offer, $offer->uid->entity);

    $form_state->setRedirect('entity.node.canonical', [
      'node' => $offer->field_offer_type->entity->id(),
    ], []);
  }

}
