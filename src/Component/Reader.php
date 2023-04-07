<?php

namespace Fraction\Component;

use Fraction\Component\Cache\CacheComponent;
use Fraction\Component\Cache\CacheEntity;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;

readonly class Reader {
  /**
   * @param CacheComponent $cache
   */
  public function __construct(private CacheComponent $cache, private Locator $locator) {
  }

  /**
   * @param string $className
   * @param CacheEntity $cacheEntity
   * @param string|null $scanDir
   * @return array
   */
  public function getClasses(string $className, CacheEntity $cacheEntity = CacheEntity::GLOBAL, ?string $scanDir = null): array {
    return $this->doFilter('is_subclass_of', $className, $cacheEntity, $scanDir);
  }

  /**
   * @param string $attributeName
   * @param CacheEntity $cacheEntity
   * @param string|null $scanDir
   * @return array
   * @throws \ReflectionException
   */
  public function getClassesWithAttribute(string $attributeName, CacheEntity $cacheEntity = CacheEntity::GLOBAL, ?string $scanDir = null): array {
    return $this->doFilter(function (string $classFullName, string $attributeName) {
      $reflection = new ReflectionClass($classFullName);

      return $reflection->getAttributes($attributeName) !== [];
    }, $attributeName, $cacheEntity, $scanDir);
  }

  /**
   * @param callable $callback
   * @param string $className
   * @param CacheEntity $cacheEntity
   * @param string|null $scanDir
   * @return array
   */
  private function doFilter(callable $callback, string $className, CacheEntity $cacheEntity = CacheEntity::GLOBAL, ?string $scanDir = null): array {
    if ($scanDir === null) {
      $scanDir = $this->locator->getSourceDir();
    }

    $this->cache->setCacheEntity($cacheEntity);
    if ($this->cache->isset('list')) {
      return $this->cache->get('list');
    }

    $configFiles = [];
    $files = $this->getFiles($scanDir);

    foreach ($files as $file) {
      $classFullName = $this->getClassFullName($file);

      if (!class_exists($classFullName)) {
        continue;
      }

      if (!$callback($classFullName, $className)) {
        continue;
      }

      $configFiles[] = $classFullName;
    }

    $this->cache->set('list', $configFiles);
    return $configFiles;
  }

  /**
   * @param string $file
   * @return string
   */
  private function getClassFullName(string $file): string {
    $className = pathinfo($file, PATHINFO_FILENAME);

    $classParts = [$className];
    $fileContents = file_get_contents($file);
    if (preg_match('/namespace (.+);/', $fileContents, $matches)) {
      $classParts = [$matches[1], ...$classParts];
    }
    return implode('\\', $classParts);
  }

  /**
   * @param string $path
   * @param string $extension
   * @return array
   */
  private function getFiles(string $path, string $extension = 'php'): array {
    $files = [];
    // Recursively scan the base directory for classes
    $dirIterator = new RecursiveDirectoryIterator($path);
    $iterator = new RecursiveIteratorIterator($dirIterator);

    foreach ($iterator as $file) {
      if (pathinfo($file, PATHINFO_EXTENSION) !== $extension) {
        continue;
      }

      $files[] = $file->getPathname();
    }

    return $files;
  }
}
