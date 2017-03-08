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

    $print = '';
    if (!empty($token)) {
      $ch = curl_init("http://ixina-fr.fbd.clients.smile.fr/index.php/rest/V1/customers/me");
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));
 
      $result = curl_exec($ch);
      $result_object = json_decode($result);

      $userData = array("username" => "dacou", "password" => "Magent_0");
      $ch = curl_init("http://ixina-fr.fbd.clients.smile.fr/index.php/rest/V1/integration/admin/token");
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));

      $token = curl_exec($ch);

      $ch = curl_init("http://ixina-fr.fbd.clients.smile.fr/index.php/rest/V1/orders?searchCriteria[filter_groups][0][filters][0][field]=customer_email&searchCriteria[filter_groups][0][filters][0][value]=" . $result_object->email);

      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));

      $result = curl_exec($ch);
      $ordersData = json_decode($result);
      $themeDatas = [];
      foreach($ordersData as $orders) {
        foreach($orders as $order) {
          $id = $order->entity_id;
          $themeDatas[$id]['base_currency_code'] = $order->base_currency_code;
          $themeDatas[$id]['subtotal'] = $order->subtotal;
          foreach($order->items as $item) {
            $themeDatas[$id]['items'][] = [
              'name' => $item->name,
              'price' => $item->row_total,
              'quantity' => $item->qty_ordered
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

