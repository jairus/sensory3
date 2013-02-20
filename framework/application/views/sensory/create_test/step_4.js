var SEAT = new function() {
    
    this.cell = {};
    
    this.code_distributor_orig_state = function() {
        
        jQuery('#code_distributor_wrapper').sortable({
            connectWith: ".connectedSortable",
            receive: function(e, ui) {

                ui.item.css('padding', '2px').css('font-size', '12px').css('padding', '2px 15px 2px 15px');
            }
        }).disableSelection();  
    }
    
    this.code_distributor_clear_selection = function() {
        
        /* Clear highlighted selection. */
        jQuery('#code_distributor_wrapper li').removeAttr('class').addClass('ui-state-highlight');
        jQuery('#code_distributor_wrapper li span').removeAttr('class');
        
    }
    
    this.clear = function(sensorium) {
        
        /* Transfer the codes to the distributor. */
        jQuery('#code_receiver_wrapper_'+ sensorium +' li li').each(function(){
            
            if(jQuery(this).length) {
                
                jQuery('#code_distributor_wrapper').append(jQuery(this).css('font-size', '12px').css('padding', '2px 15px 2px 15px'));
                
                jQuery('span', jQuery(this)).removeAttr('class');
            }
        });
    }
    
    this.fill_all = function() {
        
        var codes = [], x = 0, y = 0, rcvr = null;
        jQuery('#code_distributor_wrapper li').each(function(){ codes.push(jQuery(this)); });
        
        for(x=1; x<=16; x++) {
            
            rcvr = jQuery('#code_receiver_1_'+ x);
            
            if(rcvr.length && jQuery('li', rcvr).length < 1) {
                
                if(codes[y]) { rcvr.append(codes[y].css('font-size', '10px').css('padding', '2px').removeClass('ui-selected')); }
                y++;
            }
        }
        
        for(x=1; x<=16; x++) {
            
            rcvr = jQuery('#code_receiver_2_'+ x);
            
            if(rcvr.length && jQuery('li', rcvr).length < 1) {
                
                if(codes[y]) {rcvr.append(codes[y].css('font-size', '10px').css('padding', '2px').removeClass('ui-selected'));}
                y++;
            }
        }
    }
}

var STEP_4 = new function() {
    
    this.code_controls = [];
    //this.s1d = [];
    //this.s2d = [];
    
    this.submit = function() {
        
        var batch = jQuery('#batch').val();
        
        var s1d = this.code_distribution_get(1);
        var s2d = this.code_distribution_get(2);
        
        GBL.loader();
        
        jQuery.post(
            DOCROOT +'sensory/async_ct_step_4',
            {
                rta_id : Q.rta_id,
                q_id : Q.id,
                batch : batch,
                s1d : s1d.toString(),
                s2d : s2d.toString(),
                t : (new Date).getTime()
            },
            function() {
                
                Popup.dialog({
                    title : 'SUCCESS',
                    message : 'Codes are distributed and saved.',
                    buttons: ['Okay', 'Cancel'],
                    width: '420px'
                });
                
                GBL.loader(false);
            }
        );
    }
    
    /* Loads code distribution per station numbers. */
    this.code_distribution_load = function() {
        
        var batch = jQuery('#batch').val();
        
        GBL.loader();
        
        /* START: Clear stations & bring back the codes to the distributor. */
        jQuery("li[id^='code_receiver_1_'] li").each(function(){
            
            jQuery('#code_distributor_wrapper').append(jQuery(this).css('font-size', '12px'));//.css('padding', '2px 15px 2px 15px'));
        });
        jQuery("li[id^='code_receiver_2_'] li").each(function(){
            
            jQuery('#code_distributor_wrapper').append(jQuery(this).css('font-size', '12px'));//.css('padding', '2px 15px 2px 15px'));
        });
        /* END: Clear stations & bring back the codes to the distributor. */
        
        jQuery.post(
            DOCROOT +'sensory/async_code_distribution_load',
            {
                rta_id : Q.rta_id,
                q_id : Q.id,
                batch : batch,
                controls : this.code_controls.toString(),
                code_combinations : Q.code_combination.toString(),
                t : (new Date).getTime()
            },
            function(r) {
                
                STEP_4.code_distribute_all(r);
                GBL.loader(false);
                
            }, 'json'
        );
    }
    
    this.code_distribution_get = function(sensorium) {
        
        var d = [];
        
        jQuery("li[id^='code_receiver_"+ sensorium +"_'] li").each(function(){
            
            var tmp = jQuery(this).parents('li').attr('id').split('_');
            tmp = tmp[tmp.length - 1];
            
            if(jQuery(this).html() != '') { d.push(tmp +':'+ jQuery(this).attr('id')); }
        });
        
        return d;
    }
    
    this.code_distribute = function(s1d, s2d) {
        
        if(! s1d && ! s2d)
            return;
        
        s1d = s1d.split(',');
        s2d = s2d.split(',');
        
        var x = 0, tmp = null;
        
        for(x=0; x<s1d.length; x++) {
            
            tmp = s1d[x].split(':');
            jQuery('li#code_receiver_1_'+ tmp[0].trim()).append(jQuery('li#'+ tmp[1]).css('font-size', '10px').removeClass('ui-selected'));
        }
        
        for(x=0; x<s2d.length; x++) {
            
            tmp = s2d[x].split(':');
            jQuery('li#code_receiver_2_'+ tmp[0].trim()).append(jQuery('li#'+ tmp[1]).css('font-size', '10px').removeClass('ui-selected'));
        }        
    }
    
    this.code_distribute_all = function(d) {
        
        var batch = parseInt(jQuery('#batch').val(), 10) - 1;
        var x=0;
        
        for(x=0; x<d.length; x++) {
            
            if(x == batch) {
                
                this.code_distribute(d[x].s1d, d[x].s2d);
                
            } else {
                
                var s1d = d[x].s1d.split(',');
                var s2d = d[x].s2d.split(',');
                
                jQuery.each(s1d, function(key, value) {
                    
                    var li = value.split(':'); li = li[1];
                    jQuery('#code_standby_wrapper').append(jQuery('ul#code_distributor_wrapper li#'+ li));
                });
                
                jQuery.each(s2d, function(key, value) {
                    
                    var li = value.split(':'); li = li[1];
                    jQuery('#code_standby_wrapper').append(jQuery('ul#code_distributor_wrapper li#'+ li));
                });
            }
        }
    }
    
    /* Save combinations from the permutation generated when the
     * factorial of product samples is larger than the number of respondents. */
    this.code_combination_errormsg_flag = false;
    this.code_combination = function() {
        
        var c = [], code_combination_html = '', ctr = 1;
        jQuery("input[id^='code_']").each(function(){
            
            if(jQuery(this).is(':checked')) {
                
                var id = jQuery(this).attr('id').split('_');
                id = id[1];
                var code = jQuery(this).val();
                
                code_combination_html += '<li id="li_'+ ctr +'_'+ code.replace(/ /g, '_') +'" title="'+ code +'" style="font-size: 12px; padding: 2px 15px 2px 15px; text-align: center; cursor: pointer" class="ui-state-highlight">'+ jQuery('#code_'+ id +'_label').html() +'</li>';
                ctr++;
                c.push(code);
            }
            
        });
        
        if(! c.length) {
            
            if(this.code_combination_errormsg_flag)
                return;
            
            this.code_combination_errormsg_flag = true;
            Popup.dialog({
                title : 'ERROR',
                message : 'Codes must be selected to proceed in the next step.',
                buttons : ['Okay', 'Cancel'],
                width : '420px',
                buttonClick : function() {
                    
                    POPUPJS.overlay_show();
                    jQuery('#popupjs_btn_ok').attr('onclick', 'STEP_4.code_combination()');
                    STEP_4.code_combination_errormsg_flag = false;
                }
            });
            
            return;
        }
        
        var cs = jQuery("input[id^='code_']:checked").length;
        if(cs != Q.respondents) {
            
            if(this.code_combination_errormsg_flag)
                return;
            
            this.code_combination_errormsg_flag = true;
            
            var msg = '';
            if(cs > Q.respondents) msg = 'is too <b>large</b>.';
            else msg = 'is too <b>small</b>.';
            
            Popup.dialog({
                title : 'ERROR',
                message : 'Selected codes must be equal to the number of respondents.<br /><br />The number of codes selected '+ msg,
                buttons : ['Okay', 'Cancel'],
                width : '420px',
                buttonClick : function() {
                    
                    POPUPJS.overlay_show();
                    jQuery('#popupjs_btn_ok').attr('onclick', 'STEP_4.code_combination()');
                    STEP_4.code_combination_errormsg_flag = false;
                }
            });
            
            return;
        }
        
        GBL.loader();

        jQuery.post(
            DOCROOT +'sensory/async_code_combination_save',
            {
                q_id : Q.id,
                rta_id : Q.rta_id,
                combination : c.toString(),
                t : (new Date).getTime()
            },
            function() {
                
                jQuery('#code_distributor_wrapper').html(code_combination_html);
                ctr = 0;
                jQuery("li[id^='code_numbering_']").each(function(){

                    if(c[ctr] && c[ctr].length) {

                        var id = jQuery(this).attr('id').split('_');
                        id = id[id.length - 1];

                        var li_id = '#li_'+ (ctr + 1) +'_'+ c[ctr].replace(/ /g, '_');
                        jQuery(this)
                            .css('height', jQuery(li_id).height() + 3 +'px')
                            .css('padding', '2px');

                        ctr++;
                    }
                });

                POPUPJS.obj.hide();
                GBL.loader(false);
            }
        );
    }
}

jQuery(function(){
    
    if(Q.code_combination == 'permutate') {
        
        GBL.loader();
        
        jQuery.post(
            DOCROOT +'sensory/async_get_permutations',
            {
                code : JSON.stringify(Q.codes_1),
                code_controls : JSON.stringify(STEP_4.code_controls),
                t : (new Date).getTime()
            },
            function(r){
                
                if(r != '') {
                    
                    if(! POPUPJS.obj) POPUPJS.obj = new Popup.Window('popupjs_wrapper');
                    POPUPJS.obj.hide();
                    POPUPJS.obj.show();
                    
                    jQuery('#popupjs_wrapper').css('width', '450px');
                    jQuery('#popupjs_btn_ok').attr('onclick', 'STEP_4.code_combination()');
                    jQuery('#popupjs_wrapper .popup_content p').html(r).css('height', '300px');
                    jQuery('#checkall_trigger').click(function(){ 
                        
                        jQuery("input[id^='code_']").attr('checked', jQuery(this).is(':checked'));
                        
                        var cs = jQuery("input[id^='code_']:checked").length;
                        jQuery('#nof_selection').html(cs).css('color', '#'+ ((cs > Q.respondents) ? 'FF0000' : '000'));
                        
                    });
                    
                    jQuery("input[id^='code_']").click(function(){
                        
                        var cs = jQuery("input[id^='code_']:checked").length;
                        jQuery('#nof_selection').html(cs).css('color', '#'+ ((cs > Q.respondents) ? 'FF0000' : '000'));
                    });
                    
                    GBL.loader(false);

                } else {
                    
                    Popup.dialog({
                        title : 'ERROR',
                        message : '<div>Codes are not loaded.<br />Please ensure that you have filled-up all fields on Step #2.</div>',
                        buttons: ['Okay', 'Cancel'],
                        width: '350px'
                    });
                }
                
            }
        );

    } else {
        
        jQuery('#code_distributor_wrapper li').each(function(){
            
            var id = jQuery(this).attr('id').split('_');
            id = id[1];
            
            jQuery('#code_numbering_'+ id)
                .css('height', jQuery(this).height() + 3 +'px')
                .css('padding', '2px');
        });
    }
    
    STEP_4.code_distribution_load();
    
    SEAT.code_distributor_orig_state();
    
    jQuery('#auto_fill_trigger').click(function(){SEAT.fill_all();});
    
    jQuery("li[id^='code_receiver_']").sortable({
        connectWith: ".connectedSortable",
        receive: function(e, ui) {
            
            ui.item.removeClass('ui-selectee');
            
            if(jQuery('li', jQuery(this)).length > 1) {
                
                if( SEAT.cell[ui.item.attr('id')] &&
                    SEAT.cell[ui.item.attr('id')].length ) {
                    
                    /* Bring it back to the original seat #. */
                    jQuery('#'+ SEAT.cell[ui.item.attr('id')]).append(ui.item);
                    
                } else jQuery('#code_distributor_wrapper').append(ui.item); /* Else, bring it back to the main source. */
                
                return;
            }
            
            ui.item.css('padding', '2px').css('font-size', '10px');
        }
    }).disableSelection();
    
    jQuery("li[id^='code_receiver_']").mousehold(function(){
        
        /* Store the recent seat # occupied. */
        SEAT.cell[jQuery('li', jQuery(this)).attr('id')] =  jQuery(this).attr('id');
    });
    
    jQuery('#distribution_field_trigger').click(function() {
        
        jQuery('#distribution_field_wrapper').toggle(jQuery(this).is(':checked'));
        
        if(jQuery(this).is(':checked')) {
            
            jQuery("#code_distributor_wrapper").sortable('destroy');
            
            jQuery("#code_distributor_wrapper").selectable({
                connectWith: ".connectedSortable"
            });
            
            jQuery('#distribution_field').val('').focus();
            
        } else {
            
            jQuery("#code_distributor_wrapper").selectable('destroy');
            SEAT.code_distributor_orig_state();
            
            /* Clear highlighted selection. */
            SEAT.code_distributor_clear_selection();
        }
    });
    
    jQuery('#sensorium').change(function(){
        
        jQuery('#distribution_field').focus();
    });
    
    jQuery('#seat_fill_trigger').click(function(){
        
        var sensorium = jQuery('#sensorium').val();
        var seat = jQuery('#distribution_field');
        var dst_li = jQuery('#code_distributor_wrapper li');
        
        var seat_from = 0, seat_to = 0, codes = [], y = 0;
        
        if(seat.val().trim() == '') {
            
            seat.focus();
            return;
        }
        
        if(dst_li.length == 0) {
            
            Popup.dialog({
                title : 'ERROR',
                message : 'There are no codes to distribute.',
                buttons: ['Okay', 'Cancel'],
                width: '420px'
            });
            return;
        }
        
        dst_li.each(function(){if(jQuery(this).hasClass('ui-selected')) {codes.push(jQuery(this));}} );
        
        if(codes.length == 0) {
            
            Popup.dialog({
                title : 'ERROR',
                message : 'You must select at least one (1) code to distribute.',
                buttons: ['Okay', 'Cancel'],
                width: '420px'
            });
            return;
        }
        
        if(seat.val().indexOf('-') > -1) {
            
            var tmp = seat.val().split('-');
            
            seat_from = parseInt(tmp[0], 10);
            seat_to = parseInt(tmp[1], 10);
            
        } else {
            
            seat_from = seat_to = seat.val();
        }
        
        if((seat_from > seat_to) || seat_from == 0 || seat_to == 0) {
            
            Popup.dialog({
                title : 'ERROR',
                message : 'Seat range is not correct.<br /><br />Ex. 1-15',
                buttons: ['Okay', 'Cancel'],
                buttonClick: function() {seat.focus();},
                width: '420px'
           });
           
           return;
        }
        
        for(var x=seat_from; x<=seat_to; x++) {
            
            var rcvr = jQuery('#code_receiver_'+ sensorium +'_'+ x);
            
            if(rcvr.length && jQuery('li', rcvr).length < 1) {
                
                if(codes[y]) {rcvr.append(codes[y].css('font-size', '10px').removeClass('ui-selected'));}
                y++;
            }
        }
    });
    
    jQuery('#code_distributor_wrapper li').bind('mouseenter mouseleave', function(){
        
        if(! jQuery('#distribution_field_trigger').is(':checked')) {return;}
        
        var checker = 0;
        jQuery('#code_distributor_wrapper li').each(function(){if(jQuery(this).hasClass('ui-selected')) {checker++;}});
        jQuery('#clear_selections_trigger_wrapper').toggle(((checker > 0) ? true : false));
    });
});