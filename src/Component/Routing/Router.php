<?php

namespace Fraction\Component\Routing;

use Fraction\Component\Cache\CacheEntity;
use Fraction\Component\Config\ConfigManager;
use Fraction\Component\Controller;
use Fraction\Component\Locator;
use Fraction\Component\Reader;
use Fraction\Component\Routing\Provider\RoutingProvider;
use Fraction\Component\Routing\Tree\Trie;
use Fraction\Docs\Controller\DocsController;

class Router {
  private bool $routesLoaded = false;

  public function __construct(private readonly RoutingProvider $provider, private readonly Trie $routes, private readonly ConfigManager $configManager, private readonly Locator $locator, private readonly Reader $reader) {
  }

  public function getRoute(string $method, string $path): ?Route {
    if (!$this->routesLoaded) {

      $controllers = $this->reader->retrieveFiles(Controller::class, $this->locator->getSourceDir(), CacheEntity::CONTROLLER);

      if ($this->configManager->get('api.docs.enabled')) {
        $controllers[] = DocsController::class;
      }

      $routes = $this->provider->fetchRoutes($controllers);

      foreach ($routes as $route) {
        $this->routes->insert($route);
      }

      $this->routesLoaded = true;
    }

    return $this->routes->search($method, $path);
  }
}
