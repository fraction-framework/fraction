<?php

namespace Fraction\Component;

use Fraction\Component\Cache\CacheComponent;
use Fraction\Component\Cache\CacheEntity;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

readonly class Reader {
  public function __construct(private CacheComponent $cache) {
  }

  public function retrieveFiles(string $className, string $scanDir, CacheEntity $cacheEntity = CacheEntity::GLOBAL): array {
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

      if (!is_subclass_of($classFullName, $className)) {
        continue;
      }

      $configFiles[] = $classFullName;
    }

    $this->cache->set('list', $configFiles);
    return $configFiles;
  }

  private function getClassFullName(string $file): string {
    $className = pathinfo($file, PATHINFO_FILENAME);

    $classParts = [$className];
    $fileContents = file_get_contents($file);
    if (preg_match('/namespace (.+);/', $fileContents, $matches)) {
      $classParts = [$matches[1], ...$classParts];
    }
    return implode('\\', $classParts);
  }

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
