<?php

namespace Fraction\Component\Routing\Provider;

use Fraction\Http\Attribute\Route;
use Fraction\Throwable\FractionException;

class AttributeRoutingProvider extends RoutingProvider {

  /**
   * @return \Fraction\Component\Routing\Route[]
   * @throws FractionException|\ReflectionException
   */
  public function fetchRoutes(array $controllers): array {
    $routes = [];

    foreach ($controllers as $controller) {
      $class = new \ReflectionClass($controller);
      $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
      foreach ($methods as $method) {
        $attributes = $method->getAttributes(Route::class);

        if (count($attributes) === 0) {
          continue;
        }

        if (count($attributes) > 1) {
          throw new FractionException('Only one route attribute is allowed per method.');
        }

        $attribute = array_shift($attributes)->newInstance();

        $routes[] = \Fraction\Component\Routing\Route::create(
          method: $attribute->getMethod(),
          path: $attribute->getPath(),
          controller: $controller,
          action: $method->getName(),
          paramTemplate: $attribute->getParams()
        );
      }
    }

    return $routes;
  }
}
