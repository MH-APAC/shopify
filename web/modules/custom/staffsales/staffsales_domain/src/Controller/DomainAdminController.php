<?php
/**
 * @file
 * Contains \Drupal\staffsales_domain\Controller\DomainAdminController.
 */

namespace Drupal\staffsales_domain\Controller;

use Drupal\Core\Controller\ControllerBase;

class DomainAdminController extends ControllerBase {
  public function index() {
    $form = $this->formBuilder()->getForm('Drupal\staffsales_domain\Form\DomainAdminForm');
    $form['#cache'] = ['max-age' => 0];

    return array(
      'form' => $form,
    );
  }
}
