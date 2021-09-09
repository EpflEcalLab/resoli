<?php

namespace Drupal\Tests\qs_sharing\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\qs_test\TaxonomyTestTrait;
use Drupal\Tests\field\Traits\EntityReferenceTestTrait;

/**
 * @coversDefaultClass \Drupal\qs_sharing\Repository\VolunteerismRepository
 *
 * @group qs
 * @group qs_sharing
 * @group qs_sharing_kernel
 *
 * @internal
 */
final class VolunteerismRepositoryTest extends KernelTestBase {
  use EntityReferenceTestTrait;
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
    'taxonomy',
    'text',
    'system',
    'qs_sharing',
  ];

  /**
   * The volunteerism repository.
   *
   * @var \Drupal\qs_sharing\Repository\VolunteerismRepository
   */
  protected $volunteerismRepository;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    /** @var \Drupal\Core\Entity\EntityTypeManager $entityTypeManager */
    $this->entityTypeManager = $this->container->get('entity_type.manager');

    $this->installSchema('system', ['sequences']);
    $this->installEntitySchema('user');
    $this->installEntitySchema('taxonomy_term');

    $this->installEntitySchema('volunteerism');

    $this->volunteerismRepository = $this->container->get('qs_sharing.repository.volunteerism');
  }

  /**
   * @covers ::getAllByUser
   */
  public function testGetAllByUserReturnsExpected(): void {
    $fooUser = $this->entityTypeManager->getStorage('user')->create([
      'mail' => 'foo@example.org',
      'name' => 'foo@example.org',
    ]);
    $fooUser->save();

    $barUser = $this->entityTypeManager->getStorage('user')->create([
      'mail' => 'bar@example.org',
      'name' => 'bar@example.org',
    ]);
    $barUser->save();

    $community1 = $this->entityTypeManager->getStorage('taxonomy_term')->create([
      'vid' => 'communities',
      'name' => $this->randomString(),
    ]);
    $community1->save();

    $community2 = $this->entityTypeManager->getStorage('taxonomy_term')->create([
      'vid' => 'communities',
      'name' => $this->randomString(),
    ]);
    $community2->save();

    $theme1 = $this->entityTypeManager->getStorage('taxonomy_term')->create([
      'vid' => 'sharing_themes',
      'name' => 'Conviviality',
    ]);
    $theme1->save();

    $theme2 = $this->entityTypeManager->getStorage('taxonomy_term')->create([
      'vid' => 'sharing_themes',
      'name' => 'Mobility',
    ]);
    $theme2->save();

    $theme3 = $this->entityTypeManager->getStorage('taxonomy_term')->create([
      'vid' => 'sharing_themes',
      'name' => 'Digital',
    ]);
    $theme3->save();

    $volunteerism1 = $this->entityTypeManager->getStorage('volunteerism')->create([
      'theme' => $theme1,
      'community' => $community1,
      'user' => $fooUser,
    ]);
    $volunteerism1->save();

    $volunteerism2 = $this->entityTypeManager->getStorage('volunteerism')->create([
      'theme' => $theme2,
      'community' => $community1,
      'user' => $fooUser,
    ]);
    $volunteerism2->save();

    $volunteerism3 = $this->entityTypeManager->getStorage('volunteerism')->create([
      'theme' => $theme1,
      'community' => $community1,
      'user' => $barUser,
    ]);
    $volunteerism3->save();

    $volunteerism4 = $this->entityTypeManager->getStorage('volunteerism')->create([
      'theme' => $theme1,
      'community' => $community2,
      'user' => $barUser,
    ]);
    $volunteerism4->save();

    $volunteerisms = $this->volunteerismRepository->getAllByCommunityUser($community1, $barUser);
    self::assertCount(1, $volunteerisms);

    $volunteerisms = $this->volunteerismRepository->getAllByCommunityUser($community2, $barUser);
    self::assertCount(1, $volunteerisms);

    $volunteerisms = $this->volunteerismRepository->getAllByCommunityUser($community1, $fooUser);
    self::assertCount(2, $volunteerisms);

    $volunteerisms = $this->volunteerismRepository->getAllByCommunityUser($community2, $fooUser);
    self::assertNull($volunteerisms);
  }

  /**
   * @covers ::isUserVolunteerForTheme
   */
  public function testIsUserVolunteerForThemeReturnsExpected(): void {
    $fooUser = $this->entityTypeManager->getStorage('user')->create([
      'mail' => 'foo@example.org',
      'name' => 'foo@example.org',
    ]);
    $fooUser->save();

    $barUser = $this->entityTypeManager->getStorage('user')->create([
      'mail' => 'bar@example.org',
      'name' => 'bar@example.org',
    ]);
    $barUser->save();

    $community1 = $this->entityTypeManager->getStorage('taxonomy_term')->create([
      'vid' => 'communities',
      'name' => $this->randomString(),
    ]);
    $community1->save();

    $community2 = $this->entityTypeManager->getStorage('taxonomy_term')->create([
      'vid' => 'communities',
      'name' => $this->randomString(),
    ]);
    $community2->save();

    $theme1 = $this->entityTypeManager->getStorage('taxonomy_term')->create([
      'vid' => 'sharing_themes',
      'name' => 'Conviviality',
    ]);
    $theme1->save();

    $theme2 = $this->entityTypeManager->getStorage('taxonomy_term')->create([
      'vid' => 'sharing_themes',
      'name' => 'Mobility',
    ]);
    $theme2->save();

    $volunteerism1 = $this->entityTypeManager->getStorage('volunteerism')->create([
      'theme' => $theme1,
      'community' => $community1,
      'user' => $fooUser,
    ]);
    $volunteerism1->save();

    $volunteerism = $this->volunteerismRepository->isUserVolunteerForTheme($community1, $fooUser, $theme1);
    self::assertNotNull($volunteerism);
    self::assertEqual($volunteerism1->id(), $volunteerism->id());

    $volunteerism = $this->volunteerismRepository->isUserVolunteerForTheme($community1, $fooUser, $theme2);
    self::assertNull($volunteerism);

    $volunteerism = $this->volunteerismRepository->isUserVolunteerForTheme($community2, $fooUser, $theme1);
    self::assertNull($volunteerism);

    $volunteerism = $this->volunteerismRepository->isUserVolunteerForTheme($community1, $barUser, $theme1);
    self::assertNull($volunteerism);
  }

}
