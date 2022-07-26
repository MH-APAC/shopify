<?php

namespace Drupal\staffsales_access;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface for StaffsalesAccess
 */
interface StaffsalesAccessVerifierInterface {

  /**
   * Check if current request is blocked
   *
   * @param Request $request
   *   The current request
   *
   * @return bool
   *
   */
  public function isBlocked($request);
}
