<?php

namespace Fraction\Component\Parser;

abstract class AbstractParser {
  abstract public function parse(string $input): array;
}
