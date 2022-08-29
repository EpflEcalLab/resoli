<?php

namespace Drupal\qs_default_content\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\default_content\Event\DefaultContentEvents;
use Drupal\default_content\Event\ImportEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Generate default users and Update existing admin to ensure roles.
 */
class UserSubscriber implements EventSubscriberInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new UserSubscriber object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      DefaultContentEvents::IMPORT => [
        ['setupAdministrator', 1],
      ],
    ];
  }

  /**
   * Ensure the user ID 1 is an administrator.
   *
   * @param \Drupal\default_content\Event\ImportEvent $event
   *   The Import event.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function setupAdministrator(ImportEvent $event): void {
    $user_storage = $this->entityTypeManager->getStorage('user');
    /** @var \Drupal\user\UserInterface $admin */
    $admin = $user_storage->load(1);
    $admin->addRole('administrator');
    $admin->save();
  }

}
