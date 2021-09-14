<?php

namespace Drupal\Tests\qs_sharing\Unit;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\node\NodeInterface;
use Drupal\node\NodeStorageInterface;
use Drupal\qs_sharing\Manager\RequestManager;
use Drupal\Tests\UnitTestCase;
use Drupal\user\UserInterface;

/**
 * @coversDefaultClass \Drupal\qs_sharing\Manager\RequestManager
 *
 * @group qs
 * @group qs_unit
 * @group qs_sharing
 * @group qs_sharing_unit
 *
 * @internal
 */
final class RequestManagerTest extends UnitTestCase {

  /**
   * The entity type manager used for testing.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $entityTypeManager;

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
   * The offer type manager.
   *
   * @var \Drupal\qs_sharing\Manager\RequestManager
   */
  protected $requestManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $this->nodeStorage = $this->createMock(NodeStorageInterface::class);
    $this->mail = $this->createMock(MailManagerInterface::class);

    $this->entityTypeManager->expects(self::once())
      ->method('getStorage')
      ->with('node')
      ->willReturn($this->nodeStorage);

    $this->requestManager = new RequestManager($this->entityTypeManager, $this->mail);
  }

  /**
   * @covers ::archive
   */
  public function testArchiveReturnsExcepted() {
    $node = $this->createMock(NodeInterface::class);

    $node
      ->expects(self::once())
      ->method('save');

    $node->expects(self::once())
      ->method('set')
      ->with('moderation_state', 'archived');
    $this->requestManager->archive($node);
  }

  /**
   * @covers ::sendArchivedMail
   */
  public function testSendArchivedMailReturnsExcepted() {
    $node = $this->createMock(NodeInterface::class);
    $author = $this->createMock(UserInterface::class);
    $uidField = new \stdClass();
    $uidField->entity = $author;
    $resolver = $this->createMock(UserInterface::class);

    $node->expects(self::once())->method('get')->with('uid')->willReturn($uidField);
    $author->expects(self::once())->method('getEmail');
    $author->expects(self::once())->method('getPreferredLangcode');
    $this->mail->expects(self::once())->method('mail');

    $this->requestManager->sendArchivedMail($node, $resolver);
  }

  /**
   * @covers ::sendSolvedMail
   */
  public function testSendSolvedMailReturnsExcepted() {
    $node = $this->createMock(NodeInterface::class);
    $author = $this->createMock(UserInterface::class);
    $uidField = new \stdClass();
    $uidField->entity = $author;
    $resolver = $this->createMock(UserInterface::class);

    $node->expects(self::once())->method('get')->with('uid')->willReturn($uidField);
    $author->expects(self::once())->method('getEmail');
    $author->expects(self::once())->method('getPreferredLangcode');
    $this->mail->expects(self::once())->method('mail');

    $this->requestManager->sendSolvedMail($node, $resolver);
  }

  /**
   * @covers ::solved
   */
  public function testSolvedReturnsExcepted() {
    $node = $this->createMock(NodeInterface::class);
    $author = $this->createMock(UserInterface::class);

    $author->expects(self::once())
      ->method('id')
      ->willReturn(2);

    $node
      ->expects(self::once())
      ->method('save');

    $node->expects(self::exactly(2))
      ->method('set')
      ->withConsecutive(
        ['moderation_state', 'solved'],
        ['field_solved_by', 2]
      );
    $this->requestManager->solved($node, $author);
  }

}
