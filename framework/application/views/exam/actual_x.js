jQuery.extend(EXAM, {
    
    rta_id : 0,
    screen_code : 0,
    screen_code_next : 0,
    screen_total : 0,
    one_ss_only : true,    
    step : 0,
    flag : [],
    answers : [],
    update_flag : true,
    attributes : {},
    axl : {},
    
    go : function(step) {
        
        GBL.loader(false);
        GBL.loader();
        
        /* EXAM.step = Current step
         * EXAM.step + 1 = Next step
         **/
        
        if(! step) {step = (EXAM.step + 1);}
        /* else -> There's an specific step to go to, and not neccessarily the next step. */
        
        window.location.href = DOCROOT +'exam/actual/'+ this.rta_id +'/'+ this.screen_code +'/'+ step;
    },
    
    close : function(time) { this.go('finish/?t='+ time); },
    exec : function() {
        
        var ok = true;
        jQuery.each(this.axl, function(key, value){

            if(! EXAM.axl[key]()) {

                ok = false;
                
                /* Breaking out from jQuery.each()'s loop. */
                return false;
            }
        });

        return ok;
    },
    
    submit : function(close) {
        
        if(! close) close = false;
        var ok = this.exec();
        
        if(ok) { 
            
            GBL.loader();
            
            jQuery.post(
                DOCROOT +'exam/async_update_answers',
                {
                    close : close,
                    rta_id : this.rta_id,
                    screen_code : this.screen_code,
                    screen_no : this.step, /* Curent step is actually the screen number. */
                    answers : JSON.stringify(EXAM.answers),
                    t : (new Date).getTime()
                },
                function(r) {
                    
                    if(close) EXAM.session_updater("EXAM.close('"+ r +"')"); /* Update before leaving. */
                    else EXAM.go();
                }
            );
        }        
    },
    
    session_updater : function(callback) {
        
        jQuery.post(
            DOCROOT +'exam/async_update_session',
            {
                rta_id : EXAM.rta_id,
                screen_code : EXAM.screen_code,
                t : (new Date).getTime()
            },
            function() { if(callback) eval(callback); }
        );
    },
    
    session_updater_peritem : function(item) {
        
        jQuery.ajax({
            type: 'POST',
            url: DOCROOT +'exam/async_update_session_peritem',
            data: { 
                rta_id : EXAM.rta_id,
                screen_code : EXAM.screen_code,
                screen_count : EXAM.step,
                item : item,
                answers : JSON.stringify(EXAM.answers),
                t : (new Date).getTime()
            },
            error : function(xhr) { 
                
                /* If not reachable. */
                if(xhr.status == 0) {
                    
                    EXAM.session_updater_peritem(item);
                } /* Recall. */
            }
        });        
    },
    
    puller__station_state : function() {
        
        jQuery.ajax({
            type: 'POST',
            url: DOCROOT +'exam/async_puller__check_station_state',
            data: { 
                t : (new Date).getTime()
            },
            success: function(r) {
                
                if(EXAM.pause) return; /* Don't execute yet on pause. */
                
                r = parseInt(r, 10);
                
                if(r == 2) { 
                    
                    if(! POPUPJS.obj) {
                        
                        POPUPJS.obj = new Popup.Window('popupjs_wrapper');
                    //POPUPJS.obj.hide();
                        POPUPJS.obj.show();

                        jQuery('#popupjs_wrapper').css('width', '490px');
                        jQuery('#popupjs_wrapper .popup_content p').html('<span style="color: #CC0000">You are kicked out from this examination.</span><br /><br /><b>If you have not finished with this yet, then you are advised to inform your Administrator immediately.</b>');
                        jQuery('#popupjs_wrapper .popup_title').html('System Message');
                        jQuery('#popupjs_btn_cancel').hide();
                        jQuery('#popupjs_btn_ok').css('visibility', 'hidden');
                    }
                } else {
                    
                    if(r == 3) { GBL.go('home'); } /* Has logged-out from a diffrent window of same browser. */
                    else
                    if(POPUPJS.obj) {
                        
                        jQuery('#popupjs_wrapper .popup_content p').html('You may now proceed. Click the "Okay" button.');
                        jQuery('#popupjs_btn_ok').css('visibility', 'visible');
                        jQuery('#popupjs_btn_ok').attr('onclick', 'POPUPJS.obj.hide()');
                    }
                }
            },
            error : function(xhr) { 
                
                /* If not reachable. */
                if(xhr.status == 0) {
                    
                } /* Recall. */
            }
        });
    },
    
    redirect_on_pause : true,
    pause : false,
    
    puller__item_state : function() {
        
        jQuery.ajax({
            type: 'POST',
            url: DOCROOT +'exam/async_puller__check_item_state',
            data: { 
                rta_id : EXAM.rta_id,
                screen_code : EXAM.screen_code,                
                t : (new Date).getTime()
            },
            success : function(r) {
                
                r = parseInt(r, 10);
                if(r == 1) { 
                    
                    var url = 'exam/actual/'+ EXAM.rta_id +'/'+ EXAM.screen_code;
                    if(EXAM.redirect_on_pause) GBL.go(url);
                    else if(POPUPJS.obj && EXAM.pause) {
                        
                        EXAM.pause = false;
                        POPUPJS.obj.hide();
                    }
                    
                } else {
                    
                    if(r == 0) {
                        
                        EXAM.pause = true;
                        
                        if(! POPUPJS.obj && jQuery('#popupjs_wrapper') && jQuery('#popupjs_wrapper').length) {
                            
                            POPUPJS.obj = new Popup.Window('popupjs_wrapper');
                        //POPUPJS.obj.hide();
                            POPUPJS.obj.show();

                            jQuery('#popupjs_wrapper').css('width', '490px');
                            jQuery('#popupjs_wrapper .popup_content p').html('This exam was paused. Further instructions will be told to you shortly.</span>');
                            jQuery('#popupjs_wrapper .popup_title').html('System Message');
                            jQuery('#popupjs_btn_cancel').hide();
                            jQuery('#popupjs_btn_ok').css('visibility', 'hidden');
                        }
                    }
                }
            }
        });        
    }
});
            
jQuery(function(){
    
    if(! EXAM.step) { /* Store the current step. */
            
        var step = parseInt(jQuery.url().segment(6), 10);
        if(isNaN(step)) step = 1;
        EXAM.step = step;
    }
    
    if(EXAM.update_flag) {
        //window.setInterval(function() { EXAM.session_updater(); }, 10000);
    }
    
    window.setInterval(function() { EXAM.puller__item_state(); }, 3000); /* Sees when paused. */
    window.setInterval(function() { EXAM.puller__station_state(); }, 5000); /* Sees when kicked-out. */
});