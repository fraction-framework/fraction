<?php

namespace Fraction\Component\Cache;

use Fraction\Component\Cache\Provider\CacheProvider;

readonly class CacheComponent {
  /**
   * @param CacheProvider $provider
   */
  public function __construct(private CacheProvider $provider) {
    $provider->setExpirationTime(3600);
  }

  /**
   * @return ?bool
   */
  public function clear(): ?bool {
    return $this->provider->clear();
  }

  /**
   * @param string $key
   * @return void
   */
  public function delete(string $key): void {
    $this->provider->delete($key);
  }

  /**
   * @param string $key
   * @return mixed
   */
  public function get(string $key): mixed {
    return $this->provider->get($key);
  }

  /**
   * @param string $key
   * @return bool
   */
  public function isset(string $key): bool {
    return $this->provider->isset($key);
  }

  /**
   * @param string $key
   * @param $value
   * @return void
   */
  public function set(string $key, $value): void {
    $this->provider->set($key, $value);
  }

  /**
   * @param CacheEntity $cacheEntity
   * @return void
   */
  public function setCacheEntity(CacheEntity $cacheEntity): void {
    $this->provider->setCacheEntity($cacheEntity);
  }
}
