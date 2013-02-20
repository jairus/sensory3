jQuery(function(){
    
    jQuery('#rtas_wrapper li').dblclick(function(){
        
        var choice = jQuery(this);
        
        var id = choice.attr('id');
        if(! id || ! id.length)
            return;
        
        var check = jQuery('#'+ id +'_seat');
        if(check && check.length) {

            check.focus();
            return;
        }

        if(jQuery('#rta_assignment_wrapper tr:first td').html() == 
           '<span style="color: #CC0000">Empty. Please choose another batch or assign stations for this batch.</span>') {
           jQuery('#rta_assignment_wrapper tr:first td').remove(); 
        }
        
        var rta_id = id.split('__');
        rta_id = rta_id[0].replace('rta_', '');
        
        if(jQuery('#rta_'+ rta_id +'__name_wrapper') && jQuery('#rta_'+ rta_id +'__name_wrapper').length) {
            
            jQuery('#rta_'+ rta_id +'__name_wrapper, #rta_'+ rta_id +'__assign_wrapper, #rta_'+ rta_id +'__code_wrapper, #rta_'+ rta_id +'__codetrigger_wrapper').remove();            
            var ctr = 1; jQuery("#rta_assignment_wrapper span[id$='__numbering']").each(function(){ jQuery(this).html(ctr); ctr++; });
        }
        
        
        GBL.loader();

        jQuery.post(
            DOCROOT +'sensory/async_dt_station_assignment_fields',
            {
                rta_id : rta_id,
                batch : jQuery('#batch').val(),
                date : DT.date,
                t : (new Date).getTime()
            },
            function(r) {
                
                jQuery('#rta_assignment_wrapper').append(r.html);
                jQuery('#rta_'+ rta_id +'__numbering').html(jQuery("#rta_assignment_wrapper tr[id$='__name_wrapper']").length);
                
                jQuery('#rta_'+ rta_id +'__name_wrapper span').each(function(){
                    
                    jQuery(this).css('cursor', 'default').disableSelection();
                    if(jQuery(this).attr('title') && jQuery(this).attr('title').length) { jQuery(this).qtip({ style: { name: 'cream', width: 350, tip: true } }); }
                });
                
                jQuery('#rta_'+ rta_id +'__assign_wrapper input').attr('disabled', r.assign_per_code);
                jQuery('#rta_'+ rta_id +'__assign_wrapper .clear').attr('style', "background: url("+ DOCROOT +"media/images/16x16/clear"+ ((! r.assign_per_code) ? '' : '-off') +".png) top left no-repeat; width: 16px; height: 16px; border: 0");
                
                STEP_2.fields_check_init(r.codes);
                
                GBL.loader(false);
            }, 'json'
        );
    }).disableSelection();
    
    jQuery(".seat_wrapper li").dblclick(function(){
        
        Popup.dialog({
            title : 'RESET',
            message : '',
            buttons: ['Okay', 'Cancel'],
            buttonClick: function(button) {                    
                
            },
            width: '420px'
        });

        jQuery('.popup_content p').html('hello').css('min-height', '300px');
                    
    }).disableSelection().css('cursor', 'default');
    
    jQuery('#batch').change(function(){ STEP_2.assignments(); });  
    jQuery('#batch').trigger('change');
});

var STEP_2 = new function() {
    
    this.codes = [];
    this.fields_check_init = function(codes) {
        
        jQuery.each(codes, function(key, value){
            
            if(typeof STEP_2.codes[value] == 'undefined') {
                
                STEP_2.codes[value] = jQuery('#'+ value).val();
            }
            
            jQuery('#'+ value)
                .bind('keyup keydown paste', function(){ STEP_2.fields_check_onkey(this); })
                .attr('title', 'Station(s): '+ jQuery('#'+ value).val())
                .qtip({ style: { name: 'cream', tip: true } });
        });
        //jQuery("input[id^='rta_"+ rta_id +"__code-']").bind('keyup keydown paste', function(){ STEP_2.fields_check_onkey(this); });
        //console.log(STEP_2.codes);
    }
    
    this.fields_check_onkey = function(choice) {
        
        choice = jQuery(choice);
        
        if(choice.val() == STEP_2.codes[choice.attr('id')]) choice.css('color', '#000000');
        else choice.css('color', '#006600');        
    }
    
    this.clear = function(id, choice) {
        
        var bg = jQuery(choice).css('background-image').replace('url("', '').replace('")', '').replace(/^.*\/|\.[^.]*$/g, '');
        
        if(bg == 'clear') {
            
            jQuery(choice).css('background-image', 'url('+ DOCROOT +'media/images/16x16/undo.png)');
            jQuery('#'+ id).val('');
            
        } else {
            
            jQuery(choice).css('background-image', 'url('+ DOCROOT +'media/images/16x16/clear.png)');
            jQuery('#'+ id).val(STEP_2.codes[id]);
        }
        
        jQuery('#'+ id).focus();
    }
    
    /* Sets status (1 = Go; 0 = Pause). */
    this.go = function(choice, id, code) {
        
        var trigger = jQuery(choice);
        var state = ((trigger.val() == 'Go') ? 1 : 0);
        var seat = jQuery('#'+ id +'__code-'+ code);
        if(seat.val().trim() == '') {
            
            seat.focus();
            return;
        }
        
        var tmp = null;
        if(seat.val().indexOf('-') > -1) {
            
            tmp = seat.val().split('-');
            var seat_from = parseInt(jQuery.trim(tmp[0]), 10);
            var seat_to = parseInt(jQuery.trim(tmp[1]), 10);
            
            if(seat_from > seat_to) {
                
                Popup.dialog({
                    title : 'ERROR',
                    message : 'Digit on the left should be smaller than the right.',
                    buttons: ['Okay', 'Cancel'],
                    buttonClick: function() { seat.focus(); },
                    width: '420px'
                });

                return;
            }
        }
        
        GBL.loader();
        
        jQuery.ajax({
            type: 'POST',
            url: DOCROOT +'sensory/async_dt_station_state',
            data: { 
                date : DT.date,
                rta_id : id.replace('rta_', ''),
                code : code,
                state : state,
                batch : jQuery('#batch').val().trim(),
                station : seat.val(),
                t : (new Date).getTime()
            },
            success: function() {
                
                GBL.loader(false);
                
                trigger.val((state == 1) ? 'Pause' : 'Go');
                STEP_2.icon(id, code, state);
                
                //if(code == 'all') {
                if(isNaN(parseInt(code, 10))) {
                    
                    jQuery("#"+ id +"__code_wrapper .go").each(function(){
                        jQuery(this).val((state == 1) ? 'Pause' : 'Go');
                    });
                    
                    jQuery("input[id^='"+ id +"__code-']").each(function(){
                        jQuery(this).val(seat.val());
                    });
                    
                    jQuery("td[id^='"+ id +"__icon-']").each(function(){
                        
                        var id_attr = jQuery(this).attr('id').split('-');
                        STEP_2.icon(id, id_attr[1], state);
                        
                    });
                    
                } else {
                    
                    /*var buttons = jQuery("#"+ id +"__code_wrapper .go");
                    var ctr  = 0;

                    buttons.each(function(){ if(jQuery(this).val() == 'Pause') ctr++; });

                    if(ctr == buttons.length) jQuery("#"+ id +"__assign_wrapper .all").val('Pause');
                    else jQuery("#"+ id +"__assign_wrapper .all").val('Go');*/
                }
                
                
                
            },
            error : function(xhr) {
                
                /* If not reachable. */
                if(xhr.status == 0) {
                    GBL.loader(false);
                    STEP_2.go(choice, id, code);
                }
                
            } /* Recall. */
            
        });
    }
    
    this.assign = function(choice, id, code) {
        
        /* rta_4117__code-633/all */
        var seat = jQuery('#'+ id +'__code-'+ code);
        
        if(seat.val().length) {
            
            var response = STATION_INPUT.process(seat);
            if(! response) return;

            if( seat.val().indexOf(',-') > -1 ||
                seat.val().indexOf('-,') > -1 ||
                seat.val().indexOf('--') > -1 ||
                seat.val().indexOf(',,') > -1
            ) {

                Popup.dialog({
                    title : 'ERROR',
                    message : 'Invalid format. Use only the "separator" and the "range" signs correctly.<br /><br />Invalid formats are:<br /> (<b>,-</b>) or (<b>-,</b>) or (<b>--</b>) or (<b>,,</b>).<br /><br /><b>Correct</b> formats are:<br /> (<b>10-13,4-7</b>) or (<b>4-7,10-13,6,8</b>).',
                    buttons: ['Okay', 'Cancel'],
                    buttonClick: function() { seat.focus(); },
                    width: '420px'
                });

                return;
            }
        }
        
        GBL.loader();
        
        jQuery.ajax({
            type: 'POST',
            url: DOCROOT +'sensory/async_dt_station_assign',
            data: { 
                date : DT.date,
                rta_id : id.replace('rta_', ''),
                code : code,
                batch : jQuery('#batch').val().trim(),
                station : seat.val(),
                t : (new Date).getTime()
            },
            success: function() {
                
                GBL.loader(false);
                
                //if(code == 'all') {
                if(isNaN(parseInt(code, 10))) {
                    
                    jQuery('#'+ id +'__assign_wrapper .clear').css('background-image', 'url('+ DOCROOT +'media/images/16x16/clear.png)');  /* Bring back original state of "assign-once". */
                    
                    jQuery("input[id^='"+ id +"__code-']").each(function(){
                        jQuery(this).val(seat.val()).attr('title', 'Station(s): '+ seat.val()).qtip({ style: { name: 'cream', tip: true } });
                        
                        STEP_2.codes[jQuery(this).attr('id')] = seat.val();
                        
                        var tmp_code = jQuery(this).attr('id').split('-'); tmp_code = tmp_code[1];
                        jQuery('#'+ id +'__clear-'+ tmp_code).css('background-image', 'url('+ DOCROOT +'media/images/16x16/clear.png)');  /* Bring back original state. */
                    });
                }
                
                STEP_2.codes[id +'__code-'+ code] = seat.val();
                seat.css('color', '#000000');
                jQuery('#'+ id +'__clear-'+ code).css('background-image', 'url('+ DOCROOT +'media/images/16x16/clear.png)'); /* Bring back original state. */
                
                seat.attr('title', 'Station(s): '+ seat.val()).qtip({ style: { name: 'cream', tip: true } });
            },
            error : function(xhr) { 
                
                /* If not reachable. */
                if(xhr.status == 0) {
                    
                    GBL.loader(false);
                    STEP_2.assign(choice, id, code);
                } /* Recall. */
            }
        });
    }
    
    this.assignments = function() {
        
        GBL.loader();
        
        jQuery.ajax({
            type: 'POST',
            url: DOCROOT +'sensory/async_dt_station_assignments_bybatch',
            data: { 
                date : DT.date,
                batch : jQuery('#batch').val().trim(),
                t : (new Date).getTime()
            },
            dataType : 'json',
            success: function(r) {
                
                var assignment_wrapper = jQuery('#rta_assignment_wrapper');
                assignment_wrapper.html('');
                
                var assign_per_code_flag = [];
                jQuery.each(r, function(key, value){
                    
                    assignment_wrapper.append(value.html);
                    assign_per_code_flag.push(value.assign_per_code);
                    
                    if(value.codes) STEP_2.fields_check_init(value.codes);
                });
                
                var ctr = 0;
                jQuery("tr[id$='__assign_wrapper']").each(function(){
                    
                    jQuery('input', jQuery(this)).attr('disabled', assign_per_code_flag[ctr]).bind('keyup keydown paste', function(){ STEP_2.fields_check_onkey(this); });                    
                    jQuery('.clear', jQuery(this)).attr('style', "background: url("+ DOCROOT +"media/images/16x16/clear"+ ((! assign_per_code_flag[ctr]) ? '' : '-off') +".png) top left no-repeat; width: 16px; height: 16px; border: 0");
                    
                    ctr++;
                });
                
                ctr = 1; jQuery("#rta_assignment_wrapper span[id$='__numbering']").each(function(){ jQuery(this).html(ctr); ctr++; });
                
                jQuery("tr[id$='__name_wrapper'] span").each(function(){
                    
                    jQuery(this).css('cursor', 'default').disableSelection();
                    if(jQuery(this).attr('title') && jQuery(this).attr('title').length) { jQuery(this).qtip({ style: { name: 'cream', width: 350, tip: true } }); }
                });
                
                GBL.loader(false);
            },
            error : function(xhr) {
                
                /* If not reachable. */
                if(xhr.status == 0) {
                    
                    GBL.loader(false);
                    STEP_2.assignments();
                }
            } /* Recall. */
        });        
    }
    
    this.assign_percode__field = function(choice, id) {
        
        choice = jQuery(choice);
        var w = jQuery('#'+ id +'__code_wrapper');
        
        jQuery('#'+ id +'__assign_wrapper .clear').attr('style', "background: url("+ DOCROOT +"media/images/16x16/clear"+ ((jQuery('b', choice).html() == 'Assign Once') ? '' : '-off') +".png) top left no-repeat; width: 16px; height: 16px; border: 0");        
        
        if(jQuery('b', choice).html() == 'Assign per Code') {
            
            jQuery('b', choice).html('Assign Once');
            jQuery('#'+ id +'__assign_wrapper input').attr('disabled', true);
            w.show();
        } else {
            
            jQuery('b', choice).html('Assign per Code');
            jQuery('#'+ id +'__assign_wrapper input').attr('disabled', false);
            jQuery('#'+ id +'__code-all').focus();
            w.hide();
        }
    }
    
    this.icon = function(id, code, state) {
        
        /* START: Helper/state icons. */
        var go = jQuery("#"+ id +"__icon-"+ code+' .go');
        var pause = jQuery("#"+ id +"__icon-"+ code+' .pause');

        if(state == 1) {

            if(pause && pause.length) pause.remove();
            if(! (go && go.length)) jQuery("#"+ id +"__icon-"+ code).append('<img title="flowing" class="go" src="'+ DOCROOT +'media/images/16x16/ok.png" />');

        } else {

            if(go && go.length) go.remove();
            if(! (pause && pause.length)) jQuery("#"+ id +"__icon-"+ code).append('<img title="paused" class="pause" src="'+ DOCROOT +'media/images/16x16/pause.png" />');
        }
        /* END: Helper/state icons. */
    }
}

var STATION_INPUT = new function() {
            
    this.error = '';
    this.process = function(seat) {

        var response = null;
        var station = jQuery.trim(seat.val());
        var stations = [];

        var pattern = /^[0-9\-\,]+$/;

        if(! pattern.test(station)) {
            
            Popup.dialog({
                title : 'ERROR',
                message : 'Invalid input. Enter station numbers.',
                buttons: ['Okay', 'Cancel'],
                buttonClick: function() { seat.focus(); },
                width: '420px'
            });

            return false;
        }

        if(station.indexOf(',') > -1) {

            stations = station.split(',');
            jQuery.each(stations, function(key, value){

                response = STATION_INPUT.format(value);
                if(! response) {
                    
                    Popup.dialog({
                        title : 'ERROR',
                        message : STATION_INPUT.error,
                        buttons: ['Okay', 'Cancel'],
                        buttonClick: function() { seat.focus(); },
                        width: '420px'
                    });

                    return false; /* Exit / break for this loop. */
                }
            });
            
            if(! response) return false;
            
        } else {

            response = STATION_INPUT.format(station);
            if(! response) {

                Popup.dialog({
                    title : 'ERROR',
                    message : STATION_INPUT.error,
                    buttons: ['Okay', 'Cancel'],
                    buttonClick: function() { seat.focus(); },
                    width: '420px'
                });
                return false;
            }
        }
        
        return true;
    }

    this.format = function(pattern) {

        var tmp = [];
        if(pattern.indexOf('-') > -1) {
            tmp = pattern.split('-');

            if(tmp.length != 2) {

                this.error = 'Correct use of range must be like 1-10 and not 1-5-10.';
                return false;
            }

            var n1 = parseInt(tmp[0], 10);
            var n2 = parseInt(tmp[1], 10);

            if(isNaN(n1) || isNaN(n2)) {

                this.error = 'Be sure to enter valid numbers.';
                return false;
            }

            if(n1 > n2) {

                this.error = 'Digits on the right must always be larger than the left. No such range like '+ pattern +'.';
                return false;
            }
        }
        else {

            if(isNaN(parseInt(pattern, 10))) {

                this.error = 'Be sure to enter valid numbers.';
                return false;
            }
        }

        return true;
    }
}