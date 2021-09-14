<?php

namespace Drupal\qs_sharing\Manager;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\node\NodeInterface;
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
