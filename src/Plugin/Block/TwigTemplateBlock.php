<?php

namespace Drupal\mollo_module\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'twig_template_block' block.
 *
 * @Block(
 *  id = "mollo_module_twig_template_block",
 *  admin_label = @Translation("Twig Template Block"),
 *   category = @Translation("Mollo"),
 * )
 */
class TwigTemplateBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $variables = $this->getVars();

    $build = [];
    $block = [
      '#theme' => 'twig_template_block',
      '#attached' => [
        'library' => ['mollo_module/block'],
      ],
      '#attributes' => [
        'class' => ['mollo-module'],
        'id' => 'twig-template-block',
      ],
      '#mollo_block' => $variables,
      '#cache' => [
        'max-age' => 0,
      ],
    ];

    $build['twig_template_block'] = $block;
    return $build;


  }

  public function getVars() {

    // for Twig Variables Suggestion define Vars in MolloModuleController:
    // and include
    // {# @var mollo_module \Drupal\mollo_module\Controller\MolloModuleController #}
    // at top of your twig Template

    $variables['foo'] = 'foo - Block:Twig';
    $variables['bar'] = 'bar - Block:Twig';
    $variables['test'] = TRUE;

    return $variables;
  }

}
