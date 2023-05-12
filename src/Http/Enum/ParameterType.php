<?php

namespace Fraction\Http\Enum;

enum ParameterType: string {
  case STRING = 'string';
  case INT = 'int';
  case FLOAT = 'float';
  case BOOL = 'bool';
  case ARRAY = 'array';
  case OBJECT = 'object';
  case ANY = 'any';
}
