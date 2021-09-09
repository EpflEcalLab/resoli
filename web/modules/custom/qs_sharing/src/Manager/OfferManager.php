<?php

namespace Drupal\qs_sharing\Manager;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\node\NodeInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\user\UserInterface;

/**
 * Manage CRUD operations and actions on Offer.
 */
class OfferManager {

  /**
   * Composes and optionally sends an email message.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mail;
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
   * @param \Drupal\Core\Mail\MailManagerInterface $mail
   *   The mail manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, MailManagerInterface $mail) {
    $this->nodeStorage = $entity_type_manager->getStorage('node');
    $this->mail = $mail;
  }

  /**
   * Create an Offer Type.
   *
   * @param \Drupal\node\NodeInterface $offer_type
   *   The offer type.
   * @param \Drupal\taxonomy\TermInterface $theme
   *   The sharing theme.
   * @param \Drupal\user\UserInterface $author
   *   The author.
   * @param string $body
   *   The body rich HTML string.
   * @param string $availability
   *   The availability rich HTML string.
   * @param string $contact_firstname
   *   Contact firstname.
   * @param string $contact_lastname
   *   Contact lastname.
   * @param string|null $contact_mail
   *   Contact optional mail.
   * @param string|null $contact_phone
   *   Contact optional phone.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *
   * @return \Drupal\node\NodeInterface
   *   The created offer type.
   */
  public function create(NodeInterface $offer_type, TermInterface $theme, UserInterface $author, string $body, string $availability, string $contact_firstname, string $contact_lastname, ?string $contact_mail = NULL, ?string $contact_phone = NULL): NodeInterface {
    $offer = $this->nodeStorage->create([
      'type' => 'offer',
      'status' => TRUE,
      'moderation_state' => 'published',
      'title' => sprintf('%s | %s %s', $offer_type->getTitle(), $contact_firstname, $contact_lastname),
      'field_offer_type' => $offer_type->id(),
      'field_theme' => $theme->id(),
      'body' => [
        'format' => 'light_html',
        'value' => $body,
      ],
      'field_availability' => [
        'format' => 'light_html',
        'value' => $availability,
      ],
      'field_contact_firstname' => $contact_firstname,
      'field_contact_lastname' => $contact_lastname,
      'field_contact_mail' => $contact_mail,
      'field_contact_phone' => $contact_phone,
      'uid' => $author->id(),
    ]);
    $offer->save();

    return $offer;
  }

  /**
   * Deactivate the offer.
   *
   * @param \Drupal\node\NodeInterface $offer
   *   The offer.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *
   * @return \Drupal\node\NodeInterface
   *   The deactivated offer.
   */
  public function deactivate(NodeInterface $offer): NodeInterface {
    $offer->set('moderation_state', 'archived');
    $offer->save();

    return $offer;
  }

  /**
   * Delete the offer.
   *
   * @param \Drupal\node\NodeInterface $offer
   *   The offer.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function delete(NodeInterface $offer): void {
    $offer->delete();
  }

  /**
   * Reactivate the offer.
   *
   * @param \Drupal\node\NodeInterface $offer
   *   The offer.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *
   * @return \Drupal\node\NodeInterface
   *   The reactivated offer.
   */
  public function reactivate(NodeInterface $offer): NodeInterface {
    // Reactivate the offer.
    $offer->set('moderation_state', 'published');
    $offer->save();

    return $offer;
  }

  /**
   * Send a mail to alert the user of the moderation of its offer.
   *
   * @param \Drupal\node\NodeInterface $offer
   *   The moderated offer.
   * @param \Drupal\user\UserInterface $user
   *   The author of the offer.
   */
  public function sendModeratedMail(NodeInterface $offer, UserInterface $user): void {
    $this->mail->mail('qs_sharing', 'offer_moderated', $user->getEmail(), $user->getPreferredLangcode(), [
      'offer' => $offer,
    ]);
  }

}
