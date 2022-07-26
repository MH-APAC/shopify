<?php

/**
 * @file
 * Contains Drupal\staffsales_shopify\Form\VariantSplitForm.
 */

namespace Drupal\staffsales_shopify\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\staffsales_shopify\StaffsalesShopify;

class VariantSplitForm extends FormBase {

  private $label_prefix = [
    'MH_',
    'GR_',
    //'SS_',
  ];

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'staffsales_shopify_variant_split_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    //$products = array();

    try {
      $shopify = new StaffsalesShopify();
      $opts = [];
      $r = $shopify->get('products/count', $opts);
      $products_count = $r->count;
      $form['actions']['product_count'] = [
        '#type' => 'item',
        '#title' => 'Product count on shop: ',
        '#markup' => $products_count,
      ];

      $opts = [];
      $opts['query'] = [
        'limit' => 250,
        'page' => 1,
        'fields' => 'id,title,handle,variants',
        'order' => 'title asc',//desc
      ];
      $r = $shopify->get('products', $opts);

      $form['actions']['product_count_current_page'] = [
        '#type' => 'item',
        '#title' => 'Product count on this page: ',
        '#markup' => count($r->products),
      ];


      foreach($r->products as $i){
        $skip = FALSE;
        $options_label = [];
        foreach($i->variants as $j){
          $options_label[] = $j->title;
          $p = substr($j->title, 0, 2);
          if(in_array($p, $this->label_prefix)){
            $skip = TRUE;
          }
        }

        $title = $i->title.'('.$i->handle.')';
        $desc = implode(', ', $options_label);
        if($skip){
          $form['actions']['readonly_'.$i->id] = [
            '#type' => 'textfield',
            '#title' => $title,
            '#description' => '',
            '#default_value' => $desc,
            '#attributes' => array('readonly' => 'readonly'),
          ];
        }else{
          $form['actions']['product_id_'.$i->id] = [
            '#type' => 'checkbox',
            '#title' => $title,
            '#description' => $desc,
            '#default_value' => $i->id,
            '#attributes' => array('autocomplete' => 'off'),
          ];
        }
      }
    } catch (\Exception $e) {
      \Drupal::messenger()->addError('Cannot load products from shop: ' . $e->getMessage());
    }

    $form['actions']['page'] = [
      '#type' => 'textfield',
      '#title' => 'Page',
      '#default_value' => 1,
      '#attributes' => array('autocomplete' => 'off'),
    ];

    $form['actions']['select_all'] = [
      '#type' => 'button',
      '#value' => 'Select All',
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => 'Convert',
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
    $values = $form_state->getValues();
    foreach($values as $i => $j){
      if((stripos($i, 'product_id_') === 0) && $j == 1){
        try {
          $product_id = str_replace('product_id_', '', $i);
          $shopify = new StaffsalesShopify();
          $opts = [];
          $opts['query'] = [
            'fields' => 'id,title,handle,variants'
          ];
          $r = $shopify->get('products/'.$product_id, $opts);
          $skip = FALSE;
          $options = [];
          $options_label = '';
          if($r->product){
            $r = $r->product;
          }
          $handle = $r->handle;
          foreach($r->variants as $m){
            $o = get_object_vars($m);
            unset($o['created_at']);
            unset($o['updated_at']);
            unset($o['barcode']);
            unset($o['grams']);
            unset($o['image_id']);
            unset($o['weight']);
            unset($o['weight_unit']);
            unset($o['inventory_item_id']);
            unset($o['old_inventory_quantity']);
            unset($o['tax_code']);
            unset($o['admin_graphql_api_id']);
            $options[] = $o;
            $options_label = $m->title;
            $p = substr($m->title, 0, 2);
            if(in_array($p, $this->label_prefix)){
              $skip = TRUE;
            }
          }
          if((count($options) != 1) || (empty($options_label))){
            $skip = TRUE;
          }

          if(!$skip){
            //modify the options
            $cloned_option = $options[0];
            $ori_title = $options[0]['option1'];
            unset($cloned_option['id']);
            foreach ($this->label_prefix as $m => $n){
              if(!isset($options[$m])){
                $options[$m] = $cloned_option;
              }
              $options[$m]['option1'] = $n.$ori_title;
            }

            $data = [
              'product' => [
                'id' => $product_id,
                'variants' => $options
              ]
            ];
            $r = $shopify->put('products/'.$product_id, $data);
            if(!$r->product){
              $skip = TRUE;
            }

          }

          if($skip){
            \Drupal::messenger()->addError('Failed to save product: ' . $handle);
          }else{
            \Drupal::messenger()->addMessage('Product: '.$handle.' updated.');
          }
        } catch (\Exception $e) {
          \Drupal::messenger()->addError('Failed to save: ' .$i.' '. $e->getMessage());
        }
      }
    }
  }
}
