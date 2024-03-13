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
use Drupal\user\UserInterface;

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

    $this->acl = new AccessControl($this->currentUser, $this->entityTypeManager);
    $this->acl->setVolunteerismRepository($this->volunteerismRepository);
  }

  /**
   * Provider of ::testHasDashboardSharingAccessReturnsExcepted.
   *
   * Set of return value from hasDashboardSharingAccessReturnsExcepted
   * with expected boolean result on hasDashboardSharingAccess.
   *
   * @return iterable
   *   Return an array of arrays contains expectation.
   */
  public function hasDashboardSharingAccessReturnsExcepted(): iterable {
    yield ['4', FALSE];

    yield ['3', FALSE];

    yield ['2', TRUE];
  }

  /**
   * Provider of HasWrite access methods where Author/Admin may only write.
   *
   * @return iterable
   *   Return an array of arrays contains expectation.
   */
  public function hasWriteAccessAuthorship(): iterable {
    yield [NULL, NULL, FALSE];

    yield ['1', '1', TRUE];

    yield ['2', '1', FALSE];

    yield ['1', '2', FALSE];

    yield ['1', NULL, FALSE];

    yield [NULL, '2', FALSE];
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
   * @covers ::hasDashboardSharingAccess
   */
  public function testHasDashboardSharingAccessContextualUser() {
    $community = $this->createMock(TermInterface::class);

    $this->currentUser->expects(self::exactly(2))
      ->method('id');

    $acl = $this->getMockBuilder(AccessControl::class)
      ->onlyMethods(['hasCommunityByUser', 'hasBypass'])
      ->setConstructorArgs([$this->currentUser, $this->entityTypeManager])
      ->getMock();
    $acl->expects(self::once())->method('hasBypass')->willReturn(FALSE);
    $acl->expects(self::once())->method('hasCommunityByUser')->willReturn(TRUE);

    // Fallback on the current user.
    $acl->hasDashboardSharingAccess($community);
  }

  /**
   * Ensure the current user will be used when non given.
   *
   * @covers ::hasDashboardSharingAccess
   */
  public function testHasDashboardSharingAccessGivenUser() {
    $community = $this->createMock(TermInterface::class);

    $anotherCurrentUser = $this->createMock(AccountProxyInterface::class);
    $anotherCurrentUser->expects(self::once())
      ->method('id');
    $this->currentUser->expects(self::once())
      ->method('id');

    $acl = $this->getMockBuilder(AccessControl::class)
      ->onlyMethods(['hasCommunityByUser', 'hasBypass'])
      ->setConstructorArgs([$this->currentUser, $this->entityTypeManager])
      ->getMock();
    $acl->expects(self::once())->method('hasBypass')->willReturn(FALSE);
    $acl->expects(self::once())->method('hasCommunityByUser')->willReturn(TRUE);

    // Fallback on the current user.
    $acl->hasDashboardSharingAccess($community, $anotherCurrentUser);
  }

  /**
   * @covers ::hasDashboardSharingAccess
   *
   * @dataProvider hasDashboardSharingAccessReturnsExcepted
   */
  public function testHasDashboardSharingAccessReturnsExcepted($userId, bool $excepted) {
    $community = $this->createMock(TermInterface::class);
    $this->currentUser->expects(self::once())
      ->method('id')
      ->willReturn('2');

    $user = $this->createMock(UserInterface::class);
    $user->expects(self::once())
      ->method('id')
      ->willReturn($userId);

    $acl = $this->getMockBuilder(AccessControl::class)
      ->onlyMethods(['hasCommunityByUser', 'hasBypass'])
      ->setConstructorArgs([$this->currentUser, $this->entityTypeManager])
      ->getMock();
    $acl->expects(self::once())->method('hasBypass')->willReturn(FALSE);
    $acl->method('hasCommunityByUser')->willReturn(TRUE);

    $result = $acl->hasDashboardSharingAccess($community, $user);

    self::assertEquals($excepted, $result);
  }

  /**
   * Ensure the current user will be used when non given.
   *
   * @covers ::hasWriteAccessOffer
   */
  public function testHasWriteAccessOfferContextualUser() {
    $offer = $this->createMock(NodeInterface::class);

    $offer->expects(self::exactly(2))
      ->method('get')
      ->with('uid')
      ->willReturn((object) ['target_id' => '2']);

    $this->currentUser->expects(self::once())
      ->method('id')
      ->willReturn('2');

    // Fallback on the current user.
    $this->acl->hasWriteAccessOffer($offer);

    $anotherCurrentUser = $this->createMock(AccountProxyInterface::class);
    $anotherCurrentUser->expects(self::once())
      ->method('id')
      ->willReturn('2');

    // User the given user.
    $this->acl->hasWriteAccessOffer($offer, $anotherCurrentUser);
  }

  /**
   * @covers ::hasWriteAccessOffer
   *
   * @dataProvider hasWriteAccessAuthorship
   */
  public function testHasWriteAccessOfferReturnsExcepted($userId, $offerAuthorId, bool $excepted) {
    $offer = $this->createMock(NodeInterface::class);
    $offer->expects(self::once())
      ->method('get')
      ->with('uid')
      ->willReturn((object) ['target_id' => $offerAuthorId]);

    $this->currentUser->expects(self::once())
      ->method('id')
      ->willReturn($userId);

    // Fallback on the current user.
    $result = $this->acl->hasWriteAccessOffer($offer);

    self::assertEquals($excepted, $result);
  }

  /**
   * Ensure the current user will be used when non given.
   *
   * @covers ::hasWriteAccessRequest
   */
  public function testHasWriteAccessRequestContextualUser() {
    $request = $this->createMock(NodeInterface::class);
    $community = $this->createMock(TermInterface::class);

    $acl = $this->getMockBuilder(AccessControl::class)
      ->onlyMethods(['hasAdminAccessCommunity', 'hasBypass'])
      ->setConstructorArgs([$this->currentUser, $this->entityTypeManager])
      ->getMock();
    $acl->expects(self::once())->method('hasBypass')->willReturn(FALSE);
    $acl->expects(self::once())->method('hasAdminAccessCommunity')->willReturn(FALSE);

    $request->expects(self::exactly(2))
      ->method('get')
      ->willReturnMap([
        ['uid', (object) ['target_id' => '2']],
        ['field_community', (object) ['entity' => $community]],
      ]);

    // Fallback on the current user.
    $acl->hasWriteAccessRequest($request);
  }

  /**
   * Ensure the current user will be used when non given.
   *
   * @covers ::hasWriteAccessRequest
   */
  public function testHasWriteAccessRequestGivenUser() {
    $request = $this->createMock(NodeInterface::class);
    $community = $this->createMock(TermInterface::class);

    $anotherCurrentUser = $this->createMock(AccountProxyInterface::class);
    $anotherCurrentUser->expects(self::once())
      ->method('id');
    $this->currentUser->expects(self::never())
      ->method('id');

    $acl = $this->getMockBuilder(AccessControl::class)
      ->onlyMethods(['hasAdminAccessCommunity', 'hasBypass'])
      ->setConstructorArgs([$this->currentUser, $this->entityTypeManager])
      ->getMock();
    $acl->expects(self::once())->method('hasBypass')->willReturn(FALSE);
    $acl->expects(self::once())->method('hasAdminAccessCommunity')->willReturn(FALSE);

    $request->expects(self::exactly(2))
      ->method('get')
      ->willReturnMap([
        ['uid', (object) ['target_id' => '2']],
        ['field_community', (object) ['entity' => $community]],
      ]);

    // Use the given user.
    $acl->hasWriteAccessRequest($request, $anotherCurrentUser);
  }

  /**
   * @covers ::hasWriteAccessRequest
   *
   * @dataProvider hasWriteAccessAuthorship
   */
  public function testHasWriteAccessRequestReturnsExcepted($userId, $requestAuthorId, bool $excepted) {
    $request = $this->createMock(NodeInterface::class);
    $request->expects(self::exactly(2))
      ->method('get')
      ->willReturnMap([
        ['uid', (object) ['target_id' => $requestAuthorId]],
        ['field_community', (object) ['entity' => NULL]],
      ]);

    $this->currentUser->expects(self::once())
      ->method('id')
      ->willReturn($userId);

    // Fallback on the current user.
    $result = $this->acl->hasWriteAccessRequest($request);

    self::assertEquals($excepted, $result);
  }

  /**
   * Ensure the current user will be used when non given.
   *
   * @covers ::isCommunityVolunteer
   */
  public function testIsCommunityVolunteerContextualUser() {
    $community = $this->createMock(TermInterface::class);
    $currentUser = $this->currentUser;
    $anotherCurrentUser = $this->createMock(AccountProxyInterface::class);

    $this->volunteerismRepository->expects(self::exactly(2))
      ->method('getAllByCommunityUser')
      ->willReturnCallback(function (TermInterface $paramCommunity, AccountProxyInterface $paramCurrentUser) use ($community, $currentUser, $anotherCurrentUser): array {
        static $i = 0;
        match (++$i) {
          1 => $this->assertEquals($paramCommunity, $community) && $this->assertEquals($paramCurrentUser, $currentUser),
          2 => $this->assertEquals($paramCommunity, $community) && $this->assertEquals($paramCurrentUser, $anotherCurrentUser),
        };

        return [];
      });

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
