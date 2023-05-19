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
   * @var string|null
   */
  private ?string $paramName = null;
  /**
   * @var string|null
   */
  private ?string $paramTemplate = null;
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
      $this->setParamName($segment);

      if (!in_array($segment, $this->namedParamsList)) {
        $this->namedParamsList[] = $segment;
      }
    }

    if (array_key_exists($segment, $this->children)) {
      return $this->getChildOrNull($segment);
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
   * @return string|null
   */
  public function getParamName(): ?string {
    return $this->paramName;
  }

  /**
   * @param string|null $paramName
   */
  public function setParamName(?string $paramName): void {
    $this->paramName = $paramName;
  }

  /**
   * @return string|null
   */
  public function getParamTemplate(): ?string {
    return $this->paramTemplate;
  }

  /**
   * @param string|null $paramTemplate
   */
  public function setParamTemplate(?string $paramTemplate): void {
    $this->paramTemplate = $paramTemplate;
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
