<?php

namespace Drupal\smile_demo\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'Orders' Block.
 *
 * @Block(
 *   id = "magento_orders_block",
 *   admin_label = @Translation("Magento Orders block"),
 * )
 */
class OrdersBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $tempstore = \Drupal::service('user.private_tempstore')->get('demo_magento');
    $token = $tempstore->get('magento_token');    
    
    $ordersData = NULL;

    $header = [
      'product' => t('Produit'),
      'price' => t('Prix'),
      'quantity' => t('Quantité'),
    ];

    $print = '';
    if (!empty($token)) {
      $ch = curl_init("http://ixina-fr.fbd.vitry.intranet/index.php/rest/V1/customers/me");
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));
 
      $result = curl_exec($ch);
      $result_object = json_decode($result);

      $userData = array("username" => "dacou", "password" => "Magent_0");
      $ch = curl_init("http://ixina-fr.fbd.vitry.intranet/index.php/rest/V1/integration/admin/token");
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));

      $token = curl_exec($ch);

      $ch = curl_init("http://ixina-fr.fbd.vitry.intranet/index.php/rest/V1/orders?searchCriteria[filter_groups][0][filters][0][field]=customer_email&searchCriteria[filter_groups][0][filters][0][value]=" . $result_object->email);

      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));

      $result = curl_exec($ch);
      $ordersData = json_decode($result);
      $themeDatas = [];

      foreach($ordersData as $orders) {
        foreach($orders as $order) {
          $id = $order->entity_id;
          if (!empty($id)) {
            $themeDatas[$id]['id'] = $id;
            $themeDatas[$id]['base_currency_code'] = $order->base_currency_code;
            $themeDatas[$id]['subtotal'] = $order->subtotal;
            $rows = [];
            foreach($order->items as $item) {
              $rows[] = [         
                'product' => $item->name,
                'price' => $item->row_total . ' ' . $themeDatas[$id]['base_currency_code'],
                'quantity' => $item->qty_ordered
              ];
            }
            $themeDatas[$id]['products'] = [
              '#type' => 'table',
              '#header' => $header,
              '#rows' => $rows
            ];
          }
        }
      }
    }
    
    return array(
      '#theme' => 'orders_block',
      '#orders' => $themeDatas,
    );
  }

}

