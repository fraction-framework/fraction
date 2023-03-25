<?php

namespace Fraction\Component\Config\Tree;

class ConfigNodeFactory {
  /**
   * @param string $name
   * @param mixed|null $value
   * @param bool $fromArray
   * @return ConfigNode
   */
  public static function createNode(string $name, mixed $value = null, bool $fromArray = false): ConfigNode {
    if ($fromArray && is_array($value)) {
      $group = static::createGroup();
      foreach ($value as $key => $val) {
        $group->addChild($key, $val);
      }

      return new ConfigNode($name, $group);
    }

    return new ConfigNode($name, $value);
  }

  public static function createGroup(): ConfigNodeGroup {
    return new ConfigNodeGroup();
  }

  public static function createRootGroup(): ConfigNodeGroup {
    return new ConfigRootNodeGroup();
  }
}
