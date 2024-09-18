<?php

namespace Drupal\staffsales_access;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * HTTP middleware to implement StaffsalesAccessVerifier
 */
class StaffsalesAccessMiddleware implements HttpKernelInterface {

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
   * @param \Drupal\staffsales_access\StaffsalesAccessVerifierInterface $verifier
   *   StaffsalesAccessVerifier
   */
  public function __construct(HttpKernelInterface $http_kernel, StaffsalesAccessVerifierInterface $verifier) {
    $this->httpKernel = $http_kernel;
    $this->staffsalesAccessVerifier = $verifier;
  }

  /**
   * {@inheritdoc}
   */
  public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = TRUE) {
    $response = NULL;
    $skip = $request->attributes->get('StaffsalesAccessMiddleware_skip_handle');
    $handle = TRUE;

    if(!$skip && $this->staffsalesAccessVerifier->isBlocked($request)){
        //we don't show anything, don't let user to guess what framework we are using
        $response = new Response('403 Forbidden', 403);
        $handle = FALSE;
    }

    if($handle){
      $response = $this->httpKernel->handle($request, $type, $catch);
      //put this in staffsales_shopify??
      $response->headers->remove('X-Frame-Options');
      $response->headers->set('Access-Control-Allow-Origin', '*');
      $response->headers->set('Content-Security-Policy', 'frame-ancestors https://*.myshopify.com https://*.shopify.com');
    }

    return $response;
  }
}
