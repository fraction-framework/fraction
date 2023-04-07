<?php

namespace Fraction\Console\Commands;


use Fraction\Component\Cache\CacheComponent;
use Fraction\Component\Cache\CacheEntity;
use Fraction\Console\Attribute\Command;
use Fraction\Console\Attribute\Flag;
use Fraction\Console\ConsoleInterface;

#[Command(name: 'cache', description: 'Cache commands')]
readonly class CacheCommand {

  public function __construct(private CacheComponent $cache) {
  }

  #[Command(name: 'clear', description: 'Clear the cache')]
  #[Flag(name: 'a', description: 'Clear all')]
  #[Flag(name: 'c', description: 'Clear Controller cache')]
  #[Flag(name: 'b', description: 'Clear Binder cache')]
  #[Flag(name: 'e', description: 'Clear EventSubscriber cache')]
  #[Flag(name: 't', description: 'Clear Command cache')]
  public function clear(ConsoleInterface $console): void {
    if (empty($console->getFlags())) {
      $console->printCommandUsage('cache.clear');
    }

    $entitiesCleared = [];

    if ($console->getFlag('c')) {
      $this->cache->setCacheEntity(CacheEntity::CONTROLLER);
      $this->cache->clear();
      $entitiesCleared[] = sprintf('[%s]', CacheEntity::CONTROLLER->name);
    }
    if ($console->getFlag('b')) {
      $this->cache->setCacheEntity(CacheEntity::BINDER);
      $this->cache->clear();
      $entitiesCleared[] = sprintf('[%s]', CacheEntity::BINDER->name);
    }
    if ($console->getFlag('e')) {
      $this->cache->setCacheEntity(CacheEntity::EVENT_SUBSCRIBER);
      $this->cache->clear();
      $entitiesCleared[] = sprintf('[%s]', CacheEntity::EVENT_SUBSCRIBER->name);
    }

    if ($console->getFlag('t')) {
      $this->cache->setCacheEntity(CacheEntity::COMMAND);
      $this->cache->clear();
      $entitiesCleared[] = sprintf('[%s]', CacheEntity::COMMAND->name);
    }

    if ($console->getFlag('a')) {
      foreach (CacheEntity::cases() as $entity) {
        $this->cache->setCacheEntity($entity);
        $this->cache->clear() && $entitiesCleared[] = "[{$entity->name}]";
      }
    }

    $console->writeln('Cache successfully cleared for ' . implode(', ', $entitiesCleared));
  }
}