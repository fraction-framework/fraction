<?php

namespace Fraction\DependencyInjection;

use Fraction\Component\Config\ConfigManager;
use Fraction\Component\Routing\Route;
use Fraction\DependencyInjection\Attribute\Dependency;
use Fraction\Throwable\FractionException;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use ReflectionProperty;

class Container implements ContainerInterface {
  /**
   * @var array
   */
  private array $definitions = [];
  /**
   * @var array
   */
  private array $instances = [];

  /**
   * @param array $definitions
   */
  public function __construct(array $definitions = []) {
    $this->definitions = [...$this->definitions, ...$definitions];
  }

  /**
   * @param array $definitions
   * @return static
   */
  public static function create(array $definitions = []): static {
    $container = new static(definitions: $definitions);
    $container->set(ContainerInterface::class, fn() => $container);

    try {
      // Register components defined in config
      $components = $container->get(ConfigManager::class)->get('components');
      $container->registerComponents($components);

      // Read binders and register them in the container
      $binders = $container->get(BinderReader::class)->getBinders();
      $container->registerBinders($binders);
    } catch (FractionException) {
      // ToDO: Log error
    }

    return $container;
  }

  /**
   * @param array $definitions
   * @return array
   * @throws FractionException
   */
  public function fromArray(array $definitions): array {
    return array_map(fn($definition) => $this->get($definition), $definitions);
  }

  /**
   * @param string $id
   * @return mixed
   * @throws FractionException
   */
  public function get(string $id): mixed {
    if ($this->has($id)) {
      return $this->getInstance($id);
    }

    $definition = $this->definitions[$id] ?? $id;

    try {
      $dependency = is_callable($definition) ? $definition() : $this->resolve($definition);
    } catch (ReflectionException $e) {
      throw new FractionException(message: "Unable to resolve dependency: {$id}. {$e->getMessage()}", previous: $e);
    }

    return $this->addInstance($id, $dependency);
  }

  /**
   * @param string $id
   * @return mixed
   */
  public function getInstance(string $id): mixed {
    return $this->instances[$id];
  }

  /**
   * @param string $id
   * @return bool
   */
  public function has(string $id): bool {
    return isset($this->instances[$id]);
  }

  /**
   * @param Binder[] $binders
   * @return void
   */
  public function registerBinders(array $binders): void {
    foreach ($binders as $binder) {
      $this->set($binder->getClassName(), fn() => $this->resolveMethod($binder, 'configure'));
    }
  }

  /**
   * @param array $components
   * @return void
   */
  public function registerComponents(array $components): void {
    foreach ($components as $binding => $component) {
      $this->set($binding, $component);
    }
  }

  /**
   * @param string|object $className
   * @param string $methodName
   * @param array $builtInDefinitions
   * @return mixed
   * @throws FractionException
   * @throws ReflectionException
   */
  public function resolveMethod(string|object $className, string $methodName, array $builtInDefinitions = []): mixed {
    $reflectionClass = new ReflectionClass($className);
    $method = $reflectionClass->getMethod($methodName);
    $parameters = $method->getParameters();

    $classInstance = is_string($className) ? $this->get($className) : $className;
    if (!$parameters) {
      return $method->invoke($classInstance);
    }

    $dependencies = array_map(function (ReflectionParameter $parameter) use ($builtInDefinitions) {
      return $this->resolveDependency($parameter, $builtInDefinitions);
    }, $parameters);

    return $method->invokeArgs($classInstance, $dependencies);
  }

  /**
   * @param Route $route
   * @return mixed
   * @throws FractionException
   */
  public function resolveRouteAction(Route $route): mixed {
    [$className, $methodName] = [$route->getController(), $route->getAction()];

    try {
      return $this->resolveMethod($className, $methodName, $route?->getParams());
    } catch (ReflectionException $e) {
      throw new FractionException("Unable to resolve route action for $className::$methodName", 500, $e);
    }
  }

  /**
   * @param string $id
   * @param callable|string $value
   * @return void
   */
  public function set(string $id, callable|string $value): void {
    $this->definitions[$id] = $value;
  }

  /**
   * @param string $id
   * @param mixed $instance
   * @return mixed
   */
  private function addInstance(string $id, mixed $instance): mixed {
    $this->instances[$id] = $instance;

    return $instance;
  }

  /**
   * A recursive function that resolves dependencies for a given class
   *
   * @param string $className
   * @return mixed|object|string|null
   * @throws ReflectionException|FractionException
   */
  private function resolve(string $className): mixed {
    $reflectionClass = new ReflectionClass($className);
    $constructor = $reflectionClass->getConstructor();
    $parameters = $constructor?->getParameters();
    if (!$constructor || !$parameters) {
      return new $className();
    }

    $dependencies = array_map(function (ReflectionParameter $parameter) {
      return $this->resolveDependency($parameter);
    }, $parameters);

    $classInstance = $reflectionClass->newInstanceArgs($dependencies);

    $methodInjections = $this->resolveMethodInjections($reflectionClass);
    foreach ($methodInjections as $method => $injections) {
      $reflectionClass->getMethod($method)->invokeArgs($classInstance, $injections);
    }

    $propertyInjections = $this->resolvePropertyInjections($reflectionClass);
    foreach ($propertyInjections as $property => $injection) {
      $reflectionClass->getProperty($property)->setValue($classInstance, $injection);
    }

    return $classInstance;
  }

  /**
   * @param ReflectionParameter|ReflectionProperty $parameter
   * @param array $builtInDefinitions
   * @return mixed
   * @throws FractionException
   */
  private function resolveDependency(ReflectionParameter|ReflectionProperty $parameter, array $builtInDefinitions = []): mixed {
    [$dependencyType, $dependencyName] = [$parameter->getType(), $parameter->getName()];

    if (!$dependencyType || $dependencyType instanceof \ReflectionUnionType) {
      throw new FractionException("Unable to resolve dependency for $dependencyName");
    }

    if ($dependencyType->isBuiltin() && !isset($builtInDefinitions[$dependencyName])) {
      throw new FractionException("Unable to resolve dependency for $dependencyName");
    }

    return $builtInDefinitions[$dependencyName] ?? $this->get($dependencyType->getName());
  }

  /**
   * @param ReflectionClass $reflectionClass
   * @return array
   * @throws FractionException
   */
  private function resolveMethodInjections(ReflectionClass $reflectionClass): array {
    $methods = $reflectionClass->getMethods();
    $injections = [];
    foreach ($methods as $method) {
      $attributes = $method->getAttributes(Dependency::class);
      if (empty($attributes)) {
        continue;
      }

      $injections[$method->getName()] = array_map(function (ReflectionParameter $parameter) {
        return $this->resolveDependency($parameter);
      }, $method->getParameters());
    }

    return $injections;
  }

  /**
   * @param ReflectionClass $reflectionClass
   * @return array
   * @throws FractionException
   */
  private function resolvePropertyInjections(ReflectionClass $reflectionClass): array {
    $properties = $reflectionClass->getProperties();
    $injections = [];
    foreach ($properties as $property) {
      $attributes = $property->getAttributes(Dependency::class);
      if (empty($attributes)) {
        continue;
      }

      $injections[$property->getName()] = $this->resolveDependency($property);
    }

    return $injections;
  }
}
