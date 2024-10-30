<?php

namespace Drupal\Tests\qs_sharing\Unit;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Tests\UnitTestCase;
use Drupal\node\NodeInterface;
use Drupal\node\NodeStorageInterface;
use Drupal\qs_sharing\Manager\OfferManager;
use Drupal\taxonomy\TermInterface;
use Drupal\user\UserInterface;

/**
 * @coversDefaultClass \Drupal\qs_sharing\Manager\OfferManager
 *
 * @group qs
 * @group qs_unit
 * @group qs_sharing
 * @group qs_sharing_unit
 *
 * @internal
 */
final class OfferManagerTest extends UnitTestCase {

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
   * @var \Drupal\qs_sharing\Manager\OfferManager
   */
  protected $offerManager;

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

    $this->offerManager = new OfferManager($this->entityTypeManager, $this->mail);
  }

  /**
   * @covers ::create
   */
  public function testCreateReturnsExcepted() {
    $node = $this->createMock(NodeInterface::class);
    $theme = $this->createMock(TermInterface::class);
    $theme->expects(self::once())
      ->method('id')
      ->willReturn(1);
    $offer_type = $this->createMock(NodeInterface::class);
    $offer_type->expects(self::once())
      ->method('id')
      ->willReturn(1);
    $offer_type->expects(self::once())
      ->method('getTitle')
      ->willReturn('Mollis facilisi');
    $author = $this->createMock(UserInterface::class);
    $author->expects(self::once())
      ->method('id')
      ->willReturn(1);

    $this->nodeStorage->expects(self::once())
      ->method('create')
      ->with([
        'type' => 'offer',
        'status' => TRUE,
        'moderation_state' => 'published',
        'title' => 'Mollis facilisi | Aptent Tempus',
        'field_offer_type' => 1,
        'field_theme' => 1,
        'body' => [
          'format' => 'light_html',
          'value' => 'Feugiat mollis lacus leo nascetur neque consequat',
        ],
        'field_description' => [
          'format' => 'light_html',
          'value' => 'In porttitor justo urna nullam lectus lacus',
        ],
        'field_contact_name' => 'Aptent Tempus',
        'field_contact_mail' => 'aptent.tempus@example.org',
        'field_contact_phone' => '079 790 79 79',
        'uid' => 1,
      ])
      ->willReturn($node);

    $node->expects(self::once())
      ->method('save');

    $this->offerManager->create(
      $offer_type,
      $theme,
      $author,
      'Feugiat mollis lacus leo nascetur neque consequat',
      'In porttitor justo urna nullam lectus lacus',
      'Aptent',
      'Tempus',
      'aptent.tempus@example.org',
      '079 790 79 79',
    );
  }

  /**
   * @covers ::deactivate
   */
  public function testDeactivateReturnsExcepted(): void {
    $node = $this->createMock(NodeInterface::class);

    $node->expects(self::once())
      ->method('set')
      ->with('moderation_state', 'archived');

    $node->expects(self::once())
      ->method('save');

    $this->offerManager->deactivate($node);
  }

  /**
   * @covers ::delete
   */
  public function testDeleteReturnsExcepted() {
    $node = $this->createMock(NodeInterface::class);

    $node->expects(self::once())
      ->method('delete');

    $this->offerManager->delete($node);
  }

  /**
   * @covers ::reactivate
   */
  public function testReactivateReturnsExcepted() {
    $node = $this->createMock(NodeInterface::class);

    $node->expects(self::once())
      ->method('set')
      ->with('moderation_state', 'published');

    $this->offerManager->reactivate($node);
  }

  /**
   * @covers ::sendModeratedMail
   */
  public function testSendModeratedMailReturnsExcepted() {
    $node = $this->createMock(NodeInterface::class);
    $user = $this->createMock(UserInterface::class);

    $user->expects(self::once())->method('getEmail');
    $user->expects(self::once())->method('getPreferredLangcode');
    $this->mail->expects(self::once())->method('mail');

    $this->offerManager->sendModeratedMail($node, $user);
  }

  /**
   * @covers ::update
   */
  public function testUpdateReturnsExcepted() {
    $node = $this->createMock(NodeInterface::class);
    $node
      ->expects(self::exactly(8))
      ->method('hasField')
      ->willReturnMap([
        ['title', TRUE],
        ['field_offer_type', TRUE],
        ['field_theme', TRUE],
        ['body', TRUE],
        ['field_description', TRUE],
        ['field_contact_name', TRUE],
        ['field_contact_mail', TRUE],
        ['field_contact_phone', TRUE],
      ]);
    $node
      ->expects(self::exactly(8))
      ->method('set')
      ->willReturnCallback(function (string $field, mixed $value): void {
        static $i = 0;
        match (++$i) {
          1 => $this->assertEquals($field, 'title') && $this->assertEquals($value, 'Mollis facilisi | Aptent Tempus'),
          2 => $this->assertEquals($field, 'field_offer_type') && $this->assertEquals($value, 1),
          3 => $this->assertEquals($field, 'field_theme') && $this->assertEquals($value, 1),
          4 => $this->assertEquals($field, 'body') && $this->assertEquals($value, [
            'format' => 'light_html',
            'value' => 'Feugiat mollis lacus leo nascetur neque consequat',
          ]),
          5 => $this->assertEquals($field, 'field_description') && $this->assertEquals($value, [
            'format' => 'light_html',
            'value' => 'In porttitor justo urna nullam lectus lacus',
          ]),
          6 => $this->assertEquals($field, 'field_contact_name') && $this->assertEquals($value, 'Aptent Tempus'),
          7 => $this->assertEquals($field, 'field_contact_mail') && $this->assertEquals($value, 'aptent.tempus@example.org'),
          8 => $this->assertEquals($field, 'field_contact_phone') && $this->assertEquals($value, '079 790 79 79'),
        };
      });

    $node
      ->expects(self::once())
      ->method('save');

    $this->offerManager->update(
      $node,
      [
        'title' => 'Mollis facilisi | Aptent Tempus',
        'field_offer_type' => 1,
        'field_theme' => 1,
        'body' => [
          'format' => 'light_html',
          'value' => 'Feugiat mollis lacus leo nascetur neque consequat',
        ],
        'field_description' => [
          'format' => 'light_html',
          'value' => 'In porttitor justo urna nullam lectus lacus',
        ],
        'field_contact_name' => 'Aptent Tempus',
        'field_contact_mail' => 'aptent.tempus@example.org',
        'field_contact_phone' => '079 790 79 79',
      ],
    );
  }

}
