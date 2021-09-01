<?php

namespace Drupal\qs_sharing\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_sharing\Repository\OfferRepository;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handle offers for Sharing.
 */
class OffersController extends ControllerBase {

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
   * Render template for the Offer add form.
   */
  public function add(Request $request, TermInterface $community) {
    // @todo Handle the add form
    $variables = ['community' => $community];

    return [
      '#theme' => 'qs_sharing_add_request_page',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
          'url.query_args',
        ],
      ],
    ];
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

  /**
   * Deactivate the offer.
   */
  public function deactivate(Request $request, NodeInterface $offer) {
    $community = $offer->field_offer_type->entity->field_community->entity;

    $this->messenger()->addMessage($this->t('qs_sharing.offers.form.deactivate.success @offer', [
      '@offer' => $offer->getTitle(),
    ]));

    // Deactivate the offer.
    $offer->set('moderation_state', 'archived');
    $offer->save();

    $destination = Url::fromRoute('qs_sharing.collection.user.offers', [
      'community' => $community->id(),
      'user' => $this->currentUser->id(),
    ]);

    return new RedirectResponse($destination->toString());
  }

  /**
   * Delete the offer.
   */
  public function delete(Request $request, NodeInterface $offer) {
    $community = $offer->field_offer_type->entity->field_community->entity;

    $this->messenger()->addMessage($this->t('qs_sharing.offers.form.delete.success @offer', [
      '@offer' => $offer->getTitle(),
    ]));

    // Delete the offer.
    $offer->delete();

    $destination = Url::fromRoute('qs_sharing.collection.user.offers', [
      'community' => $community->id(),
      'user' => $this->currentUser->id(),
    ]);

    return new RedirectResponse($destination->toString());
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags(?array $nodes = NULL) {
    $tags = [
      // Invalidated whenever any Community is updated, deleted or created.
      'taxonomy_term_list:communities',
      // Invalidated whenever any Sharing Theme is updated, deleted or created.
      'taxonomy_term_list:sharing_themes',
      // Invalidated whenever any Offer is updated, deleted or created.
      'node_list:offer',
    ];

    if ($nodes) {
      foreach ($nodes as $node) {
        $tags[] = 'node:' . $node->id();
      }
    }

    return $tags;
  }

  /**
   * Reactivate the offer.
   */
  public function reactivate(Request $request, NodeInterface $offer) {
    $community = $offer->field_offer_type->entity->field_community->entity;

    $this->messenger()->addMessage($this->t('qs_sharing.offers.form.reactivate.success @offer', [
      '@offer' => $offer->getTitle(),
    ]));

    // Reactivate the offer.
    $offer->set('moderation_state', 'published');
    $offer->save();

    $destination = Url::fromRoute('qs_sharing.collection.user.offers', [
      'community' => $community->id(),
      'user' => $this->currentUser->id(),
    ]);

    return new RedirectResponse($destination->toString());
  }

}
