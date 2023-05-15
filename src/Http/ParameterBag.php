<?php

namespace Fraction\Http;

class ParameterBag {
  private array $parameters;

  private function __construct(array $parameters) {
    $this->parameters = $parameters;
  }

  public static function createFormArray(array $parameters): static {
    return new static($parameters);
  }

  public static function createFromStdIn(): static {
    $parameters = [];
    $str = file_get_contents('php://input');

    $contentType = $_SERVER['CONTENT_TYPE'] ?? 'application/x-www-form-urlencoded';

    if ($contentType === 'application/x-www-form-urlencoded') {
      parse_str($str, $parameters);
      return new static($parameters);
    }

    if ($contentType === 'application/json') {
      $parameters = json_decode($str, true);
      return new static($parameters);
    }

    return new static($parameters);
  }

  public function all(): array {
    return $this->parameters;
  }

  public function get(string $name, mixed $default = null): mixed {
    $this->parameters[$name] ??= $default;
    return $this->parameters[$name];
  }

  public function has(string $name): bool {
    return isset($this->parameters[$name]);
  }

  public function set(string $name, mixed $value): void {
    $this->parameters[$name] = $value;
  }
}
