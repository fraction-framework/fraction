<?php

namespace Fraction\Docs\Controller;

use Fraction\Component\Controller;
use Fraction\Docs\Provider\SpecProvider;
use Fraction\Http\Attribute\Route;
use Fraction\Http\Attribute\View;
use Fraction\Http\Enum\RequestMethod;
use Fraction\Http\Enum\ResponseType;

class DocsController extends Controller {

  #[Route(method: RequestMethod::GET, path: '/@fraction/docs')]
  #[View(response: ResponseType::JSON)]
  public function index(SpecProvider $specProvider): array {
    return $specProvider->getSpec();
  }
}