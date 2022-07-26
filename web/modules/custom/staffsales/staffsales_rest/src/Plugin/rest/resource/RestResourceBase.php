<?php

namespace Drupal\staffsales_rest\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use \Shopify\ClientException;

/**
 * Base class for RestResource
 */
abstract class RestResourceBase extends ResourceBase {

  //get the error message from Shopify Exception
  protected function getError(ClientException $e) {
    $errors = $e->getErrors();
    $error_messages = '';

    if (is_object($errors)) {
      $errors = get_object_vars($errors);
      $error_messages = [];
      foreach ($errors as $i => $j) {
        $error_messages[] = ucfirst($i) . ' ' . @$j[0];
      }
      $error_messages = implode("\n", $error_messages);
    }
    else {
      //not the format we are expecting
      $error_messages = $e->getMessage();
    }

    return $error_messages;
  }

  protected function noCacheResponse(array $data) {
    $response = new ResourceResponse($data);

    $build = [
      '#cache' => [
        'max-age' => 0,
      ],
    ];
    $cache_metadata = \Drupal\Core\Cache\CacheableMetadata::createFromRenderArray($build);
    $response->addCacheableDependency($cache_metadata);

    //we should allow origin in cors.config of default.services.yml
    //see https://www.drupal.org/node/2715637
    $response->headers->set('Access-Control-Allow-Origin', '*');

    return $response;
  }
}
