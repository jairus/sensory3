jQuery(function(){
    
    jQuery('#code_restore_wrapper').hide();
    
    jQuery('#batch').bind('paste keyup keydown', function(){
        
        /*var html = '';
        var batch = parseInt(jQuery(this).val().trim(), 10);
        var bc = '';
        for(var x=1; x<=batch; x++) {
            
            if(STEP_2.batch_content[x - 1] && STEP_2.batch_content[x - 1].length) bc = STEP_2.batch_content[x - 1];
            else bc = '';
            
            html += '<div style="margin-bottom: 2px"><input name="batch_value_content" type="text" value="'+ bc +'" /></div>';
        }
        
        jQuery('#batch_value_wrapper').html(html);*/
        
        var html = '';
        var batch = parseInt(jQuery(this).val().trim(), 10);
        
        var select_hours = '', select_meridiem = '';
        
        for(var x=1; x<=batch; x++) {
            
            select_hours = '';
            for(var y=1; y<13; y++) {
                
                var z = ((y < 10) ? ('0'+ y) : y);
                select_hours += '<option value="'+ z +'">'+ z +'</option>';
            }
            
            select_hours = '<select id="batch_hour_'+ x +'">'+ select_hours +'</select>';
            
            select_meridiem = '<select id="batch_meridiem_'+ x +'"><option value="AM">AM</option><option value="PM">PM</option></select>';
            
            html += '<tr><td>'+ select_hours +'</td><td id="batch_minute_wrapper_'+ x +'"><a style="margin:0 2px 0 2px" href="javascript:STEP_2.batch_minutes_interval_field('+ x +')">[<b>more ...</b>]</a></td><td>'+ select_meridiem +'</td></tr>';
            
        }
        
        jQuery('#batch_value_wrapper').html(html);
        jQuery("select[id^='batch_hour_']").each(function(){ jQuery(this)[0].selectedIndex = 8; });
        
        if(STEP_2.batch_content.length) {
            
            jQuery.each(STEP_2.batch_content, function(key, value){
                
                var tmp = value.split(' '); /* 08:30 PM */
                var hour = tmp[0].split(':'); /* 08:30 */
                var minute = hour[1]; /* 30 */
                hour = hour[0]; /* 08 */
                
                x = 0;
                jQuery('#batch_hour_'+ (key + 1) +' option').each(function(){
                    
                    if(jQuery(this).val() == hour) jQuery('#batch_hour_'+ (key + 1))[0].selectedIndex = x;
                    x++;
                });
                
                if(minute != '00') {
                    
                    x = 0; var interval = 1;
                    
                    var minute_tmp = parseInt(minute, 10);
                    
                    if((minute_tmp%30) == 0) interval = 30;
                    else if((minute_tmp%5) == 0) interval = 5;
                    
                    html = STEP_2.batch_minutes_option(interval);
                    
                    html = '<select id="batch_minute_'+ (key + 1) +'">'+ html +'</select>';
                    jQuery('#batch_minute_wrapper_'+ (key + 1) +' a').before(html);
                    jQuery("#batch_minute_"+ (key + 1) +" option[value='"+ minute +"']").attr('selected', 'selected');
                }
                
                jQuery('#batch_meridiem_'+ (key + 1) +" option[value='"+ tmp[1] +"']").attr('selected', 'selected');
            });
        }
    });
    
    if(STEP_2.batch_content.length) jQuery('#batch').trigger('keyup');
});

var STEP_2 = new function() {
    
    this.batch_content = [];
    
    this.batch_minutes_option = function(interval) {
        
        interval = parseInt(interval, 10);

        var x = 0, y = '', html = '';
        while(x < 60) {

            x += interval; y = ((x < 10) ? ('0'+ x) : x);
            if(x < 60) html += '<option value="'+ y +'">'+ y +'</option>';
            else html += '<option value="00">00</option>';
        }
        
        return html;
    }
    
    this.batch_minutes_interval_change = function(obj) {
        
        obj = jQuery(obj);
        this.batch_minutes_interval = obj.val();
    }
    
    this.batch_minutes_interval = 0;
    this.batch_minutes_interval_field = function(index) {
        
        this.batch_minutes_interval = 1;
        
        Popup.dialog({
            title : 'Minute',
            message : '<div>If you like to include "minutes". Select an interval.</div><div><select onchange="STEP_2.batch_minutes_interval_change(this)" id="batch_minute_interval_'+ index +'"><option value="1">1</option><option value="5">5</option><option value="30">30</option></select></div>',
            buttons: ['Okay', 'Cancel'],
            buttonClick: function(button) {
                
                if(button == 'Okay') {
                    
                    var html = STEP_2.batch_minutes_option(STEP_2.batch_minutes_interval);
                    var select = jQuery('#batch_minute_'+ index);
                    if(select && select.length) {

                        select.html(html);
                        jQuery('#batch_minute_wrapper_'+ index +' a').before(select);

                    } else {

                        html = '<select id="batch_minute_'+ index +'">'+ html +'</select>';
                        jQuery('#batch_minute_wrapper_'+ index +' a').before(html);
                    }
                }
            },
            width: '420px'
        });
    }
    
    this.submit = function(confirmed) {
        
        var respondents = jQuery('#respondents').val().trim() * 1;
        var batch = jQuery('#batch').val().trim() * 1;
        var codes_changed = false, codes = [], bvc = [];
        
        for(var x=1; x<=batch; x++) {
            
            var hour = jQuery('#batch_hour_'+ x).val();
            var minute = jQuery('#batch_minute_'+ x);
            if(minute && minute.length) minute = minute.val();
            else minute = '00';
            var meridiem = jQuery('#batch_meridiem_'+ x).val();
            
            bvc.push(hour +':'+ minute +' '+ meridiem);
        }
        
        jQuery("input[id^='code_']").each(function(){ 

            var tmp = jQuery(this).attr('id').split('_');
            tmp = parseInt(tmp[1], 10);

            if(tmp == 1 && Q.codes_1.length) {

                if(jQuery.inArray(jQuery(this).val(), Q.codes_1) <= -1) {
                    codes_changed = true;
                }
            }
            else
            if(tmp == 2 && Q.codes_2.length) {

                if(jQuery.inArray(jQuery(this).val(), Q.codes_2) <= -1) {
                    codes_changed = true;
                }
            }

            /* START: Collect codes to save later. */
            if( jQuery(this) &&
                jQuery(this).length &&
                jQuery(this).val() != '') {

                codes.push(jQuery(this).attr('id') +'='+ jQuery(this).val()); 
            }
            /* END: Collect codes to save later. */
        });
        
        var control_codes = jQuery("input[name='tag_control_trigger']:checked");
        if(control_codes && control_codes.length) {

            var index = control_codes.val();
            control_codes = [];

            control_codes.push('code_1_'+ index +'='+ jQuery('#code_1_'+ index).val());

            if(jQuery('#code_2_'+ index) && jQuery('#code_2_'+ index).length) {

                control_codes.push('code_2_'+ index +'='+ jQuery('#code_2_'+ index).val());
            }
        } else control_codes = [];

        var product_names = [];
        jQuery("input[id^='product_name_']").each(function(){

            var id = jQuery(this).attr('id').split('_');
            id = parseInt(id[id.length - 1], 10);

            if(jQuery(this).val() != '') product_names.push(id +'='+ jQuery(this).val());
        });
        
        if(! confirmed) {
            
            if(respondents <= 0) {
                
                Popup.dialog({
                    title : 'ERROR',
                    message : 'Please enter the number of repondents.',
                    buttons: ['Okay', 'Cancel'],
                    buttonClick: function() {
                        
                        jQuery('#respondents').focus();
                    },
                    width: '420px'
                });

                return;
            }
            else
            if(batch <= 0) {
                
                Popup.dialog({
                    title : 'ERROR',
                    message : 'Please enter the number of batch.',
                    buttons: ['Okay', 'Cancel'],
                    buttonClick: function() {
                        
                        jQuery('#batch').focus();
                    },
                    width: '420px'
                });

                return;
            }
            
            if(! codes.length) {
                
                Popup.dialog({
                    title : 'ERROR',
                    message : 'Please generate a code.',
                    buttons: ['Okay', 'Cancel'],
                    buttonClick: function() {
                        
                        jQuery('#code_1_1').focus();
                    },
                    width: '420px'
                });
                
                return;
            }
            
            if(codes_changed == true) {
                
                Popup.dialog({
                    title : 'CONFIRM',
                    message : 'Codes were changed.<br />If you proceed, the screens you created for the previous codes will be lost.<br /><br />Are you sure you want to continue?',
                    buttons: ['Yes', 'No, I cancel'],
                    buttonClick: function(button) { if(button == 'Yes') { STEP_2.submit(true); } },
                    width: '420px'
                });
                
                return;
            }
        }
        
        GBL.loader()
        
        jQuery.post(
            DOCROOT +'sensory/async_ct_step_2',
            {   rta_id : Q.rta_id,
                flow : jQuery('#flow').is(':checked'),
                panreg_e : jQuery('#employee').is(':checked'),
                panreg_ne : jQuery('#nemployee').is(':checked'),
                respondents : respondents,
                batch : batch,
                batch_content : bvc.toString(),
                control_codes : control_codes.toString().replace(/\,/g, '&'),
                product_names : product_names.toString().replace(/\,/g, '&'),
                codes : codes.toString().replace(/\,/g, '&'),
                t : (new Date).getTime()
            },
            function() { GBL.go('sensory/create_test/'+ Q.rta_id +'/?step=3'); } /* Proceed to step 3. */
        );
    }
    
    this.code_generate = function(nofs, with_2ndary_code) {
        
        if(jQuery('#code_restore_wrapper').is(':hidden')) jQuery('#code_restore_wrapper').show();
        
        GBL.loader();
        
        jQuery.post(
            DOCROOT +'sensory/async_code_generate',
            {   
                with_2ndary_code : with_2ndary_code,
                nof_samples : nofs,
                t : (new Date).getTime()
            },
            function(r){
                
                r = r.split(',');
                var y = nofs;
                
                for(var x=1; x<=nofs; x++) {
                    
                    if(Q.codes_1[x - 1] != jQuery('#code_1_'+ x).val()) jQuery('#code_1_'+ x).val(r[x - 1]);
                    
                    /* When secondary-code fields are present. */
                    if(jQuery('#code_2_'+ x) && jQuery('#code_2_'+ x).length) {
                        
                        if(Q.codes_2[x - 1] != jQuery('#code_2_'+ x).val()) jQuery('#code_2_'+ x).val(r[y]);                        
                    }
                    
                    y++;
                }
                
                GBL.loader(false);
            }
        );
    }
    
    this.code_restore = function(nofs) {
       
       if(jQuery('#code_restore_wrapper').is(':hidden') == false) jQuery('#code_restore_wrapper').hide();
       
       for(var x=1; x<=nofs; x++) {

           jQuery('#code_1_'+ x).val(Q.codes_1[x - 1]);

           /* When secondary-code fields are present. */
           if(jQuery('#code_2_'+ x) && jQuery('#code_2_'+ x).length) {

               jQuery('#code_2_'+ x).val(Q.codes_2[x - 1]);
           }
       }
       
       jQuery("input[name='tag_control_trigger']").each(function(){
           
           if(jQuery(this).val() == Q.control_codes_index) jQuery(this).attr('checked', true);
       });
   }
   
   this.code_clear = function() {
       
       if(jQuery('#code_restore_wrapper').is(':hidden')) jQuery('#code_restore_wrapper').show();
        
       jQuery("input[id^='code_1_'], input[id^='code_2_']").val('');
       jQuery("input[name='tag_control_trigger']").attr('checked', false);
   }
}   