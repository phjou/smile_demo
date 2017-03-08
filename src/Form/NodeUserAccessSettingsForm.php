<?php

namespace Drupal\smile_demo\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;

/**
 * Configure example settings for this site.
 */
class NodeUserAccessSettingsForm extends ConfigFormBase {
  /** 
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'node_special_access_settings';
  }

  /** 
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'node.special_access.settings',
    ];
  }

  /** 
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('node.special_access.settings');

    $query = \Drupal::entityQuery('user');
    $ids = $query->execute();
    $users = User::loadMultiple($ids);

    $nid = \Drupal::routeMatch()->getParameter('node')->id();

    $options = [];
    foreach ($users as $user) {
      if (!empty($user->getAccountName())) {
        $options[$user->id()] = $user->getAccountName();
      }
    }

    $form['user_special_access'] = array(
      '#type' => 'checkboxes',
      '#options' => $options,
      '#title' => $this->t('Authorized users'),
      '#default_value' => explode('+', $config->get('authorized_users_' . $nid)),
    );  

    return parent::buildForm($form, $form_state);
  }

  /** 
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration

    $nid = \Drupal::routeMatch()->getParameter('node')->id();

    $user_config = implode($form_state->getValue('user_special_access'), '+');
    $this->config('node.special_access.settings')
      // Set the submitted configuration setting
      ->set('authorized_users_' . $nid, $user_config)
      ->save();

    parent::submitForm($form, $form_state);
  }
}

