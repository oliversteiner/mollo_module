/**
 * @file
 * mollo_module behaviors.
 */

(function($, Drupal, drupalSettings) {

  'use strict';

  /**
   * Behavior molloModule.
   */
  Drupal.behaviors.molloModule = {
    attach(context, settings) {
      console.log('Mollo Module');

      const $triggerElem = $('.mollo-module-test-1-trigger');
      const $resultElem = $('.mollo-module-test-1-result');
      let counter = 0;

      $('#mollo-module', context)
        .once('mollo-module')
        .each(() => {
          $triggerElem.click(event => {
            console.log('click!');
            event.preventDefault();
            counter++;
            $resultElem.html(counter);
          });
        });
    },
  };
})(jQuery, Drupal, drupalSettings);
