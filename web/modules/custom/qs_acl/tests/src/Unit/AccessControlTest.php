<?php

namespace Drupal\Tests\qs_acl\Unit;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\node\NodeInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_sharing\Repository\VolunteerismRepository;
use Drupal\taxonomy\TermInterface;
use Drupal\Tests\UnitTestCase;

/**
 * Tests the block plugin collection.
 *
 * @coversDefaultClass \Drupal\qs_acl\Service\AccessControl
 *
 * @group qs
 * @group qs_unit
 * @group qs_acl
 * @group qs_acl_unit
 *
 * @internal
 */
final class AccessControlTest extends UnitTestCase {

  /**
   * The mock account.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The entity type manager used for testing.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $entityTypeManager;

  /**
   * The mock volunteerism repository.
   *
   * @var \Drupal\qs_sharing\Repository\VolunteerismRepository
   */
  protected $volunteerismRepository;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->currentUser = $this->createMock(AccountProxy::class);
    $this->entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $this->volunteerismRepository = $this->createMock(VolunteerismRepository::class);

    $this->acl = new AccessControl($this->currentUser, $this->entityTypeManager, $this->volunteerismRepository);
  }

  /**
   * Provider of ::testIsCommunityVolunteerContextualUser.
   *
   * Set of return value from isCommunityReturnsExcepted with excepted boolean
   * result on isCommunityVolunteer.
   *
   * @return iterable
   *   Return an array of arrays contains expectation.
   */
  public function isCommunityReturnsExcepted(): iterable {
    yield [NULL, FALSE];

    yield [[], FALSE];

    yield [[
      $this->createMock(NodeInterface::class),
    ], TRUE,
    ];
  }

  /**
   * Ensure the current user will be used when non given.
   *
   * @covers ::isCommunityVolunteer
   */
  public function testIsCommunityVolunteerContextualUser() {
    $community = $this->createMock(TermInterface::class);
    $anotherCurrentUser = $this->createMock(AccountProxyInterface::class);

    $this->volunteerismRepository->expects(self::exactly(2))
      ->method('getAllByCommunityUser')
      ->withConsecutive(
        [$community, $this->currentUser],
        [$community, $anotherCurrentUser]
      );

    // Fallback on the current user.
    $this->acl->isCommunityVolunteer($community);

    // User the given user.
    $this->acl->isCommunityVolunteer($community, $anotherCurrentUser);
  }

  /**
   * @covers ::isCommunityVolunteer
   *
   * @dataProvider isCommunityReturnsExcepted
   */
  public function testIsCommunityVolunteerReturnsExcepted($fetchResult, bool $excepted) {
    $community = $this->createMock(TermInterface::class);

    $this->volunteerismRepository->expects(self::once())
      ->method('getAllByCommunityUser')
      ->willReturn($fetchResult);

    $result = $this->acl->isCommunityVolunteer($community);

    self::assertEquals($excepted, $result);
  }

}
