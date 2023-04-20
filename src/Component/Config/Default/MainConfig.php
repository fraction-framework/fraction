<?php

namespace Fraction\Component\Config\Default;

use Fraction\Component\Config\AbstractConfig;
use Fraction\Component\Config\Tree\ConfigNodeGroup;
use Fraction\Component\Locator;
use Fraction\DependencyInjection\Attribute\Dependency;
use Fraction\Http\Enum\ResponseType;
use Fraction\Templating\TemplateEngine;

class MainConfig extends AbstractConfig {
  private Locator $locator;

  #[Dependency]
  public function setLocator(Locator $locator): void {
    $this->locator = $locator;
  }

  protected function buildConfigTree(ConfigNodeGroup $root): ConfigNodeGroup {
    $root
      ->addChild('view')
      ->addGroup(
        fn($group) => $group
          ->addChild('response')
          ->addGroup(
            function (ConfigNodeGroup $group) {
              $group->addChild('format', 'json')->addResolver(fn($value) => ResponseType::from($value));
              $group->addChild('headers', []);
            }
          )
      );

    $root->addChild('templating')
      ->addGroup(function (ConfigNodeGroup $group) {
        $group->addChild('engine', 'twig')->addResolver(fn($value) => TemplateEngine::from($value));
        $group->addChild('template_dir', 'templates')->addResolver(fn($value) => $this->locator->getProjectRoot() . DIRECTORY_SEPARATOR . $value);
      });


    $root->addChild('api')
      ->addGroup(function (ConfigNodeGroup $group) {
        $group->addChild('docs')->addGroup(function (ConfigNodeGroup $group) {
          $group->addChild('enabled', false);
          $group->addChild('version', '1.0');
          $group->addChild('title', 'Fraction Framework API');
          $group->addChild('servers', []);
        });
      });

    return $root;
  }

  protected function getConfigFile(): string {
    return 'main.yml';
  }
}
