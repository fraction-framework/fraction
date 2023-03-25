<?php

namespace Fraction\Component;

use Fraction\Http\Request;

abstract class Controller {
  public function __construct(protected Request $request) {
  }
}
