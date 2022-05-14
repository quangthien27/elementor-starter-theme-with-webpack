export function onWindowEvents(foo, isReady, isLoad, isResize, isScroll) {
  if (isReady) {
    $(document).ready(foo);
  }

  if (isLoad) {
    $(window).bind('load', foo);
  }

  if (isResize) {
    const throttleResizing = _.throttle(foo, 100);
    $(window).bind('resize', throttleResizing);
  }

  if (isScroll) {
    const throttleScroll = _.throttle(foo, 10);
    $(window).bind('scroll', throttleScroll);
  }
}

export function balanceElements(container, clr, gapDelta) {
  const $ = jQuery;

  clr = (typeof clr !== 'undefined' ? clr : false);
  gapDelta = (typeof gapDelta !== 'undefined' ? gapDelta : 10);
  let currentTallest = 0,
    currentRowStart = 0,
    rowDivs = [],
    el,
    currentDiv,
    topPosition = 0;
  let c = $(container).filter(':visible');
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
}
