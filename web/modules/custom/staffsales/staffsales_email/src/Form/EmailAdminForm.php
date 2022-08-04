<?php

/**
 * @file
 * Contains Drupal\staffsales_email\Form\EmailAdminForm.
 */

namespace Drupal\staffsales_email\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\staffsales_shopify\StaffsalesShopify;

class EmailAdminForm extends FormBase {

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
    return 'staffsales_email_admin_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $emails = static::getAllEmails();
    $yearround_emails = $emails['yearround_emails'];
    $yearround_emails2 = $emails['yearround_emails2'];
    $yearround_emails3 = $emails['yearround_emails3'];
    $yearround_emails4 = $emails['yearround_emails4'];
    $yearround_emails5 = $emails['yearround_emails5'];
    $yearround_emails6 = $emails['yearround_emails6'];
    $seasonal_emails = $emails['seasonal_emails'];
    $vip_emails = $emails['vip_emails'];
    $vip_emails2 = $emails['vip_emails2'];
    $vip_emails3 = $emails['vip_emails3'];
    $vip_emails4 = $emails['vip_emails4'];
    $vip_emails5 = $emails['vip_emails5'];
    $vip_emails6 = $emails['vip_emails6'];
    $vip_emailss = $emails['vip_emailss'];

    $form[1] = [
      '#type' => 'fieldset',
      '#title' => 'Year Round for MH (MH)',
      '#description' => 'Email inputted here will override the general domain quota. Email list in multiple lines, no comma.',
    ];
    $form[1]['yearround_emails'] = [
      '#type' => 'textarea', '#title' => 'General Emails',
      '#rows' => 4, '#cols' => 80, '#resizable' => TRUE,
      '#default_value' => $yearround_emails,
    ];
    $form[1]['vip_emails'] = [
      '#type' => 'textarea', '#title' => 'VIP Emails (MH)',
      '#rows' => 2, '#cols' => 80, '#resizable' => TRUE,
      '#default_value' => $vip_emails,
    ];

    $form[2] = [
      '#type' => 'fieldset',
      '#title' => 'Year Round for LVMH (LV)',
      '#description' => 'Email inputted here will override the general domain quota. Email list in multiple lines, no comma.',
    ];
    $form[2]['yearround_emails2'] = [
      '#type' => 'textarea', '#title' => 'General Emails',
      '#rows' => 4, '#cols' => 80, '#resizable' => TRUE,
      '#default_value' => $yearround_emails2,
    ];
    $form[2]['vip_emails2'] = [
      '#type' => 'textarea', '#title' => 'VIP Emails (LV)',
      '#rows' => 2, '#cols' => 80, '#resizable' => TRUE,
      '#default_value' => $vip_emails2,
    ];

    $form[3] = [
      '#type' => 'fieldset',
      '#title' => 'Year Round for Other Corporates (OC)',
      '#description' => 'Email inputted here will override the general domain quota. Email list in multiple lines, no comma.',
    ];
    $form[3]['yearround_emails3'] = [
      '#type' => 'textarea', '#title' => 'General Emails',
      '#rows' => 4, '#cols' => 80, '#resizable' => TRUE,
      '#default_value' => $yearround_emails3,
    ];
    $form[3]['vip_emails3'] = [
      '#type' => 'textarea', '#title' => 'VIP Emails (OC)',
      '#rows' => 2, '#cols' => 80, '#resizable' => TRUE,
      '#default_value' => $vip_emails3,
    ];

    $form[4] = [
      '#type' => 'fieldset',
      '#title' => 'Year Round for Friends & Family (FF)',
      '#description' => 'Email inputted here will override the general domain quota. Email list in multiple lines, no comma.',
    ];
    $form[4]['yearround_emails4'] = [
      '#type' => 'textarea', '#title' => 'General Emails',
      '#rows' => 4, '#cols' => 80, '#resizable' => TRUE,
      '#default_value' => $yearround_emails4,
    ];
    $form[4]['vip_emails4'] = [
      '#type' => 'textarea', '#title' => 'VIP Emails (FF)',
      '#rows' => 2, '#cols' => 80, '#resizable' => TRUE,
      '#default_value' => $vip_emails4,
    ];

    $form[5] = [
      '#type' => 'fieldset',
      '#title' => 'Year Round for Others (OP1)',
      '#description' => 'Email inputted here will override the general domain quota. Email list in multiple lines, no comma.',
    ];
    $form[5]['yearround_emails5'] = [
      '#type' => 'textarea', '#title' => 'General Emails',
      '#rows' => 4, '#cols' => 80, '#resizable' => TRUE,
      '#default_value' => $yearround_emails5,
    ];
    $form[5]['vip_emails5'] = [
      '#type' => 'textarea', '#title' => 'VIP Emails (OP1)',
      '#rows' => 2, '#cols' => 80, '#resizable' => TRUE,
      '#default_value' => $vip_emails5,
    ];

    $form[6] = [
      '#type' => 'fieldset',
      '#title' => 'Year Round for Others (OP2)',
      '#description' => 'Email inputted here will override the general domain quota. Email list in multiple lines, no comma.',
    ];
    $form[6]['yearround_emails6'] = [
      '#type' => 'textarea', '#title' => 'General Emails',
      '#rows' => 4, '#cols' => 80, '#resizable' => TRUE,
      '#default_value' => $yearround_emails6,
    ];
    $form[6]['vip_emails6'] = [
      '#type' => 'textarea', '#title' => 'VIP Emails (OP2)',
      '#rows' => 2, '#cols' => 80, '#resizable' => TRUE,
      '#default_value' => $vip_emails6,
    ];

    $form[7] = [
      '#type' => 'fieldset',
      '#title' => 'Seasonal for all',
      '#description' => 'Email inputted here will override the general domain quota. Email list in multiple lines, no comma.',
    ];
    $form[7]['seasonal_emails'] = [
      '#type' => 'textarea', '#title' => 'General Emails',
      '#rows' => 4, '#cols' => 80, '#resizable' => TRUE,
      '#default_value' => $seasonal_emails,
    ];
    $form[7]['vip_emailss'] = [
      '#type' => 'textarea', '#title' => 'VIP Emails (Seasonal)',
      '#rows' => 2, '#cols' => 80, '#resizable' => TRUE,
      '#default_value' => $vip_emailss,
    ];
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit', '#value' => 'Save', '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $yearround_emails = $form_state->getValue('yearround_emails');
    $yearround_emails2 = $form_state->getValue('yearround_emails2');
    $yearround_emails3 = $form_state->getValue('yearround_emails3');
    $yearround_emails4 = $form_state->getValue('yearround_emails4');
    $yearround_emails5 = $form_state->getValue('yearround_emails5');
    $yearround_emails6 = $form_state->getValue('yearround_emails6');
    $seasonal_emails = $form_state->getValue('seasonal_emails');
    $vip_emails = $form_state->getValue('vip_emails');
    $vip_emails2 = $form_state->getValue('vip_emails2');
    $vip_emails3 = $form_state->getValue('vip_emails3');
    $vip_emails4 = $form_state->getValue('vip_emails4');
    $vip_emails5 = $form_state->getValue('vip_emails5');
    $vip_emails6 = $form_state->getValue('vip_emails6');
    $vip_emailss = $form_state->getValue('vip_emailss');

    $yearround_valid = $this->isEmailFieldValid($yearround_emails);
    if (!is_array($yearround_valid)) {
      $form_state->setErrorByName('yearround_emails', 'MH(MH) emails: ' . $yearround_valid);
    }
    $yearround2_valid = $this->isEmailFieldValid($yearround_emails2);
    if (!is_array($yearround2_valid)) {
      $form_state->setErrorByName('yearround_emails2', 'LVMH(LV) emails: ' . $yearround2_valid);
    }
    $yearround3_valid = $this->isEmailFieldValid($yearround_emails3);
    if (!is_array($yearround3_valid)) {
      $form_state->setErrorByName('yearround_emails3', 'Other Corporates (OC) emails: ' . $yearround3_valid);
    }
    $yearround4_valid = $this->isEmailFieldValid($yearround_emails4);
    if (!is_array($yearround4_valid)) {
      $form_state->setErrorByName('yearround_emails4', 'FRIENDS & FAMILY (FF) emails: ' . $yearround4_valid);
    }
    $yearround5_valid = $this->isEmailFieldValid($yearround_emails5);
    if (!is_array($yearround5_valid)) {
      $form_state->setErrorByName('yearround_emails5', 'OP1 (OP1) emails: ' . $yearround5_valid);
    }
    $yearround6_valid = $this->isEmailFieldValid($yearround_emails6);
    if (!is_array($yearround6_valid)) {
      $form_state->setErrorByName('yearround_emails6', 'OP2 (OP2) emails: ' . $yearround6_valid);
    }
    $seasonal_valid = $this->isEmailFieldValid($seasonal_emails);
    if (!is_array($seasonal_valid)) {
      $form_state->setErrorByName('seasonal_emails', 'Seasonal emails: ' . $seasonal_valid);
    }

    $vip_valid = $this->isEmailFieldValid($vip_emails);
    if (!is_array($vip_valid)) {
      $form_state->setErrorByName('vip_emails', 'MH VIP emails: ' . $vip_valid);
    }
    $vip_valid2 = $this->isEmailFieldValid($vip_emails2);
    if (!is_array($vip_valid2)) {
      $form_state->setErrorByName('vip_emails2', 'LV VIP emails: ' . $vip_valid2);
    }
    $vip_valid3 = $this->isEmailFieldValid($vip_emails3);
    if (!is_array($vip_valid3)) {
      $form_state->setErrorByName('vip_emails3', 'OC VIP emails: ' . $vip_valid3);
    }
    $vip_valid4 = $this->isEmailFieldValid($vip_emails4);
    if (!is_array($vip_valid4)) {
      $form_state->setErrorByName('vip_emails4', 'FF VIP emails: ' . $vip_valid4);
    }
    $vip_valid5 = $this->isEmailFieldValid($vip_emails5);
    if (!is_array($vip_valid5)) {
      $form_state->setErrorByName('vip_emails5', 'OP1 VIP emails: ' . $vip_valid5);
    }
    $vip_valid6 = $this->isEmailFieldValid($vip_emails6);
    if (!is_array($vip_valid6)) {
      $form_state->setErrorByName('vip_emails6', 'OP2 VIP emails: ' . $vip_valid6);
    }
    $vip_valids = $this->isEmailFieldValid($vip_emailss);
    if (!is_array($vip_valids)) {
      $form_state->setErrorByName('vip_emailss', 'Seasonal VIP emails: ' . $vip_valids);
    }

    //save the prepared values for submitForm() handling
    if (is_array($yearround_valid) && is_array($seasonal_valid) && is_array($yearround2_valid)
      && is_array($yearround3_valid) && is_array($yearround4_valid)
      && is_array($yearround5_valid) && is_array($yearround6_valid)
      && is_array($vip_valid2) && is_array($vip_valid3) && is_array($vip_valid4) && is_array($vip_valid5)
      && is_array($vip_valid6) && is_array($vip_valids)) {
      $form_state->setValue('yearround_emails', $yearround_valid);
      $form_state->setValue('yearround_emails2', $yearround2_valid);
      $form_state->setValue('yearround_emails3', $yearround3_valid);
      $form_state->setValue('yearround_emails4', $yearround4_valid);
      $form_state->setValue('yearround_emails5', $yearround5_valid);
      $form_state->setValue('yearround_emails6', $yearround6_valid);
      $form_state->setValue('seasonal_emails', $seasonal_valid);
      $form_state->setValue('vip_emails', $vip_valid);
      $form_state->setValue('vip_emails2', $vip_valid2);
      $form_state->setValue('vip_emails3', $vip_valid3);
      $form_state->setValue('vip_emails4', $vip_valid4);
      $form_state->setValue('vip_emails5', $vip_valid5);
      $form_state->setValue('vip_emails6', $vip_valid6);
      $form_state->setValue('vip_emailss', $vip_valids);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $yearround_emails = array_unique($form_state->getValue('yearround_emails'));
    $yearround_emails2 = array_unique($form_state->getValue('yearround_emails2'));
    $yearround_emails3 = array_unique($form_state->getValue('yearround_emails3'));
    $yearround_emails4 = array_unique($form_state->getValue('yearround_emails4'));
    $yearround_emails5 = array_unique($form_state->getValue('yearround_emails5'));
    $yearround_emails6 = array_unique($form_state->getValue('yearround_emails6'));
    $seasonal_emails = array_unique($form_state->getValue('seasonal_emails'));
    $vip_emails = array_unique($form_state->getValue('vip_emails'));
    $vip_emails2 = array_unique($form_state->getValue('vip_emails2'));
    $vip_emails3 = array_unique($form_state->getValue('vip_emails3'));
    $vip_emails4 = array_unique($form_state->getValue('vip_emails4'));
    $vip_emails5 = array_unique($form_state->getValue('vip_emails5'));
    $vip_emails6 = array_unique($form_state->getValue('vip_emails6'));
    $vip_emailss = array_unique($form_state->getValue('vip_emailss'));

    $db = Database::getConnection();
    $txn = $db->startTransaction();

    try {
      $db->truncate('staffsales_email')->execute();
      foreach ($yearround_emails as $i) {
        $db->insert('staffsales_email')
          ->fields(['email' => $i, 'shop' => self::SHOP_YEAR_ROUND,])
          ->execute();
      }
      foreach ($yearround_emails2 as $i) {
        $db->insert('staffsales_email')
          ->fields(['email' => $i, 'shop' => self::SHOP_YEAR_ROUND2,])
          ->execute();
      }
      foreach ($yearround_emails3 as $i) {
        $db->insert('staffsales_email')
          ->fields(['email' => $i, 'shop' => self::SHOP_YEAR_ROUND3,])
          ->execute();
      }
      foreach ($yearround_emails4 as $i) {
        $db->insert('staffsales_email')
          ->fields(['email' => $i, 'shop' => self::SHOP_YEAR_ROUND4,])
          ->execute();
      }
      foreach ($yearround_emails5 as $i) {
        $db->insert('staffsales_email')
          ->fields(['email' => $i, 'shop' => self::SHOP_YEAR_ROUND5,])
          ->execute();
      }
      foreach ($yearround_emails6 as $i) {
        $db->insert('staffsales_email')
          ->fields(['email' => $i, 'shop' => self::SHOP_YEAR_ROUND6,])
          ->execute();
      }
      foreach ($seasonal_emails as $i) {
        $db->insert('staffsales_email')
          ->fields(['email' => $i, 'shop' => self::SHOP_SEASONAL,])
          ->execute();
      }
      $db->truncate('staffsales_email_role')->execute();
      foreach ($vip_emails as $i) {
        $db->insert('staffsales_email_role')
          ->fields(['email' => $i, 'role' => 'vip',])
          ->execute();
      }
      foreach ($vip_emails2 as $i) {
        $db->insert('staffsales_email_role')
          ->fields(['email' => $i, 'role' => 'vip2',])
          ->execute();
      }
      foreach ($vip_emails3 as $i) {
        $db->insert('staffsales_email_role')
          ->fields(['email' => $i, 'role' => 'vip3',])
          ->execute();
      }
      foreach ($vip_emails4 as $i) {
        $db->insert('staffsales_email_role')
          ->fields(['email' => $i, 'role' => 'vip4',])
          ->execute();
      }
      foreach ($vip_emails5 as $i) {
        $db->insert('staffsales_email_role')
          ->fields(['email' => $i, 'role' => 'vip5',])
          ->execute();
      }
      foreach ($vip_emails6 as $i) {
        $db->insert('staffsales_email_role')
          ->fields(['email' => $i, 'role' => 'vip6',])
          ->execute();
      }
      foreach ($vip_emailss as $i) {
        $db->insert('staffsales_email_role')
          ->fields(['email' => $i, 'role' => 'vips',])
          ->execute();
      }

      $shopify = new StaffsalesShopify();

      $yearround_emails = implode (',', $yearround_emails);
      $data = ['metafield' => ['namespace' => 'staffsales', 'key' => 'yearround_emails', 'value' => $yearround_emails, 'value_type' => 'string',],];
      $shopify->post('metafields', $data);
      $vip_emails = implode (',', $vip_emails);
      $data = ['metafield' => ['namespace' => 'staffsales', 'key' => 'yearround_emails_vip', 'value' => $vip_emails, 'value_type' => 'string',],];
      $shopify->post('metafields', $data);

      $yearround_emails2 = implode (',', $yearround_emails2);
      $data = ['metafield' => ['namespace' => 'staffsales', 'key' => 'yearround_emails2', 'value' => $yearround_emails2, 'value_type' => 'string',],];
      $shopify->post('metafields', $data);
      $vip_emails2 = implode (',', $vip_emails2);
      $data = ['metafield' => ['namespace' => 'staffsales', 'key' => 'yearround_emails_vip2', 'value' => $vip_emails2, 'value_type' => 'string',],];
      $shopify->post('metafields', $data);

      $yearround_emails3 = implode (',', $yearround_emails3);
      $data = ['metafield' => ['namespace' => 'staffsales', 'key' => 'yearround_emails3', 'value' => $yearround_emails3, 'value_type' => 'string',],];
      $shopify->post('metafields', $data);
      $vip_emails3 = implode (',', $vip_emails3);
      $data = ['metafield' => ['namespace' => 'staffsales', 'key' => 'yearround_emails_vip3', 'value' => $vip_emails3, 'value_type' => 'string',],];
      $shopify->post('metafields', $data);

      $yearround_emails4 = implode (',', $yearround_emails4);
      $data = ['metafield' => ['namespace' => 'staffsales', 'key' => 'yearround_emails4', 'value' => $yearround_emails4, 'value_type' => 'string',],];
      $shopify->post('metafields', $data);
      $vip_emails4 = implode (',', $vip_emails4);
      $data = ['metafield' => ['namespace' => 'staffsales', 'key' => 'yearround_emails_vip4', 'value' => $vip_emails4, 'value_type' => 'string',],];
      $shopify->post('metafields', $data);

      $yearround_emails5 = implode (',', $yearround_emails5);
      $data = ['metafield' => ['namespace' => 'staffsales', 'key' => 'yearround_emails5', 'value' => $yearround_emails5, 'value_type' => 'string',],];
      $shopify->post('metafields', $data);
      $vip_emails5 = implode (',', $vip_emails5);
      $data = ['metafield' => ['namespace' => 'staffsales', 'key' => 'yearround_emails_vip5', 'value' => $vip_emails5, 'value_type' => 'string',],];
      $shopify->post('metafields', $data);

      $yearround_emails6 = implode (',', $yearround_emails6);
      $data = ['metafield' => ['namespace' => 'staffsales', 'key' => 'yearround_emails6', 'value' => $yearround_emails6, 'value_type' => 'string',],];
      $shopify->post('metafields', $data);
      $vip_emails6 = implode (',', $vip_emails6);
      $data = ['metafield' => ['namespace' => 'staffsales', 'key' => 'yearround_emails_vip6', 'value' => $vip_emails6, 'value_type' => 'string',],];
      $shopify->post('metafields', $data);

      $seasonal_emails = implode (',', $seasonal_emails);
      $data = ['metafield' => ['namespace' => 'staffsales', 'key' => 'seasonal_emails', 'value' => $seasonal_emails, 'value_type' => 'string',],];
      $shopify->post('metafields', $data);
      $vip_emailss = implode (',', $vip_emailss);
      $data = ['metafield' => ['namespace' => 'staffsales', 'key' => 'yearround_emails_vips', 'value' => $vip_emailss, 'value_type' => 'string',],];
      $shopify->post('metafields', $data);

      \Drupal::messenger()->addMessage('Updated successfully');
    } catch (\Exception $e) {
      $txn->rollBack();
      \Drupal::messenger()->addError($e->getMessage());
    }
  }

  public static function getAllEmails(){
    $yearround_emails = [];
    $yearround_emails2 = [];
    $yearround_emails3 = [];
    $yearround_emails4 = [];
    $yearround_emails5 = [];
    $yearround_emails6 = [];
    $seasonal_emails = [];
    $vip_emails = [];
    $vip_emails2 = [];
    $vip_emails3 = [];
    $vip_emails4 = [];
    $vip_emails5 = [];
    $vip_emails6 = [];
    $vip_emailss = [];
    $db = Database::getConnection();

    //get all emails in one query
    $query = $db->select('staffsales_email', 't')
      ->fields('t', ['shop', 'email'])
      ->orderBy('t.shop')
      ->orderBy('t.email');
    $emails = $query->execute()->fetchAll();

    //split the email list
    foreach ($emails as $i => $j) {
      switch ($j->shop) {
        case self::SHOP_YEAR_ROUND:
          $yearround_emails[] = $j->email;
          break;
        case self::SHOP_YEAR_ROUND2:
          $yearround_emails2[] = $j->email;
          break;
        case self::SHOP_YEAR_ROUND3:
          $yearround_emails3[] = $j->email;
          break;
        case self::SHOP_YEAR_ROUND4:
          $yearround_emails4[] = $j->email;
          break;
        case self::SHOP_YEAR_ROUND5:
          $yearround_emails5[] = $j->email;
          break;
        case self::SHOP_YEAR_ROUND6:
          $yearround_emails6[] = $j->email;
          break;
        case self::SHOP_SEASONAL:
          $seasonal_emails[] = $j->email;
          break;
        default:
          break;
      }
    }

    $query = $db->select('staffsales_email_role', 't')
      ->fields('t', ['role', 'email'])
      ->orderBy('t.role')
      ->orderBy('t.email');
    $roles = $query->execute()->fetchAll();

    foreach ($roles as $i => $j) {
      switch ($j->role) {
        case 'vip':
          $vip_emails[] = $j->email;
          break;
        case 'vip2':
          $vip_emails2[] = $j->email;
          break;
        case 'vip3':
          $vip_emails3[] = $j->email;
          break;
        case 'vip4':
          $vip_emails4[] = $j->email;
          break;
        case 'vip5':
          $vip_emails5[] = $j->email;
          break;
        case 'vip6':
          $vip_emails6[] = $j->email;
          break;
        case 'vips':
          $vip_emailss[] = $j->email;
          break;
        default:
          break;
      }
    }

    $yearround_emails = implode(PHP_EOL, $yearround_emails);
    $yearround_emails2 = implode(PHP_EOL, $yearround_emails2);
    $yearround_emails3 = implode(PHP_EOL, $yearround_emails3);
    $yearround_emails4 = implode(PHP_EOL, $yearround_emails4);
    $yearround_emails5 = implode(PHP_EOL, $yearround_emails5);
    $yearround_emails6 = implode(PHP_EOL, $yearround_emails6);
    $seasonal_emails = implode(PHP_EOL, $seasonal_emails);
    $vip_emails = implode(PHP_EOL, $vip_emails);
    $vip_emails2 = implode(PHP_EOL, $vip_emails2);
    $vip_emails3 = implode(PHP_EOL, $vip_emails3);
    $vip_emails4 = implode(PHP_EOL, $vip_emails4);
    $vip_emails5 = implode(PHP_EOL, $vip_emails5);
    $vip_emails6 = implode(PHP_EOL, $vip_emails6);
    $vip_emailss = implode(PHP_EOL, $vip_emailss);

    $r = [];
    $r['yearround_emails'] = $yearround_emails;
    $r['yearround_emails2'] = $yearround_emails2;
    $r['yearround_emails3'] = $yearround_emails3;
    $r['yearround_emails4'] = $yearround_emails4;
    $r['yearround_emails5'] = $yearround_emails5;
    $r['yearround_emails6'] = $yearround_emails6;
    $r['seasonal_emails'] = $seasonal_emails;
    $r['vip_emails'] = $vip_emails;
    $r['vip_emails2'] = $vip_emails2;
    $r['vip_emails3'] = $vip_emails3;
    $r['vip_emails4'] = $vip_emails4;
    $r['vip_emails5'] = $vip_emails5;
    $r['vip_emails6'] = $vip_emails6;
    $r['vip_emailss'] = $vip_emailss;

    return $r;
  }

  public static function getEmail(string $email) {
    $yearround = FALSE;
    $seasonal = FALSE;
    $roles = [];
    $db = Database::getConnection();

    //get all emails in one query
    $query = $db->select('staffsales_email', 't')
      ->fields('t', ['shop', 'email'])
      ->condition('t.email', $email, '=')
      ->orderBy('t.shop')
      ->orderBy('t.email');
    $emails = $query->execute()->fetchAll();

    foreach ($emails as $i => $j) {
      switch ($j->shop) {
        case self::SHOP_YEAR_ROUND:
          $yearround = TRUE;
          break;
        case self::SHOP_YEAR_ROUND2:
          $yearround = TRUE;
          break;
        case self::SHOP_YEAR_ROUND3:
          $yearround = TRUE;
          break;
        case self::SHOP_YEAR_ROUND4:
          $yearround = TRUE;
          break;
        case self::SHOP_YEAR_ROUND5:
          $yearround = TRUE;
          break;
        case self::SHOP_YEAR_ROUND6:
          $yearround = TRUE;
          break;
        case self::SHOP_SEASONAL:
          $seasonal = TRUE;
          break;
        default:
          break;
      }
    }

    $query = $db->select('staffsales_email_role', 't')
      ->fields('t', ['role', 'email'])
      ->condition('t.email', $email, '=')
      ->orderBy('t.role')
      ->orderBy('t.email');
    $emails = $query->execute()->fetchAll();

    foreach ($emails as $i => $j) {
      $roles[] = $j->role;
    }

    $r = [];
    $r['yearround_allow'] = $yearround;
    $r['seasonal_allow'] = $seasonal;
    $r['roles'] = $roles;

    return $r;
  }

  private function isEmailFieldValid(string $emails) {
    $r = [];
    $emails = trim($emails);

    if (empty($emails)) {
      $r = 'Email list cannot be empty.';
    }

    if (!empty($emails)) {
      $emails = preg_split("/\r\n|\n|\r/", $emails);
      $emails = array_map('trim', $emails);
      $emails = array_filter($emails);//empty line
      $emails = array_map('strtolower', $emails);

      //ref: https://emailregex.com/
      $regex = '/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';
      $invalid = [];

      foreach ($emails as $i) {
        $m = preg_match($regex, $i);

        if ($m !== 1) {
          $invalid[] = $i;
        }
      }

      if (empty($invalid)) {
        $r = $emails;
      }
      else {
        $r = 'invalid email address ' . implode(', ', $invalid);
      }
    }

    return $r;
  }

}
