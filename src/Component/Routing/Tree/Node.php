<?php

namespace Fraction\Component\Routing\Tree;

use Fraction\Component\Routing\Route;

class Node {
  /**
   * @var array
   */
  private array $children = [];
  /**
   * @var array
   */
  private array $namedParamsList = [];
  /**
   * @var Route|null
   */
  private ?Route $route = null;

  /**
   * @param string $segment
   * @return Node
   */
  public function addChild(string $segment): Node {
    if (preg_match('/^{([a-zA-Z0-9_]+)}$/', $segment, $matches)) {
      $segment = $matches[1];
      $this->namedParamsList[] = $segment;
    }

    if (array_key_exists($segment, $this->children)) {
      return $this->children[$segment];
    }

    $child = new self();
    $this->children[$segment] = $child;

    return $child;
  }

  /**
   * @param $segment
   * @return Node|null
   */
  public function getChildOrNull($segment): ?Node {
    return $this->children[$segment] ?? null;
  }

  /**
   * @return Node[]
   */
  public function getNamedParams(): array {
    $params = [];

    foreach ($this->namedParamsList as $paramName) {
      $params[$paramName] = $this->children[$paramName];
    }

    return $params;
  }

  /**
   * @return Route|null
   */
  public function getRoute(): ?Route {
    return $this->route;
  }

  /**
   * @param Route $route
   * @return void
   */
  public function setRoute(Route $route): void {
    $this->route = $route;
  }
}
