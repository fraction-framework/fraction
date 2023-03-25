<?php

namespace Fraction\Throwable;

use Fraction\Http\Enum\ResponseStatus;

class UnauthorizedException extends RequestException {
  public function __construct(array $errors = [], string $message = 'Unauthorized', \Throwable $previous = null) {
    parent::__construct($message, ResponseStatus::Unauthorized, $errors, $previous);
  }
}
