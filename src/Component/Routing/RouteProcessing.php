<?php

namespace Fraction\Component\Routing;

use Fraction\DependencyInjection\ContainerInterface;
use Fraction\Http\Request;
use Fraction\Throwable\BadRequestException;

readonly class RouteProcessing {
  public function __construct(private Request $request, private ContainerInterface $container) {
  }

  /**
   * @throws \ReflectionException
   * @throws BadRequestException
   */
  public function __invoke(Route $route): mixed {
    $route->validateParametersForRequest($this->request);

    return $this->container->resolveRouteAction($route);
  }
}
