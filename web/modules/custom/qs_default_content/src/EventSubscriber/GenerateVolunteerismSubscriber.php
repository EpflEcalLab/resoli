<?php

namespace Drupal\qs_default_content\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\default_content\Event\DefaultContentEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GenerateVolunteerismSubscriber implements EventSubscriberInterface
{

  /**
   * The Volunteerism storage.
   *
   * @var \Drupal\Core\Entity\ContentEntityStorageInterface
   */
  private $volunteerismStorage;

  /**
   * Class constructor.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->volunteerismStorage = $entity_type_manager->getStorage('volunteerism');
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      DefaultContentEvents::IMPORT => [
        ['generateVolunteerisms', 1000],
      ],
    ];
  }

  /**
   * generate Volunteerism entities
   */
  public function generateVolunteerisms() {
    $volunteerism1 = $this->volunteerismStorage->create(
      [
        'id' => 1,
        'community' => 1,
        'user' => 2,
        'theme' => 21,
      ]
    );
    $volunteerism1->save();

    $volunteerism2 = $this->volunteerismStorage->create(
      [
        'id' => 2,
        'community' => 1,
        'user' => 2,
        'theme' => 20,
      ]
    );
    $volunteerism2->save();

    $volunteerism3 = $this->volunteerismStorage->create(
      [
        'id' => 3,
        'community' => 1,
        'user' => 2,
        'theme' => 23,
      ]
    );
    $volunteerism3->save();

    $volunteerism4 = $this->volunteerismStorage->create(
      [
        'id' => 4,
        'community' => 2,
        'user' => 8,
        'theme' => 23,
      ]
    );
    $volunteerism4->save();

    $volunteerism5 = $this->volunteerismStorage->create(
      [
        'id' => 5,
        'community' => 1,
        'user' => 8,
        'theme' => 23,
      ]
    );
    $volunteerism5->save();

    $volunteerism6 = $this->volunteerismStorage->create(
      [
        'id' => 6,
        'community' => 1,
        'user' => 8,
        'theme' => 20,
      ]
    );
    $volunteerism6->save();
  }
}
