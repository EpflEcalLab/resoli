<?php

namespace Drupal\qs_test;

use Drupal\workflows\Entity\Workflow;
use Drupal\workflows\WorkflowInterface;

/**
 * Provides helper methods for the Workflow tests.
 *
 * @see \Drupal\Tests\content_moderation\Traits\ContentModerationTestTrait
 */
trait SharingWorkflowTestTrait {

  /**
   * Adds an entity type ID / bundle ID to the given workflow.
   *
   * @param \Drupal\workflows\WorkflowInterface $workflow
   *   A workflow object.
   * @param string $entity_type_id
   *   The entity type ID to add.
   * @param string $bundle
   *   The bundle ID to add.
   */
  protected function addEntityTypeAndBundleToWorkflow(WorkflowInterface $workflow, $entity_type_id, $bundle) {
    $workflow->getTypePlugin()->addEntityTypeAndBundle($entity_type_id, $bundle);
    $workflow->save();
  }

  /**
   * Creates the offer workflow.
   *
   * @return \Drupal\workflows\Entity\Workflow
   *   The offer workflow entity.
   */
  protected function createOfferWorkflow() {
    $workflow = Workflow::create([
      'type' => 'content_moderation',
      'id' => 'offers',
      'label' => 'Offers',
      'type_settings' => [
        'states' => [
          'archived' => [
            'label' => 'Archived',
            'weight' => 5,
            'published' => FALSE,
            'default_revision' => TRUE,
          ],
          'draft' => [
            'label' => 'Unpublished',
            'published' => FALSE,
            'default_revision' => FALSE,
            'weight' => -5,
          ],
          'published' => [
            'label' => 'Published',
            'published' => TRUE,
            'default_revision' => TRUE,
            'weight' => 0,
          ],
        ],
        'transitions' => [
          'archive' => [
            'label' => 'Archive',
            'from' => ['published', 'draft', 'archived'],
            'to' => 'archived',
            'weight' => 3,
          ],
          'unpublish' => [
            'label' => 'Unpublish',
            'from' => ['published', 'archived'],
            'to' => 'unpublished',
            'weight' => 2,
          ],
          'create_new' => [
            'label' => 'Create New',
            'to' => 'draft',
            'weight' => 0,
            'from' => [
              'draft',
            ],
          ],
          'publish' => [
            'label' => 'Publish',
            'to' => 'published',
            'weight' => 1,
            'from' => [
              'draft',
              'published',
              'archived',
            ],
          ],
        ],
      ],
    ]);
    $workflow->save();

    return $workflow;
  }

  /**
   * Creates the request workflow.
   *
   * @return \Drupal\workflows\Entity\Workflow
   *   The request workflow entity.
   */
  protected function createRequestWorkflow() {
    $workflow = Workflow::create([
      'type' => 'content_moderation',
      'id' => 'requests',
      'label' => 'Requests',
      'type_settings' => [
        'states' => [
          'archived' => [
            'label' => 'Archived',
            'weight' => 5,
            'published' => FALSE,
            'default_revision' => TRUE,
          ],
          'draft' => [
            'label' => 'Unpublished',
            'published' => FALSE,
            'default_revision' => FALSE,
            'weight' => -5,
          ],
          'published' => [
            'label' => 'Published',
            'published' => TRUE,
            'default_revision' => TRUE,
            'weight' => 0,
          ],
          'solved' => [
            'label' => 'Solved',
            'published' => TRUE,
            'default_revision' => FALSE,
            'weight' => 2,
          ],
        ],
        'transitions' => [
          'archive' => [
            'label' => 'Archive',
            'from' => ['published', 'draft', 'archived', 'solved'],
            'to' => 'archived',
            'weight' => 3,
          ],
          'solve' => [
            'label' => 'Solve',
            'from' => ['published', 'solved'],
            'to' => 'solved',
            'weight' => 2,
          ],
          'create_new' => [
            'label' => 'Create New',
            'to' => 'draft',
            'weight' => 0,
            'from' => [
              'draft',
            ],
          ],
          'publish' => [
            'label' => 'Publish',
            'to' => 'published',
            'weight' => 1,
            'from' => [
              'draft',
              'published',
              'archived',
            ],
          ],
        ],
      ],
    ]);
    $workflow->save();

    return $workflow;
  }

}
