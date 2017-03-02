<?php

namespace Drupal\smile_demo\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Product' Block.
 *
 * @Block(
 *   id = "product_block",
 *   admin_label = @Translation("Product block"),
 * )
 */
class ProductBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return array(
      '#theme' => 'block_esi',
      '#product_id' => 'berghoff_3600381',
    );
  }

}

