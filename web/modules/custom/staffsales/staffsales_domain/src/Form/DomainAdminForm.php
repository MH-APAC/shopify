<?php

/**
 * @file
 * Contains Drupal\staffsales_domain\Form\DomainAdminForm.
 */

namespace Drupal\staffsales_domain\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\staffsales_shopify\StaffsalesShopify;

class DomainAdminForm extends FormBase {

  const SHOP_YEAR_ROUND = 0;
  const SHOP_SEASONAL = 1;
  const SHOP_YEAR_ROUND2 = 2;
  const SHOP_YEAR_ROUND3 = 3;
  const SHOP_YEAR_ROUND4 = 4;
  const SHOP_YEAR_ROUND5 = 5;
  const SHOP_YEAR_ROUND6 = 6;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'staffsales_domain_admin_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $domains = static::getAllDomains();
    $yearround_domains = $domains['yearround_domains'];
    $yearround_domains2 = $domains['yearround_domains2'];
    $yearround_domains3 = $domains['yearround_domains3'];
    $yearround_domains4 = $domains['yearround_domains4'];
    $yearround_domains5 = $domains['yearround_domains5'];
    $yearround_domains6 = $domains['yearround_domains6'];
    $seasonal_domains = $domains['seasonal_domains'];

    $form['yearround_domains'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Allowed Email Domains for MH (MH) - Year Round'),
      '#description' => $this->t('Domain list in multiple lines, no comma, e.g. example.com'),
      '#rows' => 5,
      '#cols' => 80,
      '#resizable' => TRUE,
      '#default_value' => $yearround_domains,
    ];

    $form['yearround_domains2'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Allowed Email Domains for LVMH (LV) - Year Round'),
      '#description' => $this->t('Domain list in multiple lines, no comma, e.g. example.com'),
      '#rows' => 5,
      '#cols' => 80,
      '#resizable' => TRUE,
      '#default_value' => $yearround_domains2,
    ];

    $form['yearround_domains3'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Allowed Email Domains for Other Corporates (OC) - Year Round'),
      '#description' => $this->t('Domain list in multiple lines, no comma. Public email domains should not be entered.'),
      '#rows' => 5,
      '#cols' => 80,
      '#resizable' => TRUE,
      '#default_value' => $yearround_domains3,
    ];

    $form['yearround_domains4'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Allowed Email Domains for FRIENDS & FAMILY (FF) - Year Round'),
      '#description' => $this->t('Domain list in multiple lines, no comma. Public email domains should not be entered.'),
      '#rows' => 5,
      '#cols' => 80,
      '#resizable' => TRUE,
      '#default_value' => $yearround_domains4,
    ];

    $form['yearround_domains5'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Allowed Email Domains for Others (OP1) - Year Round'),
      '#description' => $this->t('Domain list in multiple lines, no comma. Public email domains should not be entered.'),
      '#rows' => 5,
      '#cols' => 80,
      '#resizable' => TRUE,
      '#default_value' => $yearround_domains5,
    ];

    $form['yearround_domains6'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Allowed Email Domains for Others (OP2) - Year Round'),
      '#description' => $this->t('Domain list in multiple lines, no comma. Public email domains should not be entered.'),
      '#rows' => 5,
      '#cols' => 80,
      '#resizable' => TRUE,
      '#default_value' => $yearround_domains6,
    ];

    $form['seasonal_domains'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Allowed Email Domains for Seasonal (SS)'),
      '#description' => $this->t('Domain list in multiple lines, no comma, e.g. example.com'),
      '#rows' => 5,
      '#cols' => 80,
      '#resizable' => TRUE,
      '#default_value' => $seasonal_domains,
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
    $yearround_domains = $form_state->getValue('yearround_domains');
    $yearround_domains2 = $form_state->getValue('yearround_domains2');
    $yearround_domains3 = $form_state->getValue('yearround_domains3');
    $yearround_domains4 = $form_state->getValue('yearround_domains4');
    $yearround_domains5 = $form_state->getValue('yearround_domains5');
    $yearround_domains6 = $form_state->getValue('yearround_domains6');
    $seasonal_domains = $form_state->getValue('seasonal_domains');

    $yearround_valid = $this->isDomainFieldValid($yearround_domains);
    if (!is_array($yearround_valid)) {
      $form_state->setErrorByName('yearround_domains', 'Year Round - MH: ' . $yearround_valid);
    }

    $yearround2_valid = $this->isDomainFieldValid($yearround_domains2);
    if (!is_array($yearround2_valid)) {
      $form_state->setErrorByName('yearround_domains2', 'Year Round - LV: ' . $yearround2_valid);
    }

    $yearround3_valid = $this->isDomainFieldValid($yearround_domains3);
    if (!is_array($yearround3_valid)) {
      $form_state->setErrorByName('yearround_domains3', 'Year Round - OC: ' . $yearround3_valid);
    }

    $yearround4_valid = $this->isDomainFieldValid($yearround_domains4);
    if (!is_array($yearround4_valid)) {
      $form_state->setErrorByName('yearround_domains4', 'Year Round - FF: ' . $yearround4_valid);
    }

    $yearround5_valid = $this->isDomainFieldValid($yearround_domains5);
    if (!is_array($yearround5_valid)) {
      $form_state->setErrorByName('yearround_domains5', 'Year Round - OP1: ' . $yearround5_valid);
    }

    $yearround6_valid = $this->isDomainFieldValid($yearround_domains6);
    if (!is_array($yearround6_valid)) {
      $form_state->setErrorByName('yearround_domains6', 'Year Round - OP2: ' . $yearround6_valid);
    }

    $seasonal_valid = $this->isDomainFieldValid($seasonal_domains);
    if (!is_array($seasonal_valid)) {
      $form_state->setErrorByName('seasonal_domains', 'Seasonal: ' . $seasonal_valid);
    }

    //save the prepared values for submitForm() handling
    if (is_array($yearround_valid) && is_array($yearround2_valid) && is_array($yearround3_valid) &&
      is_array($yearround4_valid) && is_array($yearround5_valid) && is_array($yearround6_valid) && is_array($seasonal_valid)) {
      $form_state->setValue('yearround_domains', $yearround_valid);
      $form_state->setValue('yearround_domains2', $yearround2_valid);
      $form_state->setValue('yearround_domains3', $yearround3_valid);
      $form_state->setValue('yearround_domains4', $yearround4_valid);
      $form_state->setValue('yearround_domains5', $yearround5_valid);
      $form_state->setValue('yearround_domains6', $yearround6_valid);
      $form_state->setValue('seasonal_domains', $seasonal_valid);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $yearround_domains = array_unique($form_state->getValue('yearround_domains'));
    $yearround_domains2 = array_unique($form_state->getValue('yearround_domains2'));
    $yearround_domains3 = array_unique($form_state->getValue('yearround_domains3'));
    $yearround_domains4 = array_unique($form_state->getValue('yearround_domains4'));
    $yearround_domains5 = array_unique($form_state->getValue('yearround_domains5'));
    $yearround_domains6 = array_unique($form_state->getValue('yearround_domains6'));
    $seasonal_domains = $form_state->getValue('seasonal_domains');

    $shopify = new StaffsalesShopify();
    $db = Database::getConnection();
    $txn = $db->startTransaction();

    try {
      $db->truncate('staffsales_domain')->execute();
      foreach ($yearround_domains as $i) {
        $db->insert('staffsales_domain')
          ->fields([
            'domain' => $i,
            'shop' => self::SHOP_YEAR_ROUND,
          ])
          ->execute();
      }
      foreach ($yearround_domains2 as $i) {
        $db->insert('staffsales_domain')
          ->fields([
            'domain' => $i,
            'shop' => self::SHOP_YEAR_ROUND2,
          ])
          ->execute();
      }
      foreach ($yearround_domains3 as $i) {
        $db->insert('staffsales_domain')
          ->fields([
            'domain' => $i,
            'shop' => self::SHOP_YEAR_ROUND3,
          ])
          ->execute();
      }
      foreach ($yearround_domains4 as $i) {
        $db->insert('staffsales_domain')
          ->fields([
            'domain' => $i,
            'shop' => self::SHOP_YEAR_ROUND4,
          ])
          ->execute();
      }
      foreach ($yearround_domains5 as $i) {
        $db->insert('staffsales_domain')
          ->fields([
            'domain' => $i,
            'shop' => self::SHOP_YEAR_ROUND5,
          ])
          ->execute();
      }
      foreach ($yearround_domains6 as $i) {
        $db->insert('staffsales_domain')
          ->fields([
            'domain' => $i,
            'shop' => self::SHOP_YEAR_ROUND6,
          ])
          ->execute();
      }
      foreach ($seasonal_domains as $i) {
        $db->insert('staffsales_domain')
          ->fields([
            'domain' => $i,
            'shop' => self::SHOP_SEASONAL,
          ])
          ->execute();
      }

      $domain_price_tier1 = implode (',', $yearround_domains);
      $data = [
        'metafield' => [
          'namespace' => 'staffsales',
          'key' => 'domain_price_tier1',
          'value' => $domain_price_tier1,
          'value_type' => 'string',
        ],
      ];
      $shopify->post('metafields', $data);

      $domain_price_tier2 = implode (',', $yearround_domains2);
      $data2 = [
        'metafield' => [
          'namespace' => 'staffsales',
          'key' => 'domain_price_tier2',
          'value' => $domain_price_tier2,
          'value_type' => 'string',
        ],
      ];
      $shopify->post('metafields', $data2);

      $domain_price_tier3 = implode (',', $yearround_domains3);
      $data3 = [
        'metafield' => [
          'namespace' => 'staffsales',
          'key' => 'domain_price_tier3',
          'value' => $domain_price_tier3,
          'value_type' => 'string',
        ],
      ];
      $shopify->post('metafields', $data3);

      $domain_price_tier4 = implode (',', $yearround_domains4);
      $data4 = [
        'metafield' => [
          'namespace' => 'staffsales',
          'key' => 'domain_price_tier4',
          'value' => $domain_price_tier4,
          'value_type' => 'string',
        ],
      ];
      $shopify->post('metafields', $data4);

      $domain_price_tier5 = implode (',', $yearround_domains5);
      $data5 = [
        'metafield' => [
          'namespace' => 'staffsales',
          'key' => 'domain_price_tier5',
          'value' => $domain_price_tier5,
          'value_type' => 'string',
        ],
      ];
      $shopify->post('metafields', $data5);

      $domain_price_tier6 = implode (',', $yearround_domains6);
      $data6 = [
        'metafield' => [
          'namespace' => 'staffsales',
          'key' => 'domain_price_tier6',
          'value' => $domain_price_tier6,
          'value_type' => 'string',
        ],
      ];
      $shopify->post('metafields', $data6);

      $domain_price_seasonal = implode (',', $seasonal_domains);
      $data6 = [
        'metafield' => [
          'namespace' => 'staffsales',
          'key' => 'domain_price_seasonal',
          'value' => $domain_price_seasonal,
          'value_type' => 'string',
        ],
      ];
      $shopify->post('metafields', $data6);

      \Drupal::messenger()->addMessage('Updated successfully');
    } catch (\Exception $e) {
      $txn->rollBack();
      \Drupal::messenger()->addError($e->getMessage());
    }
  }

  public static function getAllDomains() {
    $yearround_domains = [];
    $yearround_domains2 = [];
    $yearround_domains3 = [];
    $yearround_domains4 = [];
    $yearround_domains5 = [];
    $yearround_domains6 = [];
    $seasonal_domains = [];
    $db = Database::getConnection();

    //get all domains in one query
    $query = $db->select('staffsales_domain', 'd')
      ->fields('d', ['shop', 'domain'])
      ->orderBy('d.shop')
      ->orderBy('d.domain');
    $domains = $query->execute()->fetchAll();

    //split the domain list
    foreach ($domains as $j) {
      switch ($j->shop) {
        case self::SHOP_YEAR_ROUND:
          $yearround_domains[] = $j->domain;
          break;
        case self::SHOP_YEAR_ROUND2:
          $yearround_domains2[] = $j->domain;
          break;
        case self::SHOP_YEAR_ROUND3:
          $yearround_domains3[] = $j->domain;
          break;
        case self::SHOP_YEAR_ROUND4:
          $yearround_domains4[] = $j->domain;
          break;
        case self::SHOP_YEAR_ROUND5:
          $yearround_domains5[] = $j->domain;
          break;
        case self::SHOP_YEAR_ROUND6:
          $yearround_domains6[] = $j->domain;
          break;
        case self::SHOP_SEASONAL:
          $seasonal_domains[] = $j->domain;
          break;
        default:
          break;
      }
    }
    $yearround_domains = implode(PHP_EOL, $yearround_domains);
    $yearround_domains2 = implode(PHP_EOL, $yearround_domains2);
    $yearround_domains3 = implode(PHP_EOL, $yearround_domains3);
    $yearround_domains4 = implode(PHP_EOL, $yearround_domains4);
    $yearround_domains5 = implode(PHP_EOL, $yearround_domains5);
    $yearround_domains6 = implode(PHP_EOL, $yearround_domains6);
    $seasonal_domains = implode(PHP_EOL, $seasonal_domains);

    $r = [];
    $r['yearround_domains'] = $yearround_domains;
    $r['yearround_domains2'] = $yearround_domains2;
    $r['yearround_domains3'] = $yearround_domains3;
    $r['yearround_domains4'] = $yearround_domains4;
    $r['yearround_domains5'] = $yearround_domains5;
    $r['yearround_domains6'] = $yearround_domains6;
    $r['seasonal_domains'] = $seasonal_domains;

    return $r;
  }

  private function isDomainFieldValid(string $domains) {
    $r = [];
    $domains = trim($domains);

    if (empty($domains)) {
      $r = 'Domain list cannot be empty.';
    }
    else {
      $domains = preg_split("/\r\n|\n|\r/", $domains);
      $domains = array_map('trim', $domains);
      $domains = array_filter($domains);
      $domains = array_map('strtolower', $domains);

      /*
       * any domain name with or without subdomain
       * e.g.
       * @example.com
       * @example.com.hk
       * @hk.example-one.com
       */
      $regex = '/^(?:(?!-)[a-z0-9-]{0,62}[a-z0-9]\.)+[a-z]{2,}$/';//https://stackoverflow.com/a/25717506/439567
      $invalid = [];

      foreach ($domains as $i) {
        $m = preg_match($regex, $i);

        if ($m !== 1) {
          $invalid[] = $i;
        }
      }

      if (empty($invalid)) {
        $r = $domains;
      }
      else {
        $r = 'Invalid domain name ' . implode(', ', $invalid);
      }
    }

    return $r;
  }

}
