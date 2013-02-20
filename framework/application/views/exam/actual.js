jQuery(function(){
    
    if(! EXAM.step) { /* Store the current step. */
            
        var step = parseInt(jQuery.url().segment(6), 10);
        if(isNaN(step)) step = 1;
        EXAM.step = step;
    }
    
    if(EXAM.update_flag) {
        //window.setInterval(function() { EXAM.session_updater(); }, 10000);
    }
    
    window.setInterval(function() { EXAM.puller__queue(); }, 2000); /* Updates queue. */
    window.setInterval(function() { EXAM.puller__item_state(); }, 3000); /* Sees when paused. */
    window.setInterval(function() { EXAM.puller__station_state(); }, 5000); /* Sees when kicked-out. */
    
});