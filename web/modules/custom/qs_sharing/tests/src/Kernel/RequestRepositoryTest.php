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
 * @coversDefaultClass \Drupal\qs_sharing\Repository\RequestRepository
 *
 * @group qs
 * @group qs_sharing
 * @group qs_sharing_kernel
 *
 * @internal
 */
final class RequestRepositoryTest extends KernelTestBase {
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
   * The request repository.
   *
   * @var \Drupal\qs_sharing\Repository\RequestRepository
   */
  protected $requestRepository;

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

    $this->createNodeType('request');

    $workflow = $this->createRequestWorkflow();
    $workflow->getTypePlugin()->addEntityTypeAndBundle('node', 'request');
    $workflow->save();

    // Add the field "Belongs to community" on Request.
    $this->createEntityReferenceField(
      'node',
      'request',
      'field_community',
      NULL,
      'taxonomy_term'
    );

    // Add the field "Belongs to theme" on Request.
    $this->createEntityReferenceField(
      'node',
      'request',
      'field_theme',
      NULL,
      'taxonomy_term'
    );

    // Add the field "Solved by" on Request.
    $this->createEntityReferenceField(
      'node',
      'request',
      'field_solved_by',
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

    $this->request1 = $this->entityTypeManager->getStorage('node')->create([
      'title' => 'Sharing request N°1 by John Doe - Archived',
      'type' => 'request',
      'field_community' => $this->community1,
      'field_theme' => $this->theme1,
      'uid' => $this->user1,
      'moderation_state' => 'archived',
    ]);
    $this->request1->save();

    $this->request2 = $this->entityTypeManager->getStorage('node')->create([
      'title' => 'Sharing request N°2 by John Doe',
      'type' => 'request',
      'field_community' => $this->community1,
      'field_theme' => $this->theme1,
      'uid' => $this->user1,
      'moderation_state' => 'published',
    ]);
    $this->request2->save();

    $this->request3 = $this->entityTypeManager->getStorage('node')->create([
      'title' => 'Sharing request N°3 by John Doe',
      'type' => 'request',
      'field_community' => $this->community2,
      'field_theme' => $this->theme1,
      'uid' => $this->user1,
      'moderation_state' => 'published',
    ]);
    $this->request3->save();

    $this->request4 = $this->entityTypeManager->getStorage('node')->create([
      'title' => 'Sharing request N°4 by Jane Doe - Archived',
      'type' => 'request',
      'field_community' => $this->community1,
      'field_theme' => $this->theme2,
      'uid' => $this->user2,
      'moderation_state' => 'archived',
    ]);
    $this->request4->save();

    $this->request5 = $this->entityTypeManager->getStorage('node')->create([
      'title' => 'Sharing request N°5 by Jane Doe - Solved by John doe',
      'type' => 'request',
      'field_community' => $this->community1,
      'field_theme' => $this->theme1,
      'uid' => $this->user2,
      'moderation_state' => 'solved',
      'field_solved_by' => $this->user1,
    ]);
    $this->request5->save();

    $this->requestRepository = $this->container->get('qs_sharing.repository.request');
  }

  /**
   * @covers ::getAllByCommunity
   */
  public function testGetAllByCommunityReturnsExpected(): void {
    $requests = $this->requestRepository->getAllByCommunity($this->community1);
    self::containsOnlyInstancesOf(NodeInterface::class, $requests);
    self::assertCount(2, $requests);

    $requests = array_values($requests);

    // Ensure the elements are ordered
    // by moderation_state (first published, then solved, archived not shown).
    self::assertSame('Sharing request N°2 by John Doe', $requests[0]->title->value);
    self::assertSame('Sharing request N°5 by Jane Doe - Solved by John doe', $requests[1]->title->value);

    $requests = $this->requestRepository->getAllByCommunity($this->community2);
    self::containsOnlyInstancesOf(NodeInterface::class, $requests);
    self::assertCount(1, $requests);

    // Ensure the elements are ordered
    // by moderation_state (first published, then solved, archived not shown).
    self::assertSame('Sharing request N°3 by John Doe', $requests[3]->title->value);
  }

}
