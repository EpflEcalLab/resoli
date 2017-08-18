<?php

namespace Drupal\qs_acl\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines the Privilege entity.
 *
 * Populate and updated when a user request new privilege on an Entity such:
 *   - Community (Taxonomy Term), to became Member.
 *   - Activity (Node), to became Member.
 *
 * @ContentEntityType(
 *   id = "privilege",
 *   label = @Translation("Privilege"),
 *   base_table = "privileges",
 *   admin_permission = "administer privilege entity",
 *   fieldable = false,
 *   entity_keys = {
 *     "id" = "id"
 *   },
 * )
 */
class Privilege extends ContentEntityBase implements ContentEntityInterface, EntityChangedInterface {

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
  public function getPrivilege() {
    return $this->get('privilege');
  }

  /**
   * {@inheritdoc}
   */
  public function setPrivilege($privilege) {
    // Load services. No way to inject them on EntityInterface.
    $field_manger = \Drupal::service('entity_field.manager');

    // Load the field definitions.
    $bundle_fields = $field_manger->getFieldDefinitions('privilege', 'privilege');
    $field_definition = $bundle_fields['privilege'];
    $allowed_values = $field_definition->getSetting('allowed_values');

    // Set data only if allowed values.
    if (isset($allowed_values[$privilege])) {
      $this->set('privilege', $privilege);
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getBundle() {
    return $this->get('bundle');
  }

  /**
   * {@inheritdoc}
   */
  public function setBundle($bundle) {
    // Load services. No way to inject them on EntityInterface.
    $field_manger = \Drupal::service('entity_field.manager');

    // Load the field definitions.
    $bundle_fields = $field_manger->getFieldDefinitions('privilege', 'privilege');
    $field_definition = $bundle_fields['bundle'];
    $allowed_values = $field_definition->getSetting('allowed_values');

    // Set data only if allowed values.
    if (isset($allowed_values[$bundle])) {
      $this->set('bundle', $bundle);
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntity() {

    // Load services. No way to inject them on EntityInterface.
    $type_manager = \Drupal::service('entity_type.manager');

    // Get the storage according the bundle.
    $storage = $type_manager->getStorage($this->bundle->value);
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
  public function setReviewer(UserInterface $account) {
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
      ->setDescription(new TranslatableMarkup('The ID of the request for privilege.'))
      ->setReadOnly(TRUE);

    $fields['privilege'] = BaseFieldDefinition::create('list_string')
      ->setLabel(new TranslatableMarkup('Privilege'))
      ->setDescription(new TranslatableMarkup('The requested privilege.'))
      ->addConstraint('UniqueField')
      ->setRequired(TRUE)
      ->setSettings([
        'allowed_values' => [
            // Communities bundle privilege.
          'community_managers'   => new TranslatableMarkup('Manage of community'),
          'community_organizers' => new TranslatableMarkup('Organizer of community'),
          'community_members'    => new TranslatableMarkup('Member of community'),
            // Activity bundle privilege.
          'activity_organizers'  => new TranslatableMarkup('Manage of activity'),
          'activity_maintainers' => new TranslatableMarkup('Organizer of activity'),
          'activity_members'     => new TranslatableMarkup('Member of activity'),
        ],
      ]);

    $fields['bundle'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Bundle'))
      ->setDescription(new TranslatableMarkup('The bundle this privilege is attached (community or activity).'))
      ->setRequired(TRUE)
      ->setSettings([
        'allowed_values' => [
          'taxomony_term' => new TranslatableMarkup('Communities (taxomony_term)'),
          'node'          => new TranslatableMarkup('Activity (node)'),
        ],
      ]);

    $fields['entity'] = BaseFieldDefinition::create('integer')
      ->setLabel(new TranslatableMarkup('Entity'))
      ->setDescription(new TranslatableMarkup('The entity ID this privilege is attached.'))
      ->setRequired(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['user'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(new TranslatableMarkup('Used by'))
      ->setDescription(new TranslatableMarkup('The user which request the privilege.'))
      ->setRequired(TRUE)
      ->setSetting('target_type', 'user');

    $fields['reviewer'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(new TranslatableMarkup('Reviewer'))
      ->setDescription(new TranslatableMarkup('The user which operate the review.'))
      ->setSetting('target_type', 'user');

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(new TranslatableMarkup('Request status'))
      ->setDescription(new TranslatableMarkup('A boolean indicating whether the request is pending|accepted|declined.'))
      ->setDefaultValue(NULL);

    $fields['reviewed'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(new TranslatableMarkup('Reviewed'))
      ->setDescription(new TranslatableMarkup('The time that the request has been reviewed.'));

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(new TranslatableMarkup('Created'))
      ->setDescription(new TranslatableMarkup('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(new TranslatableMarkup('Changed'))
      ->setDescription(new TranslatableMarkup('The time that the entity was last edited.'));

    return $fields;
  }

}
