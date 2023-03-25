<?php

namespace Fraction\Component\Cache\Provider;

use Fraction\Component\Cache\CacheEntity;

abstract class CacheProvider {
  protected string $cacheEntity;
  protected int $expirationTime;

  public function get(string $key) {
    if ($this->isset($key)) {
      return $this->readFromCache($key);
    } else {
      return false;
    }
  }

  public function isset(string $key): bool {
    $cacheData = $this->readCacheFile();
    if (isset($cacheData[$key]) && time() < $cacheData[$key]['expires']) {
      return true;
    } else {
      return false;
    }
  }

  public function set(string $key, $value): void {
    $cacheData = $this->readCacheFile();
    $cacheData[$key] = [
      'value' => $value,
      'expires' => time() + $this->expirationTime,
    ];
    $this->writeToCache($cacheData);
  }

  /**
   * @param CacheEntity $cacheEntity
   */
  public function setCacheEntity(CacheEntity $cacheEntity): void {
    $this->cacheEntity = $cacheEntity->value;
  }

  /**
   * @param int $expirationTime
   */
  public function setExpirationTime(int $expirationTime): void {
    $this->expirationTime = $expirationTime;
  }

  abstract protected function readCacheFile(): array;

  protected function readFromCache(string $key) {
    $cacheData = $this->readCacheFile();
    return $cacheData[$key]['value'];
  }

  abstract protected function writeToCache(array $cacheData): void;
}
