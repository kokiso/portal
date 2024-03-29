// Native Javascript for Bootstrap 3 v2.0.25 | © dnp_theme | MIT-License
(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD support:
    define([], factory);
  } else if (typeof module === 'object' && module.exports) {
    // CommonJS-like:
    module.exports = factory();
  } else {
    // Browser globals (root is window)
    var bsn = factory();
    root.Affix      = bsn.Affix;
    root.Alert      = bsn.Alert;
    root.Button     = bsn.Button;
    root.Carousel   = bsn.Carousel;
    root.Collapse   = bsn.Collapse;
    root.Dropdown   = bsn.Dropdown;
    root.Modal      = bsn.Modal;
    root.Popover    = bsn.Popover;
    root.ScrollSpy  = bsn.ScrollSpy;
    root.Tab        = bsn.Tab;
    root.Tooltip    = bsn.Tooltip;
  }
}(this, function () {
  /////////////////////////////////////////////////////////////////////
  // Javascript nativo para Bootstrap 3 | Funções de Utilitário Interno
  /////////////////////////////////////////////////////////////////////
  "use strict";
  /////////
  // global
  /////////
  var globalObject = typeof global !== 'undefined' ? global : this||window,
    DOC = document
    ,HTML = DOC.documentElement
    /////////////////////////////////////////////////
    // permitir que a biblioteca seja usada em <head>
    /////////////////////////////////////////////////
    ,body = 'body' 
    //////////////////////////////////////////////////////
    // JavaScript nativo para o objeto global do Bootstrap
    //////////////////////////////////////////////////////
    ,BSN = globalObject.BSN = {}
    ,supports = BSN.supports = []
    ////////////////////////////
    // função alternar atributos
    ////////////////////////////
    ,dataToggle    = 'data-toggle'
    ,dataDismiss   = 'data-dismiss'
    ,dataSpy       = 'data-spy'
    ,dataRide      = 'data-ride'
    //////////////
    // componentes
    //////////////
    ,stringAffix     = 'Affix'
    ,stringAlert     = 'Alert'
    ,stringButton    = 'Button'
    ,stringCarousel  = 'Carousel'
    ,stringCollapse  = 'Collapse'
    ,stringDropdown  = 'Dropdown'
    ,stringModal     = 'Modal'
    ,stringPopover   = 'Popover'
    ,stringScrollSpy = 'ScrollSpy'
    ,stringTab       = 'Tab'
    ,stringTooltip   = 'Tooltip'
    //////////////////
    // opcoes DATA API
    //////////////////
    ,databackdrop      = 'data-backdrop'
    ,dataKeyboard      = 'data-keyboard'
    ,dataTarget        = 'data-target'
    ,dataInterval      = 'data-interval'
    ,dataHeight        = 'data-height'
    ,dataPause         = 'data-pause'
    ,dataTitle         = 'data-title'  
    ,dataOriginalTitle = 'data-original-title'
    ,dataOriginalText  = 'data-original-text'
    ,dataDismissible   = 'data-dismissible'
    ,dataTrigger       = 'data-trigger'
    ,dataAnimation     = 'data-animation'
    ,dataContainer     = 'data-container'
    ,dataPlacement     = 'data-placement'
    ,dataDelay         = 'data-delay'
    ,dataOffsetTop     = 'data-offset-top'
    ,dataOffsetBottom  = 'data-offset-bottom',
    ////////////////
    // opcao chaves
    ////////////////
    backdrop    = 'backdrop'
    ,keyboard   = 'keyboard'
    ,delay      = 'delay'
    ,content    = 'content'
    ,target     = 'target'
    ,interval   = 'interval'
    ,pause      = 'pause'
    ,animation  = 'animation'
    ,placement  = 'placement'
    ,container  = 'container' 
    //////////////////
    // modelo de caixa
    //////////////////
    ,offsetTop    = 'offsetTop'
    ,offsetBottom = 'offsetBottom'
    ,offsetLeft   = 'offsetLeft'
    ,scrollTop    = 'scrollTop'
    ,scrollLeft   = 'scrollLeft'
    ,clientWidth  = 'clientWidth'
    ,clientHeight = 'clientHeight'
    ,offsetWidth  = 'offsetWidth'
    ,offsetHeight = 'offsetHeight'
    ,innerWidth   = 'innerWidth'
    ,innerHeight  = 'innerHeight'
    ,scrollHeight = 'scrollHeight'
    ,height       = 'height'
    ///////
    // aria
    ///////
    ,ariaExpanded = 'aria-expanded'
    ,ariaHidden   = 'aria-hidden'
    //////////
    // eventos
    //////////
    ,clickEvent    = 'click'
    ,hoverEvent    = 'hover'
    ,keydownEvent  = 'keydown'
    ,keyupEvent    = 'keyup'  
    ,resizeEvent   = 'resize'
    ,scrollEvent   = 'scroll',
    // originalEvents
    showEvent     = 'show',
    shownEvent    = 'shown',
    hideEvent     = 'hide',
    hiddenEvent   = 'hidden',
    closeEvent    = 'close',
    closedEvent   = 'closed',
    slidEvent     = 'slid',
    slideEvent    = 'slide',
    changeEvent   = 'change'
    /////////
    // outros
    /////////
    ,getAttribute           = 'getAttribute'
    ,setAttribute           = 'setAttribute'
    ,hasAttribute           = 'hasAttribute'
    ,createElement          = 'createElement'
    ,appendChild            = 'appendChild'
    ,innerHTML              = 'innerHTML'
    ,getElementsByTagName   = 'getElementsByTagName'
    ,preventDefault         = 'preventDefault'
    ,getBoundingClientRect  = 'getBoundingClientRect'
    ,querySelectorAll       = 'querySelectorAll'
    ,getElementsByCLASSNAME = 'getElementsByClassName'
    ,getComputedStyle       = 'getComputedStyle'  
  
    ,indexOf      = 'indexOf'
    ,parentNode   = 'parentNode'
    ,length       = 'length'
    ,toLowerCase  = 'toLowerCase'
    ,Transition   = 'Transition'
    ,Duration     = 'Duration'  
    ,Webkit       = 'Webkit'
    ,style        = 'style'
    ,push         = 'push'
    ,tabindex     = 'tabindex'
    ,contains     = 'contains'  
    
    ,active     = 'active'
    ,inClass    = 'in'
    ,collapsing = 'collapsing'
    ,disabled   = 'disabled'
    ,loading    = 'loading'
    ,left       = 'left'
    ,right      = 'right'
    ,top        = 'top'
    ,bottom     = 'bottom'
    //////
    // IE8
    //////
    ,isIE8 = !('opacity' in HTML[style])
    ////////////////////
    // tooltip / popover
    ////////////////////
    ,mouseHover = ('onmouseleave' in DOC) ? [ 'mouseenter', 'mouseleave'] : [ 'mouseover', 'mouseout' ]
    ,tipPositions = /\b(top|bottom|left|right)+/
    ////////
    // modal
    ////////
    ,modalOverlay = 0
    ,fixedTop = 'navbar-fixed-top'
    ,fixedBottom = 'navbar-fixed-bottom'
    ////////////////////////////
    // transição Fim desde 2.0.4
    ////////////////////////////
    ,supportTransitions = Webkit+Transition in HTML[style] || Transition[toLowerCase]() in HTML[style]
    ,transitionEndEvent = Webkit+Transition in HTML[style] ? Webkit[toLowerCase]()+Transition+'End' : Transition[toLowerCase]()+'end'
    ,transitionDuration = Webkit+Duration in HTML[style] ? Webkit[toLowerCase]()+Transition+Duration : Transition[toLowerCase]()+Duration,
    ////////////////////////////////////////////
    // definir novo elemento de foco desde 2.0.3
    ////////////////////////////////////////////
    setFocus = function(element){
      element.focus ? element.focus() : element.setActive();
    },
    ///////////////////////////////////////////////////////
    // classe manipulacao, desde 2.0.0 requires polyfill.js
    ///////////////////////////////////////////////////////
    addClass = function(element,classNAME) {
      element.classList.add(classNAME);
    },
    removeClass = function(element,classNAME) {
      element.classList.remove(classNAME);
    },
    hasClass = function(element,classNAME){
      return element.classList[contains](classNAME);
    },
    /////////////////////
    // selecao de metodos
    /////////////////////
    nodeListToArray = function(nodeList){
      var childItems = []; for (var i = 0, nll = nodeList[length]; i<nll; i++) { childItems[push]( nodeList[i] ) }
      return childItems;
    },
    getElementsByClassName = function(element,classNAME) { 
      var selectionMethod = isIE8 ? querySelectorAll : getElementsByCLASSNAME;      
      return nodeListToArray(element[selectionMethod]( isIE8 ? '.' + classNAME.replace(/\s(?=[a-z])/g,'.') : classNAME ));
    },
    queryElement = function (selector, parent) {
      var lookUp = parent ? parent : DOC;
      return typeof selector === 'object' ? selector : lookUp.querySelector(selector);
    },
    getClosest = function (element, selector) { //element is the element and selector is for the closest parent element to find
      var firstChar = selector.charAt(0), selectorSubstring = selector.substr(1);
      if ( firstChar === '.' ) {// If selector is a class
        for ( ; element && element !== DOC; element = element[parentNode] ) { // Get closest match
          if ( queryElement(selector,element[parentNode]) !== null && hasClass(element,selectorSubstring) ) { return element; }
        }
      } else if ( firstChar === '#' ) { // If selector is an ID
        for ( ; element && element !== DOC; element = element[parentNode] ) { // Get closest match
          if ( element.id === selectorSubstring ) { return element; }
        }
      }
      return false;
    },
  
    // event attach jQuery style / trigger  since 1.2.0
    on = function (element, event, handler) {
      element.addEventListener(event, handler, false);
    },
    off = function(element, event, handler) {
      element.removeEventListener(event, handler, false);
    },
    one = function (element, event, handler) { // one since 2.0.4
      on(element, event, function handlerWrapper(e){
        handler(e);
        off(element, event, handlerWrapper);
      });
    },
    getTransitionDurationFromElement = function(element) {
      var duration = supportTransitions ? globalObject[getComputedStyle](element)[transitionDuration] : 0;
      duration = parseFloat(duration);
      duration = typeof duration === 'number' && !isNaN(duration) ? duration * 1000 : 0;
      return duration + 50; // we take a short offset to make sure we fire on the next frame after animation
    },
    emulateTransitionEnd = function(element,handler){ // emulateTransitionEnd since 2.0.4
      var called = 0, duration = getTransitionDurationFromElement(element);
      supportTransitions && one(element, transitionEndEvent, function(e){ handler(e); called = 1; });
      setTimeout(function() { !called && handler(); }, duration);
    },
    bootstrapCustomEvent = function (eventName, componentName, related) {
      var OriginalCustomEvent = new CustomEvent( eventName + '.bs.' + componentName);
      OriginalCustomEvent.relatedTarget = related;
      this.dispatchEvent(OriginalCustomEvent);
    },
  
    // tooltip / popover stuff
    getScroll = function() { // also Affix and ScrollSpy uses it
      return {
        y : globalObject.pageYOffset || HTML[scrollTop],
        x : globalObject.pageXOffset || HTML[scrollLeft]
      }
    },
    styleTip = function(link,element,position,parent) { // both popovers and tooltips (target,tooltip/popover,placement,elementToAppendTo)
      var elementDimensions = { w : element[offsetWidth], h: element[offsetHeight] },
          windowWidth = (HTML[clientWidth] || DOC[body][clientWidth]),
          windowHeight = (HTML[clientHeight] || DOC[body][clientHeight]),
          rect = link[getBoundingClientRect](), 
          scroll = parent === DOC[body] ? getScroll() : { x: parent[offsetLeft] + parent[scrollLeft], y: parent[offsetTop] + parent[scrollTop] },
          linkDimensions = { w: rect[right] - rect[left], h: rect[bottom] - rect[top] },
          arrow = queryElement('[class*="arrow"]',element),
          topPosition, leftPosition, arrowTop, arrowLeft,
  
          halfTopExceed = rect[top] + linkDimensions.h/2 - elementDimensions.h/2 < 0,
          halfLeftExceed = rect[left] + linkDimensions.w/2 - elementDimensions.w/2 < 0,
          halfRightExceed = rect[left] + elementDimensions.w/2 + linkDimensions.w/2 >= windowWidth,
          halfBottomExceed = rect[top] + elementDimensions.h/2 + linkDimensions.h/2 >= windowHeight,
          topExceed = rect[top] - elementDimensions.h < 0,
          leftExceed = rect[left] - elementDimensions.w < 0,
          bottomExceed = rect[top] + elementDimensions.h + linkDimensions.h >= windowHeight,
          rightExceed = rect[left] + elementDimensions.w + linkDimensions.w >= windowWidth;
  
      // recompute position
      position = (position === left || position === right) && leftExceed && rightExceed ? top : position; // first, when both left and right limits are exceeded, we fall back to top|bottom
      position = position === top && topExceed ? bottom : position;
      position = position === bottom && bottomExceed ? top : position;
      position = position === left && leftExceed ? right : position;
      position = position === right && rightExceed ? left : position;
      
      // apply styling to tooltip or popover
      if ( position === left || position === right ) { // secondary|side positions
        if ( position === left ) { // LEFT
          leftPosition = rect[left] + scroll.x - elementDimensions.w;
        } else { // RIGHT
          leftPosition = rect[left] + scroll.x + linkDimensions.w;
        }
  
        // adjust top and arrow
        if (halfTopExceed) {
          topPosition = rect[top] + scroll.y;
          arrowTop = linkDimensions.h/2;
        } else if (halfBottomExceed) {
          topPosition = rect[top] + scroll.y - elementDimensions.h + linkDimensions.h;
          arrowTop = elementDimensions.h - linkDimensions.h/2;
        } else {
          topPosition = rect[top] + scroll.y - elementDimensions.h/2 + linkDimensions.h/2;
        }
      } else if ( position === top || position === bottom ) { // primary|vertical positions
        if ( position === top) { // TOP
          topPosition =  rect[top] + scroll.y - elementDimensions.h;
        } else { // BOTTOM
          topPosition = rect[top] + scroll.y + linkDimensions.h;
        }
        // adjust left | right and also the arrow
        if (halfLeftExceed) {
          leftPosition = 0;
          arrowLeft = rect[left] + linkDimensions.w/2;
        } else if (halfRightExceed) {
          leftPosition = windowWidth - elementDimensions.w*1.01;
          arrowLeft = elementDimensions.w - ( windowWidth - rect[left] ) + linkDimensions.w/2;
        } else {
          leftPosition = rect[left] + scroll.x - elementDimensions.w/2 + linkDimensions.w/2;
        }
      }
  
      // apply style to tooltip/popover and it's arrow
      element[style][top] = topPosition + 'px';
      element[style][left] = leftPosition + 'px';
  
      arrowTop && (arrow[style][top] = arrowTop + 'px');
      arrowLeft && (arrow[style][left] = arrowLeft + 'px');
  
      element.className[indexOf](position) === -1 && (element.className = element.className.replace(tipPositions,position));
    };
  BSN.version = '2.0.25';
  ////////////////////////////////////////////
  // Native Javascript for Bootstrap 3 | Affix
  // AFFIX DEFINITION
  ////////////////////////////////////////////
  var Affix = function(element, options) {
    ///////////////////////
    // Iniciando o elemento
    ///////////////////////
    element = queryElement(element);
  
    // set options
    options = options || {};
  
    // read DATA API
    var targetData        = element[getAttribute](dataTarget),
        offsetTopData     = element[getAttribute](dataOffsetTop),
        offsetBottomData  = element[getAttribute](dataOffsetBottom),
        
        // component specific strings
        affix = 'affix', affixed = 'affixed', fn = 'function', update = 'update',
        affixTop = 'affix-top', affixedTop = 'affixed-top',
        affixBottom = 'affix-bottom', affixedBottom = 'affixed-bottom';
  
    this[target] = options[target] ? queryElement(options[target]) : queryElement(targetData) || null; // target is an object
    this[offsetTop] = options[offsetTop] ? options[offsetTop] : parseInt(offsetTopData) || 0; // offset option is an integer number or function to determine that number
    this[offsetBottom] = options[offsetBottom] ? options[offsetBottom]: parseInt(offsetBottomData) || 0;
  
    if ( !this[target] && !( this[offsetTop] || this[offsetBottom] ) ) { return; } // invalidate
  
    // internal bind
    var self = this,
  
      // constants
      pinOffsetTop, pinOffsetBottom, maxScroll, scrollY, pinnedTop, pinnedBottom,
      affixedToTop = false, affixedToBottom = false,
      
      // private methods 
      getMaxScroll = function(){
        return Math.max( DOC[body][scrollHeight], DOC[body][offsetHeight], HTML[clientHeight], HTML[scrollHeight], HTML[offsetHeight] );
      },
      getOffsetTop = function () {
        if ( self[target] !== null ) {
          return self[target][getBoundingClientRect]()[top] + scrollY;
        } else if ( self[offsetTop] ) {
          return parseInt(typeof self[offsetTop] === fn ? self[offsetTop]() : self[offsetTop] || 0);
        }
      },
      getOffsetBottom = function () {
        if ( self[offsetBottom] ) {
          return maxScroll - element[offsetHeight] - parseInt( typeof self[offsetBottom] === fn ? self[offsetBottom]() : self[offsetBottom] || 0 );
        }
      },
      checkPosition = function () {
        maxScroll = getMaxScroll();
        scrollY = parseInt(getScroll().y,0);
        pinOffsetTop = getOffsetTop();
        pinOffsetBottom = getOffsetBottom(); 
        pinnedTop = ( parseInt(pinOffsetTop) - scrollY < 0) && (scrollY > parseInt(pinOffsetTop) );
        pinnedBottom = ( parseInt(pinOffsetBottom) - scrollY < 0) && (scrollY > parseInt(pinOffsetBottom) );
      },
      pinTop = function () {
        if ( !affixedToTop && !hasClass(element,affix) ) { // on loading a page halfway scrolled these events don't trigger in Chrome
          bootstrapCustomEvent.call(element, affix, affix);
          bootstrapCustomEvent.call(element, affixTop, affix);
          addClass(element,affix);
          affixedToTop = true;
          bootstrapCustomEvent.call(element, affixed, affix);
          bootstrapCustomEvent.call(element, affixedTop, affix);
        }
      },
      unPinTop = function () {
        if ( affixedToTop && hasClass(element,affix) ) {
          removeClass(element,affix);
          affixedToTop = false;
        }
      },
      pinBottom = function () {
        if ( !affixedToBottom && !hasClass(element, affixBottom) ) {
          bootstrapCustomEvent.call(element, affix, affix);
          bootstrapCustomEvent.call(element, affixBottom, affix);
          addClass(element,affixBottom);
          affixedToBottom = true;
          bootstrapCustomEvent.call(element, affixed, affix);
          bootstrapCustomEvent.call(element, affixedBottom, affix);
        }
      },
      unPinBottom = function () {
        if ( affixedToBottom && hasClass(element,affixBottom) ) {
          removeClass(element,affixBottom);
          affixedToBottom = false;
        }
      },
      updatePin = function () {
        if ( pinnedBottom ) {
          if ( pinnedTop ) { unPinTop(); }
          pinBottom(); 
        } else {
          unPinBottom();
          if ( pinnedTop ) { pinTop(); } 
          else { unPinTop(); }
        }
      };
  
    // public method
    this[update] = function () {
      checkPosition();
      updatePin(); 
    };
  
    // init
    if ( !(stringAffix in element ) ) { // prevent adding event handlers twice
      on( globalObject, scrollEvent, self[update] );
      !isIE8 && on( globalObject, resizeEvent, self[update] );
    }
    element[stringAffix] = self;
  
    self[update]();
  };
  /////////////////
  // AFFIX DATA API
  /////////////////
  supports[push]([stringAffix, Affix, '['+dataSpy+'="affix"]']);
  //
  //  
  ////////////////////////////////////////////
  // Native Javascript for Bootstrap 3 | Alert
  ////////////////////////////////////////////
  var Alert = function( element ) {
    ///////////////////////
    // Iniciando o elemento
    ///////////////////////
    element = queryElement(element);
  
    // bind, target alert, duration and stuff
    var self = this, component = 'alert',
      alert = getClosest(element,'.'+component),
      triggerHandler = function(){ hasClass(alert,'fade') ? emulateTransitionEnd(alert,transitionEndHandler) : transitionEndHandler(); },
      // handlers
      clickHandler = function(e){
        alert = getClosest(e[target],'.'+component);
        element = queryElement('['+dataDismiss+'="'+component+'"]',alert);
        element && alert && (element === e[target] || element[contains](e[target])) && self.close();
      },
      transitionEndHandler = function(){
        bootstrapCustomEvent.call(alert, closedEvent, component);
        off(element, clickEvent, clickHandler); // detach it's listener
        alert[parentNode].removeChild(alert);
      };
    
    // public method
    this.close = function() {
      if ( alert && element && hasClass(alert,inClass) ) {
        bootstrapCustomEvent.call(alert, closeEvent, component);
        removeClass(alert,inClass);
        alert && triggerHandler();
      }
    };
  
    // init
    if ( !(stringAlert in element ) ) { // prevent adding event handlers twice
      on(element, clickEvent, clickHandler);
    }
    element[stringAlert] = self;
  };
  supports[push]([stringAlert, Alert, '['+dataDismiss+'="alert"]']);
  //  
  //
  /////////////////////////////////////////////
  // Native Javascript for Bootstrap 3 | Button
  /////////////////////////////////////////////
  var Button = function( element, option ) {
    ///////////////////////
    // Iniciando o elemento
    ///////////////////////
    element = queryElement(element);
  
    // set option
    option = option || null;
  
    // constant
    var toggled = false, // toggled makes sure to prevent triggering twice the change.bs.button events
  
        // strings
        component = 'button',
        checked = 'checked',
        reset = 'reset',
        LABEL = 'LABEL',
        INPUT = 'INPUT',
  
      // private methods
      setState = function() {
        if ( !! option && option !== reset ) {
          if ( option === loading ) {
            addClass(element,disabled);
            element[setAttribute](disabled,disabled);
            element[setAttribute](dataOriginalText, element[innerHTML].trim()); // trim the text
          }
          element[innerHTML] = element[getAttribute]('data-'+option+'-text');
        }
      },
      resetState = function() {
        if (element[getAttribute](dataOriginalText)) {
          if ( hasClass(element,disabled) || element[getAttribute](disabled) === disabled ) {
            removeClass(element,disabled);
            element.removeAttribute(disabled);
          }
          element[innerHTML] = element[getAttribute](dataOriginalText);
        }
      },
      keyHandler = function(e){ 
        var key = e.which || e.keyCode;
        key === 32 && e[target] === DOC.activeElement && toggle(e);
      },
      preventScroll = function(e){ 
        var key = e.which || e.keyCode;
        key === 32 && e[preventDefault]();
      },    
      toggle = function(e) {
        var label = e[target].tagName === LABEL ? e[target] : e[target][parentNode].tagName === LABEL ? e[target][parentNode] : null; // the .btn label
        
        if ( !label ) return; //react if a label or its immediate child is clicked
  
        var eventTarget = e[target], // the button itself, the target of the handler function
          labels = getElementsByClassName(eventTarget[parentNode],'btn'), // all the button group buttons
          input = label[getElementsByTagName](INPUT)[0];
  
        if ( !input ) return; //return if no input found
  
        // manage the dom manipulation
        if ( input.type === 'checkbox' ) { //checkboxes
          if ( !input[checked] ) {
            addClass(label,active);
            input[getAttribute](checked);
            input[setAttribute](checked,checked);
            input[checked] = true;
          } else {
            removeClass(label,active);
            input[getAttribute](checked);
            input.removeAttribute(checked);
            input[checked] = false;
          }
  
          if (!toggled) { // prevent triggering the event twice
            toggled = true;
            bootstrapCustomEvent.call(input, changeEvent, component); //trigger the change for the input
            bootstrapCustomEvent.call(element, changeEvent, component); //trigger the change for the btn-group
          }
        }
  
        if ( input.type === 'radio' && !toggled ) { // radio buttons
          if ( !input[checked] ) { // don't trigger if already active
            addClass(label,active);
            input[setAttribute](checked,checked);
            input[checked] = true;
            bootstrapCustomEvent.call(input, changeEvent, component); //trigger the change for the input
            bootstrapCustomEvent.call(element, changeEvent, component); //trigger the change for the btn-group
  
            toggled = true;
            for (var i = 0, ll = labels[length]; i<ll; i++) {
              var otherLabel = labels[i], otherInput = otherLabel[getElementsByTagName](INPUT)[0];
              if ( otherLabel !== label && hasClass(otherLabel,active) )  {
                removeClass(otherLabel,active);
                otherInput.removeAttribute(checked);
                otherInput[checked] = false;
                bootstrapCustomEvent.call(otherInput, changeEvent, component); // trigger the change
              }
            }
          }
        }
        setTimeout( function() { toggled = false; }, 50 );
      };
  
    // init
    if ( hasClass(element,'btn') ) { // when Button text is used we execute it as an instance method
      if ( option !== null ) {
        if ( option !== reset ) { setState(); } 
        else { resetState(); }
      }
    } else { // if ( hasClass(element,'btn-group') ) // we allow the script to work outside btn-group component
      
      if ( !( stringButton in element ) ) { // prevent adding event handlers twice
        on( element, clickEvent, toggle );
        queryElement('['+tabindex+']',element) && on( element, keyupEvent, keyHandler ), 
                                                  on( element, keydownEvent, preventScroll );
      }
  
      // activate items on load
      var labelsToACtivate = getElementsByClassName(element, 'btn'), lbll = labelsToACtivate[length];
      for (var i=0; i<lbll; i++) {
        !hasClass(labelsToACtivate[i],active) && queryElement('input',labelsToACtivate[i])[getAttribute](checked)
                                              && addClass(labelsToACtivate[i],active);
      }
      element[stringButton] = this;
    }
  };
  supports[push]( [ stringButton, Button, '['+dataToggle+'="buttons"]' ] );
  //  
  //
  ///////////////////////////////////////////////
  // Native Javascript for Bootstrap 3 | Carousel
  ///////////////////////////////////////////////
  var Carousel = function( element, options ) {
    ///////////////////////
    // Iniciando o elemento
    ///////////////////////
    element = queryElement( element );
  
    // set options
    options = options || {};
  
    // DATA API
    var intervalAttribute = element[getAttribute](dataInterval),
        intervalOption = options[interval],
        intervalData = intervalAttribute === 'false' ? 0 : parseInt(intervalAttribute),  
        pauseData = element[getAttribute](dataPause) === hoverEvent || false,
        keyboardData = element[getAttribute](dataKeyboard) === 'true' || false,
      
        // strings
        component = 'carousel',
        paused = 'paused',
        direction = 'direction',
        dataSlideTo = 'data-slide-to'; 
  
    this[keyboard] = options[keyboard] === true || keyboardData;
    this[pause] = (options[pause] === hoverEvent || pauseData) ? hoverEvent : false; // false / hover
  
    this[interval] = typeof intervalOption === 'number' ? intervalOption
                   : intervalOption === false || intervalData === 0 || intervalData === false ? 0
                   : isNaN(intervalData) ? 5000 // bootstrap carousel default interval
                   : intervalData;
  
    // bind, event targets
    var self = this, index = element.index = 0, timer = element.timer = 0, 
      isSliding = false, // isSliding prevents click event handlers when animation is running
      slides = getElementsByClassName(element,'item'), total = slides[length],
      slideDirection = this[direction] = left,
      controls = getElementsByClassName(element,component+'-control'),
      leftArrow = controls[0], rightArrow = controls[1],
      indicator = queryElement( '.'+component+'-indicators', element ),
      indicators = indicator && indicator[getElementsByTagName]( "LI" ) || [];
  
    // invalidate when not enough items
    if (total < 2) { return; }
  
    // handlers
    var pauseHandler = function () {
        if ( self[interval] !==false && !hasClass(element,paused) ) {
          addClass(element,paused);
          !isSliding && ( clearInterval(timer), timer = null );
        }
      },
      resumeHandler = function() {
        if ( self[interval] !== false && hasClass(element,paused) ) {
          removeClass(element,paused);
          !isSliding && ( clearInterval(timer), timer = null );
          !isSliding && self.cycle();
        }
      },
      indicatorHandler = function(e) {
        e[preventDefault]();
        if (isSliding) return;
  
        var eventTarget = e[target]; // event target | the current active item
  
        if ( eventTarget && !hasClass(eventTarget,active) && eventTarget[getAttribute](dataSlideTo) ) {
          index = parseInt( eventTarget[getAttribute](dataSlideTo), 10 );
        } else { return false; }
  
        self.slideTo( index ); //Do the slide
      },
      controlsHandler = function (e) {
        e[preventDefault]();
        if (isSliding) return;
  
        var eventTarget = e.currentTarget || e.srcElement;
  
        if ( eventTarget === rightArrow ) {
          index++;
        } else if ( eventTarget === leftArrow ) {
          index--;
        }
  
        self.slideTo( index ); //Do the slide
      },
      keyHandler = function (e) {
        if (isSliding) return;
        switch (e.which) {
          case 39:
            index++;
            break;
          case 37:
            index--;
            break;
          default: return;
        }
        self.slideTo( index ); //Do the slide
      },
      // private methods
      isElementInScrollRange = function () {
        var rect = element[getBoundingClientRect](),
          viewportHeight = globalObject[innerHeight] || HTML[clientHeight]
        return rect[top] <= viewportHeight && rect[bottom] >= 0; // bottom && top
      },  
      setActivePage = function( pageIndex ) { //indicators
        for ( var i = 0, icl = indicators[length]; i < icl; i++ ) {
          removeClass(indicators[i],active);
        }
        if (indicators[pageIndex]) addClass(indicators[pageIndex], active);
      };
  
  
    // public methods
    this.cycle = function() {
      if (timer) {
        clearInterval(timer);
        timer = null;
      }
  
      timer = setInterval(function() {
        isElementInScrollRange() && (index++, self.slideTo( index ) );
      }, this[interval]);
    };
    this.slideTo = function( next ) {
      if (isSliding) return; // when controled via methods, make sure to check again    
      var activeItem = this.getActiveIndex(), // the current active
          orientation;
      
        // first return if we're on the same item #227
        if ( activeItem === next ) {
          return;
        // or determine slideDirection
        } else if  ( (activeItem < next ) || (activeItem === 0 && next === total -1 ) ) {
        slideDirection = self[direction] = left; // next
      } else if  ( (activeItem > next) || (activeItem === total - 1 && next === 0 ) ) {
        slideDirection = self[direction] = right; // prev
      }
  
      // find the right next index 
      if ( next < 0 ) { next = total - 1; } 
      else if ( next >= total ){ next = 0; }
  
      // update index
      index = next;
      
      orientation = slideDirection === left ? 'next' : 'prev'; //determine type
      bootstrapCustomEvent.call(element, slideEvent, component, slides[next]); // here we go with the slide
  
      isSliding = true;
      clearInterval(timer);
      timer = null;
      setActivePage( next );
  
      if ( supportTransitions && hasClass(element,'slide') ) {
  
        addClass(slides[next],orientation);
        slides[next][offsetWidth];
        addClass(slides[next],slideDirection);
        addClass(slides[activeItem],slideDirection);
  
        one(slides[next], transitionEndEvent, function(e) {
          var timeout = e[target] !== slides[next] ? e.elapsedTime*1000+100 : 20;
          isSliding && setTimeout(function(){
            isSliding = false;
  
            addClass(slides[next],active);
            removeClass(slides[activeItem],active);
  
            removeClass(slides[next],orientation);
            removeClass(slides[next],slideDirection);
            removeClass(slides[activeItem],slideDirection);
  
            bootstrapCustomEvent.call(element, slidEvent, component, slides[next]);
  
            if ( self[interval] && !hasClass(element,paused) ) {
              self.cycle();
            }
          }, timeout);
        });
  
      } else {
        addClass(slides[next],active);
        slides[next][offsetWidth];
        removeClass(slides[activeItem],active);
        setTimeout(function() {
          isSliding = false;
          if ( self[interval] && !hasClass(element,paused) ) {
            self.cycle();
          }
          bootstrapCustomEvent.call(element, slidEvent, component, slides[next]); // here we go with the slid event
        }, 100 );
      }
    };
    this.getActiveIndex = function () {
      return slides[indexOf](getElementsByClassName(element,'item active')[0]) || 0;
    };
  
    // init
    if ( !(stringCarousel in element ) ) { // prevent adding event handlers twice
  
      if ( self[pause] && self[interval] ) {
        on( element, mouseHover[0], pauseHandler );
        on( element, mouseHover[1], resumeHandler );
        on( element, 'touchstart', pauseHandler );
        on( element, 'touchend', resumeHandler );
      }
    
      rightArrow && on( rightArrow, clickEvent, controlsHandler );
      leftArrow && on( leftArrow, clickEvent, controlsHandler );
    
      indicator && on( indicator, clickEvent, indicatorHandler );
      self[keyboard] && on( globalObject, keydownEvent, keyHandler );
  
    }
    if (self.getActiveIndex()<0) {
      slides[length] && addClass(slides[0],active);
      indicators[length] && setActivePage(0);
    }
  
    if ( self[interval] ){ self.cycle(); }
    element[stringCarousel] = self;
  };
  supports[push]( [ stringCarousel, Carousel, '['+dataRide+'="carousel"]' ] );
  //
  //
  ///////////////////////////////////////////////  
  // Native Javascript for Bootstrap 3 | Collapse
  ///////////////////////////////////////////////
  var Collapse = function( element, options ) {
    ///////////////////////
    // Iniciando o elemento
    ///////////////////////
    element = queryElement(element);
    // set options
    options = options || {};
//  
    // event targets and constants
    var accordion = null
        ,collapse = null
        ,self = this
        ,accordionData = element[getAttribute]('data-parent')
        ,activeCollapse, activeElement,
  
      // component strings
      component = 'collapse',
      collapsed = 'collapsed',
      isAnimating = 'isAnimating',
      // private methods
      openAction = function(collapseElement,toggle) {
        bootstrapCustomEvent.call(collapseElement, showEvent, component);
        collapseElement[isAnimating] = true;
        addClass(collapseElement,collapsing);
        removeClass(collapseElement,component);
        collapseElement[style][height] = collapseElement[scrollHeight] + 'px';
        ///////////////////////////////////////////////////////////////
        // Para quando abrir a janela mandar o foco para algum elemento
        ///////////////////////////////////////////////////////////////
        if( collapseElement.getAttribute("data-foco") !== null ){
          document.getElementById(collapseElement.getAttribute("data-foco")).focus();
        };  
        emulateTransitionEnd(collapseElement, function() {
          collapseElement[isAnimating] = false;
          collapseElement[setAttribute](ariaExpanded,'true');
          toggle[setAttribute](ariaExpanded,'true');          
          removeClass(collapseElement,collapsing);
          addClass(collapseElement, component);
          addClass(collapseElement, inClass);
          collapseElement[style][height] = '';
          bootstrapCustomEvent.call(collapseElement, shownEvent, component);
        });

      },
      closeAction = function(collapseElement,toggle) {
        bootstrapCustomEvent.call(collapseElement, hideEvent, component);
        collapseElement[isAnimating] = true;
        collapseElement[style][height] = collapseElement[scrollHeight] + 'px'; // set height first
        removeClass(collapseElement,component);
        removeClass(collapseElement, inClass);
        addClass(collapseElement, collapsing);
        collapseElement[offsetWidth]; // force reflow to enable transition
        collapseElement[style][height] = '0px';
        
        emulateTransitionEnd(collapseElement, function() {
          collapseElement[isAnimating] = false;
          collapseElement[setAttribute](ariaExpanded,'false');
          toggle[setAttribute](ariaExpanded,'false');
          removeClass(collapseElement,collapsing);
          addClass(collapseElement,component);
          collapseElement[style][height] = '';
          bootstrapCustomEvent.call(collapseElement, hiddenEvent, component);
        });
      },
      getTarget = function() {
        var href = element.href && element[getAttribute]('href'),
          parent = element[getAttribute](dataTarget),
          id = href || ( parent && parent.charAt(0) === '#' ) && parent;
        return id && queryElement(id);
      };
    
    // public methods
    this.toggle = function(e) {
      e[preventDefault]();
      if (!hasClass(collapse,inClass)) { self.show(); } 
      else { self.hide(); }
    };
    this.hide = function() {
      if ( collapse[isAnimating] ) return;
      closeAction(collapse,element);
      addClass(element,collapsed);
    };
    this.show = function() {
      if ( accordion ) {
        activeCollapse = queryElement('.'+component+'.'+inClass,accordion);
        activeElement = activeCollapse && (queryElement('['+dataToggle+'="'+component+'"]['+dataTarget+'="#'+activeCollapse.id+'"]', accordion)
                      || queryElement('['+dataToggle+'="'+component+'"][href="#'+activeCollapse.id+'"]',accordion) );
      }
  
      if ( !collapse[isAnimating] || activeCollapse && !activeCollapse[isAnimating] ) {
        if ( activeElement && activeCollapse !== collapse ) {
          closeAction(activeCollapse,activeElement);
          addClass(activeElement,collapsed); 
        }
        openAction(collapse,element);
        removeClass(element,collapsed);
      }
    };
  
    // init
    if ( !(stringCollapse in element ) ) { // prevent adding event handlers twice
      on(element, clickEvent, self.toggle);
    }
    collapse = getTarget();
    collapse[isAnimating] = false;  // when true it will prevent click handlers  
    accordion = queryElement(options.parent) || accordionData && getClosest(element, accordionData);
    element[stringCollapse] = self;
  };
  supports[push]( [ stringCollapse, Collapse, '['+dataToggle+'="collapse"]' ] );
  //
  //
  ///////////////////////////////////////////////  
  // Native Javascript for Bootstrap 3 | Dropdown
  ///////////////////////////////////////////////
  var Dropdown = function( element, option ) {
    ///////////////////////
    // Iniciando o elemento
    ///////////////////////
    element = queryElement(element);
  
    // set option
    this.persist = option === true || element[getAttribute]('data-persist') === 'true' || false;
  
    // constants, event targets, strings
    var self = this, children = 'children',
      parent = element[parentNode],
      component = 'dropdown', open = 'open',
      relatedTarget = null,
      menu = queryElement('.dropdown-menu', parent),
      menuItems = (function(){
        let set = menu[children];
        let newSet = [];
        for ( var i=0; i<set[length]; i++ ){
          set[i][children][length] && (set[i][children][0].tagName === 'A' && newSet[push](set[i])); 
          /*    
          if( set[i].getAttribute("role")=="presentation" ){
            let img  = document.createElement("i");
            img.setAttribute("class",'fa fa-circle-o pull-right');               
            if( set[i].children[0].innerHTML==checado ){
              img.setAttribute("class",'fa fa-circle pull-right'); 
            } else {
              img.setAttribute("class",'fa fa-circle-o pull-right');               
            }  
            set[i].children[0].appendChild(img); 
          };  
          */
        }
        return newSet;
      })(),

      // preventDefault on empty anchor links
      preventEmptyAnchor = function(anchor){
        (anchor.href && anchor.href.slice(-1) === '#' || anchor[parentNode] && anchor[parentNode].href 
          && anchor[parentNode].href.slice(-1) === '#') && this[preventDefault]();      
      },
  
      // toggle dismissible events
      toggleDismiss = function(){
        var type = element[open] ? on : off;
        type(DOC, clickEvent, dismissHandler); 
        type(DOC, keydownEvent, preventScroll);
        type(DOC, keyupEvent, keyHandler);
      },
  
      // handlers
      dismissHandler = function(e) {
        var eventTarget = e[target], hasData = eventTarget && (stringDropdown in eventTarget || stringDropdown in eventTarget[parentNode]);
       
        if ( (eventTarget === menu || menu[contains](eventTarget)) && (self.persist || hasData) ) { 
          return; 
        } else {
          relatedTarget = eventTarget === element || element[contains](eventTarget) ? element : null;
          hide();
        }

        preventEmptyAnchor.call(e,eventTarget);
      },
      clickHandler = function(e) {
        relatedTarget = element;
        show();
        preventEmptyAnchor.call(e,e[target]);
      },
      preventScroll = function(e){
        var key = e.which || e.keyCode;
        if( key === 38 || key === 40 ) { e[preventDefault](); }
      },
      keyHandler = function(e){
        var key = e.which || e.keyCode, 
            activeItem = DOC.activeElement,
            idx = menuItems[indexOf](activeItem[parentNode]),
            isSameElement = activeItem === element,
            isInsideMenu = menu[contains](activeItem),
            isMenuItem = activeItem[parentNode][parentNode] === menu;
        
        if ( isMenuItem || isSameElement ) { // navigate up | down
          idx = isSameElement ? 0 
                              : key === 38 ? (idx>1?idx-1:0) 
                              : key === 40 ? (idx<menuItems[length]-1?idx+1:idx) : idx;
          menuItems[idx] && setFocus(menuItems[idx][children][0]);
        }
        if ( (menuItems[length] && isMenuItem // menu has items
          || !menuItems[length] && (isInsideMenu || isSameElement)  // menu might be a form
          || !isInsideMenu ) // or the focused element is not in the menu at all
          && element[open] && key === 27 // menu must be open
        ) {
          self.toggle();
          relatedTarget = null;
        }
      },  
  
      // private methods
      show = function() {
        bootstrapCustomEvent.call(parent, showEvent, component, relatedTarget);
        addClass(parent,open);
        element[setAttribute](ariaExpanded,true);
        bootstrapCustomEvent.call(parent, shownEvent, component, relatedTarget);
        element[open] = true;
        off(element, clickEvent, clickHandler);
        setTimeout(function(){ 
          setFocus( menu[getElementsByTagName]('INPUT')[0] || element ); // focus the first input item | element
          toggleDismiss(); 
        },1);
      },
      hide = function() {
        bootstrapCustomEvent.call(parent, hideEvent, component, relatedTarget);
        removeClass(parent,open);
        element[setAttribute](ariaExpanded,false);
        bootstrapCustomEvent.call(parent, hiddenEvent, component, relatedTarget);
        element[open] = false;
        toggleDismiss();
        setFocus(element);
        setTimeout(function(){ on(element, clickEvent, clickHandler); },1);
      };
  
    // set initial state to closed
    element[open] = false;
  
    // public methods
    this.toggle = function() {
      
      if (hasClass(parent,open) && element[open]) { 
        hide(); 
      } else { 
        show(); 
      }
    };
  
    // init
    if (!(stringDropdown in element)) { // prevent adding event handlers twice
      !tabindex in menu && menu[setAttribute](tabindex, '0'); // Fix onblur on Chrome | Safari
      on(element, clickEvent, clickHandler);
    }
  
    element[stringDropdown] = self;
  };
  supports[push]( [stringDropdown, Dropdown, '['+dataToggle+'="dropdown"]'] );
  //
  //
  ////////////////////////////////////////////
  // Native Javascript for Bootstrap 3 | Modal
  ////////////////////////////////////////////
  var Modal = function(element, options) { // element can be the modal/triggering button
    // the modal (both JavaScript / DATA API init) / triggering button element (DATA API)
    element = queryElement(element);
  
    // determine modal, triggering element
    var btnCheck = element[getAttribute](dataTarget)||element[getAttribute]('href'),
      checkModal = queryElement( btnCheck ),
      modal = hasClass(element,'modal') ? element : checkModal,
      overlayDelay,
  
      // strings
      component = 'modal',
      staticString = 'static',
      paddingLeft = 'paddingLeft',
      paddingRight = 'paddingRight',
      modalBackdropString = 'modal-backdrop';
  
    if ( hasClass(element,'modal') ) { element = null; } // modal is now independent of it's triggering element
  
    if ( !modal ) { return; } // invalidate
  
    // set options
    options = options || {};
  
    this[keyboard] = options[keyboard] === false || modal[getAttribute](dataKeyboard) === 'false' ? false : true;
    this[backdrop] = options[backdrop] === staticString || modal[getAttribute](databackdrop) === staticString ? staticString : true;
    this[backdrop] = options[backdrop] === false || modal[getAttribute](databackdrop) === 'false' ? false : this[backdrop];
    this[content]  = options[content]; // JavaScript only
  
    // bind, constants, event targets and other vars
    var self = this, relatedTarget = null,
      bodyIsOverflowing, modalIsOverflowing, scrollbarWidth, overlay,
  
      // also find fixed-top / fixed-bottom items
      fixedItems = getElementsByClassName(HTML,fixedTop).concat(getElementsByClassName(HTML,fixedBottom)),
  
      // private methods
      getWindowWidth = function() {
        var htmlRect = HTML[getBoundingClientRect]();
        return globalObject[innerWidth] || (htmlRect[right] - Math.abs(htmlRect[left]));
      },
      setScrollbar = function () {
        var bodyStyle = DOC[body].currentStyle || globalObject[getComputedStyle](DOC[body]),
            bodyPad = parseInt((bodyStyle[paddingRight]), 10), itemPad;
        if (bodyIsOverflowing) {
          DOC[body][style][paddingRight] = (bodyPad + scrollbarWidth) + 'px';
          if (fixedItems[length]){
            for (var i = 0; i < fixedItems[length]; i++) {
              itemPad = (fixedItems[i].currentStyle || globalObject[getComputedStyle](fixedItems[i]))[paddingRight];
              fixedItems[i][style][paddingRight] = ( parseInt(itemPad) + scrollbarWidth) + 'px';
            }
          }
        }
      },
      resetScrollbar = function () {
        DOC[body][style][paddingRight] = '';
        if (fixedItems[length]){
          for (var i = 0; i < fixedItems[length]; i++) {
            fixedItems[i][style][paddingRight] = '';
          }
        }
      },
      measureScrollbar = function () { // thx walsh
        var scrollDiv = document.createElement("div"), scrollBarWidth;
        scrollDiv.className = component+'-scrollbar-measure'; // this is here to stay
        DOC[body][appendChild](scrollDiv);
        scrollBarWidth = scrollDiv[offsetWidth] - scrollDiv[clientWidth];
        DOC[body].removeChild(scrollDiv);
      return scrollBarWidth;
      },
      checkScrollbar = function () {
        bodyIsOverflowing = DOC[body][clientWidth] < getWindowWidth();
        modalIsOverflowing = modal[scrollHeight] > HTML[clientHeight];
        scrollbarWidth = measureScrollbar();
      },
      adjustDialog = function () {
        modal[style][paddingLeft] = !bodyIsOverflowing && modalIsOverflowing ? scrollbarWidth + 'px' : '';
        modal[style][paddingRight] = bodyIsOverflowing && !modalIsOverflowing ? scrollbarWidth + 'px' : '';
      },
      resetAdjustments = function () {
        modal[style][paddingLeft] = '';
        modal[style][paddingRight] = '';
      },
      createOverlay = function() {
        modalOverlay = 1;
        
        var newOverlay = document.createElement("div");
        overlay = queryElement('.'+modalBackdropString);
  
        if ( overlay === null ) {
          newOverlay[setAttribute]('class',modalBackdropString+' fade');
          overlay = newOverlay;
          DOC[body][appendChild](overlay);
        }
      },
      removeOverlay = function() {
        overlay = queryElement('.'+modalBackdropString);
        if ( overlay && overlay !== null && typeof overlay === 'object' ) {
          modalOverlay = 0;
          DOC[body].removeChild(overlay); overlay = null;
        }
        bootstrapCustomEvent.call(modal, hiddenEvent, component);      
      },
      keydownHandlerToggle = function() {
        if (hasClass(modal,inClass)) {
          on(DOC, keydownEvent, keyHandler);
        } else {
          off(DOC, keydownEvent, keyHandler);
        }
      },
      resizeHandlerToggle = function() {
        if (hasClass(modal,inClass)) {
          on(globalObject, resizeEvent, self.update);
        } else {
          off(globalObject, resizeEvent, self.update);
        }
      },
      dismissHandlerToggle = function() {
        if (hasClass(modal,inClass)) {
          on(modal, clickEvent, dismissHandler);
        } else {
          off(modal, clickEvent, dismissHandler);
        }
      },
      // triggers
      triggerShow = function() {
        resizeHandlerToggle();
        dismissHandlerToggle();
        keydownHandlerToggle();
        setFocus(modal);
        bootstrapCustomEvent.call(modal, shownEvent, component, relatedTarget);
      },
      triggerHide = function() {
        modal[style].display = '';
        element && (setFocus(element));
        
        (function(){
          if (!getElementsByClassName(DOC,component+' '+inClass)[0]) {
            resetAdjustments();
            resetScrollbar();
            removeClass(DOC[body],component+'-open');
            overlay && hasClass(overlay,'fade') ? (removeClass(overlay,inClass), emulateTransitionEnd(overlay,removeOverlay)) 
            : removeOverlay();
  
            resizeHandlerToggle();
            dismissHandlerToggle();
            keydownHandlerToggle();
          }
        }());
      },
      // handlers
      clickHandler = function(e) {
        var clickTarget = e[target];
        clickTarget = clickTarget[hasAttribute](dataTarget) || clickTarget[hasAttribute]('href') ? clickTarget : clickTarget[parentNode];
        if ( clickTarget === element && !hasClass(modal,inClass) ) {
          modal.modalTrigger = element;
          relatedTarget = element;
          self.show();
          e[preventDefault]();
        }
      },
      keyHandler = function(e) {
        var key = e.which || e.keyCode; // keyCode for IE8
        if (self[keyboard] && key == 27 && hasClass(modal,inClass)) {
          self.hide();
        }
      },
      dismissHandler = function(e) {
        var clickTarget = e[target];
        if ( hasClass(modal,inClass) && (clickTarget[parentNode][getAttribute](dataDismiss) === component
            || clickTarget[getAttribute](dataDismiss) === component
            || (clickTarget === modal && self[backdrop] !== staticString) ) ) {
          self.hide(); relatedTarget = null;
          e[preventDefault]();
        }
      };
  
    // public methods
    this.toggle = function() {
      if ( hasClass(modal,inClass) ) {this.hide();} else {this.show();}
    };
    this.show = function() {
      bootstrapCustomEvent.call(modal, showEvent, component, relatedTarget);
  
      // we elegantly hide any opened modal
      var currentOpen = getElementsByClassName(DOC,component+' in')[0];
      currentOpen && currentOpen !== modal && currentOpen.modalTrigger[stringModal].hide();
  
      if ( this[backdrop] ) {
        !modalOverlay && createOverlay();
      }
  
      if ( overlay && modalOverlay && !hasClass(overlay,inClass)) {
        overlay[offsetWidth]; // force reflow to enable trasition
        overlayDelay = getTransitionDurationFromElement(overlay);
        addClass(overlay,inClass);
      }
  
      setTimeout(function() {
        modal[style].display = 'block';
  
        checkScrollbar();
        setScrollbar();
        adjustDialog();
  
        addClass(DOC[body],component+'-open');
        addClass(modal,inClass);
        modal[setAttribute](ariaHidden, false);
  
        hasClass(modal,'fade') ? emulateTransitionEnd(modal, triggerShow) : triggerShow();
      }, supportTransitions && overlay ? overlayDelay : 0);
    };
    this.hide = function() {
      bootstrapCustomEvent.call(modal, hideEvent, component);
      overlay = queryElement('.'+modalBackdropString);
      overlayDelay = overlay && getTransitionDurationFromElement(overlay);
  
      removeClass(modal,inClass);
      modal[setAttribute](ariaHidden, true);
  
      setTimeout(function(){
        hasClass(modal,'fade') ? emulateTransitionEnd(modal, triggerHide) : triggerHide();
      }, supportTransitions && overlay ? overlayDelay : 0);
    };
    this.setContent = function( content ) {
      queryElement('.'+component+'-content',modal)[innerHTML] = content;
    };
    this.update = function() {
      if (hasClass(modal,inClass)) {
        checkScrollbar();
        setScrollbar();
        adjustDialog();
      }
    };
  
    // init
    // prevent adding event handlers over and over
    // modal is independent of a triggering element
    if ( !!element && !(stringModal in element) ) {
      on(element, clickEvent, clickHandler);
    }
    if ( !!self[content] ) { self.setContent( self[content] ); }
    !!element && (element[stringModal] = self);
  };
  supports[push]( [ stringModal, Modal, '['+dataToggle+'="modal"]' ] );
  //
  //
  //////////////////////////////////////////////
  // Native Javascript for Bootstrap 3 | Popover
  //////////////////////////////////////////////
  var Popover = function( element, options ) {
    ///////////////////////
    // Iniciando o elemento
    ///////////////////////
//    
    element = queryElement(element);
  
    // set options
    options = options || {};
    // DATA API
    var triggerData = element[getAttribute](dataTrigger), // click / hover / focus
        animationData = element[getAttribute](dataAnimation), // true / false
        placementData = element[getAttribute](dataPlacement),
        dismissibleData = element[getAttribute](dataDismissible),
        delayData = element[getAttribute](dataDelay),
        containerData = element[getAttribute](dataContainer),
  
        // internal strings
        component = 'popover',
        template = 'template',
        trigger = 'trigger',
        div = 'div',
        fade = 'fade',
        content = 'content',
        dataContent = 'data-content',
        dismissible = 'dismissible',
        closeBtn = '<button type="button" class="close">×</button>',
  
        // check container
        containerElement = queryElement(options[container]),
        containerDataElement = queryElement(containerData),      
        
        // maybe the element is inside a modal
        modal = getClosest(element,'.modal'),
        
        // maybe the element is inside a fixed navbar
        navbarFixedTop = getClosest(element,'.'+fixedTop),
        navbarFixedBottom = getClosest(element,'.'+fixedBottom);
    // set instance options
    this[template] = options[template] ? options[template] : null; // JavaScript only
    this[trigger] = options[trigger] ? options[trigger] : triggerData || hoverEvent;
    this[animation] = options[animation] && options[animation] !== fade ? options[animation] : animationData || fade;
    //this[placement] = ( options[placement] ? options[placement] : placementData ) || top;     //orlandoooooooooooooooooooooooooooooo    
    this[placement]= ( placementData !== null ? placementData : top );                          //orlandoooooooooooooooooooooooooooooo    
    this[delay] = parseInt(options[delay] || delayData) || 200;
    this[dismissible] = options[dismissible] || dismissibleData === 'true' ? true : false;
    this[container] = containerElement ? containerElement 
                    : containerDataElement ? containerDataElement 
                    : navbarFixedTop ? navbarFixedTop
                    : navbarFixedBottom ? navbarFixedBottom
                    : modal ? modal : DOC[body];
    /////////////////////////////////
    // bind(Ligar), content(Conteudo)
    /////////////////////////////////
    var self = this, 
      tituloString    = element[getAttribute](dataTitle) || null,
      conteudoString  = element[getAttribute](dataContent) || null;
    /////////////////  
    // invalido
    // this = Popover
    /////////////////  
    if ( (!conteudoString) && (!this[template]) ) 
      return; 
    ////////////////////////
    //constantes e variaveis
    ////////////////////////
    var popover = null, timer = 0, placementSetting = this[placement],
      //////////////////////////
      // handlers(Manipuladores)
      //////////////////////////
      dismissibleHandler = function(e) {
        if (popover !== null && e[target] === queryElement('.close',popover)) {
          self.hide();
        }
      },
      ///////////////////
      // metodos privados
      ///////////////////
      removeHint = function() {
        self[container].removeChild(popover);
        timer   = null; 
        popover = null; 
      },
      novoHint = function() {
        ////////////////////////////////
        // verifica o conteúdo novamente
        ////////////////////////////////
        tituloString    = element.getAttribute("data-title");
        conteudoString  = element.getAttribute("data-content");
        popover         = document.createElement("div");
        ////////////////////////////////////////////////
        // crie o popover a partir de atributos de dados
        ////////////////////////////////////////////////
        if( (conteudoString !== null) && (self[template] === null) ) { 
          popover.setAttribute('role','tooltip');
          if (tituloString !== null) {
            let hintTitulo = document.createElement("h3");  //DOC[createElement]('h3');
            hintTitulo.setAttribute("class",component+'-title');
            ///////////////////////////////////////////////////////////
            // Se for um alerta importante destaco o titulo em vermelho
            ///////////////////////////////////////////////////////////
            if( element.getAttribute("data-titleW") !== null )
              hintTitulo.setAttribute("style","color:red;");
            //
            hintTitulo.innerHTML = (self[dismissible] ? tituloString + closeBtn : tituloString);
            popover.appendChild(hintTitulo);
          };
          let hintSeta      = document.createElement("div");
          let hintConteudo  = document.createElement("div");
          hintSeta.setAttribute("class",'arrow'); 
          hintConteudo.setAttribute("class",component+'-content');
          popover.appendChild(hintSeta); 
          popover.appendChild(hintConteudo);
          hintConteudo.innerHTML = self[dismissible] && tituloString === null ? conteudoString + closeBtn : conteudoString;
        } else {  
          ///////////////////////////////////////
          // ou crie o popover a partir do modelo
          ///////////////////////////////////////
          var popoverModelo = document.createElement("div");
          popoverModelo.innerHTML = self[template];
          popover.innerHTML = popoverModelo.firstChild[innerHTML];
        }
        ////////////////////////////  
        // Appendar para o container
        ////////////////////////////
        self.container.appendChild(popover);
        popover.style.display = 'block';
        popover.setAttribute("class", component+ ' ' + placementSetting + ' ' + self[animation]);
      },
      showHint = function () {
        !hasClass(popover,inClass) && ( addClass(popover,inClass) );
      },
      updateHint = function() {
        styleTip(element,popover,placementSetting,self[container]);
      },
      
      // event toggle
      dismissHandlerToggle = function(type){
        if (clickEvent == self[trigger] || 'focus' == self[trigger]) {
          !self[dismissible] && type( element, 'blur', self.hide );
        }
        self[dismissible] && type( DOC, clickEvent, dismissibleHandler );
        !isIE8 && type( globalObject, resizeEvent, self.hide );
      },
  
      // triggers
      showTrigger = function() {
        dismissHandlerToggle(on);
        bootstrapCustomEvent.call(element, shownEvent, component);
      },
      hideTrigger = function() {
        dismissHandlerToggle(off);
        removeHint();
        bootstrapCustomEvent.call(element, hiddenEvent, component);
      };
  
    // public methods / handlers
    this.toggle = function() {
      if (popover === null){ 
        self.show(); 
      } else { 
        self.hide(); 
      }
    };
    this.show = function() {
      clearTimeout(timer);
      timer = setTimeout( function() {
        if (popover === null) {
          placementSetting = self[placement]; // we reset placement in all cases
          novoHint();
          updateHint();
          showHint();
          bootstrapCustomEvent.call(element, showEvent, component);
          !!self[animation] ? emulateTransitionEnd(popover, showTrigger) : showTrigger();
        }
      }, 20 );
    };
    this.hide = function() {
      clearTimeout(timer);
      timer = setTimeout( function() {
        if (popover && popover !== null && hasClass(popover,inClass)) {
          bootstrapCustomEvent.call(element, hideEvent, component);
          removeClass(popover,inClass);
          !!self[animation] ? emulateTransitionEnd(popover, hideTrigger) : hideTrigger();
        }
      }, self[delay] );
    };
  
    // init
    if ( !(stringPopover in element) ) { // prevent adding event handlers twice
      if (self[trigger] === hoverEvent) {
        on( element, mouseHover[0], self.show );
        if (!self[dismissible]) { on( element, mouseHover[1], self.hide ); }
      } else if (clickEvent == self[trigger] || 'focus' == self[trigger]) {
        on( element, self[trigger], self.toggle );
      }    
    }
    element[stringPopover] = self;
  };
  supports[push]( [ stringPopover, Popover, '['+dataToggle+'="popover"]' ] );
  
  //
  //
  ////////////////////////////////////////////////
  // Native Javascript for Bootstrap 3 | ScrollSpy
  ////////////////////////////////////////////////
  var ScrollSpy = function(element, options) {
  
    // initialization element, the element we spy on
    element = queryElement(element); 
  
    // DATA API
    var targetData = queryElement(element[getAttribute](dataTarget)),
        offsetData = element[getAttribute]('data-offset');
  
    // set options
    options = options || {};
    if ( !options[target] && !targetData ) { return; } // invalidate
  
    // event targets, constants
    var self = this, spyTarget = options[target] && queryElement(options[target]) || targetData,
        links = spyTarget && spyTarget[getElementsByTagName]('A'),
        offset = parseInt(offsetData || options['offset']) || 10,      
        items = [], targetItems = [], scrollOffset,
        scrollTarget = element[offsetHeight] < element[scrollHeight] ? element : globalObject, // determine which is the real scrollTarget
        isWindow = scrollTarget === globalObject;  
  
    // populate items and targets
    for (var i=0, il=links[length]; i<il; i++) {
      var href = links[i][getAttribute]('href'), 
          targetItem = href && href.charAt(0) === '#' && href.slice(-1) !== '#' && queryElement(href);
      if ( !!targetItem ) {
        items[push](links[i]);
        targetItems[push](targetItem);
      }
    }
  
    // private methods
    var updateItem = function(index) {
      var parent = items[index][parentNode], // item's parent LI element
          targetItem = targetItems[index], // the menu item targets this element
          dropdown = getClosest(parent,'.dropdown'),
          targetRect = isWindow && targetItem[getBoundingClientRect](),
  
          isActive = hasClass(parent,active) || false,
  
          topEdge = (isWindow ? targetRect[top] + scrollOffset : targetItem[offsetTop]) - offset,
          bottomEdge = isWindow ? targetRect[bottom] + scrollOffset - offset : targetItems[index+1] ? targetItems[index+1][offsetTop] - offset : element[scrollHeight],
  
          inside = scrollOffset >= topEdge && bottomEdge > scrollOffset;
  
        if ( !isActive && inside ) {
          if ( parent.tagName === 'LI' && !hasClass(parent,active) ) {
            addClass(parent,active);
            if (dropdown && !hasClass(dropdown,active) ) {
              addClass(dropdown,active);
            }
            bootstrapCustomEvent.call(element, 'activate', 'scrollspy', items[index]);
          }
        } else if ( !inside ) {
          if ( parent.tagName === 'LI' && hasClass(parent,active) ) {
            removeClass(parent,active);
            if (dropdown && hasClass(dropdown,active) && !getElementsByClassName(parent[parentNode],active).length ) {
              removeClass(dropdown,active);
            }
          }
        } else if ( !inside && !isActive || isActive && inside ) {
          return;
        }
      },
      updateItems = function(){
        scrollOffset = isWindow ? getScroll().y : element[scrollTop];
        for (var index=0, itl=items[length]; index<itl; index++) {
          updateItem(index)
        }
      };
  
    // public method
    this.refresh = function () {
      updateItems();
    }
  
    // init
    if ( !(stringScrollSpy in element) ) { // prevent adding event handlers twice
      on( scrollTarget, scrollEvent, self.refresh );
      !isIE8 && on( globalObject, resizeEvent, self.refresh ); 
    }
    self.refresh();
    element[stringScrollSpy] = self;
  };
  supports[push]( [ stringScrollSpy, ScrollSpy, '['+dataSpy+'="scroll"]' ] );
  //
  //
  //////////////////////////////////////////
  // Native Javascript for Bootstrap 3 | Tab
  //////////////////////////////////////////
  var Tab = function( element, options ) {
    ///////////////////////
    // Iniciando o elemento
    ///////////////////////
    element = queryElement(element);
  
    // DATA API
    var heightData = element[getAttribute](dataHeight),
      
        // strings
        component = 'tab', height = 'height', float = 'float', isAnimating = 'isAnimating';
  
    // set options
    options = options || {};
    this[height] = supportTransitions ? (options[height] || heightData === 'true') : false; // filter legacy browsers
  
    // bind, event targets
    var self = this, next,
      tabs = getClosest(element,'.nav'),
      tabsContentContainer = false,
      dropdown = tabs && queryElement('.dropdown',tabs),
      activeTab, activeContent, nextContent, containerHeight, equalContents, nextHeight,
  
      // trigger
      triggerEnd = function(){
        tabsContentContainer[style][height] = '';
        removeClass(tabsContentContainer,collapsing);
        tabs[isAnimating] = false;
      },
      triggerShow = function() {
        if (tabsContentContainer) { // height animation
          if ( equalContents ) {
            triggerEnd();
          } else {
            setTimeout(function(){ // enables height animation
              tabsContentContainer[style][height] = nextHeight + 'px'; // height animation
              tabsContentContainer[offsetWidth];
              emulateTransitionEnd(tabsContentContainer, triggerEnd);
            },50);
          }
        } else {
          tabs[isAnimating] = false; 
        }
        bootstrapCustomEvent.call(next, shownEvent, component, activeTab);
      },
      triggerHide = function() {
        if (tabsContentContainer) {
          activeContent[style][float] = left;
          nextContent[style][float] = left;        
          containerHeight = activeContent[scrollHeight];
        }
        
        addClass(nextContent,active);
        bootstrapCustomEvent.call(next, showEvent, component, activeTab);
        
        removeClass(activeContent,active);
        bootstrapCustomEvent.call(activeTab, hiddenEvent, component, next);
        
        if (tabsContentContainer) {
          nextHeight = nextContent[scrollHeight];
          equalContents = nextHeight === containerHeight;
          addClass(tabsContentContainer,collapsing);
          tabsContentContainer[style][height] = containerHeight + 'px'; // height animation
          tabsContentContainer[offsetHeight];
          activeContent[style][float] = '';
          nextContent[style][float] = '';
        }
  
        if ( hasClass(nextContent, 'fade') ) {
          setTimeout(function(){ // makes sure to go forward
            addClass(nextContent,inClass);
            emulateTransitionEnd(nextContent,triggerShow);
          },20);
        } else { triggerShow(); }        
      };
  
    if (!tabs) return; // invalidate 
  
    // set default animation state
    tabs[isAnimating] = false;
      
    // private methods
    var getActiveTab = function() {
        var activeTabs = getElementsByClassName(tabs,active), activeTab;
        if ( activeTabs[length] === 1 && !hasClass(activeTabs[0],'dropdown') ) {
          activeTab = activeTabs[0];
        } else if ( activeTabs[length] > 1 ) {
          activeTab = activeTabs[activeTabs[length]-1];
        }
        return activeTab[getElementsByTagName]('A')[0];
      },
      getActiveContent = function() {
        return queryElement(getActiveTab()[getAttribute]('href'));
      },
      // handler
      clickHandler = function(e) {
        var href = e[target][getAttribute]('href');
        e[preventDefault]();
        next = e[target][getAttribute](dataToggle) === component || (href && href.charAt(0) === '#')
             ? e[target] : e[target][parentNode]; // allow for child elements like icons to use the handler
        !tabs[isAnimating] && !hasClass(next[parentNode],active) && self.show();
      };
  
    // public method
    this.show = function() { // the tab we clicked is now the next tab
      next = next || element;
      nextContent = queryElement(next[getAttribute]('href')); //this is the actual object, the next tab content to activate
      activeTab = getActiveTab(); 
      activeContent = getActiveContent();
  
      tabs[isAnimating] = true;
      removeClass(activeTab[parentNode],active);
      addClass(next[parentNode],active);
  
      if ( dropdown ) {
        if ( !hasClass(element[parentNode][parentNode],'dropdown-menu') ) {
          if (hasClass(dropdown,active)) removeClass(dropdown,active);
        } else {
          if (!hasClass(dropdown,active)) addClass(dropdown,active);
        }
      }
      
      bootstrapCustomEvent.call(activeTab, hideEvent, component, next);
      
      if (hasClass(activeContent, 'fade')) {
        removeClass(activeContent,inClass);
        emulateTransitionEnd(activeContent, triggerHide);
      } else { triggerHide(); }
    };
  
    // init
    if ( !(stringTab in element) ) { // prevent adding event handlers twice
      on(element, clickEvent, clickHandler);
    }
    if (self[height]) { tabsContentContainer = getActiveContent()[parentNode]; }
    element[stringTab] = self;
  };
  supports[push]( [ stringTab, Tab, '['+dataToggle+'="tab"]' ] );
  //
  //
  //////////////////////////////////////////////
  // Native Javascript for Bootstrap 3 | Tooltip
  //////////////////////////////////////////////
  var Tooltip = function( element,options ) {
    ///////////////////////
    // Iniciando o elemento
    ///////////////////////
    element = queryElement(element);
  
    // set options
    options = options || {};
  
    // DATA API
    var animationData = element[getAttribute](dataAnimation),
        placementData = element[getAttribute](dataPlacement),
        delayData = element[getAttribute](dataDelay),
        containerData = element[getAttribute](dataContainer),
        
        // strings
        component = 'tooltip',
        //classString = 'class',
        title = 'title',
        fade = 'fade',
        div = 'div',
  
        // check container
        containerElement = queryElement(options[container]),
        containerDataElement = queryElement(containerData),        
  
        // maybe the element is inside a modal
        modal = getClosest(element,'.modal'),
        
        // maybe the element is inside a fixed navbar
        navbarFixedTop = getClosest(element,'.'+fixedTop),
        navbarFixedBottom = getClosest(element,'.'+fixedBottom);
  
    // set instance options
    this[animation] = options[animation] && options[animation] !== fade ? options[animation] : animationData || fade;
    this[placement] = options[placement] ? options[placement] : placementData || top;
    this[delay] = parseInt(options[delay] || delayData) || 200;
    this[container] = containerElement ? containerElement 
                    : containerDataElement ? containerDataElement 
                    : navbarFixedTop ? navbarFixedTop
                    : navbarFixedBottom ? navbarFixedBottom
                    : modal ? modal : DOC[body];
  
    // bind, event targets, title and constants
    var self = this, timer = 0, placementSetting = this[placement], tooltip = null,
      tituloString = element[getAttribute](title) || element[getAttribute](dataTitle) || element[getAttribute](dataOriginalTitle);
  
    if ( !tituloString || tituloString == "" ) return; // invalidate
  
    // private methods
    var removeToolTip = function() {
        self[container].removeChild(tooltip);
        tooltip = null; timer = null;
      },
      createToolTip = function() {
        tituloString = element[getAttribute](title) || element[getAttribute](dataTitle) || element[getAttribute](dataOriginalTitle); // read the title again
        if ( !tituloString || tituloString == "" ) return false; // invalidate
        
        tooltip = document.createElement("div");
        tooltip[setAttribute]('role',component);
  
        var tooltipArrow = document.createElement("div"), tooltipInner = document.createElement("div");
        tooltipArrow[setAttribute]("class", component+'-arrow'); tooltipInner[setAttribute]("class",component+'-inner');
  
        tooltip[appendChild](tooltipArrow); tooltip[appendChild](tooltipInner);
  
        tooltipInner[innerHTML] = tituloString;
  
        self[container][appendChild](tooltip);
        tooltip[setAttribute]("class", component + ' ' + placementSetting + ' ' + self[animation]);
      },
      updateTooltip = function () {
        styleTip(element,tooltip,placementSetting,self[container]);
      },
      showTooltip = function () {
        !hasClass(tooltip,inClass) && ( addClass(tooltip,inClass) );
      },
      // triggers
      showTrigger = function() {
        bootstrapCustomEvent.call(element, shownEvent, component);
        !isIE8 && on( globalObject, resizeEvent, self.hide );      
      },
      hideTrigger = function() {
        !isIE8 && off( globalObject, resizeEvent, self.hide );      
        removeToolTip();
        bootstrapCustomEvent.call(element, hiddenEvent, component);
      };
  
    // public methods
    this.show = function() {
      clearTimeout(timer);
      timer = setTimeout( function() {
        if (tooltip === null) {
          placementSetting = self[placement]; // we reset placement in all cases
          if(createToolTip() == false) return;
          updateTooltip();
          showTooltip();
          bootstrapCustomEvent.call(element, showEvent, component);
          !!self[animation] ? emulateTransitionEnd(tooltip, showTrigger) : showTrigger();
        }
      }, 20 );
    };
    this.hide = function() {
      clearTimeout(timer);
      timer = setTimeout( function() {
        if (tooltip && hasClass(tooltip,inClass)) {
          bootstrapCustomEvent.call(element, hideEvent, component);
          removeClass(tooltip,inClass);
          !!self[animation] ? emulateTransitionEnd(tooltip, hideTrigger) : hideTrigger();
        }
      }, self[delay]);
    };
    this.toggle = function() {
      if (!tooltip) { self.show(); } 
      else { self.hide(); }
    };
  
    // init
    if ( !(stringTooltip in element) ) { // prevent adding event handlers twice
      element[setAttribute](dataOriginalTitle,tituloString);
      element.removeAttribute(title);
      on(element, mouseHover[0], self.show);
      on(element, mouseHover[1], self.hide);
    }
    element[stringTooltip] = self;
  };
  supports[push]( [ stringTooltip, Tooltip, '['+dataToggle+'="tooltip"]' ] );
  
  //
  //
  //////////////////////////////////////////////////////////
  // Native Javascript for Bootstrap 3 | Initialize Data API
  //////////////////////////////////////////////////////////
  var initializeDataAPI = function( constructor, collection ){
      for (var i=0, l=collection[length]; i<l; i++) {
        new constructor(collection[i]);
      }
    },
    initCallback = BSN.initCallback = function(lookUp){
      lookUp = lookUp || DOC;
      for (var i=0, l=supports[length]; i<l; i++) {
        initializeDataAPI( supports[i][1], lookUp[querySelectorAll] (supports[i][2]) );
      }
    };
  
  // bulk inicializar todos os componentes
  DOC[body] ? initCallback() : on( DOC, 'DOMContentLoaded', function(){ initCallback(); } );
  
  return {
    Affix: Affix,
    Alert: Alert,
    Button: Button,
    Carousel: Carousel,
    Collapse: Collapse,
    Dropdown: Dropdown,
    Modal: Modal,
    Popover: Popover,
    ScrollSpy: ScrollSpy,
    Tab: Tab,
    Tooltip: Tooltip
  };
}));

/*
MODELOS FUNCIONAIS
-----------------
-Dropdown simples
-----------------
<div class="btn-group" style="margin-bottom:10px">
  <div class="dropdown">
    <button class="btn btn-primary" id="myDropdown" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dropdown trigger <span class="caret"></span></button>
    <ul class="dropdown-menu" aria-labelledby="myDropdown">
      <li role="presentation"><a role="menuitem" href="#">Action1</a></li>
      <li role="presentation"><a role="menuitem" href="#">Another action2</a></li>
      <li role="presentation"><a role="menuitem" href="#">Something else here3</a></li>
    </ul>
  </div>
</div>
-------------------------------
-Dropdown com formulario/inputs
-------------------------------
<div class="btn-group" style="margin-bottom:10px">
  <button id="makeMeDropdown" class="btn btn-default disabled" disabled="true">Selecione</button>
  <div class="dropdown btn-group">
    <button id="formDropdown" type="button" 
                              class="btn btn-primary dropdown-toggle" 
                              data-toggle="dropdown" 
                              aria-haspopup="true" 
                              role="button" 
                              aria-expanded="false" 
                              tabindex="0">Login <span class="caret"></span>
    </button>
    <form class="form-vertical dropdown-menu">
      <div class="form-group">
        <label for="inputEmail3" class="control-label">EmailX</label>
        <div class="">
          <input type="email" class="form-control" id="inputEmail3" placeholder="Email">
        </div>
      </div>
      <div class="form-group">
        <label for="inputPassword3" class="control-label">Password</label>
        <div class="">
          <input type="password" class="form-control" id="inputPassword3"placeholder="Password">
        </div>
      </div>
      <div class="form-group">
        <div class="">
          <button type="submit" class="btn btn-default">Confirmar</button>
        </div>
      </div>
    </form>
  </div>
</div>          
-------------------------------
-Collapse
-------------------------------
<section id="collapseExamples" style="height:90px;margin-left:58px;width:90%;position:relative;float:left;">
  <div id="collapseExampleWrapper" class="panel panel-default">
    <p>
      <span class="btn-group">
        <!--<a class="btn btn-primary" data-toggle="collapse" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">HREF</a>-->
        <button id="cbStatus" class="btn btn-default disabled" disabled="true">Buscar</button>
        <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="true" aria-controls="collapseExample">Placas</button>
      </span>
    </p>
    <div class="collapse" id="collapseExample" aria-expanded="false" role="presentation">
      <div class="well">
        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident.
      </div>
    </div>
  </div>

  <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingOne">
      <h4 class="panel-title" id="-collapsible-group-item-#1-">
      <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
        Collapsible Group Item #1
      </a>
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body">
      Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
      </div>
    </div>
    </div>
    <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingTwo">
      <h4 class="panel-title" id="-collapsible-group-item-#2-">
      <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
        Collapsible Group Item #2
      </a>
    </h4>
    </div>
    <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
      <div class="panel-body">
      Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
      </div>
    </div>
    </div>
    <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingThree">
      <h4 class="panel-title" id="-collapsible-group-item-#3-">
      <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
        Collapsible Group Item #3
      </a>
      </h4>
    </div>
    <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
      <div class="panel-body">
      Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
      </div>
    </div>
    </div>
  </div>
</section>
*/