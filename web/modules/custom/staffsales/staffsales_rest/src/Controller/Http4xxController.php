<?php

namespace Drupal\staffsales_rest\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Http4xxController
 * Alternatively, we can subscribe to KernelEvents::EXCEPTION
 * extend DefaultExceptionHtmlSubscriber
 * and use on4xx(GetResponseForExceptionEvent $event){} to get exact HTTP status
 * code error and handling more properly
 */
class Http4xxController extends ControllerBase {

  /**
   * Handling on REST error, e.g. 405 Method Not Allowed
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   A render array containing the message to display for 4xx errors.
   */
  public function on4xx() {
    $response = new Response();

    //showing nothing to the outside world
    $markup = '4xx Error';
    $response->setContent($markup);

    return $response;
  }
}
