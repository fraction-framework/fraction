<?php

namespace Fraction\Throwable;

use Fraction\Http\Enum\ResponseStatus;

class NotFoundException extends RequestException {
  public function __construct(array $errors = [], string $message = 'Not Found', \Throwable $previous = null) {
    parent::__construct($message, ResponseStatus::NotFound, $errors, $previous);
  }
}
