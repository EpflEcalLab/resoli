<?php

namespace Drupal\Tests\qs_sharing\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\NodeInterface;
use Drupal\qs_test\NodeTestTrait;
use Drupal\qs_test\SharingWorkflowTestTrait;
use Drupal\qs_test\TaxonomyTestTrait;
use Drupal\qs_test\UserTestTrait;
use Drupal\Tests\field\Traits\EntityReferenceTestTrait;

/**
 * @coversDefaultClass \Drupal\qs_sharing\Repository\OfferRepository
 *
 * @group qs
 * @group qs_sharing
 * @group qs_sharing_kernel
 *
 * @internal
 */
final class OfferRepositoryTest extends KernelTestBase {

  use EntityReferenceTestTrait;
  use NodeTestTrait;
  use SharingWorkflowTestTrait;
  use TaxonomyTestTrait;
  use UserTestTrait;

  /**
   * The Entity Type Manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'user',
    'node',
    'taxonomy',
    'field',
    'qs_sharing',
    'text',
    'filter',
    'system',
    'content_moderation',
    'workflows',
  ];

  /**
   * The offer repository.
   *
   * @var \Drupal\qs_sharing\Repository\OfferRepository
   */
  protected $offerRepository;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    /** @var \Drupal\Core\Entity\EntityTypeManager $entityTypeManager */
    $this->entityTypeManager = $this->container->get('entity_type.manager');

    $this->installSchema('system', ['sequences']);
    $this->installEntitySchema('content_moderation_state');
    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->installEntitySchema('taxonomy_term');
    $this->installConfig(['content_moderation']);

    $this->setupTaxonomy();

    $this->createVocabulary('sharing_themes');
    $this->createVocabulary('communities');

    $this->createNodeType('offer_type');
    $this->createNodeType('offer');

    $workflow = $this->createOfferWorkflow();
    $workflow->getTypePlugin()->addEntityTypeAndBundle('node', 'offer');
    $workflow->save();

    // Add the field "Belongs to community" on Offer's Type.
    $this->createEntityReferenceField(
      'node',
      'offer_type',
      'field_community',
      NULL,
      'taxonomy_term'
    );

    // Add the field "Belongs to an Offer's type" on Offer.
    $this->createEntityReferenceField(
      'node',
      'offer',
      'field_offer_type',
      NULL,
      'node'
    );

    // Add the field "Belongs to theme" on Offer.
    $this->createEntityReferenceField(
      'node',
      'offer',
      'field_theme',
      NULL,
      'taxonomy_term'
    );

    // Add the field "Belongs to user" on Offer.
    $this->createEntityReferenceField(
      'node',
      'offer',
      'field_theme',
      NULL,
      'user'
    );

    $this->user1 = $this->entityTypeManager->getStorage('user')->create([
      'vid' => 'users',
      'name' => $this->randomString(),
    ]);

    $this->user2 = $this->entityTypeManager->getStorage('user')->create([
      'vid' => 'users',
      'name' => $this->randomString(),
    ]);

    $this->community1 = $this->entityTypeManager->getStorage('taxonomy_term')->create([
      'vid' => 'communities',
      'name' => $this->randomString(),
    ]);
    $this->community1->save();

    $this->community2 = $this->entityTypeManager->getStorage('taxonomy_term')->create([
      'vid' => 'communities',
      'name' => $this->randomString(),
    ]);
    $this->community2->save();

    $this->theme1 = $this->entityTypeManager->getStorage('taxonomy_term')->create([
      'vid' => 'sharing_themes',
      'name' => 'Conviviality',
    ]);
    $this->theme1->save();

    $this->theme2 = $this->entityTypeManager->getStorage('taxonomy_term')->create([
      'vid' => 'sharing_themes',
      'name' => 'Mobility',
    ]);
    $this->theme2->save();

    $this->offer_type1 = $this->entityTypeManager->getStorage('node')->create([
      'title' => $this->randomString(),
      'type' => 'offer_type',
      'field_community' => $this->community1,
    ]);
    $this->offer_type1->save();

    $this->offer_type2 = $this->entityTypeManager->getStorage('node')->create([
      'title' => $this->randomString(),
      'type' => 'offer_type',
      'field_community' => $this->community1,
    ]);
    $this->offer_type2->save();

    $this->offer_type3 = $this->entityTypeManager->getStorage('node')->create([
      'title' => $this->randomString(),
      'type' => 'offer_type',
      'field_community' => $this->community2,
    ]);
    $this->offer_type3->save();

    $this->offer_type4 = $this->entityTypeManager->getStorage('node')->create([
      'title' => $this->randomString(),
      'type' => 'offer_type',
      'field_community' => $this->community1,
    ]);
    $this->offer_type4->save();

    $this->offer1 = $this->entityTypeManager->getStorage('node')->create([
      'title' => 'Offer 1',
      'type' => 'offer',
      'field_offer_type' => $this->offer_type1,
      'field_theme' => $this->theme1,
      'uid' => $this->user1,
      'moderation_state' => 'archived',
    ]);
    $this->offer1->save();

    $this->offer2 = $this->entityTypeManager->getStorage('node')->create([
      'title' => 'Offer 2',
      'type' => 'offer',
      'field_offer_type' => $this->offer_type1,
      'field_theme' => $this->theme1,
      'uid' => $this->user1,
      'moderation_state' => 'published',
    ]);
    $this->offer2->save();

    $this->offer3 = $this->entityTypeManager->getStorage('node')->create([
      'title' => 'Offer 3',
      'type' => 'offer',
      'field_offer_type' => $this->offer_type2,
      'field_theme' => $this->theme1,
      'uid' => $this->user1,
      'moderation_state' => 'published',
    ]);
    $this->offer3->save();

    $this->offer4 = $this->entityTypeManager->getStorage('node')->create([
      'title' => 'Offer 4',
      'type' => 'offer',
      'field_offer_type' => $this->offer_type3,
      'field_theme' => $this->theme2,
      'uid' => $this->user2,
      'moderation_state' => 'archived',
    ]);
    $this->offer4->save();

    $this->offer5 = $this->entityTypeManager->getStorage('node')->create([
      'title' => 'Offer 5',
      'type' => 'offer',
      'field_offer_type' => $this->offer_type1,
      'field_theme' => $this->theme1,
      'status' => FALSE,
      'uid' => $this->user2,
      'moderation_state' => 'published',
    ]);
    $this->offer5->save();

    $this->offerRepository = $this->container->get('qs_sharing.repository.offer');
  }

  /**
   * @covers ::getAllByCommunity
   */
  public function testGetAllByCommunityReturnsExpected(): void {
    $offers = $this->offerRepository->getAllByCommunity($this->community1);
    self::containsOnlyInstancesOf(NodeInterface::class, $offers);
    self::assertCount(3, $offers);

    $offers = $this->offerRepository->getAllByCommunity($this->community2);
    self::containsOnlyInstancesOf(NodeInterface::class, $offers);
    self::assertNull($offers);
  }

  /**
   * @covers ::getAllByOffersByTypeByTheme
   */
  public function testGetAllByOffersByTypeByThemeReturnsExpected(): void {
    $offers = $this->offerRepository->getAllByOffersByTypeByTheme($this->offer_type1, $this->theme1);
    self::containsOnlyInstancesOf(NodeInterface::class, $offers);
    self::assertCount(2, $offers);

    $offers = $this->offerRepository->getAllByOffersByTypeByTheme($this->offer_type1, $this->theme2);
    self::assertNull($offers);

    $offers = $this->offerRepository->getAllByOffersByTypeByTheme($this->offer_type2, $this->theme1);
    self::containsOnlyInstancesOf(NodeInterface::class, $offers);
    self::assertCount(1, $offers);

    $offers = $this->offerRepository->getAllByOffersByTypeByTheme($this->offer_type2, $this->theme2);
    self::assertNull($offers);

    $offers = $this->offerRepository->getAllByOffersByTypeByTheme($this->offer_type3, $this->theme1);
    self::assertNull($offers);

    $offers = $this->offerRepository->getAllByOffersByTypeByTheme($this->offer_type3, $this->theme2);
    self::containsOnlyInstancesOf(NodeInterface::class, $offers);
    self::assertNull($offers);
  }

  /**
   * @covers ::getAllOffersByUser
   */
  public function testGetAllOffersByUserReturnsExpected(): void {
    $results = $this->offerRepository->getAllOffersByUser($this->user1, $this->community1);
    self::containsOnlyInstancesOf(NodeInterface::class, $results);
    self::assertCount(3, $results);
    // Ensure the elements are ordered
    // by moderation_state (first published, then archived).
    self::assertSame('Offer 2', $results[0]->title->value);
    self::assertSame('Offer 3', $results[1]->title->value);
    self::assertSame('Offer 1', $results[2]->title->value);

    $results = $this->offerRepository->getAllOffersByUser($this->user2, $this->community2);
    self::assertCount(1, $results);

    $results = $this->offerRepository->getAllOffersByUser($this->user2, $this->community1);
    self::assertCount(1, $results);

    $results = $this->offerRepository->getAllOffersByUser($this->user1, $this->community2);
    self::assertNull($results);
  }

}
