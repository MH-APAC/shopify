<?php

namespace Drupal\staffsales_rest;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceModifierInterface;

/**
 * Register 'application/x-www-form-urlencoded' and accept it in request header
 * of 'Content-Type'
 */
class StaffsalesRestServiceProvider implements ServiceModifierInterface {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    if ($container->has('http_middleware.negotiation') && is_a($container->getDefinition('http_middleware.negotiation')
        ->getClass(), '\Drupal\Core\StackMiddleware\NegotiationMiddleware', TRUE)) {
      $container->getDefinition('http_middleware.negotiation')
        ->addMethodCall('registerFormat', [
          'form',
          ['application/x-www-form-urlencoded'],
        ]);
    }
  }

}
