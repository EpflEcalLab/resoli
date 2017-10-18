<?php

namespace Drupal\qs_subscription\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines the Subscription entity.
 *
 * Populated and updated when a user requests a new subscriptions on an Event.
 *
 * @ContentEntityType(
 *   id = "subscription",
 *   label = @Translation("Subscription"),
 *   base_table = "subscriptions",
 *   admin_permission = "administer subscription entity",
 *   fieldable = false,
 *   entity_keys = {
 *     "id" = "id"
 *   },
 * )
 */
class Subscription extends ContentEntityBase implements ContentEntityInterface, EntityChangedInterface {

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getChangedTime() {
    return $this->get('changed')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setChangedTime($timestamp) {
    $this->set('changed', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getChangedTimeAcrossTranslations() {
    $changed = $this->getUntranslated()->getChangedTime();
    foreach ($this->getTranslationLanguages(FALSE) as $language) {
      $translation_changed = $this->getTranslation($language->getId())->getChangedTime();
      $changed = max($translation_changed, $changed);
    }
    return $changed;
  }

  /**
   * {@inheritdoc}
   */
  public function getReviewedTime() {
    return $this->get('reviewed')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setReviewedTime($timestamp) {
    $this->set('reviewed', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user');
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntity() {
    // Load services. No way to inject them on EntityInterface.
    $type_manager = \Drupal::service('entity_type.manager');

    // Get the storage according the event.
    $storage = $type_manager->getStorage('node');
    if (empty($storage)) {
      return NULL;
    }

    // Load the entity.
    $entity = $storage->load($this->entity->value);
    if (empty($entity)) {
      return NULL;
    }

    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setEntity(EntityInterface $entity) {
    $this->set('entity', $entity);
    return $this;
  }

  /**
   * {@inheritdoc}
   *
   * How to humanize the boolean status:
   *  - pending: status = NULL & reviewer == NULL
   *  - accepted: status = 1 & reviewer != NULL
   *  - declined: status = 0 & reviewer != NULL.
   */
  public function getStatus() {
    return $this->get('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setStatus($status) {
    $this->set('status', $status);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getReviewer() {
    return $this->get('reviewer');
  }

  /**
   * {@inheritdoc}
   */
  public function setReviewer(AccountInterface $account) {
    $this->set('reviewer', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    // Standard field, used as unique if primary index.
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(new TranslatableMarkup('ID'))
      ->setDescription(new TranslatableMarkup('The ID of the request for subscription.'))
      ->setReadOnly(TRUE);

    $fields['entity'] = BaseFieldDefinition::create('integer')
      ->setLabel(new TranslatableMarkup('Entity'))
      ->setDescription(new TranslatableMarkup('The entity ID this subscription is attached to.'))
      ->setRequired(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['user'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(new TranslatableMarkup('Used by'))
      ->setDescription(new TranslatableMarkup('The user requesting the subscription.'))
      ->setRequired(TRUE)
      ->setSetting('target_type', 'user');

    $fields['reviewer'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(new TranslatableMarkup('Reviewer'))
      ->setDescription(new TranslatableMarkup('The user reviewing the request.'))
      ->setSetting('target_type', 'user');

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(new TranslatableMarkup('Request status'))
      ->setDescription(new TranslatableMarkup('A boolean indicating whether the request is pending|accepted|declined.'))
      ->setDefaultValue(NULL);

    $fields['reviewed'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(new TranslatableMarkup('Reviewed'))
      ->setDescription(new TranslatableMarkup('When the request was reviewed.'));

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(new TranslatableMarkup('Created'))
      ->setDescription(new TranslatableMarkup('When the request was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(new TranslatableMarkup('Changed'))
      ->setDescription(new TranslatableMarkup('When the request was last edited.'));

    return $fields;
  }

}
