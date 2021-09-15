<?php

namespace Drupal\qs_sharing\Manager;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\node\NodeInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\user\UserInterface;

/**
 * Manage CRUD operations and actions on Request.
 */
class RequestManager {

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
   * Archive the request.
   *
   * @param \Drupal\node\NodeInterface $request
   *   The archive.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *
   * @return \Drupal\node\NodeInterface
   *   The archived request.
   */
  public function archive(NodeInterface $request): NodeInterface {
    $request->set('moderation_state', 'archived');
    $request->save();

    return $request;
  }

  /**
   * Create a Request.
   *
   * @param \Drupal\taxonomy\TermInterface $theme
   *   The sharing theme.
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community.
   * @param \Drupal\user\UserInterface $author
   *   The author.
   * @param string $body
   *   The body rich HTML string.
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
  public function create(TermInterface $theme, TermInterface $community, UserInterface $author, string $body, string $contact_firstname, string $contact_lastname, ?string $contact_mail = NULL, ?string $contact_phone = NULL): NodeInterface {
    $request = $this->nodeStorage->create([
      'type' => 'request',
      'status' => TRUE,
      'moderation_state' => 'published',
      'title' => sprintf('%s | %s %s', $theme->getName(), $contact_firstname, $contact_lastname),
      'field_community' => $community->id(),
      'field_theme' => $theme->id(),
      'body' => [
        'format' => 'light_html',
        'value' => $body,
      ],
      'field_contact_firstname' => $contact_firstname,
      'field_contact_lastname' => $contact_lastname,
      'field_contact_mail' => $contact_mail,
      'field_contact_phone' => $contact_phone,
      'uid' => $author->id(),
    ]);
    $request->save();

    return $request;
  }

  /**
   * Send a mail to alert the user its request has been archived.
   *
   * @param \Drupal\node\NodeInterface $request
   *   The archived request.
   * @param \Drupal\user\UserInterface $archived_by
   *   The author of the archive operation.
   */
  public function sendArchivedMail(NodeInterface $request, UserInterface $archived_by): void {
    $author = $request->get('uid')->entity;
    $this->mail->mail('qs_sharing', 'request_archived', $author->getEmail(), $author->getPreferredLangcode(), [
      'request' => $request,
      'archived_by' => $archived_by,
    ]);
  }

  /**
   * Send a mail to alert the user on request resolution.
   *
   * @param \Drupal\node\NodeInterface $request
   *   The solved request.
   * @param \Drupal\user\UserInterface $solved_by
   *   The author of the resolution.
   */
  public function sendSolvedMail(NodeInterface $request, UserInterface $solved_by): void {
    $author = $request->get('uid')->entity;
    $this->mail->mail('qs_sharing', 'request_solved', $author->getEmail(), $author->getPreferredLangcode(), [
      'request' => $request,
      'solved_by' => $solved_by,
    ]);
  }

  /**
   * Resolve the request.
   *
   * @param \Drupal\node\NodeInterface $request
   *   The request.
   * @param \Drupal\user\UserInterface $solved_by
   *   The author of the resolution.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *
   * @return \Drupal\node\NodeInterface
   *   The deactivated request.
   */
  public function solved(NodeInterface $request, UserInterface $solved_by): NodeInterface {
    $request->set('moderation_state', 'solved');
    $request->set('field_solved_by', $solved_by->id());
    $request->save();

    return $request;
  }

}
