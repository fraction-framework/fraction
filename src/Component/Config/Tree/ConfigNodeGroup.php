<?php

namespace Fraction\Component\Config\Tree;

class ConfigNodeGroup {
  /**
   * @var ConfigNode[]
   */
  private array $nodes = [];

  /**
   * @param string $name
   * @param mixed|null $value
   * @param bool $fromArray
   * @return ConfigNode
   */
  public function addChild(string $name, mixed $value = null, bool $fromArray = false): ConfigNode {
    $node = ConfigNodeFactory::createNode($name, $value, $fromArray);
    $this->nodes[$name] = $node;
    return $node;
  }

  /**
   * @param string $name
   * @return ConfigNode|null
   */
  public function getChild(string $name): ?ConfigNode {
    return $this->nodes[$name] ?? null;
  }

  /**
   * @return ConfigNode[]
   */
  public function getChildren(): array {
    return $this->nodes;
  }

  /**
   * @param string $name
   * @return bool
   */
  public function hasChild(string $name): bool {
    return isset($this->nodes[$name]);
  }

  /**
   * @return bool
   */
  public function hasChildren(): bool {
    return count($this->nodes) > 0;
  }
}
