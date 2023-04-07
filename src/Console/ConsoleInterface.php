<?php

namespace Fraction\Console;

interface ConsoleInterface {
  /**
   * @return array
   */
  public function getArgs(): array;

  /**
   * @param string $name
   * @return mixed
   */
  public function getFlag(string $name): mixed;

  /**
   * @return array
   */
  public function getFlags(): array;

  /**
   * @param string $name
   * @return bool
   */
  public function hasFlag(string $name): bool;

  /**
   * @param string $commandName
   * @return void
   */
  public function printCommandUsage(string $commandName): void;

  /**
   * @param string $message
   * @return void
   */
  public function write(string $message): void;

  /**
   * @param string $message
   * @return void
   */
  public function writeln(string $message): void;
}