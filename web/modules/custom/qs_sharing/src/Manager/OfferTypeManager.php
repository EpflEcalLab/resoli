<?php

namespace Drupal\qs_sharing\Manager;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\user\UserInterface;

/**
 * Manage CRUD operations and actions on Offer's Types.
 */
class OfferTypeManager {
  /**
   * The node Storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * Class constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->nodeStorage = $entity_type_manager->getStorage('node');
  }

  /**
   * Create an Offer Type.
   *
   * @param string $title
   *   The new offer's type title.
   * @param \Drupal\taxonomy\TermInterface $theme
   *   The sharing theme.
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community entity.
   * @param \Drupal\user\UserInterface $author
   *   The author.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *
   * @return \Drupal\node\NodeInterface
   *   The created offer type.
   */
  public function create(string $title, TermInterface $theme, TermInterface $community, UserInterface $author): NodeInterface {
    $offer_type = $this->nodeStorage->create([
      'type' => 'offer_type',
      'status' => TRUE,
      'moderation_state' => 'published',
      'title' => $title,
      'field_theme' => $theme->id(),
      'field_community' => $community->id(),
      'uid' => $author->id(),
    ]);
    $offer_type->save();

    return $offer_type;
  }

}
