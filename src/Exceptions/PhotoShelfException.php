<?php
namespace Ternaryop\PhotoshelfUtil\Exceptions;

use RuntimeException;

class PhotoShelfException extends RuntimeException {
  public function __construct(string $message, int $code = 401)
  {
    parent::__construct($message, $code);
  }
}
