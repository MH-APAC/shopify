<?php

/**
 * @file
 * Contains Drupal\staffsales_access\Form\ConfigForm.
 */

namespace Drupal\staffsales_access\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class ConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'staffsales_access',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'staffsales_access_admin_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('staffsales_access.settings');

    $form['allowed_ip'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Allowed IP'),
      '#description' => $this->t('Allowed IP address in multiple lines'),
      '#default_value' => $config->get('allowed_ip'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->configFactory->getEditable('staffsales_access.settings')
      ->set('allowed_ip', $form_state->getValue('allowed_ip'))
      ->save();
  }
}
