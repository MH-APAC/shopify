<?php
/**
 * @file
 * Contains \Drupal\staffsales_email\Controller\EmailAdminController.
 */

namespace Drupal\staffsales_email\Controller;

use Drupal\Core\Controller\ControllerBase;

class EmailAdminController extends ControllerBase {
  public function index() {
    $form = $this->formBuilder()->getForm('Drupal\staffsales_email\Form\EmailAdminForm');
    $form['#cache'] = ['max-age' => 0];

    return array(
      'form' => $form,
    );
  }
}
