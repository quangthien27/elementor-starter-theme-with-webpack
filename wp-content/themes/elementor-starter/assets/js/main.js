import '../scss/main.scss';

jQuery(document).ready(function($) {
  var body = $('body');
  var header = $('.header');
  var screen_admin_min = 783;
  var adminBarHeight = 0;

  // Calculate admin bar
  if (body.hasClass('admin-bar')) {
    if ($(window).width() > screen_admin_min) {
      adminBarHeight = 32;
    } else {
      adminBarHeight = 46;
    }
  }

  // Go to element
  $.fn.goToElement = function(offset) {
    offset = 70;
    if ($(this).length) {
      var offsetTop = $(this).offset().top,
        args = {
          scrollTop: (offsetTop - adminBarHeight - offset)
        };
      $('html, body').animate(args, 'fast');
    }
    return this;
  };
  body.on('click', '.anchor-link', function(e) {
    var _this = $(this);
    if (0 === _this.attr('href').indexOf('#')) {
      e.preventDefault();
      $(_this.attr('href')).goToElement();
    }
  });

  // Window events
  var onWindowEvents = function(foo, isReady, isLoad, isResize, isScroll) {
    if (isReady) {
      $(document).ready(foo);
    }

    if (isLoad) {
      $(window).bind('load', foo);
    }

    if (isResize) {
      var throttleResizing = _.throttle(foo, 100);
      $(window).bind('resize', throttleResizing);
    }

    if (isScroll) {
      var throttleScroll = _.throttle(foo, 10);
      $(window).bind('scroll', throttleScroll);
    }
  };

  // Balance elements
  var balanceElements = function(container, clr, gapDelta) {
    clr = (typeof clr !== 'undefined' ? clr : false);
    gapDelta = (typeof gapDelta !== 'undefined' ? gapDelta : 10);
    var currentTallest = 0,
      currentRowStart = 0,
      rowDivs = [],
      el,
      currentDiv,
      topPosition = 0;
    var c = $(container).filter(':visible');
    if (!c.length) {
      return false;
    }
    if (!clr) {
      c.css('height', 'auto');
    } else {
      c.removeAttr('style');
    }
    c.each(function() {
      el = $(this);
      topPosition = el.offset().top;
      if ((currentRowStart < (topPosition + gapDelta)) && (currentRowStart > (topPosition - gapDelta))) {
        rowDivs.push(el);
        currentTallest = (currentTallest < el.outerHeight()) ? (el.outerHeight()) : (currentTallest);
      } else {
        for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {
          rowDivs[currentDiv].css('height', currentTallest + 'px');
        }
        rowDivs.length = 0; // empty the array
        currentRowStart = topPosition;
        currentTallest = el.outerHeight();
        rowDivs.push(el);
      }
      for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {
        rowDivs[currentDiv].css('height', currentTallest + 'px');
      }
    });
    return true;
  };

  var balanceAll = function() {
    balanceElements($('.balance-elements'), false, 30);
  };

  onWindowEvents(balanceAll, 1, 1, 1, 0);
});
