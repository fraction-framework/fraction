<?php

namespace Fraction\Http\Attribute;

use Attribute;
use Fraction\Http\Enum\ResponseStatus;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
readonly class Error {
  /**
   * @param ResponseStatus $responseStatus
   * @param string $reference
   */
  public function __construct(
    private ResponseStatus $responseStatus,
    private string         $reference,
  ) {
  }

  /**
   * @return string
   */
  public function getReference(): string {
    return $this->reference;
  }

  /**
   * @return ResponseStatus
   */
  public function getResponseStatus(): ResponseStatus {
    return $this->responseStatus;
  }

  /**
   * @return int
   */
  public function getStatusCode(): int {
    return $this->responseStatus->value;
  }

  /**
   * @return string
   */
  public function getStatusMessage(): string {
    return $this->responseStatus->name;
  }
}