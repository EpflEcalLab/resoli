<?php

namespace Drupal\qs_sharing\Form;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_sharing\Repository\OfferRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base form for all offer actions.
 */
abstract class OfferActionFormBase extends FormBase {
  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The node Storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * The entity QS Offer Manager.
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
  public function __construct(AccessControl $acl, OfferRepository $offer_repository, EntityTypeManagerInterface $entity_type_manager, AccountInterface $current_user) {
    $this->acl = $acl;
    $this->offerRepository = $offer_repository;
    $this->nodeStorage = $entity_type_manager->getStorage('node');
    $this->currentUser = $current_user;
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
    // Save the offer for submission.
    if ($offer) {
      $form_state->set('offer', $offer->id());
    }

    $form_state->setRequestMethod('POST');
    $form_state->setCached(TRUE);

    $form['#attached']['library'][] = 'qs_site/unload';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
    // Load customs services used in this class.
      $container->get('qs_acl.access_control'),
      $container->get('qs_sharing.repository.offer'),
      $container->get('entity_type.manager'),
      $container->get('current_user')
    );
  }

}
