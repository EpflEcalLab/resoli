<?php

namespace Drupal\qs_acl\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\user\UserInterface;

/**
 * Defines the Privilege entity.
 *
 * Populated and updated when a user requests a new privilege on an Entity such
 * as:
 *   - Community (Taxonomy Term), to become Member.
 *   - Activity (Node), to become Member.
 *
 * @ContentEntityType(
 *     id="privilege",
 *     label=@Translation("Privilege"),
 *     handlers={
 *         "view_builder": "Drupal\Core\Entity\EntityViewBuilder",
 *         "views_data": "Drupal\views\EntityViewsData",
 *         "form": {
 *             "default": "Drupal\Core\Entity\ContentEntityForm",
 *         },
 *     },
 *     base_table="privileges",
 *     admin_permission="administer privilege entity",
 *     fieldable=false,
 *     entity_keys={
 *         "id": "id"
 *     },
 *     links={
 *         "collection": "/admin/content/privileges",
 *     },
 * )
 */
class Privilege extends ContentEntityBase implements ContentEntityInterface, EntityChangedInterface {

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
          'community_managers' => new TranslatableMarkup('Community Manager'),
          'community_organizers' => new TranslatableMarkup('Community Organizer'),
          'community_members' => new TranslatableMarkup('Community Member'),
          // Activity bundle privilege.
          'activity_organizers' => new TranslatableMarkup('Activity Organizer'),
          'activity_maintainers' => new TranslatableMarkup('Activity Maintainer'),
          'activity_members' => new TranslatableMarkup('Activity Member'),
        ],
      ]);

    $fields['bundle'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Bundle'))
      ->setDescription(new TranslatableMarkup('The bundle this privilege is attached to (community or activity).'))
      ->setRequired(TRUE)
      ->setSettings([
        'allowed_values' => [
          'taxonomy_term' => new TranslatableMarkup('Communities (taxonomy_term)'),
          'node' => new TranslatableMarkup('Activity (node)'),
        ],
      ]);

    $fields['entity'] = BaseFieldDefinition::create('integer')
      ->setLabel(new TranslatableMarkup('Entity'))
      ->setDescription(new TranslatableMarkup('The entity ID this privilege is attached to.'))
      ->setRequired(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['user'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(new TranslatableMarkup('Used by'))
      ->setDescription(new TranslatableMarkup('The user requesting the privilege.'))
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

  /**
   * {@inheritdoc}
   */
  public function getBundle() {
    return $this->get('bundle');
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
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntity() {
    // Load services. No way to inject them on EntityInterface.
    $type_manager = \Drupal::service('entity_type.manager');

    // Get the storage according the bundle.
    /** @var \Drupal\Core\Entity\ContentEntityStorageInterface|null $storage */
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
  public function getOwner() {
    return $this->get('user');
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
  public function getReviewedTime() {
    return $this->get('reviewed')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getReviewer() {
    return $this->get('reviewer');
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
  public function setBundle($bundle) {
    // Load services. No way to inject them on EntityInterface.
    $field_manager = \Drupal::service('entity_field.manager');

    // Load the field definitions.
    $bundle_fields = $field_manager->getFieldDefinitions('privilege', 'privilege');
    $field_definition = $bundle_fields['bundle'];
    $allowed_values = $field_definition->getSetting('allowed_values');

    // Set data only if allowed values.
    if (!isset($allowed_values[$bundle])) {
      throw new \Exception(new TranslatableMarkup('Privilege entity not allowed value :value for bundle.', [':value' => $bundle]));
    }

    $this->set('bundle', $bundle);

    return $this;
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
  public function setEntity(EntityInterface $entity) {
    $this->set('entity', $entity);

    return $this;
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
  public function setPrivilege($privilege) {
    // Load services. No way to inject them on EntityInterface.
    $field_manager = \Drupal::service('entity_field.manager');

    // Load the field definitions.
    $bundle_fields = $field_manager->getFieldDefinitions('privilege', 'privilege');
    $field_definition = $bundle_fields['privilege'];
    $allowed_values = $field_definition->getSetting('allowed_values');

    // Set data only if allowed values.
    if (!isset($allowed_values[$privilege])) {
      throw new \Exception(new TranslatableMarkup('Privilege entity not allowed value :value for privilege.', [':value' => $privilege]));
    }

    $this->set('privilege', $privilege);

    return $this;
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
  public function setReviewer(AccountInterface $account) {
    $this->set('reviewer', $account->id());

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setStatus($status) {
    $this->set('status', $status);

    return $this;
  }

}
