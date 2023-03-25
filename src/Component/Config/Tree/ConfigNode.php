<?php

namespace Fraction\Component\Config\Tree;

class ConfigNode {
  /**
   * @var bool
   */
  private bool $mergeValueOnApply = false;
  /**
   * @var callable[]
   */
  private array $resolvers = [];

  public function __construct(private string $name, private mixed $value = null) {
  }

  /**
   * @param callable|null $callback
   * @return ConfigNodeGroup
   */
  public function addGroup(?callable $callback = null): ConfigNodeGroup {
    $group = ConfigNodeFactory::createGroup();
    $this->value = $group;

    if ($callback) {
      $callback($group);
    }

    return $group;
  }

  /**
   * @param callable $callable
   * @return ConfigNode
   */
  public function addResolver(callable $callable): static {
    $this->resolvers[] = $callable;
    return $this;
  }

  /**
   * @return string
   */
  public function getName(): string {
    return $this->name;
  }

  /**
   * @return mixed
   */
  public function getValue(): mixed {
    return array_reduce($this->resolvers, fn($value, $resolver) => $resolver($value), $this->value);
  }

  /**
   * @return bool
   */
  public function isGroupValue(): bool {
    return $this->value instanceof ConfigNodeGroup;
  }

  /**
   * @return bool
   */
  public function isMergeValueOnApply(): bool {
    return $this->mergeValueOnApply;
  }

  /**
   * @param bool $mergeValueOnApply
   */
  public function setMergeValueOnApply(bool $mergeValueOnApply): void {
    $this->mergeValueOnApply = $mergeValueOnApply;
  }

  /**
   * @param mixed $value
   * @return void
   */
  public function mergeValue(mixed $value): void {
    if (is_array($this->value) && is_array($value)) {
      $this->value = array_merge($this->value, $value);
    }
  }

  /**
   * @param string $name
   * @return ConfigNode
   */
  public function setName(string $name): static {
    $this->name = $name;
    return $this;
  }

  /**
   * @param mixed $value
   * @return void
   */
  public function setValue(mixed $value): void {
    $this->value = $value;
  }

  /**
   * @return array
   */
  public function toArray(): array {
    if ($this->isGroupValue()) {
      return array_map(fn($node) => $node->toArray(), $this->value->getChildren());
    }

    return [$this->name => $this->value];
  }
}
