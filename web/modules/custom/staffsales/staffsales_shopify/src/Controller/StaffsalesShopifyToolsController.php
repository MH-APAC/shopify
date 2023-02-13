<?php
/**
 * @file
 * Contains \Drupal\staffsales_shopify\Controller\StaffsalesShopifyToolsController.
 */

namespace Drupal\staffsales_shopify\Controller;

use Drupal\Core\Controller\ControllerBase;

class StaffsalesShopifyToolsController extends ControllerBase {
  public function variant_split() {
    $build = $this->buildAdminForm('VariantSplitForm');

    return $build;
  }

  private function buildAdminForm($formName) {
    $form = $this->formBuilder()->getForm('Drupal\staffsales_shopify\Form\\'.$formName);
    $form['#cache'] = ['max-age' => 0];

    return array(
      'form' => $form,
      '#attached' => array(
        'library' => array(
          'staffsales_shopify/variant-split',
        ),
      ),
    );
  }
}
