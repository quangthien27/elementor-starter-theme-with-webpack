// LOAD STYLESHEETS
import '../scss/main.scss';

require.context('app/components', true, /\.scss$/i).keys().map(name => {
  name = name.slice(2, -5);
  import(`app/components/${name}.scss`);
});

// LOAD LIBS/JS
import {balanceElements, onWindowEvents} from './utils';

// MAIN SCRIPT
jQuery(document).ready(function($) {
  const App = window['App'] || {};

  App.init = function() {
    // Balance all elements
    onWindowEvents(() => {
      App.balanceAllElements();
    }, 1, 1, 1, 0);

    // Balance elements after Ajax Load More
    window.almComplete = function(alm) {
      App.balanceAllElements();
    };

    // Wow animations
    if ('WOW' in window) {
      const wow = new WOW({
        boxClass: 'wow',
        animateClass: 'animate__animated',
        offset: 0,
        mobile: true,
        live: true
      });

      wow.init();
    }
  };

  App.balanceAllElements = function() {
    balanceElements($('.balance-elements'), false, 30);
  };

  // No code should be added below this line
  window['App'] = App;
  App.init();
});
