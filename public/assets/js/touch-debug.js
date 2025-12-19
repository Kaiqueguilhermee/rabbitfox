// Diagnostic helper: logs callers of preventDefault() for touch events.
// Load only when needed (e.g., append ?dbg_touch=1 to URL).
(function(){
    const orig = Event.prototype.preventDefault;
    Event.prototype.preventDefault = function(){
        try{
            const e = this;
            if (e && e.type && ['touchstart','touchmove','touchend','touchcancel'].includes(e.type)){
                try{
                    console.groupCollapsed('[touch-debug] preventDefault on', e.type, 'target:', e.target);
                    console.log('event:', e);
                    console.log('stack:', (new Error()).stack);
                    console.trace();
                    console.groupEnd();
                }catch(_){
                    console.warn('[touch-debug] preventDefault called (stack unavailable) for', e.type, e.target);
                }
            }
        }catch(err){
            // ignore
        }
        return orig.apply(this, arguments);
    };

    // Also log touchstart/move/end with passive capture so we can see defaultPrevented state
    function logEvent(e){
        if(['touchstart','touchmove','touchend','touchcancel'].includes(e.type)){
            console.log('[touch-debug] ', e.type, 'target:', e.target, 'defaultPrevented:', e.defaultPrevented);
        }
    }
    document.addEventListener('touchstart', logEvent, {capture:true, passive:true});
    document.addEventListener('touchmove', logEvent, {capture:true, passive:true});
    document.addEventListener('touchend', logEvent, {capture:true, passive:true});
})();
