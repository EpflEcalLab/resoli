<?php

namespace Drupal\qs_photo\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_activity\Service\EventManager;
use Drupal\qs_photo\Service\PhotoManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * ActivityController.
 */
class ActivityController extends ControllerBase {

  /**
   * The entity QS Event Manager.
   *
   * @var \Drupal\qs_activity\Service\EventManager
   */
  protected $eventManager;

  /**
   * The node Storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * The entity QS Photo Manager.
   *
   * @var \Drupal\qs_photo\Service\PhotoManager
   */
  protected $photoManager;

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, PhotoManager $photo_manager, EventManager $event_manager) {
    $this->acl = $acl;
    $this->photoManager = $photo_manager;
    $this->eventManager = $event_manager;
    $this->nodeStorage = $this->entityTypeManager()->getStorage('node');
  }

  /**
   * Checks access.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\node\NodeInterface $activity
   *   Run access checks for this activity.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account, NodeInterface $activity) {
    $access = AccessResult::forbidden();

    if ($this->acl->hasAccessPhoto($activity)) {
      $access = AccessResult::allowed();
    }

    return $access;
  }

  /**
   * Collection by months.
   */
  public function activity(Request $request, NodeInterface $activity) {
    $variables = ['activity' => $activity];

    // Get all past events for the given activity by event end date.
    $variables['events'] = $this->eventManager->getAllPrev($activity);

    // Get all photos for the given activity by event end date.
    $photos = $this->photoManager->getByActivity($activity, NULL);

    // Order photos by events.
    $variables['photos'] = [];

    if ($photos) {
      foreach ($photos as $photo) {
        $variables['photos'][$photo->field_event->target_id][] = $photo;
      }
    }

    return [
      '#theme' => 'qs_photo_collection_by_activity_page',
      '#variables' => $variables,
      '#attached' => [
        'library' => [
          'quartiers_solidaires/photoswipe',
        ],
      ],
      '#cache' => [
        'contexts' => [
          'user',
          'url.query_args',
        ],
        'tags' => $this->getCacheTags($activity, $photos),
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
    // Load customs services used in this class.
      $container->get('qs_acl.access_control'),
      $container->get('qs_photo.photo_manager'),
      $container->get('qs_activity.event_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags(NodeInterface $activity, ?array $photos = NULL) {
    $tags[] = 'node:' . $activity->id();
    $tags = [
      // Invalidated whenever any Photo is updated, deleted or created.
      'node_list:photo',
      // Invalidated whenever any Privilege is updated, deleted or created.
      'privilege_list:privilege',
    ];

    if ($photos) {
      foreach ($photos as $photo) {
        $tags[] = 'node:' . $photo->id();
      }
    }

    return $tags;
  }

}
