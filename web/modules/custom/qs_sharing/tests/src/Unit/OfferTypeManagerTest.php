<?php

namespace Drupal\Tests\qs_sharing\Unit;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;
use Drupal\node\NodeStorageInterface;
use Drupal\qs_sharing\Manager\OfferTypeManager;
use Drupal\taxonomy\TermInterface;
use Drupal\Tests\UnitTestCase;
use Drupal\user\UserInterface;

/**
 * @coversDefaultClass \Drupal\qs_sharing\Manager\OfferTypeManager
 *
 * @group qs
 * @group qs_unit
 * @group qs_sharing
 * @group qs_sharing_unit
 *
 * @internal
 */
final class OfferTypeManagerTest extends UnitTestCase {

  /**
   * The entity type manager used for testing.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $entityTypeManager;

  /**
   * The node Storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * The offer type manager.
   *
   * @var \Drupal\qs_sharing\Manager\OfferTypeManager
   */
  protected $offerTypeManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $this->nodeStorage = $this->createMock(NodeStorageInterface::class);

    $this->entityTypeManager->expects(self::once())
      ->method('getStorage')
      ->with('node')
      ->willReturn($this->nodeStorage);

    $this->offerTypeManager = new OfferTypeManager($this->entityTypeManager);
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
    $community = $this->createMock(TermInterface::class);
    $community->expects(self::once())
      ->method('id')
      ->willReturn(1);
    $author = $this->createMock(UserInterface::class);
    $author->expects(self::once())
      ->method('id')
      ->willReturn(1);

    $this->nodeStorage->expects(self::once())
      ->method('create')
      ->with([
        'type' => 'offer_type',
        'status' => TRUE,
        'moderation_state' => 'published',
        'title' => 'foo',
        'field_theme' => 1,
        'field_community' => 1,
        'uid' => 1,
      ])
      ->willReturn($node);

    $node->expects(self::once())
      ->method('save');

    $this->offerTypeManager->create('foo', $theme, $community, $author);
  }

}
