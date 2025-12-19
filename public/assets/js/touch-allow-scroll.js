// Allow native vertical scrolling when touch starts inside game cards.
// It monkey-patches Event.prototype.preventDefault to noop for touch events
// whose target is inside the card wrapper selector. This is a narrow, last-resort
// mitigation to avoid vendor code (e.g. Livewire/Alpine) from blocking scroll.
(function(){
    var CARD_SELECTOR = '.d-steam-card-wrapper';
    var allowedTypes = {
        'touchstart': true,
        'touchmove': true,
        'touchend': true,
        'pointerdown': true,
        'pointermove': true,
        'pointerup': true
    };

    var originalPrevent = Event.prototype.preventDefault;

    Event.prototype.preventDefault = function(){
        try{
            var ev = this;
            if(ev && ev.type && allowedTypes[ev.type]){
                var node = ev.target;
                while(node && node !== document){
                    if(node.matches && node.matches(CARD_SELECTOR)){
                        if(window.console && console.debug){
                            console.debug('[touch-allow-scroll] ignored preventDefault on', ev.type, 'inside', CARD_SELECTOR);
                        }
                        return; // ignore preventDefault for touches inside cards
                    }
                    node = node.parentNode;
                }
            }
        }catch(e){
            // swallow errors and fall back to original behavior
        }
        return originalPrevent.apply(this, arguments);
    };
})();
