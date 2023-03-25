<?php

namespace Fraction\Component\Cache;

use Fraction\Component\Cache\Provider\CacheProvider;

readonly class CacheComponent {
  public function __construct(private CacheProvider $provider) {
    $provider->setExpirationTime(3600);
  }

  public function get(string $key): mixed {
    return $this->provider->get($key);
  }

  public function isset(string $key): bool {
    return $this->provider->isset($key);
  }

  public function set(string $key, $value): void {
    $this->provider->set($key, $value);
  }

  public function setCacheEntity(CacheEntity $cacheEntity): void {
    $this->provider->setCacheEntity($cacheEntity);
  }
}
