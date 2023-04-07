<?php

namespace Fraction\Component\Cache\Provider;

use Fraction\Component\Cache\CacheEntity;

abstract class CacheProvider {
  /**
   * @var string
   */
  protected string $cacheEntity;
  /**
   * @var int
   */
  protected int $expirationTime;

  /**
   * @return ?bool
   */
  abstract public function clear(): ?bool;

  /**
   * @param string $key
   * @return void
   */
  public function delete(string $key): void {
    $cacheData = $this->readCacheFile();
    unset($cacheData[$key]);
    $this->writeToCache($cacheData);
  }

  /**
   * @param string $key
   * @return false|mixed
   */
  public function get(string $key): mixed {
    if ($this->isset($key)) {
      return $this->readFromCache($key);
    } else {
      return false;
    }
  }

  /**
   * @param string $key
   * @return bool
   */
  public function isset(string $key): bool {
    $cacheData = $this->readCacheFile();
    if (isset($cacheData[$key]) && time() < $cacheData[$key]['expires']) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * @param string $key
   * @param $value
   * @return void
   */
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

  /**
   * @return array
   */
  abstract protected function readCacheFile(): array;

  /**
   * @param string $key
   * @return mixed
   */
  protected function readFromCache(string $key): mixed {
    $cacheData = $this->readCacheFile();
    return $cacheData[$key]['value'];
  }

  /**
   * @param array $cacheData
   * @return void
   */
  abstract protected function writeToCache(array $cacheData): void;
}
