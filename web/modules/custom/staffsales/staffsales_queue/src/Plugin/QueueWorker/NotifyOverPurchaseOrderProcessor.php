<?php

namespace Drupal\staffsales_queue\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\staffsales_shopify\StaffsalesShopify;

/**
 * Processes notify_over_purchase_order.
 *
 * @QueueWorker(
 *   id = "notify_over_purchase_order_processor",
 *   title = @Translation("Queue processor: notify_over_purchase_order_processor"),
 *   cron = {"time" = 10}
 * )
 */
class NotifyOverPurchaseOrderProcessor extends QueueWorkerBase {

  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
    $data = json_decode($data, TRUE);
    $over_purchase_quantity = 60;
    $over_purchase_email = NULL;

    $shopify = new StaffsalesShopify();
    $opts = [];
    $opts['query'] = ['namespace' => 'staffsales'];
    $metafields = $shopify->get('metafields', $opts);
    $metafields = $metafields->metafields;

    foreach ($metafields as $i) {
      switch ($i->key) {
        case 'quota_overpurchase_quantity':
          $over_purchase_quantity = $i->value;
          break;
        case 'quota_overpurchase_email':
          $over_purchase_email = $i->value;
          break;
        default:
          break;
      }
    }

    $order_id = $data['id'];
    $line_items = $data['line_items'];
    $total_quantity = 0;
    foreach ($line_items as $i => $j) {
      $total_quantity = $total_quantity + $j['quantity'];
    }

    if ($total_quantity >= $over_purchase_quantity) {
      \Drupal::logger('NotifyOverPurchaseOrderProcessor')
        ->notice('Order ID: ' . $order_id . ', total_quantity: ' . $total_quantity);
      $this->notifyOverPurchaseOrder($data, $over_purchase_email, $total_quantity);
      $this->tagOrder($order_id);
    }
  }

  private function notifyOverPurchaseOrder($data, $over_purchase_email, $total_quantity) {
    $line_items = $data['line_items'];
    $order_number = $data['order_number'];
    $customer = $data['customer'];
    $email = $customer['email'];
    $first_name = $customer['first_name'];
    $last_name = $customer['last_name'];
    $total_price = $data['total_price'];

    $params = [];
    $params['order_number'] = $order_number;
    $params['email'] = $email;
    $params['first_name'] = $first_name;
    $params['last_name'] = $last_name;
    $params['total_price'] = $total_price;
    $params['total_quantity'] = $total_quantity;
    $params['line_items'] = $line_items;

    $mailManager = \Drupal::service('plugin.manager.mail');
    $module = 'staffsales_queue';
    $key = 'over_purchase_order';
    $to = $over_purchase_email;
    $langcode = \Drupal::languageManager()->getDefaultLanguage()->getId();
    $from = \Drupal::config('system.site')->get('mail');
    $send = TRUE;
    $mailManager->mail($module, $key, $to, $langcode, $params, $from, $send);
  }

  private function tagOrder($order_id) {
    $tag = 'over_purchase';
    try {
      $shopify = new StaffsalesShopify();
      $r = $shopify->get('orders/' . $order_id);
      $tags = $r->order->tags;
      if (empty($tags)) {
        $tags = $tag;
      }
      else {
        $tags = $tags . ',' . $tag;
      }
      $notes = $r->order->note;
      if (empty($notes)) {
        $notes = $tag;
      }
      else {
        $notes = $notes . ', ' . $tag;
      }
      $data = [];
      $data['order'] = [];
      $data['order']['id'] = $order_id;
      $data['order']['note'] = $notes;
      $data['order']['tags'] = $tags;
      $r = $shopify->put('orders/' . $order_id, $data);
    } catch (\Exception $e) {
      \Drupal::messenger()
        ->addError('NotifyOverPurchaseOrderProcessor::tagOrder(): ' . $e->getMessage());
    }
  }
}
