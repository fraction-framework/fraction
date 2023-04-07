<?php

namespace Fraction\Component\Event;

use BackedEnum;
use Fraction\Component\Cache\CacheEntity;
use Fraction\Component\Event\Attribute\EventListener;
use Fraction\Component\Event\Enum\EventType;
use Fraction\Component\Reader;
use Fraction\DependencyInjection\ContainerInterface;
use InvalidArgumentException;
use JetBrains\PhpStorm\NoReturn;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class EventDispatcher {
  /**
   * @var array
   */
  private array $listeners = [];

  /**
   * @param EventFactory $eventFactory
   * @param Reader $reader
   * @param ContainerInterface $container
   * @throws ReflectionException
   */
  #[NoReturn] public function __construct(private readonly EventFactory $eventFactory, Reader $reader, private readonly ContainerInterface $container) {

    $this->registerListeners($reader);
  }

  /**
   * @param EventType|BackedEnum|string $eventType
   * @param callable $listener
   * @return void
   */
  public function addListener(EventType|BackedEnum|string $eventType, callable $listener): void {
    $eventName = $this->retrieveEventName($eventType);
    $this->listeners[$eventName][] = $listener;
  }

  /**
   * @param EventType|BackedEnum|string $eventType
   * @param mixed|null $data
   * @return void
   */
  public function dispatch(EventType|BackedEnum|string $eventType, mixed $data = null): void {
    $eventName = $this->retrieveEventName($eventType);

    if (!$this->hasListeners($eventName)) {
      return;
    }

    $event = $this->eventFactory->createEvent($eventType, $data);

    foreach ($this->getListeners($eventName) as $listener) {
      $listener($event);
    }
  }

  /**
   * @param string $eventName
   * @return array
   */
  private function getListeners(string $eventName): array {
    return $this->listeners[$eventName];
  }

  /**
   * @param string $eventName
   * @return bool
   */
  private function hasListeners(string $eventName): bool {
    return isset($this->listeners[$eventName]);
  }

  /**
   * @param Reader $reader
   * @return void
   * @throws ReflectionException
   */
  #[NoReturn] private function registerListeners(Reader $reader): void {
    $subscriberClasses = $reader->getClasses(EventSubscriberInterface::class, CacheEntity::EVENT_SUBSCRIBER);

    foreach ($subscriberClasses as $subscriberClass) {

      // Find all methods with #[EventListener] attribute
      $reflectionClass = new ReflectionClass($subscriberClass);
      $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);

      foreach ($methods as $method) {
        $attributes = $method->getAttributes(EventListener::class);

        if (empty($attributes)) {
          continue;
        }

        $attribute = array_shift($attributes);
        $eventType = $attribute->newInstance()->getEventType();

        $this->addListener($eventType, fn($event) => $this->container->resolveMethod($subscriberClass, $method->getName(), ['event' => $event]));
      }
    }
  }

  /**
   * @param EventType|BackedEnum|string $eventType
   * @return string
   */
  private function retrieveEventName(EventType|BackedEnum|string $eventType): string {
    if ($eventType instanceof EventType) {
      return $eventType->value;
    }

    if ($eventType instanceof BackedEnum) {
      return $eventType->value;
    }

    if (is_string($eventType)) {
      return $eventType;
    }

    throw new InvalidArgumentException('Invalid event type');
  }
}
