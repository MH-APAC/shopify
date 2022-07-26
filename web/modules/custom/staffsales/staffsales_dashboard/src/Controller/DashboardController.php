<?php
/**
 * @file
 * Contains \Drupal\staffsales_dashboard\Controller\DashboardController.
 */

namespace Drupal\staffsales_dashboard\Controller;
use Drupal\Core\Controller\ControllerBase;

class DashboardController extends ControllerBase {
  public function index() {
    $s = time();

    return array(
      '#theme' => 'index_template',
      '#attached' => array(
        'library' => array(
          'staffsales_dashboard/shopify-app-bridge-index',
          'staffsales_dashboard/shopify-app-bridge-action',
        ),
      ),
      '#test_var' => $s,
    );
  }
}
