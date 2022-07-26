<?php

namespace Drupal\staffsales_shopify;

use Shopify\PublicApp;

class StaffsalesShopify extends PublicApp{

  public $client;
  public $debug = FALSE;
  public $access_token = NULL;

  public function __construct() {
    $shopify_api_config = \Drupal::service('config.factory')
      ->get('shopify_api.settings');
    $config = \Drupal::service('config.factory')->get('staffsales_shopify.settings');

    $domain = $shopify_api_config->get('domain');
    $api_key = $shopify_api_config->get('api_key');
    $shared_secret = $shopify_api_config->get('shared_secret');
    $shopify_access_token = $config->get('shopify_access_token');

    $this->shop_domain = $domain;
    $this->shared_secret = $shared_secret;
    $this->api_key = $api_key;
    $this->client_type = 'public';
    if(!empty($shopify_access_token)){
      $this->access_token = $shopify_access_token;
      $this->setAccessToken($shopify_access_token);
    }
    $this->initClient();
  }

  public function initClient(array $opts = array()){
    if(!isset($opts['base_uri'])){
      $opts['base_uri'] = $this->getApiUrl();
    }
    $opts['debug'] = $this->debug;
    $this->client = $this->getNewHttpClient($opts);
  }

  //@FIXME: to use StaffsalesShopify
  public static function isHmacValid(){
    $valid = FALSE;

    try {
      $shopify_api_config = \Drupal::service('config.factory')
        ->get('shopify_api.settings');
      $domain = $shopify_api_config->get('domain');
      $api_key = $shopify_api_config->get('api_key');
      $shared_secret = $shopify_api_config->get('shared_secret');

      $client = new PublicApp($domain, $api_key, $shared_secret);
      $params = $_GET;
      $valid = $client->hmacSignatureValid($params);
    }catch (\Exception $e) {
      $valid = FALSE;
    }

    return $valid;
  }

  public static function getShopifyAppRedirectPath(){
    //\Drupal\Core\Url::fromRoute('staffsales_shopify.shopify_admin_redirect') won't work as Drupal may not init yet

    return '/shopify-admin-redirect';
  }
}
