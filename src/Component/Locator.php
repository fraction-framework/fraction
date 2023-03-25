<?php

namespace Fraction\Component;

class Locator {
  private string $frameworkRoot;
  private string $projectRoot;

  public function __construct() {
    $path = realpath('.');

    if (basename($path) === 'public') {
      $path = dirname($path);
    }

    $this->setProjectRoot($path);
    $this->setFrameworkRoot(realpath(__DIR__ . '/../../'));
  }

  public function getCacheDir(): string {
    return $this->getProjectRoot() . '/var/cache';
  }

  public function getConfigDir(): string {
    return $this->getProjectRoot() . '/config';
  }

  /**
   * @return string
   */
  public function getFrameworkRoot(): string {
    return $this->frameworkRoot;
  }

  public function getProjectRoot(): string {
    return $this->projectRoot;
  }

  public function setProjectRoot(string $projectRoot): void {
    $this->projectRoot = $projectRoot;
  }

  public function getSourceDir(): string {
    return $this->getProjectRoot() . '/src';
  }

  private function setFrameworkRoot(string $dirname): void {
    $this->frameworkRoot = $dirname;
  }
}
