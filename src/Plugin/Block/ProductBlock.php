<?php

namespace Drupal\smile_demo\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'Product' Block.
 *
 * @Block(
 *   id = "product_block",
 *   admin_label = @Translation("Product block"),
 * )
 */
class ProductBlock extends BlockBase implements BlockPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();

    $sku = '';
    if (!empty($config['product_id'])) {
      $sku = $config['product_id'];
    }

    return array(
      '#theme' => 'block_esi',
      '#product_id' => $sku,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    $form['product_sku'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('SKU'),
      '#description' => $this->t('The product identifier'),
      '#default_value' => isset($config['product_id']) ? $config['product_id'] : '',
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['product_id'] = $form_state->getValue('product_sku');
  }

}

