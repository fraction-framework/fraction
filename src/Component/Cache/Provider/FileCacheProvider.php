<?php

namespace Fraction\Component\Cache\Provider;

use Fraction\Component\Cache\CacheEntity;
use Fraction\Component\Locator;
use Fraction\Throwable\FractionException;

class FileCacheProvider extends CacheProvider {
  /**
   * @param Locator $locator
   */
  public function __construct(private readonly Locator $locator) {
  }

  /**
   * @return void
   */
  public function clear(): void {
    if (file_exists($this->cacheEntity)) {
      unlink($this->cacheEntity);
    }
  }

  /**
   * @throws FractionException
   */
  public function setCacheEntity(CacheEntity $cacheEntity): void {
    if (!$this->locator->getProjectRoot()) {
      throw new FractionException('Project root is not set.');
    }

    if (!file_exists($this->locator->getCacheDir())) {
      mkdir($this->locator->getCacheDir(), 0755, true);
    }

    $this->cacheEntity = sprintf('%s/%s.cache', $this->locator->getCacheDir(), $cacheEntity->value);
  }

  /**
   * @return array
   */
  protected function readCacheFile(): array {
    if (file_exists($this->cacheEntity)) {
      $cacheData = unserialize(file_get_contents($this->cacheEntity));
    } else {
      $cacheData = [];
    }
    return $cacheData;
  }

  /**
   * @param array $cacheData
   * @return void
   */
  protected function writeToCache(array $cacheData): void {
    file_put_contents($this->cacheEntity, serialize($cacheData));
  }
}
