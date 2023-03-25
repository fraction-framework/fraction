<?php

namespace Fraction\Component\Config\Default;

use Fraction\Component\Cache\Provider\CacheProvider;
use Fraction\Component\Cache\Provider\FileCacheProvider;
use Fraction\Component\Config\AbstractConfig;
use Fraction\Component\Config\Tree\ConfigNodeGroup;
use Fraction\Component\Routing\Provider\AttributeRoutingProvider;
use Fraction\Component\Routing\Provider\RoutingProvider;
use Fraction\Docs\Provider\OpenApiSpecProvider;
use Fraction\Docs\Provider\SpecProvider;

class ComponentsConfig extends AbstractConfig {
  protected function buildConfigTree(ConfigNodeGroup $root): ConfigNodeGroup {
    $root
      ->addChild('components', [
        CacheProvider::class => FileCacheProvider::class,
        RoutingProvider::class => AttributeRoutingProvider::class,
        SpecProvider::class => OpenApiSpecProvider::class
      ])
      ->setMergeValueOnApply(true);

    return $root;
  }

  protected function getConfigFile(): string {
    return 'components.yml';
  }
}
