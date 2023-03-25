<?php

namespace Fraction\Http\Enum;

enum RequestMethod: string {
  case GET = 'GET';
  case POST = 'POST';
  case PUT = 'PUT';
  case DELETE = 'DELETE';
  case PATCH = 'PATCH';
  case HEAD = 'HEAD';
  case OPTIONS = 'OPTIONS';
  case TRACE = 'TRACE';
  case CONNECT = 'CONNECT';
}