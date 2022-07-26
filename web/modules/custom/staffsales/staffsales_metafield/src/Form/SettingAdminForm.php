<?php

/**
 * @file
 * Contains Drupal\staffsales_metafield\Form\SettingAdminForm.
 */

namespace Drupal\staffsales_metafield\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\staffsales_shopify\StaffsalesShopify;

class SettingAdminForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'staffsales_metafield_setting_admin_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $seasonal_enabled = NULL;
    $seasonal_begin = NULL;
    $seasonal_end = NULL;
    try {
      $shopify = new StaffsalesShopify();
      $opts = [];
      $opts['query'] = ['namespace' => 'staffsales'];
      $metafields = $shopify->get('metafields', $opts);
      $metafields = $metafields->metafields;

      foreach ($metafields as $i){
        switch ($i->key){
          case 'seasonal_enabled':
            $seasonal_enabled = $i->value;
            break;
          case 'seasonal_begin':
            $seasonal_begin = $i->value;
            break;
          case 'seasonal_end':
            $seasonal_end = $i->value;
            break;
          default:
            break;
        }
      }
    } catch (\Exception $e) {
      \Drupal::messenger()->addError('Cannot load metafields from shop: ' . $e->getMessage());
    }

    $form['actions']['seasonal_enabled'] = [
      '#type' => 'checkbox',
      '#title' => 'Enable Seasonal Sales',
      '#description' => 'You must tick this box for the scheduling below to work.  If this box is un-tick, the Seasonal shop will block users login regardless of the scheduling.',
      '#default_value' => $seasonal_enabled,
      '#attributes' => array('autocomplete' => 'off'),
    ];

    $form['actions']['seasonal_begin'] = [
      '#type' => 'textfield',
      '#title' => 'Seasonal Sales start time',
      '#description' => 'e.g. 2017-11-27 10:30:00',
      '#default_value' => $seasonal_begin,
      '#attributes' => array('autocomplete' => 'off'),
    ];

    $form['actions']['seasonal_end'] = [
      '#type' => 'textfield',
      '#title' => 'Seasonal Sales end time',
      '#description' => 'e.g. 2017-11-30 23:00:00',
      '#default_value' => $seasonal_end,
      '#attributes' => array('autocomplete' => 'off'),
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => 'Save',
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $seasonal_enabled = $form_state->getValue('seasonal_enabled');
    $seasonal_begin = trim($form_state->getValue('seasonal_begin'));
    $seasonal_end = trim($form_state->getValue('seasonal_end'));

    try {
      //Can we post them all at once?
      $shopify = new StaffsalesShopify();
      $data = [
        'metafield' => [
          'namespace' => 'staffsales',
          'key' => 'seasonal_enabled',
          'value' => $seasonal_enabled,
          'value_type' => 'integer',
          ]
      ];
      $r = $shopify->post('metafields', $data);

      $data = [
        'metafield' => [
          'namespace' => 'staffsales',
          'key' => 'seasonal_begin',
          'value' => $seasonal_begin,
          'value_type' => 'string',
          ]
      ];
      $r = $shopify->post('metafields', $data);

      $data = [
        'metafield' => [
          'namespace' => 'staffsales',
          'key' => 'seasonal_end',
          'value' => $seasonal_end,
          'value_type' => 'string',
          ]
      ];
      $r = $shopify->post('metafields', $data);
      \Drupal::messenger()->addMessage('Updated successfully');

    } catch (\Exception $e) {
      \Drupal::messenger()->addError('Failed to save: ' . $e->getMessage());
    }
  }
}
