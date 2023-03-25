<?php

namespace Fraction\Http\Enum;

enum ResponseType: string {
  case HTML = 'html';
  case JSON = 'json';
  case XML = 'xml';
  case PLAIN = 'plain';
}
