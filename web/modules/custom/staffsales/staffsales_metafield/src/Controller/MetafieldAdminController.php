<?php
/**
 * @file
 * Contains \Drupal\staffsales_metafield\Controller\MetafieldAdminController.
 */

namespace Drupal\staffsales_metafield\Controller;

use Drupal\Core\Controller\ControllerBase;

class MetafieldAdminController extends ControllerBase {
  public function setting() {
    $build = $this->buildAdminForm('SettingAdminForm');

    return $build;
  }

  public function quota() {
    $build = $this->buildAdminForm('QuotaAdminForm');

    return $build;
  }

  private function buildAdminForm($formName) {
    $form = $this->formBuilder()->getForm('Drupal\staffsales_metafield\Form\\'.$formName);
    $form['#cache'] = ['max-age' => 0];

    return array(
      'form' => $form,
    );
  }
}
