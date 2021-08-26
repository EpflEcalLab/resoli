<?php

namespace Drupal\qs_sharing\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\taxonomy\TermInterface;
use Drupal\user\UserInterface;

/**
 * Defines the sharing Volunteerism entity.
 *
 * Created or deleted when a user become volunteer on a Community for a Themes.
 *
 * @ContentEntityType(
 *     id="volunteerism",
 *     label=@Translation("Volunteerism"),
 *     handlers={
 *         "view_builder": "Drupal\Core\Entity\EntityViewBuilder",
 *         "views_data": "Drupal\views\EntityViewsData",
 *         "form": {
 *             "default": "Drupal\Core\Entity\ContentEntityForm",
 *         },
 *     },
 *     base_table="volunteerisms",
 *     admin_permission="administer volunteerism entity",
 *     fieldable=false,
 *     entity_keys={
 *         "id": "id",
 *     },
 *     links={
 *         "collection": "/admin/content/volunteerisms",
 *     },
 * )
 */
class Volunteerism extends ContentEntityBase {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = [];

    // Standard field, used as unique id primary index.
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(new TranslatableMarkup('ID'))
      ->setDescription(new TranslatableMarkup('The ID of the involvement as a volunteer.'))
      ->setReadOnly(TRUE);

    $fields['community'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(new TranslatableMarkup('Community'))
      ->setDescription(new TranslatableMarkup('The community for which the user volunteers.'))
      ->setRequired(TRUE)
      ->setSetting('target_type', 'taxonomy_term');

    $fields['user'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(new TranslatableMarkup('Volunteer'))
      ->setDescription(new TranslatableMarkup('The user volunteering.'))
      ->setRequired(TRUE)
      ->setSetting('target_type', 'user');

    $fields['theme'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(new TranslatableMarkup('Theme'))
      ->setDescription(new TranslatableMarkup('The sharing theme for which the user is volunteering.'))
      ->setRequired(TRUE)
      ->setSetting('target_type', 'taxonomy_term');

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(new TranslatableMarkup('Created'))
      ->setDescription(new TranslatableMarkup('When the request was created.'));

    return $fields;
  }

  /**
   * Get the community for which the user volunteers.
   */
  public function getCommunity() {
    return $this->get('community');
  }

  /**
   * Get the volunteering created timestamp.
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * Get the sharing theme for which the user is volunteering.
   */
  public function getTheme() {
    return $this->get('theme');
  }

  /**
   * Get the user volunteering.
   */
  public function getVolunteer() {
    return $this->get('user');
  }

  /**
   * Set the community for which the user volunteers.
   */
  public function setCommunity(TermInterface $community): self {
    $this->set('community', $community);

    return $this;
  }

  /**
   * Set the sharing theme for which the user is volunteering.
   */
  public function setTheme(TermInterface $theme): self {
    $this->set('theme', $theme);

    return $this;
  }

  /**
   * Set the user volunteering.
   */
  public function setVolunteer(UserInterface $account): self {
    $this->set('user', $account->id());

    return $this;
  }

}
