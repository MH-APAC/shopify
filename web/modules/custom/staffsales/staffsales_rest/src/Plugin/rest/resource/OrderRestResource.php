<?php

namespace Drupal\staffsales_rest\Plugin\rest\resource;

use Drupal\Core\Database\Database;
use Drupal\staffsales_shopify\StaffsalesShopify;
use \Shopify\ClientException;
use \Shopify\IncomingWebhook;
use \Shopify\WebhookException;

/**
 * Provides a resource for shopify orders
 *
 * @RestResource(
 *   id = "order_rest_resource",
 *   label = @Translation("OrderRestResource"),
 *   uri_paths = {
 *     "canonical" = "/api/v1/order",
 *     "https://www.drupal.org/link-relations/create" = "/api/v1/order"
 *   }
 * )
 */
class OrderRestResource extends RestResourceBase {

  //sample code to test if GET works
  public function get() {
    $response = [
      'msg' => 'order',
      't' => time(),
    ];
    return $this->noCacheResponse($response);
  }

  /**
   * Responds to POST requests.
   *
   * e.g. ?op=webhook_create
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

    if (!empty($op)) {
      switch ($op) {
        //X-Shopify-Topic: orders/create
        case 'webhook_create':
          return $this->op_webhook_create($data);
          break;
        default:
          break;
      }
    }

    return $this->noCacheResponse([]);
  }

  private function op_webhook_create($data){
    $config = \Drupal::configFactory()->getEditable('staffsales_shopify.settings');
    $shopify_webhook_secret = $config->get('shopify_webhook_secret');

    if(empty($shopify_webhook_secret)){
      \Drupal::logger('staffsales_rest')->alert('shopify_webhook_secret not configured');
    }else{
      try {
        /*
         * By default, the $webhook->validate() will use $data from file_get_contents('php://input');
         * It is possible to pass our own $data manually, i.e. $webhook->validate($data)
         * However, some unicode characters are encoded differently and HMAC become invalid
         * e.g. "Moët & Chandon Impérial"
         * The $data parameter in our function, after json_encode($data) will become "Moët \u0026 Chandon Impérial"
         * However, from 'php://input'(raw data submitted from Shopify), is "Mo\u00ebt & Chandon Imp\u00e9rial"
         */
        $webhook = new IncomingWebhook($shopify_webhook_secret);
        $webhook->validate();
        $data = json_encode($data);

        \Drupal::logger('staffsales_rest')->notice('webhook_create validated, '.$data);

        //we have to reply back Shopify asap, so just capture it and let queue to handle the rest
        $queue_factory = \Drupal::service('queue');
        $queue = $queue_factory->get('notify_over_purchase_order_processor');
        $queue->createItem($data);
      } catch (WebhookException $e) {
        $header = json_encode($_SERVER);
        $data = json_encode($data);
        \Drupal::logger('staffsales_rest')->alert('HMAC invalid. header: '.$header.' data: '.$data);
      }
    }

    $data = [];

    return $this->noCacheResponse($data);
  }
}
