// Small delegated touch handler to differentiate swipe vs tap
// Allows scrolling when user swipes on card elements, only triggers click on a short tap
(function(){
    const SELECTOR = '.d-steam-card-wrapper';
    const THRESHOLD = 10; // pixels
    const MAX_TAP_DURATION = 500; // ms
    const state = new WeakMap();

    function onStart(e){
        const t = e.touches && e.touches[0];
        if(!t) return;
        const el = e.target && e.target.closest && e.target.closest(SELECTOR);
        if(!el) return;
        state.set(el, {
            x: t.clientX,
            y: t.clientY,
            time: Date.now(),
            moved: false
        });
    }

    function onMove(e){
        const t = e.touches && e.touches[0];
        if(!t) return;
        const el = e.target && e.target.closest && e.target.closest(SELECTOR);
        if(!el) return;
        const s = state.get(el);
        if(!s) return;
        const dx = Math.abs(t.clientX - s.x);
        const dy = Math.abs(t.clientY - s.y);
        if(dx > THRESHOLD || dy > THRESHOLD) s.moved = true;
    }

    function onEnd(e){
        // Note: changedTouches may be empty on touchend in some browsers
        const target = e.target;
        const el = target && target.closest && target.closest(SELECTOR);
        if(!el) return;
        const s = state.get(el);
        if(!s) return;
        const duration = Date.now() - s.time;
        state.delete(el);
        if(!s.moved && duration <= MAX_TAP_DURATION){
            // treat as tap — trigger the anchor's click handler
            try{
                if(typeof el.click === 'function') el.click();
                else if(el.href) window.location.href = el.href;
            }catch(err){
                // ignore
            }
        }
    }

    // Attach delegated listeners on document. Use passive:true for scroll performance.
    try{
        document.addEventListener('touchstart', onStart, {passive:true});
        document.addEventListener('touchmove', onMove, {passive:true});
        document.addEventListener('touchend', onEnd, {passive:true});
    }catch(e){
        // fallback for older browsers
        document.addEventListener('touchstart', onStart);
        document.addEventListener('touchmove', onMove);
        document.addEventListener('touchend', onEnd);
    }
})();
