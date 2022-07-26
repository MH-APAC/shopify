<?php

namespace Drupal\staffsales_rest\Encoder;

use Drupal\serialization\Encoder\JsonEncoder as SerializationJsonEncoder;

/**
 * Encodes Form post data to array
 */
class FormEncoder extends SerializationJsonEncoder {

  /**
   * The formats that this Encoder supports.
   *
   * @var string
   */
  protected static $format = ['form'];

  /**
   * {@inheritdoc}
   */
  public function decode($data, $format, array $context = array()) {
    parse_str($data, $result);
    return $result;
  }
}
