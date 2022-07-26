<?php

/**
 * @file
 * Contains \Drupal\staffsales_rest\Authentication\Provider\RESTAuth
 */

namespace Drupal\staffsales_rest\Authentication\Provider;

use Drupal\Core\Authentication\AuthenticationProviderInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\staffsales_rest\StaffsalesRestMiddleware;
use Symfony\Component\HttpFoundation\Request;

/**
 * staffsales_access has blocked all non-whitelist IP visit
 * we want to allow all visitors to access REST api
 */
class RESTAuth implements AuthenticationProviderInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a HTTP basic authentication provider object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity manager service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function applies(Request $request) {
    //Only apply this auth if request is api
    return StaffsalesRestMiddleware::isAPIPath($request);
  }

  /**
   * {@inheritdoc}
   */
  public function authenticate(Request $request) {
    //return anonymous user
    return $this->entityTypeManager->getStorage('user')->load(0);
  }
}
