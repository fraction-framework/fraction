<?php

namespace Fraction\Throwable;

use Fraction\Http\Enum\ResponseStatus;

abstract class RequestException extends FractionException {
  private array $errors;

  public function __construct(string $message = '', ResponseStatus $responseStatus = ResponseStatus::BadRequest, $errors = [], \Throwable $previous = null) {
    parent::__construct($message, $responseStatus->value, $previous);
    $this->errors = $errors;
  }

  public function getErrors(): array {
    return $this->errors;
  }

  public function getResponse(): array {
    return [
      'message' => $this->getMessage(),
      'errors' => $this->getErrors(),
    ];
  }

  public function getResponseStatus(): ResponseStatus {
    return ResponseStatus::from($this->getCode());
  }
}
