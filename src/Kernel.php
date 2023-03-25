<?php

namespace Fraction;

use Fraction\Component\Config\ConfigManager;
use Fraction\Component\Event\Enum\EventType;
use Fraction\Component\Event\EventDispatcher;
use Fraction\Component\Routing\RouteProcessing;
use Fraction\Component\Routing\Router;
use Fraction\Component\View\ViewHandler;
use Fraction\DependencyInjection\BinderReader;
use Fraction\DependencyInjection\Container;
use Fraction\DependencyInjection\ContainerInterface;
use Fraction\Http\Enum\ResponseStatus;
use Fraction\Http\Request;
use Fraction\Throwable\FractionException;
use Fraction\Throwable\NotFoundException;
use Fraction\Throwable\RequestException;
use ReflectionException;
use Throwable;

class Kernel {
  /**
   * @var ?EventDispatcher
   */
  private ?EventDispatcher $eventDispatcher = null;
  /**
   * @var ViewHandler
   */
  private ViewHandler $viewHandler;

  /**
   * @return static
   */
  public static function Application(): static {
    $container = Container::create(
      definitions: [
        Request::class => fn() => Request::createFromGlobals(),
      ]
    );

    $kernel = new static();

    try {
      $kernel->bootstrap(...$container->fromArray([ContainerInterface::class, ConfigManager::class]));
    } catch (RequestException $e) {
      $kernel->getViewHandler()->setData($e->getResponse(), $e->getResponseStatus());
    } catch (Throwable $e) {
      $kernel->getEventDispatcher()?->dispatch(EventType::Exception, $e);
      $kernel
        ->getViewHandler()
        ->setData(
          ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTrace()],
          ResponseStatus::InternalServerError
        );
    }

    $kernel->run();

    return $kernel;
  }

  /**
   * @param ContainerInterface $container
   * @param ConfigManager $configManager
   * @throws NotFoundException
   * @throws ReflectionException|FractionException
   */
  private function bootstrap(ContainerInterface $container, ConfigManager $configManager): void {
    // Register components defined in config
    $container->registerComponents($configManager->get('components'));

    // Initialize event dispatcher
    $this->eventDispatcher = $container->get(EventDispatcher::class);

    // Read binders and register them in the container
    $binders = $container->get(BinderReader::class)->getBinders();
    $container->registerBinders($binders);

    // Initialize view handler with data from config
    $this->getViewHandler()->initializeFromConfig($configManager);

    // Handle request
    $this->handleRoute(...$container->fromArray([Request::class, Router::class, RouteProcessing::class]));
  }

  /**
   * @return ?EventDispatcher
   */
  private function getEventDispatcher(): ?EventDispatcher {
    return $this->eventDispatcher;
  }

  /**
   * @return ViewHandler
   */
  private function getViewHandler(): ViewHandler {
    if (!isset($this->viewHandler)) {
      $this->viewHandler = new ViewHandler();
    }

    return $this->viewHandler;
  }

  /**
   * @throws ReflectionException
   * @throws NotFoundException
   * @throws FractionException
   */
  private function handleRoute(Request $request, Router $router, RouteProcessing $routeProcessing): void {
    $this->eventDispatcher->dispatch(EventType::Request, $request);
    $route = $router->getRoute($request->getMethod(), $request->getPath());

    if (!$route) {
      throw new NotFoundException();
    }

    $this->eventDispatcher->dispatch(EventType::Controller, $route);
    $this->getViewHandler()->forRoute($route, $routeProcessing);

    $this->eventDispatcher->dispatch(EventType::View, $this->getViewHandler());
  }

  /**
   * @return void
   */
  private function run(): void {
    $response = $this->getViewHandler()->render();
    $this->eventDispatcher?->dispatch(EventType::Response, $response);

    $response->send();

    // Finish the request and close the connection
    if (function_exists('fastcgi_finish_request')) {
      fastcgi_finish_request();
    }

    $this->eventDispatcher?->dispatch(EventType::Terminate);
  }
}
