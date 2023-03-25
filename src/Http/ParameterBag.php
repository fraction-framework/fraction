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
