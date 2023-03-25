<?php

namespace Fraction\Http\Attribute;

use Attribute;
use Fraction\Http\Enum\RequestMethod;

#[Attribute(Attribute::TARGET_METHOD)]
class Route {
  public function __construct(public RequestMethod $method = RequestMethod::GET, public string $path = '', public string $description = '', public ?array $params = null) {
  }

  /**
   * @return string
   */
  public function getDescription(): string {
    return $this->description;
  }

  /**
   * @return RequestMethod
   */
  public function getMethod(): RequestMethod {
    return $this->method;
  }

  /**
   * @return string
   */
  public function getMethodValue(): string {
    return $this->method->value;
  }

  /**
   * @return array|null
   */
  public function getParams(): ?array {
    return $this->params;
  }

  /**
   * @return string
   */
  public function getPath(): string {
    return $this->path;
  }
}
