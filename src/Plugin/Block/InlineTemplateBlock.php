<?php

namespace Drupal\mollo_module\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\NodeInterface;

/**
 * Provides a 'Inline Template Block' block.
 *
 * @Block(
 *  id = "mollo_module_inline_template_block",
 *  admin_label = @Translation("Inline Template Block"),
 *   category = @Translation("Mollo"),
 * )
 */
class InlineTemplateBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    [$test, $foo, $bar] = $this->getVariables();

    $class = $test ? 'success' : 'failed';
    $build = [];

    $html = '
<div class="mollo-module-inline-template-block">
  <div class="system-status-general-info__description">
    Twig Variable Test <br>
    <code>Plugin:Block / inline</code>
  </div>

  <table class="twig-variable-test ' . $class . '">
    <tr>
      <th>Foo:</th>
      <td class="' . $class . '">' . $foo . '</td>
    </tr>
    <tr>
      <th>Bar:</th>
      <td class="' . $class . '">' . $bar . '</td>
    </tr>
  </table>
</div>
';

    $build['#markup'] = $html;

    return $build;
  }

  /**
   * @return array
   */
  private function getVariables(): array {
    $test = TRUE;
    $foo = 'foo - Block:inline';
    $bar = 'bar - Block:inline';

    return [$test, $foo, $bar];
  }

}
