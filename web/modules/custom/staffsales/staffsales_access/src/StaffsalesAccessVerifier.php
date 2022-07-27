<?php

namespace Drupal\staffsales_access;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\staffsales_shopify\StaffsalesShopify;
use Symfony\Component\HttpFoundation\Request;

class StaffsalesAccessVerifier implements StaffsalesAccessVerifierInterface {

  const TRUSTED_PROXIES = [
    //Cloudflare, https://www.cloudflare.com/ips/
    '103.21.244.0/22',
    '103.22.200.0/22',
    '103.31.4.0/22',
    '104.16.0.0/12',
    '108.162.192.0/18',
    '131.0.72.0/22',
    '141.101.64.0/18',
    '162.158.0.0/15',
    '172.64.0.0/13',
    '173.245.48.0/20',
    '188.114.96.0/20',
    '190.93.240.0/20',
    '197.234.240.0/22',
    '198.41.128.0/17',
    '2400:cb00::/32',
    '2405:8100::/32',
    '2405:b500::/32',
    '2606:4700::/32',
    '2803:f800::/32',
    '2c0f:f248::/32',
    '2a06:98c0::/29',
  ];

  /**
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   *
   * We cannot get the request in constructor as it may not initialized by the system yet
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   Config Factory
   */
  public function __construct(ConfigFactoryInterface $configFactory) {
    $this->config = $configFactory->get('staffsales_access.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function isBlocked($request) {
    $blocked = TRUE;

    //CLI, cron, admin user, IP, query_string
    $is_cli = (PHP_SAPI == 'cli');

    //for getClientIps() to get client's IP correctly
    $trusted_ips = self::TRUSTED_PROXIES;
    $trusted_ips[] = '127.0.0.1';
    Request::setTrustedProxies(
      $trusted_ips,
      Request::HEADER_X_FORWARDED_ALL
    );
    $ip_addresses = $request->getClientIps();
    $hmac = $request->query->get('hmac');
    $allowed_ip = $this->config->get('allowed_ip');
    $allowed_ip = $allowed_ip ? trim($allowed_ip) : $allowed_ip;

    //we only start blocking if admin has configured whitelist IP
    if($is_cli || empty($allowed_ip)){
      $blocked = FALSE;
    }

    if($allowed_ip) {
      $allowed_ip = preg_split("/\r\n|\n|\r/", $allowed_ip);
      $allowed_ip = array_map('trim', $allowed_ip);
      foreach ($ip_addresses as $i){
        if(in_array($i, $allowed_ip)){
          $blocked = FALSE;
          break;
        }
      }
    }

    $current_path = $request->getPathInfo();
    $is_shopify_app_redirect = ($current_path === StaffsalesShopify::getShopifyAppRedirectPath()) ? TRUE : FALSE;

    if(!empty($hmac) && !$is_shopify_app_redirect){
      $valid = StaffsalesShopify::isHmacValid();
      if($valid){
        $blocked = FALSE;
      }else{
        $blocked = TRUE;
      }
    }

    if(stripos($current_path, 'cron') !== FALSE){
      $blocked = FALSE;
    }

    return $blocked;
  }
}
