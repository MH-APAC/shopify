<?php

namespace Drupal\staffsales_rest\Plugin\rest\resource;

use Drupal\Core\Database\Database;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\staffsales_domain\Form\DomainAdminForm;
use Drupal\staffsales_email\Form\EmailAdminForm;
use Drupal\staffsales_shopify\StaffsalesShopify;
use \Shopify\ClientException;

/**
 * Provides a resource for shopify customers
 * see https://www.drupal.org/project/drupal/issues/2847859#comment-11898533
 * for POST error '405 Method Not Allowed'
 *
 * It is also possible to implement jsonp callback, see CacheableJsonResponse->setCallback()
 *
 * @RestResource(
 *   id = "customer_rest_resource",
 *   label = @Translation("CustomerRestResource"),
 *   uri_paths = {
 *     "canonical" = "/api/v1/customer",
 *     "create" = "/api/v1/customer"
 *   }
 * )
 */
class CustomerRestResource extends RestResourceBase {

  //sample code to test if GET works
  public function get() {
    $response = [
      'msg' => 'customer',
      't' => time(),
    ];
    return $this->noCacheResponse($response);
  }

  /**
   * Responds to POST requests.
   *
   * For customer operation, ?op=allow
   *
   * @param array $data
   *   The post param
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response of op
   *
   */
  public function post(array $data) {
    $op = $_GET['op'];
    $seasonal_enabled = isset($data['seasonal_enabled']) ? $data['seasonal_enabled'] : 0;
    $email = isset($data['email']) ? $data['email'] : NULL;
    $email = trim($email);
    $email = strtolower($email);
    $password = isset($data['password']) ? $data['password'] : NULL;

    if (!empty($op)) {
      switch ($op) {
        case 'is_allow':
          if(!empty($email)){
            return $this->op_is_allow($email, $seasonal_enabled);
          }
          break;
        case 'login':
          if(!empty($email) && !empty($password)) {
            return $this->op_login($email, $password, $seasonal_enabled);
          }
          break;
        case 'webhook_login':
          if (!empty($email)) {
            return $this->op_webhook_login($email);
          }
          break;
        case 'register':
          if(!empty($email)) {
            return $this->op_register($email, $seasonal_enabled, $data);
          }
          break;
        case 'verify_passcode':
          $passcode = isset($data['passcode']) ? $data['passcode'] : NULL;
          $passcode = trim($passcode);
          if(!empty($email) && !empty($passcode)) {
            return $this->op_verify_passcode($email, $passcode);
          }
          break;
        default:
          break;
      }
    }

    return $this->noCacheResponse([]);
  }

  private function op_is_allow($email, $seasonal_enabled) {
    $is_allow = $this->is_allow($email, $seasonal_enabled);

    $data = [];
    $data['is_allow'] = $is_allow;

    return $this->noCacheResponse($data);
  }

  //we never store or use the password for security reason,
  //but keep in param just in case we need it in future
  private function op_login($email, $password, $seasonal_enabled) {
    $is_allow = $this->is_allow($email, $seasonal_enabled);
    $has_account = $this->has_account($email);
    $passcode = FALSE;

    if($is_allow && $has_account){
      //generate passcode
      $passcode = $this->sendPasscode($email);
      if($passcode){
        //don't disclose the passcode, just return the status
        $passcode = TRUE;
      }
    }

    $data = [];
    $data['is_allow'] = $is_allow;
    $data['has_account'] = $has_account;
    $data['passcode'] = $passcode;

    return $this->noCacheResponse($data);
  }

  private function op_webhook_login($email) {
    $success = TRUE;
    $status = FALSE;
    $error_messages = '';
    $customer_id = NULL;
    $tags = '';
    $new_tags = '';

    $roles = EmailAdminForm::getEmail($email);
    $roles = $roles['roles'];
    $shopify = new StaffsalesShopify();
    $opts = [];
    $opts['query'] = [
      'fields' => 'id,email,tags',
      'query' => 'email:' . $email,
    ];

    try {
      $r = $shopify->get('customers/search', $opts);
      if ($r->customers) {
        foreach ($r->customers as $i => $j) {
          if ($j->email == $email) {
            $customer_id = $j->id;
            //Shopify could put spaces between each tags, but we don't need them
            $tags = str_replace(' ', '', $j->tags);
            $tags = explode(',', $tags);
            $new_tags = [];
            //remove all tags with role_
            foreach ($tags as $i) {
              if (stripos($i, 'role_') === FALSE) {
                $new_tags[] = $i;
              }
            }
            //now we have tags without role_, add our new role_
            foreach ($roles as $i) {
              $new_tags[] = 'role_' . $i;
            }
            //sort both tags for comparision
            asort($tags);
            asort($new_tags);
            $tags = implode(',', $tags);
            $new_tags = implode(',', $new_tags);
            break;
          }
        }
      }
    } catch (ClientException $e) {
      $success = FALSE;
      $error_messages = $this->getError($e);
    }

    //see if tags are different
    if (!empty($customer_id) && ($tags != $new_tags)) {
      $data = [];
      $data['customer'] = [];
      $data['customer']['tags'] = $new_tags;
      try {
        $r = $shopify->put('customers/' . $customer_id, $data);
        if ($r->customer) {
          $status = TRUE;
        }
      } catch (ClientException $e) {
        $success = FALSE;
        $error_messages = $this->getError($e);
      }
    }

    $data = [];
    //REST executed without error
    $data['success'] = $success;
    //Customer tags modified
    $data['status'] = $status;
    $data['errors'] = $error_messages;

    return $this->noCacheResponse($data);
  }

  private function op_register($email, $seasonal_enabled, $form) {
    $is_allow = $this->is_allow($email, $seasonal_enabled);
    $success = FALSE;
    $error_messages = '';

    if($is_allow){
      $first_name = isset($form['first_name']) ? $form['first_name'] : NULL;
      $first_name = trim($first_name);
      $last_name = isset($form['last_name']) ? $form['last_name'] : NULL;
      $last_name = trim($last_name);
      $accepts_marketing = isset($form['accepts_marketing']) ? $form['accepts_marketing'] : NULL;
      $accepts_marketing = trim($accepts_marketing);
      $customer_note = isset($form['note']) ? $form['note'] : NULL;
      $customer_note = trim($customer_note);

      $shopify = new StaffsalesShopify();
      $data = [
        'customer' => [
          'first_name' => $first_name,
          'last_name' => $last_name,
          'email' => $email,
          'email_marketing_consent' => $accepts_marketing,
          'note' => $customer_note,
          'send_email_invite' => TRUE
        ],
      ];

      try {
        $r = $shopify->post('customers', $data);
        if($r->customer){
          $success = TRUE;
        }
      } catch (ClientException $e) {
        $error_messages = $this->getError($e);
      }
    }

    $data = [];
    $data['is_allow'] = $is_allow;
    $data['success'] = $success;
    $data['errors'] = $error_messages;

    return $this->noCacheResponse($data);
  }

  private function op_verify_passcode($email, $passcode) {
    $valid = FALSE;

    $db = Database::getConnection();
    $query = $db->select('staffsales_passcode', 't')
      ->fields('t', ['email', 'passcode'])
      ->condition('t.email', $email, '=')
      ->condition('t.passcode', $passcode, '=');
    $emails = $query->execute()->fetchAll();

    //user may attempt to get passcode multiple times, we just match any one of them
    if (count($emails) > 0) {
      $valid = TRUE;
      try {
        $query = $db->delete('staffsales_passcode')
          ->condition('email', $email, '=')
          ->execute();
      } catch (\Exception $e) {
        //watchdog
      }
    }

    $data = [];
    $data['valid'] = $valid;

    return $this->noCacheResponse($data);
  }

  private function sendPasscode($email) {
    $keyspace = '0123456789';
    $passcode = $this->genPasscode(6, $keyspace);

    try {
      $db = Database::getConnection();
      $db->insert('staffsales_passcode')
        ->fields([
          'email' => $email,
          'passcode' => $passcode,
          'created' => time(),
        ])->execute();
    } catch (\Exception $e) {
      $passcode = FALSE;
    }

    if ($passcode) {
      $mailManager = \Drupal::service('plugin.manager.mail');
      $module = 'staffsales_rest';
      $key = 'passcode';
      $to = $email;
      $langcode = \Drupal::languageManager()->getDefaultLanguage()->getId();
      $params['passcode'] = $passcode;
      $from = \Drupal::config('system.site')->get('mail');
      $send = TRUE;
      $mailManager->mail($module, $key, $to, $langcode, $params, $from, $send);
    }

    return $passcode;
  }

  private function is_allow($email, $seasonal_enabled) {
    $email_domain = preg_match('/\@([\w\.\-]*)/i', $email, $m);
    if(count($m) >= 2){
      $email_domain = $m[1];
      $email_domain = strtolower($email_domain);
    }

    $is_allow = FALSE;

    $domains = DomainAdminForm::getAllDomains();
    $yearround_domains = explode(PHP_EOL, $domains['yearround_domains']);
    $yearround_domains2 = explode(PHP_EOL, $domains['yearround_domains2']);
    $yearround_domains3 = explode(PHP_EOL, $domains['yearround_domains3']);
    $yearround_domains4 = explode(PHP_EOL, $domains['yearround_domains4']);
    $yearround_domains5 = explode(PHP_EOL, $domains['yearround_domains5']);
    $yearround_domains6 = explode(PHP_EOL, $domains['yearround_domains6']);
    $seasonal_domains = explode(PHP_EOL, $domains['seasonal_domains']);

    $emails = EmailAdminForm::getAllEmails();
    $yearround_emails = explode(PHP_EOL, $emails['yearround_emails']);
    $yearround_emails2 = explode(PHP_EOL, $emails['yearround_emails2']);
    $yearround_emails3 = explode(PHP_EOL, $emails['yearround_emails3']);
    $yearround_emails4 = explode(PHP_EOL, $emails['yearround_emails4']);
    $yearround_emails5 = explode(PHP_EOL, $emails['yearround_emails5']);
    $yearround_emails6 = explode(PHP_EOL, $emails['yearround_emails6']);
    $seasonal_emails = explode(PHP_EOL, $emails['seasonal_emails']);
    $vip_emails = explode(PHP_EOL, $emails['vip_emails']);
    $vip_emails2 = explode(PHP_EOL, $emails['vip_emails2']);
    $vip_emails3 = explode(PHP_EOL, $emails['vip_emails3']);
    $vip_emails4 = explode(PHP_EOL, $emails['vip_emails4']);
    $vip_emails5 = explode(PHP_EOL, $emails['vip_emails5']);
    $vip_emails6 = explode(PHP_EOL, $emails['vip_emails6']);
    $vip_emailss = explode(PHP_EOL, $emails['vip_emailss']);


    if(!empty($seasonal_enabled)) {
      if (in_array($email_domain, $seasonal_domains) || in_array($email, $seasonal_emails) || in_array($email, $vip_emailss)) {
        $is_allow = TRUE;
      }
    }
    //year_round_sales is always allowed
    if(in_array($email_domain, $yearround_domains)) {
      $is_allow = TRUE;
    }elseif(in_array($email_domain, $yearround_domains2)) {
      $is_allow = TRUE;
    }elseif(in_array($email_domain, $yearround_domains3)) {
      $is_allow = TRUE;
    }elseif(in_array($email_domain, $yearround_domains4)) {
      $is_allow = TRUE;
    }elseif(in_array($email_domain, $yearround_domains5)) {
      $is_allow = TRUE;
    }elseif(in_array($email_domain, $yearround_domains6)) {
      $is_allow = TRUE;
    }elseif(in_array($email, $yearround_emails) || in_array($email, $vip_emails)) {
      $is_allow = TRUE;
    }elseif(in_array($email, $yearround_emails2) || in_array($email, $vip_emails2)) {
      $is_allow = TRUE;
    }elseif(in_array($email, $yearround_emails3) || in_array($email, $vip_emails3)) {
      $is_allow = TRUE;
    }elseif(in_array($email, $yearround_emails4) || in_array($email, $vip_emails4)) {
      $is_allow = TRUE;
    }elseif(in_array($email, $yearround_emails5) || in_array($email, $vip_emails5)) {
      $is_allow = TRUE;
    }elseif(in_array($email, $yearround_emails6) || in_array($email, $vip_emails6)) {
      $is_allow = TRUE;
    }
    return $is_allow;
  }

  private function has_account($email){
    $has_account = FALSE;

    $shopify = new StaffsalesShopify();
    $opts = [];
    $opts['query'] = [
      'fields' => 'id,email,state',
      'query' => 'email:' . $email,
    ];

    try {
      $r = $shopify->get('customers/search', $opts);
      if ($r->customers) {
        foreach ($r->customers as $j) {
          if ($j->email == $email) {
            if($j->state == 'enabled'){
              $has_account = TRUE;
            }
            break;
          }
        }
      }
    } catch (ClientException $e) {
      $this->getError($e);
    }

    return $has_account;
  }

  /**
   * https://stackoverflow.com/questions/4356289/php-random-string-generator/31107425#31107425
   */
  private function genPasscode($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
    $pieces = [];
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
      $pieces [] = $keyspace[random_int(0, $max)];
    }
    return implode('', $pieces);
  }
}
