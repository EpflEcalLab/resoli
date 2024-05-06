<?php

namespace Drupal\Tests\qs_sharing\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\NodeInterface;
use Drupal\qs_test\NodeTestTrait;
use Drupal\qs_test\TaxonomyTestTrait;
use Drupal\Tests\field\Traits\EntityReferenceFieldCreationTrait;

/**
 * @coversDefaultClass \Drupal\qs_sharing\Repository\OfferTypeRepository
 *
 * @group qs
 * @group qs_sharing
 * @group qs_sharing_kernel
 *
 * @internal
 */
final class OfferTypeRepositoryTest extends KernelTestBase {

  use EntityReferenceFieldCreationTrait;
  use NodeTestTrait;
  use TaxonomyTestTrait;

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
  ];

  /**
   * The offer's type repository.
   *
   * @var \Drupal\qs_sharing\Repository\OfferTypeRepository
   */
  protected $offerTypeRepository;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    /** @var \Drupal\Core\Entity\EntityTypeManager $entityTypeManager */
    $this->entityTypeManager = $this->container->get('entity_type.manager');

    $this->installSchema('system', ['sequences']);
    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->installEntitySchema('taxonomy_term');
    $this->setupTaxonomy();

    $this->createVocabulary('sharing_themes');
    $this->createVocabulary('communities');

    $this->createNodeType('offer_type');
    $this->createNodeType('offer');

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
      'title' => $this->randomString(),
      'type' => 'offer',
      'field_offer_type' => $this->offer_type1,
      'field_theme' => $this->theme1,
    ]);
    $this->offer1->save();

    $this->offer2 = $this->entityTypeManager->getStorage('node')->create([
      'title' => $this->randomString(),
      'type' => 'offer',
      'field_offer_type' => $this->offer_type1,
      'field_theme' => $this->theme1,
    ]);
    $this->offer2->save();

    $this->offer3 = $this->entityTypeManager->getStorage('node')->create([
      'title' => $this->randomString(),
      'type' => 'offer',
      'field_offer_type' => $this->offer_type2,
      'field_theme' => $this->theme1,
    ]);
    $this->offer3->save();

    $this->offer4 = $this->entityTypeManager->getStorage('node')->create([
      'title' => $this->randomString(),
      'type' => 'offer',
      'field_offer_type' => $this->offer_type3,
      'field_theme' => $this->theme2,
    ]);
    $this->offer4->save();

    $this->offer5 = $this->entityTypeManager->getStorage('node')->create([
      'title' => $this->randomString(),
      'type' => 'offer',
      'field_offer_type' => $this->offer_type1,
      'field_theme' => $this->theme1,
      'status' => FALSE,
    ]);
    $this->offer5->save();

    $this->offer6 = $this->entityTypeManager->getStorage('node')->create([
      'title' => $this->randomString(),
      'type' => 'offer',
      'field_offer_type' => $this->offer_type1,
      'field_theme' => $this->theme2,
    ]);
    $this->offer6->save();

    $this->offerTypeRepository = $this->container->get('qs_sharing.repository.offer_type');
  }

  /**
   * @covers ::getAllByCommunityByThemeWithOffersCount
   */
  public function testGetAllByCommunityByThemeWithOffersCountReturnsExpected(): void {
    $resultsTheme1 = $this->offerTypeRepository->getAllByCommunityByThemeWithOffersCount($this->community1, $this->theme1);
    $resultsTheme2 = $this->offerTypeRepository->getAllByCommunityByThemeWithOffersCount($this->community1, $this->theme2);

    self::containsOnlyInstancesOf(NodeInterface::class, $resultsTheme1);
    self::assertCount(2, $resultsTheme1);
    self::assertEquals(2, $resultsTheme1[0]->offersCount);
    self::assertEquals(1, $resultsTheme1[1]->offersCount);

    self::containsOnlyInstancesOf(NodeInterface::class, $resultsTheme2);
    self::assertCount(1, $resultsTheme2);
    self::assertEquals(1, $resultsTheme2[0]->offersCount);

    $results = $this->offerTypeRepository->getAllByCommunityByThemeWithOffersCount($this->community1, $this->theme2);
    self::assertCount(1, $results);

    $results = $this->offerTypeRepository->getAllByCommunityByThemeWithOffersCount($this->community2, $this->theme1);
    self::assertCount(0, $results);

    $results = $this->offerTypeRepository->getAllByCommunityByThemeWithOffersCount($this->community2, $this->theme2);
    self::containsOnlyInstancesOf(NodeInterface::class, $results);
    self::assertCount(1, $results);
    self::assertEquals(1, $results[0]->offersCount);
  }

  /**
   * @covers ::getAllByCommunity
   */
  public function testGetAllByCommunityReturnsExpected(): void {
    $results = $this->offerTypeRepository->getAllByCommunity($this->community1);
    self::containsOnlyInstancesOf(NodeInterface::class, $results);
    self::assertCount(3, $results);

    $results = $this->offerTypeRepository->getAllByCommunity($this->community2);
    self::containsOnlyInstancesOf(NodeInterface::class, $results);
    self::assertCount(1, $results);
  }

}
