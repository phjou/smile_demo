<?php

namespace Drupal\smile_demo\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'User' Block.
 *
 * @Block(
 *   id = "magento_user_block",
 *   admin_label = @Translation("Magento User block"),
 * )
 */
class UserBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $tempstore = \Drupal::service('user.private_tempstore')->get('demo_magento');
    $token = $tempstore->get('magento_token');    
    
    $print = '';
    if (!empty($token)) {
      $ch = curl_init("http://ixina-fr.fbd.vitry.intranet/index.php/rest/V1/customers/me");
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));
 
      $result = curl_exec($ch);
 
      $result_object = json_decode($result);
   
      $print .= $this->formatAddress($result_object, 'default_billing');
      $print .= $this->formatAddress($result_object, 'default_shipping');
    }
   
    return array(
      '#markup' => $print,
    );
  }

  public function formatAddress($result_object, $type) {
    $address = '';
    if (!empty($result_object->{$type})) {
      $address_string = '';
      foreach ($result_object->addresses as $address) {
        if ($address->id == $result_object->{$type}) {
          $address_string .= '<p><b>' . $type . '</b><br/>';
          if (!empty($address->street[0])) {
            $address_string .= $address->street[0];
          }
          if (!empty($address->postcode)) {
            $address_string .= ' ' . $address->postcode;
          }
          if (!empty($address->city)) {
            $address_string .= ' ' . $address-> city;
          }
          if (!empty($address->telephone)) {
            $address_string .= '<br/> Tel: ' . $address-> telephone;
          }

          $address_string .= '</p>';
        }
      }
    }
    return $address_string;
  }
}

