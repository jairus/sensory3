var EXAM = new function() {
    
    this.queue = null;
    this.rta_id = 0;
    this.screen_code = 0;
    this.screen_code_next = 0;
    this.screen_total = 0;
    this.one_ss_only = true;
    this.step = 0;
    this.flag = [];
    this.answers = [];
    this.update_flag = true;
    this.attributes = {};
    this.axl = {};
//    this.ctr = 0;
    
    this.go = function(step) {
        
        GBL.loader(false);
        GBL.loader();
        
        var batch = jQuery.url().param('batch');
        var date = jQuery.url().param('date');
        
        if(step) {
            
            this.step = step;
            GBL.go('exam/actual/'+ this.rta_id +'/'+ this.screen_code +'/'+ step +'/?batch='+ batch +'&date='+ date);
            
        } else {
            
            if(this.step < this.screen_total) {
                
                step = this.step + 1; /* By default it's going forward. */
                GBL.go('exam/actual/'+ this.rta_id +'/'+ this.screen_code +'/'+ step +'/?batch='+ batch +'&date='+ date);
                
            } else {
                
                var next = null;
                
                if(this.queue.code[this.rta_id].length > 1) {
                    
                    var next_code_pos = jQuery.inArray(this.screen_code.toString(), EXAM.queue.code[this.rta_id]) + 1;
                    if(next_code_pos < this.queue.code[this.rta_id].length) {
                        
                        /* Redirect to the next code. */
                        GBL.go('exam/actual/'+ this.rta_id +'/'+ this.queue.code[this.rta_id][next_code_pos] +'/?batch='+ batch +'&date='+ date);
                        
                    } else {
                        
                        next = this.find_next_in_queue();
                        if(next) GBL.go('exam/actual/'+ next.rta +'/'+ next.code +'/?batch='+ next.batch.replace(' ', '-') +'&date='+ date);
                        else this.get_unix_timestamp(); /* Redirect to finish. */

                    }

                } else {
                    
                    next = this.find_next_in_queue();
                    if(next) GBL.go('exam/actual/'+ next.rta +'/'+ next.code +'/?batch='+ next.batch.replace(' ', '-') +'&date='+ date);
                    else this.get_unix_timestamp(); /* Redirect to finish. */
                    
                }
            }
        }
    }
    
    this.find_next_in_queue = function() {
        
        var pos = 0, next = null, checker = 0;
        
        /* START: Try to find what's next in queue then redirect into it. */
        jQuery.each(EXAM.queue.code, function(key){ pos++; if(key == EXAM.rta_id) return false; /* Break from this loop. */ });

        if(pos < Object.size(EXAM.queue.code)) {
            
            var checker_rta = null;
            jQuery.each(EXAM.queue.code, function(key){ /* Get the code next to the current code. */
                
                checker++;
                if((pos + 1) == checker) { 
                    
                    checker_rta = key;
                    return false; /* Break from this loop. */
                }
            });

            jQuery.each(EXAM.queue.data, function(key, value){ 

                if(checker_rta == value.rta) {

                    next = EXAM.queue.data[key];
                    return false; /* Break from this loop. */
                }
            });
        }
        /* END: Try to find what's next in queue so redirect into it. */
        //alert(next.rta +' '+ next.batch +' '+ next.code);
        
        return next;
    }
    
    this.close = function(time) { 
        
        GBL.go('exam/actual/'+ this.rta_id +'/'+ this.screen_code +'/finish/?t='+ time);
        //this.go('finish/?t='+ time);
    }
    
    this.get_unix_timestamp = function() {
        
        jQuery.ajax({
            type: 'POST',
            url: DOCROOT +'exam/async_unix_timestamp',
            data: { t : (new Date).getTime() },
            success : function(r) {
                EXAM.close(r);
            },
            error : function(xhr) { 
                
                /* If not reachable. */
                if(xhr.status == 0) {
                    
                    EXAM.get_unix_timestamp();
                } /* Recall. */
            }
        });        
    }
    
    this.exec = function() {
        
        var ok = true;
        
        jQuery.each(this.axl, function(key, value){

            if(! EXAM.axl[key]()) {

                ok = false;
                
                /* Breaking out from jQuery.each()'s loop. */
                return false;
            }
        });

        return ok;
    }
    
    this.submit = function(close) {
        
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
                    
                    //if(close) EXAM.session_updater("EXAM.close('"+ r +"')"); /* Update before leaving. */
                    //else EXAM.go();
                    
                    EXAM.go();
                }
            );
        }        
    }
    
    this.session_updater = function(callback) {
        
        jQuery.post(
            DOCROOT +'exam/async_update_session',
            {
                rta_id : EXAM.rta_id,
                screen_code : EXAM.screen_code,
                t : (new Date).getTime()
            },
            function() {if(callback) eval(callback);}
        );
    }
    
    this.session_updater_peritem = function(item) {
        
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
    }
    
    this.puller__station_state = function() {
        
        jQuery.ajax({
            type: 'POST',
            url: DOCROOT +'exam/async_puller__check_station_state',
            data: { 
                rta_id : EXAM.rta_id,
                screen_code : EXAM.screen_code,
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
                    
                    if(r == 3) {GBL.go('home');} /* Has logged-out from a diffrent window of same browser. */
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
    }
    
    this.puller__queue = function() {
        
        jQuery.ajax({
            type: 'POST',
            url: DOCROOT +'exam/async_puller__queue',
            dataType : 'json',
            data: {t : (new Date).getTime()},
            success : function(r) {EXAM.queue = r;}
        });
    }
    
    this.redirect_on_pause = true;
    this.pause = false;
    this.puller__item_state = function() {
        
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
                        POPUPJS.obj = null; /* Unset for the next pull. */
                    }
                    
                } else {
                    
                    if(r == 0) {
                        
                        EXAM.pause = true;
                        
                        if((! POPUPJS.obj) && jQuery('#popupjs_wrapper') && jQuery('#popupjs_wrapper').length) {
                            
                            POPUPJS.obj = new Popup.Window('popupjs_wrapper');
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
    
    this.comment_field_onkey = function(obj, ctr) {
        
        obj = jQuery(obj);
        
        EXAM.answers[ctr - 1]['axl'] = obj.val();
        EXAM.session_updater_peritem(ctr);
    }
    
    this.ljc__extract_id = function(id) {
        
        var tmp = id.split('___');
        
        var type_ctr = tmp[0]; tmp = tmp[1].split('__');
        var attr = tmp[0];
        var index = parseInt(tmp[1], 10);
        
        return [type_ctr, attr, index];
    }
};