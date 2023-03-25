<?php

namespace Fraction\Http\Attribute;

use Attribute;
use Fraction\Http\Enum\ResponseType;

#[Attribute(Attribute::TARGET_METHOD)]
class View {
  public function __construct(private readonly ?string $resource = null, private readonly ?string $template = null, private ?ResponseType $response = null) {
  }

  /**
   * @return string|null
   */
  public function getResource(): ?string {
    return $this->resource;
  }

  /**
   * @return ResponseType|null
   */
  public function getResponseType(): ?ResponseType {
    return $this->response;
  }

  /**
   * @return string|null
   */
  public function getResponseTypeValue(): ?string {
    return $this->response?->value;
  }

  /**
   * @return ?string
   */
  public function getTemplate(): ?string {
    return $this->template;
  }
}
