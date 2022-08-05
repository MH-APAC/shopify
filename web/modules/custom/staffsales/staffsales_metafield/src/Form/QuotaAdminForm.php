<?php
/**
 * @file
 * Contains Drupal\staffsales_metafield\Form\QuotaAdminForm.
 */

namespace Drupal\staffsales_metafield\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\staffsales_shopify\StaffsalesShopify;

class QuotaAdminForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'staffsales_metafield_quota_admin_form';
  }

  //shopify metafield key has 30 characters max. limit
  private $metafield_keys = [
    'quota_t1_yr_general_purchase' => NULL,
    'quota_t1_yr_general_amount' => NULL,
    'quota_t1_yr_vip_purchase' => NULL,
    'quota_t1_yr_vip_amount' => NULL,

    'quota_t2_yr_general_purchase' => NULL,
    'quota_t2_yr_general_amount' => NULL,
    'quota_t2_yr_vip_purchase' => NULL,
    'quota_t2_yr_vip_amount' => NULL,

    'quota_t3_yr_general_purchase' => NULL,
    'quota_t3_yr_general_amount' => NULL,
    'quota_t3_yr_vip_purchase' => NULL,
    'quota_t3_yr_vip_amount' => NULL,

    'quota_t4_yr_general_purchase' => NULL,
    'quota_t4_yr_general_amount' => NULL,
    'quota_t4_yr_vip_purchase' => NULL,
    'quota_t4_yr_vip_amount' => NULL,

    'quota_t5_yr_general_purchase' => NULL,
    'quota_t5_yr_general_amount' => NULL,
    'quota_t5_yr_vip_purchase' => NULL,
    'quota_t5_yr_vip_amount' => NULL,

    'quota_t6_yr_general_purchase' => NULL,
    'quota_t6_yr_general_amount' => NULL,
    'quota_t6_yr_vip_purchase' => NULL,
    'quota_t6_yr_vip_amount' => NULL,

    'quota_all_ss_general_purchase' => NULL,
    'quota_all_ss_general_amount' => NULL,
    'quota_all_ss_vip_purchase' => NULL,
    'quota_all_ss_vip_amount' => NULL,

    'quota_overpurchase_quantity' => NULL,
    'quota_overpurchase_email' => NULL,
  ];

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    try {
      $shopify = new StaffsalesShopify();
      $opts = [];
      $opts['query'] = ['namespace' => 'staffsales'];
      $metafields = $shopify->get('metafields', $opts);
      $metafields = $metafields->metafields;

      foreach ($metafields as $i) {
        if (array_key_exists($i->key, $this->metafield_keys)) {
          $this->metafield_keys[$i->key] = $i->value;
        }
      }
    } catch (\Exception $e) {
      \Drupal::messenger()
        ->addError('Cannot load metafields from shop: ' . $e->getMessage());
    }

    foreach ($this->metafield_keys as $k => $v) {
      $k_split = explode('_', $k);
      if (count($k_split) < 5) {
        continue;
      }
      if ($k_split[0] != 'quota') {
        continue;
      }
      $title = '';
      switch ($k_split[2]) {
        case 'yr':
          $title = 'Year Round for ';
          break;
        case 'ss':
          $title = 'Seasonal for ';
          break;
        default:
          break;
      }
      $fieldset_key = $k_split[1] . '_' . $k_split[2];

      if ($k_split[1] == 't1') {
        $title = $title . 'MH (MH)';
      }
      elseif ($k_split[1] == 't2') {
        $title = $title . 'LVMH (LV)';
      }
      elseif ($k_split[1] == 't3') {
        $title = $title . 'Other Corporates (OC)';
      }
      elseif ($k_split[1] == 't4') {
        $title = $title . 'FRIENDS & FAMILY (FF)';
      }
      elseif ($k_split[1] == 't5') {
        $title = $title . 'Others (OP1)';
      }
      elseif ($k_split[1] == 't6') {
        $title = $title . 'Others (OP2)';
      }
      else {
        $title = $title . 'All';
      }

      if (!isset($form[$fieldset_key])) {
        $form[$fieldset_key] = [
          '#type' => 'fieldset',
          '#title' => $title,
          '#description' => '-1 for unlimited. Comma/dot/space/$ is not allowed. Settings are per-month basis.',
        ];
      }

      switch ($k_split[4]) {
        case 'purchase':
          $title = 'Number of Order (' . strtoupper($k_split[3]) . ')';
          break;
        case 'amount':
          $title = 'Purchase Amount (' . strtoupper($k_split[3]) . ')';
          break;
        default:
          break;
      }
      $form[$fieldset_key][$k] = [
        '#type' => 'textfield',
        '#title' => $title,
        '#default_value' => $v,
        '#attributes' => ['autocomplete' => 'off'],
      ];
    }
    $form['overpurchase'] = [
      '#type' => 'fieldset',
      '#title' => 'Over Purchase notification',
      '#description' => 'Notification setting if number of bottles for single order reach limit.',
    ];
    $form['overpurchase']['quota_overpurchase_quantity'] = [
      '#type' => 'textfield',
      '#title' => 'Bottle limit',
      '#default_value' => $this->metafield_keys['quota_overpurchase_quantity'],
      '#attributes' => ['autocomplete' => 'off'],
    ];
    $form['overpurchase']['quota_overpurchase_email'] = [
      '#type' => 'textfield',
      '#title' => 'Notify Email',
      '#default_value' => $this->metafield_keys['quota_overpurchase_email'],
      '#description' => 'To have multiple recipients, separate emails with comma',
      '#attributes' => ['autocomplete' => 'off'],
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
    //clean up the values and se them for submitForm() handling
    //we should also use is_numeric to validate
    $remove = [
      '$',
      '.',
      ',',
    ];
    foreach ($this->metafield_keys as $k => $v) {
      if($k == 'quota_overpurchase_email') {
        continue;
      }
      $value = $form_state->getValue($k);
      $value = trim($value);
      $value = str_replace($remove, '', $value);
      $form_state->setValue($k, $value);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    try {
      //Can we post them all at once?
      $shopify = new StaffsalesShopify();

      foreach ($this->metafield_keys as $k => $v) {
        $type = 'integer';
        if($k == 'quota_overpurchase_email'){
          $type = 'string';
        }
        $value = $form_state->getValue($k);
        $data = [
          'metafield' => [
            'namespace' => 'staffsales',
            'key' => $k,
            'value' => $value,
            'value_type' => $type,
          ],
        ];
        $shopify->post('metafields', $data);
      }

      \Drupal::messenger()->addMessage('Updated successfully');

    } catch (\Exception $e) {
      \Drupal::messenger()->addError('Failed to save: ' . $e->getMessage());
    }
  }
}
