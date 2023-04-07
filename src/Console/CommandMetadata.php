<?php

namespace Fraction\Console;

use Fraction\Console\Attribute\Command;
use Fraction\Console\Attribute\Flag;
use Fraction\Throwable\FractionException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class CommandMetadata {

  /**
   * @param string $commandName
   * @param string $className
   * @param string $methodName
   * @param string $description
   * @param Flag[] $flags
   */
  public function __construct(private string $commandName, private string $className, private string $methodName, private string $description, private array $flags = []) {
  }

  /**
   * @throws ReflectionException
   * @throws FractionException
   */
  public static function fromCommandClass(string $commandClass): array {
    $classReflection = new ReflectionClass($commandClass);
    $classCommandAttribute = $classReflection->getAttributes(Command::class)[0] ?? null;
    if ($classCommandAttribute === null) {
      throw new FractionException("Class {$commandClass} is not a command.");
    }

    $classCommand = $classCommandAttribute->newInstance();
    $commands = [];
    foreach ($classReflection->getMethods() as $method) {
      $methodCommandAttribute = $method->getAttributes(Command::class)[0] ?? null;
      if ($methodCommandAttribute) {
        $methodCommand = $methodCommandAttribute->newInstance();
        $commandName = "{$classCommand->getName()}.{$methodCommand->getName()}";

        $metadata = new CommandMetadata($commandName, $commandClass, $method->getName(), $methodCommand->getDescription());
        $metadata->populateFlags($method);
        $commands[$commandName] = $metadata;
      }
    }

    return $commands;
  }

  /**
   * @return string
   */
  public function getClassName(): string {
    return $this->className;
  }

  /**
   * @return string
   */
  public function getCommandName(): string {
    return $this->commandName;
  }

  /**
   * @return string
   */
  public function getDescription(): string {
    return $this->description;
  }

  /**
   * @return array
   */
  public function getFlags(): array {
    return $this->flags;
  }

  /**
   * @return string
   */
  public function getMethodName(): string {
    return $this->methodName;
  }

  protected function populateFlags(ReflectionMethod $method): void {
    $this->flags = array_reduce($method->getAttributes(Flag::class), function ($carry, $attribute) {
      $flag = $attribute->newInstance();
      $carry[$flag->getName()] = $flag;
      return $carry;
    }, []);
  }
}