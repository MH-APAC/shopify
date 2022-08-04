<?php

namespace Drupal\staffsales_queue\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\staffsales_shopify\StaffsalesShopify;
use function GuzzleHttp\default_ca_bundle;

/**
 * Processes sync_inventory_order.
 *
 * @QueueWorker(
 *   id = "sync_inventory_order_processor",
 *   title = @Translation("Queue processor: sync_inventory_order_processor"),
 *   cron = {"time" = 10}
 * )
 */
class SyncInventoryOrderProcessor extends QueueWorkerBase {

  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
    $data = json_decode($data, TRUE);

    $order_id = $data['id'];
    $line_items = $data['line_items'];
    $products = [];

    foreach ($line_items as $j) {
      $prefix = substr($j['variant_title'], 0, 3);
      $prefix = strtoupper($prefix);
      switch ($prefix){
        case 'MH_':
          $products[] = array(
            ''
          );
          break;
        case 'GR_':
          break;
        default:
          break;
      }
      $total_quantity = $total_quantity + $j['quantity'];
      $j['variant_title'];
    }

    if ($total_quantity >= $over_purchase_quantity) {
      \Drupal::logger('NotifyOverPurchaseOrderProcessor')
        ->notice('Order ID: ' . $order_id . ', total_quantity: ' . $total_quantity);
      $this->notifyOverPurchaseOrder($data, $over_purchase_email, $total_quantity);
      $this->tagOrder($order_id);
    }
  }
}
