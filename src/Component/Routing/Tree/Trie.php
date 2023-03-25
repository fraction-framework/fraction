<?php

namespace Fraction\Component\Routing\Tree;

use Fraction\Component\Routing\Route;

class Trie {
  /**
   * @param Node[] $root
   */
  private array $root = [];

  /**
   * @param Route $route
   * @return void
   */
  public function insert(Route $route): void {
    $node = $this->getRoot($route->getMethod());

    foreach ($route->getPathSegments() as $segment) {
      $node = $node->addChild($segment);
    }

    $node->setRoute($route);
  }

  /**
   * @param string $method
   * @param string $path
   * @return Route|null
   */
  public function search(string $method, string $path): ?Route {
    $segments = explode('/', ltrim($path, '/'));
    $node = $this->getRoot($method);

    $requestParams = [];

    foreach ($segments as $segment) {
      if ($child = $node->getChildOrNull($segment)) {
        $node = $child;
        continue;
      }

      $lastMatchedNode = null;
      foreach ($node->getNamedParams() as $paramName => $paramNode) {
        if (!($route = $paramNode->getRoute())) {
          continue;
        }

        $paramTemplate = $route->getParamTemplate($paramName);

        if (preg_match("#^$paramTemplate$#", $segment, $matches)) {
          $requestParams[$paramName] = array_pop($matches);
          $node = $paramNode;
          $lastMatchedNode = $paramNode;
          break;
        }
      }

      if (!$lastMatchedNode) {
        return null;
      }
    }

    $route = $node?->getRoute();

    if ($route?->getMethod() === $method) {
      $route->setParams($requestParams);
      return $route;
    }

    return null;
  }

  private function getRoot(string $method): Node {
    return $this->root[$method] ??= new Node();
  }
}
