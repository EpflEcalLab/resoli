<?php

namespace Drupal\Tests\qs_sharing\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\NodeInterface;
use Drupal\qs_test\NodeTestTrait;
use Drupal\qs_test\TaxonomyTestTrait;
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
    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->installEntitySchema('taxonomy_term');
    $this->setupTaxonomy();

    $this->createVocabulary('sharing_themes');

    $this->createNodeType('offer_type');
    $this->createNodeType('offer');

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
    ]);
    $this->offer_type1->save();

    $this->offer_type2 = $this->entityTypeManager->getStorage('node')->create([
      'title' => $this->randomString(),
      'type' => 'offer_type',
    ]);
    $this->offer_type2->save();

    $this->offer_type3 = $this->entityTypeManager->getStorage('node')->create([
      'title' => $this->randomString(),
      'type' => 'offer_type',
    ]);
    $this->offer_type3->save();

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

    $this->offerRepository = $this->container->get('qs_sharing.repository.offer');
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
    self::assertCount(1, $offers);
  }

}
