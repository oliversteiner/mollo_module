<?php

namespace Drupal\mollo_module\Controller;

use Drupal\Core\Controller\ControllerBase;


/**
 * Class MolloModuleController.
 *
 *

 */
class MolloModuleController extends ControllerBase {

  // public  Vars for Twig Var Suggestion. Use in Template via:
  // {# @var mollo_module \Drupal\mollo_module\Controller\MolloModuleController #}

  public $test;

  public $foo;

  public $bar;


  /**
   * Name of our module.
   *
   * @return string
   *   A module name.
   */
  public function getModuleName(): string {
    return 'mollo_module';
  }


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
   * Get full path to the template.
   *
   * @return string
   *   Path string.
   */
  protected function getTemplatePath(): string {
    return drupal_get_path('module', $this->getModuleName()) .
      '/templates/';
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
