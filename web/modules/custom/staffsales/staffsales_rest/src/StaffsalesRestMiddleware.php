<?php

namespace Drupal\staffsales_rest;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * StaffsalesRestMiddleware
 */
class StaffsalesRestMiddleware implements HttpKernelInterface {

  /**
   * HttpKernelInterface
   *
   * @var \Symfony\Component\HttpKernel\HttpKernelInterface
   */
  protected $httpKernel;

  /**
   * StaffsalesAccessVerifier
   *
   * @var \Drupal\staffsales_access\StaffsalesAccessVerifierInterface
   */
  protected $staffsalesAccessVerifier;

  /**
   * AccountProxyInterface
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   *
   * @param \Symfony\Component\HttpKernel\HttpKernelInterface $http_kernel
   *   The decorated kernel.
   */
  public function __construct(HttpKernelInterface $http_kernel) {
    $this->httpKernel = $http_kernel;
  }

  /**
   * {@inheritdoc}
   */
  public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = TRUE) {
    if(static::isAPIPath($request)){
      //it is also possible to implement ServiceModifierInterface and modify other service
      $request->attributes->set('StaffsalesAccessMiddleware_skip_handle', TRUE);
    }

    $response = $this->httpKernel->handle($request, $type, $catch);

    return $response;
  }

  public static function isAPIPath($request){
    $is_api = stripos($request->getRequestUri(), '/api/');

    if ($is_api !== FALSE) {
      $is_api = TRUE;
    }

    return $is_api;
  }
}
