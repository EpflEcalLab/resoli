<?php

namespace Drupal\qs_menu\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Previous Block.
 *
 * @Block(
 *   id = "qs_menu_previous_block",
 *   admin_label = @Translation("Previous Navigation"),
 * )
 */
class PreviousBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build($params = []) {
    return [
      '#theme'     => 'qs_menu_previous_block',
      '#variables' => [],
      '#cache' => [
        'contexts' => [
          'user',
          'url',
        ],
      ],
    ];
  }

}
