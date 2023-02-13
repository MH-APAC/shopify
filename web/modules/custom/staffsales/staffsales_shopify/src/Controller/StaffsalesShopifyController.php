<?php

namespace Drupal\staffsales_shopify\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Shopify\PublicApp;

/**
 * StaffsalesShopifyController
 */
class StaffsalesShopifyController extends ControllerBase {

  /**
   * Redirect page for Shopify partner app during OAuth
   */
  public function shopify_admin_redirect() {
    $shopify_api_config = \Drupal::service('config.factory')->get('shopify_api.settings');
    $domain = $shopify_api_config->get('domain');
    $api_key = $shopify_api_config->get('api_key');
    $shared_secret = $shopify_api_config->get('shared_secret');

    $tempstore = \Drupal::service('tempstore.private')->get('staffsales_shopify');
    $shopify_oauth_state = $tempstore->get('shopify_oauth_state');

    $client = new PublicApp($domain, $api_key, $shared_secret);
    //the $_GET['code'] param is automatically assigned in getAccessToken()
    $token = $client->getAccessToken();
    $client->setState($shopify_oauth_state);

    if(empty($token)){
      throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException($client->getLastResponse());
    }else{
      //store token and redirect to dashboard
      \Drupal::service('config.factory')->getEditable('staffsales_shopify.settings')
        ->set('shopify_access_token', $token)
        ->save();

      $redirect = 'https://'.$domain.'/admin/apps';
      $response = new TrustedRedirectResponse($redirect);

      return $response;
    }
  }

  /**
   * Install this Shopify partner app using OAuth
   */
  public function shopify_admin_install() {
    $shopify_api_config = \Drupal::service('config.factory')->get('shopify_api.settings');
    $domain = $shopify_api_config->get('domain');
    $api_key = $shopify_api_config->get('api_key');

    $shopify_oauth_state = uniqid();
    $tempstore = \Drupal::service('tempstore.private')->get('staffsales_shopify');
    $tempstore->set('shopify_oauth_state', $shopify_oauth_state);

    $scopes = array(
      'read_content',
      'write_content',
      'read_themes',
      'write_themes',
      'read_products',
      'write_products',
      'read_product_listings',
      'read_customers',
      'write_customers',
      'read_orders',
      'write_orders',
      'read_draft_orders',
      'write_draft_orders',
      'read_inventory',
      'write_inventory',
      'read_locations',
      'read_script_tags',
      'write_script_tags',
      'read_fulfillments',
      'write_fulfillments',
      'read_shipping',
      'write_shipping',
      'read_analytics',
      'read_checkouts',
      'write_checkouts',
      'read_reports',
      'write_reports',
      'read_price_rules',
      'write_price_rules',
      'read_marketing_events',
      'write_marketing_events',
      'read_resource_feedbacks',
      'write_resource_feedbacks',
    );

    //toString() trigger the core early render and will cause error:
    //"The controller result claims to be providing relevant cache metadata, but leaked metadata was detected"
    //using toString(TRUE) to let Drupal take care cache metadata
    //https://www.lullabot.com/articles/early-rendering-a-lesson-in-debugging-drupal-8
    $redirect = \Drupal\Core\Url::fromRoute('staffsales_shopify.shopify_admin_redirect', [], ['absolute' => TRUE])->toString(TRUE);
    $redirect = $redirect->getGeneratedUrl();

    $url = strtr(PublicApp::AUTHORIZE_URL_FORMAT, [
      '{shop_domain}' => $domain,
      '{api_key}' => $api_key,
      '{scopes}' => implode(',', $scopes),
      '{redirect_uri}' => urlencode($redirect),
      '{state}' => $shopify_oauth_state,
    ]);

    $response = new TrustedRedirectResponse($url);

    return $response;
  }
}
