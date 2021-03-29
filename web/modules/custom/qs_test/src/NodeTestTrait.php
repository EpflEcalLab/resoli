<?php

namespace Drupal\qs_test;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\node\Entity\NodeType;

/**
 * Provides common helper methods for Node module tests.
 */
trait NodeTestTrait {

  /**
   * Create a custom field for node.
   *
   * @param string $name
   *   The field name.
   * @param string $type
   *   The field type.
   * @param int $bundle
   *   The node bundle name.
   * @param array $settings
   *   The fields settings.
   */
  public function createNodeField($name, $type, $bundle, array $settings = []) {
    $field_storage = FieldStorageConfig::create([
      'field_name' => $name,
      'entity_type' => 'node',
      'type' => $type,
      'settings' => $settings,
    ]);
    $field_storage->save();
    $instance = FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => $bundle,
      'label' => $this->randomMachineName(),
    ]);
    $instance->save();
  }

  /**
   * Returns a new node type with random properties.
   */
  public function createNodeType($type = NULL) {
    if (!$type) {
      $type = mb_strtolower($this->randomMachineName());
    }

    // Create a node type.
    $type = NodeType::create([
      'name' => $this->randomMachineName(),
      'type' => $type,
    ]);
    $type->save();

    return $type;
  }

}
