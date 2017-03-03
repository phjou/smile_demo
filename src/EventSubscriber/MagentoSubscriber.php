<?php
namespace Drupal\smile_demo\EventSubscriber;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;


class MagentoSubscriber implements EventSubscriberInterface {
  /**
  * {@inheritdoc}
  */
  static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = array('createMagentoUser');
    return $events;
  }

  /**
   * Redirect to an "Access Forbidden" when a user try to create an account by the page for anonymous users
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   * @throws AccessDeniedHttpException
   */
  public function createMagentoUser(GetResponseEvent $event) {
    $current_path = \Drupal::service('path.current')->getPath();
    if ($current_path == '/user/register' && !empty($_POST)) {
      $userData = [
        "customer" => [
          "firstname" => $_POST['field_firstname'][0]['value'],
          "lastname" => $_POST['field_lastname'][0]['value'],
          "email" => $_POST['mail'],
        ],
        "password" => $_POST['pass']['pass1']
      ];
      $ch = curl_init("http://ixina-fr.fbd.vitry.intranet/index.php/rest/V1/customers");
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));

      $req = curl_exec($ch);
      $return = json_decode($req);
      if (!empty($return->id)) {
        $tempstore = \Drupal::service('user.private_tempstore')->get('magento_data');
        $tempstore->set('magento_id', $return->id);
      }
    }
  }
}

