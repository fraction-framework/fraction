<?php

namespace Fraction\Throwable;

use Fraction\Http\Enum\ResponseStatus;

class BadRequestException extends RequestException {
  public function __construct(array $errors = [], string $message = 'Bad Request', \Throwable $previous = null) {
    parent::__construct($message, ResponseStatus::BadRequest, $errors, $previous);
  }
}
