<?php

namespace Fraction\Component\Config;

use Fraction\Component\Config\Tree\ConfigNode;
use Fraction\Component\Config\Tree\ConfigNodeFactory;
use Fraction\Component\Config\Tree\ConfigNodeGroup;
use Fraction\Component\Locator;
use Fraction\Component\Parser\Yaml;

abstract class AbstractConfig {
  private ConfigNode|ConfigNodeGroup|null $config;

  public function __construct(private readonly Locator $locator, private readonly Yaml $yaml) {
    $configFile = $this->locator->getConfigDir() . '/' . $this->getConfigFile();

    $configTree = $this->buildConfigTree(ConfigNodeFactory::createRootGroup());

    if (!file_exists($configFile)) {
      $this->config = $configTree;
      return;
    }

    $configData = $this->yaml->parseFile($configFile);
    $this->config = $this->applyConfig($configTree, $configData);
  }

  public function getConfig(): ConfigNode|ConfigNodeGroup|null {
    return $this->config;
  }

  abstract protected function buildConfigTree(ConfigNodeGroup $root): ConfigNodeGroup;

  abstract protected function getConfigFile(): string;

  private function applyConfig(ConfigNodeGroup $configTree, array $configData): ConfigNodeGroup {
    foreach ($configData as $key => $value) {
      $node = $configTree->getChild($key);

      if (!$node) {
        continue;
      }

      if ($node->isGroupValue() && is_array($value)) {
        $this->applyConfig($node->getValue(), $value);
      } else {
        $node->isMergeValueOnApply() ? $node->mergeValue($value) : $node->setValue($value);
      }
    }

    return $configTree;
  }
}
