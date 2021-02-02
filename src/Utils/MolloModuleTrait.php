<?php
/**
 *  Mollo Module Trait
 *
 */


namespace Drupal\mollo_module\Utils;


trait MolloModuleTrait {

  /**
   * Name of our module.
   *
   * @return string
   *   A module name.
   */

  protected function getModuleName(): string {
    return 'mollo_module';
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

}
