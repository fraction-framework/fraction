<?php

namespace Fraction\Component\Config;

use Fraction\Component\Config\Tree\ConfigNode;
use Fraction\Component\Config\Tree\ConfigNodeFactory;
use Fraction\Component\Config\Tree\ConfigNodeGroup;
use Fraction\Component\Config\Tree\ConfigRootNodeGroup;
use Fraction\Component\Locator;
use Fraction\DependencyInjection\ContainerInterface;
use Fraction\Throwable\FractionException;

class ConfigManager {
  /**
   * @var ConfigNodeGroup
   */
  private ConfigNodeGroup $config;

  /**
   * @var array|string[]
   */
  private array $configClasses = [Default\MainConfig::class, Default\ComponentsConfig::class];

  /**
   * @param Locator $locator
   * @param ContainerInterface $container
   */
  public function __construct(private readonly Locator $locator, private readonly ContainerInterface $container) {
    $rootGroup = ConfigNodeFactory::createRootGroup();

    $rootGroup->addChild('framework', $this->readComposerConfig(), true);

    $configList = [$rootGroup, ...$this->fetchConfigs()];
    $this->config = $this->merge(...$configList);
  }

  /**
   * @param string $path
   * @param mixed|null $default
   * @return mixed
   */
  public function get(string $path, mixed $default = null): mixed {
    $pathParts = explode('.', $path);
    $currentNode = $this->config;

    while ($pathParts) {
      $name = array_shift($pathParts);

      if ($currentNode instanceof ConfigNode && $currentNode->isGroupValue()) {
        $currentNode = $currentNode->getValue();
      }

      if (!$currentNode || ($currentNode instanceof ConfigNode && !$currentNode->isGroupValue())) {
        return $default;
      }

      $currentNode = $currentNode->getChild($name);
    }

    if ($currentNode?->isGroupValue()) {
      return $currentNode->toArray();
    }

    return $currentNode?->getValue();
  }

  /**
   * @throws FractionException
   */
  public function registerConfig(string $configClass): void {
    if (!is_subclass_of($configClass, AbstractConfig::class)) {
      throw new FractionException(sprintf('Config class %s must extend %s', $configClass, AbstractConfig::class));
    }

    $this->configClasses[] = $configClass;

    $this->merge($this->config, ...$this->container->get($configClass)->getConfig());
  }

  /**
   * @return array
   */
  private function fetchConfigs(): array {
    return array_map(fn($configClass) => $this->container->get($configClass)->getConfig(), $this->configClasses);
  }

  /**
   * @param ConfigNode|ConfigNodeGroup ...$configs
   * @return ConfigNodeGroup
   */
  private function merge(...$configs): ConfigNodeGroup {
    $mergedConfig = array_shift($configs);

    while ($configs) {
      $config = array_shift($configs);

      if ($config instanceof ConfigRootNodeGroup) {
        $configs = array_merge($config->getChildren(), $configs);
        continue;
      }

      if ($config instanceof ConfigNodeGroup) {
        foreach ($config->getChildren() as $child) {
          $mergedConfig->addChild($child->getName(), $child->getValue());
        }
      }

      if ($config instanceof ConfigNode) {
        $mergedConfig->addChild($config->getName(), $config->getValue());
      }
    }

    return $mergedConfig;
  }

  /**
   * @return array
   */
  private function readComposerConfig(): array {
    $composer = json_decode(file_get_contents($this->locator->getFrameworkRoot() . '/composer.json'), true);
    $composerFields = ['name', 'version', 'description', 'authors'];

    return array_filter($composer, fn($key) => in_array($key, $composerFields), ARRAY_FILTER_USE_KEY);
  }
}
