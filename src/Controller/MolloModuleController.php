<?php

namespace Drupal\mollo_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\mollo_module\Utils\MolloModuleTrait;


/**
 * Class MolloModuleController.
 *
 *

 */
class MolloModuleController extends ControllerBase {

  use MolloModuleTrait;

  // public  Vars for Twig Var Suggestion. Use in Template via:
  // {# @var mollo_module \Drupal\mollo_module\Controller\MolloModuleController #}

  public $test;

  public $foo;

  public $bar;




  /**
   * @return array[]
   */
  public function page(): array {

    $template_name = 'mollo-module-page.html.twig';
    $template_file = $this->getTemplatePath() . $template_name;
    $template = file_get_contents($template_file);


    return [
      'description' => [
        '#type' => 'inline_template',
        '#template' => $template,
        '#context' => $this->getPageVars(),
      ],
    ];
  }


  /**
   * @return array
   *
   */
  private function getPageVars(): array {

    $test = TRUE;
    $foo = 'Foo - Page';
    $bar = 'Bar - Page';

    $variables['mollo_page']['test'] = $test;
    $variables['mollo_page']['foo'] = $foo;
    $variables['mollo_page']['bar'] = $bar;

    return $variables;
  }

}
