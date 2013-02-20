jQuery(function(){
    
    jQuery(document).click(function(e){

        /*if(e.button == 0) {
            
            var ss = jQuery('#ss_search_result');
            if(! ss.is(':hidden')) {
                
                ss.hide();
                jQuery('#ss_search_field').val('').focus();                
            }
        }*/
    });
    
    jQuery('#step_4_trigger').click(function(){ GBL.loader(); GBL.go('sensory/create_test/'+ Q.rta_id +'/?step=4'); });
    if(SCREEN.save_flag) {for(var x in SCREEN.save_flag) {jQuery('#screen_ae_and_cancel_wrapper_'+ x).toggle(SCREEN.save_flag[x]);}}
});

var STEP_3 = new function() {
    
    this.default_instructions = [];
    this.library = {};
    this.ss_code = {}; /* Where to load the score-sheet chosen from the library. */
    
    /* Revision: 26 May 2012; 05 June 2012 */
    this.screen_ae = function(code) { /* AXL */
        
        GBL.loader();
        
        jQuery.post(
            DOCROOT +'screen/async_ae',
            {
                rta_id : Q.rta_id,
                screen_code : code,
                t : (new Date).getTime()
            },
            function(r) {
                
                STEP_3.screen_fill(code, r.html, r.count, r.flag);
                GBL.loader(false);
                
            }, 'json'
        );
    }
    
    this.scale_toggle = function(item, scale) { /* AXL */
       
       if(scale > 10) {
           
           jQuery('#'+ item +'_edit_attr_wrapper').html('<div style="text-align: left">Limit exceeded. Please choose a smaller number.</div>');
           return;
       }
       
       var label = [];
       var html = '';
       var start = 1;
       var label_number = scale;
       
       if(item == 'liking') {
           
           label[9] = 'Like extremely';
           label[8] = 'Like very much';
           label[7] = 'Like moderately';
           label[6] = 'Like slightly';
           label[5] = 'Neither like nor dislike';
           label[4] = 'Dislike slightly';
           label[3] = 'Dislike moderately';
           label[2] = 'Dislike very much';
           label[1] = 'Dislike extremely';
       }
       else
       if(item == 'compatibility') {
           
           label[9] = 'Extremely compatible';
           label[8] = 'Very much compatible';
           label[7] = 'Moderately compatible';
           label[6] = 'Slightly compatible';
           label[5] = 'Neither compatible nor incompatible';
           label[4] = 'Slightly incompatible';
           label[3] = 'Moderately incompatible';
           label[2] = 'Very much incompatible';
           label[1] = 'Extremely incompatible';
       }     
       
       if(scale == 7) start = 2;
       else if(scale == 5) start = 3;
       
       if(scale == 9 || scale == 7 || scale == 5) {
           
           if(scale == 7) scale = 8;
           if(scale == 5) scale = 7;
           
       }
       else
       if(scale == 2) {
           
           if(item == 'liking') {
               label[2] = 'Like';               
               label[1] = 'Dislike';
           }
           else
           if(item == 'compatibility') {
               
               label[2] = 'Compatible';
               label[1] = 'Inompatible';
           }
       }
       else
       if(scale == 3) {
           
           if(item == 'liking') {
               label[3] = 'Like';
               label[2] = 'Neither';
               label[1] = 'Dislike';
           }
           else
           if(item == 'compatibility') {
               
               label[3] = 'Compatible';
               label[2] = 'Neither compatible nor incompatible';
               label[1] = 'Inompatible';
           }           
       } else label = [];
       
       jQuery('#'+ item +'_scale_init_data').val('');
       
       var event = '';
       if(jQuery('#'+ ITEM.item_type +'_edit_scale_other').is(':hidden') == false) {
           
           event = ' onkeydown="ITEM.scale_init_data_update('+ scale +')" onkeyup="ITEM.scale_init_data_update('+ scale +')" ';            
       }
        
       /* Default values. */
       var initial_detail = [];
       
       for(var x=scale; x>=start; x--) {

           var caption = (label[x]) ? label[x] : '';

           html += '<div style="padding-bottom: 2px">'+ label_number +' <input id="'+ item +'_edit_scale_field_'+ label_number +'" '+ event +' type="text" value="'+ caption +'" /></div>';
           label_number--;
           
           initial_detail.push(caption.replace(/,/g, '[comma]'));
       }
       
       /* START: Store initial scale data. */
       if(initial_detail.length) {jQuery('#'+ item +'_scale_init_data').val(initial_detail.toString().replace(/,/g, '[row]').replace(/\[comma\]/g, ','));}
       /* END: Store initial scale data. */
       
       var on_attr_update_wrapper = jQuery('#'+ ITEM.item_type +'_edit_scale_field_wrapper');
       if(on_attr_update_wrapper && on_attr_update_wrapper.length) on_attr_update_wrapper.html(html);
       else jQuery('#'+ item +'_edit_attr_wrapper').html(html);
   }
   
   this.screen_reset = function(code, confirmed) { /* AXL */
       
       if(! confirmed) {
           
           Popup.dialog({
                title : 'RESET',
                message : 'This will bring back the screen\'s original structure and items attached to it.<br /><br />Do you want to proceed?',
                buttons: ['Yes', 'No, I Cancel'],
                buttonClick: function(button) {                    
                    if(button == 'Yes') {STEP_3.screen_reset(code, 1);}                    
                },
                width: '420px'
           });
            
       } else {
           
           GBL.loader();
           
           jQuery.post(
                DOCROOT +'sensory/async_screen_reset',
                {
                    code : code,
                    rta_id : Q.rta_id,
                    t : (new Date).getTime()
                },
                function(r) {
                    
                    STEP_3.screen_fill(code, r.html, r.count, false);
                    
                    var btn = jQuery('#screencopy_code_'+ code);
                    if(btn && btn.length) {
                        
                        btn[0].selectedIndex = 0;
                        btn.trigger('change');
                    }
                    
                    GBL.loader(false);
                }, 'json'
            );
        }
   }
   
   this.screen_fill = function(code, html, count, flag) {
       
       var ctr = 0;
       jQuery('#tr_'+ code +'_1 td').each(function(){

            ctr++;
            if(ctr == 1 || ctr == 3) {jQuery(this).attr('rowspan', count + 1);}                        
       });

       /* START: Clear. */
       jQuery("tr[id^='tr_"+ code +"_']").each(function(){

            var id = jQuery(this).attr('id').split('_');
            id = parseInt(id[id.length - 1], 10);
            if(id > 1) jQuery(this).remove();
       }); /* END: Clear. */

       jQuery('#tr_'+ code +'_1').after(html);

       SCREEN.save_flag[code] = flag;
       jQuery('#screen_ae_and_cancel_wrapper_'+ code).toggle(SCREEN.save_flag[code]);
   }
   
   this.screen_copy = function(code) { /* AXL */
       
       var code_to_copy = jQuery('#screencopy_code_'+ code).val();
       
       if(jQuery("tr[id^='tr_"+ code_to_copy +"_']").length <= 1) {
           
           Popup.dialog({
                title : 'ERROR',
                message : 'Code : <b>'+ code_to_copy +'</b> is empty.<br /><br />There are no screens to copy.',
                buttons: ['Okay', 'Cancel'],
                width: '420px'
           });
            
           return;
       }
       
       jQuery.post(
            DOCROOT +'sensory/async_screen_copy',
            {
                screen_code : code,
                code_to_copy : code_to_copy,
                rta_id : Q.rta_id,
                t : (new Date).getTime()
            },
            function(r) {
                
                STEP_3.screen_fill(code, r.html, r.count, ((r.flag == 'true') ? true : false));
                
            }, 'json'
       );
   }

/* 
 * START: Score-sheet manipulation goes below.
 * */
   
   this.ss_save_full_flag = {};
   this.ss_save = function(code, go) { /* AXL */
        
        if(! go) {
            
            if(SCREEN.save_flag[code] == true) {
                
                var message = 'There are some changes made to this questionnaire.<br /><br />'+
                    'Clik "<b>Okay</b>" to save with the recent changes or "<b>No</b>" to save only the original structure.';
                
                Popup.dialog({
                    title : 'CONFIRM',
                    message : message,
                    buttons: ['Okay', 'No', 'Cancel'],
                    buttonClick: function(button) {
                        
                        if(button == 'Okay') STEP_3.ss_save_full_flag[code] = true;
                        if(button == 'No') {
                            
                            STEP_3.ss_check(code);
                            return;
                        }
                        
                        if(button != 'Cancel') STEP_3.ss_name_toggle(code, true);
                    },
                    width: '420px'
                });
                
                return;
                
            }
        }
        
        if(STEP_3.ss_save_full_flag[code] == false) {
            
            if(! go) {
                
                STEP_3.ss_check(code);
                return;
            }
        }
        
        var name = jQuery('#ss_name_'+ code);
        if(name.val().trim() == '') {
            
            name.focus();
            return;
        }
        
        GBL.loader();
        
        jQuery.post(
            DOCROOT +'sensory/async_scoresheet_save',
            {
                rta_id : Q.rta_id,
                full : STEP_3.ss_save_full_flag[code],
                code : code,
                name : name.val(),
                t : (new Date).getTime()
            },
            function() {
                
                STEP_3.ss_name_toggle(code, false);
                STEP_3.ss_save_full_flag[code] = false;
                
                GBL.loader(false);
                
                Popup.dialog({
                    title : 'SUCCESS',
                    message : 'You have successfully saved this score-sheet.',
                    buttons: ['Okay', 'Cancel'],                    
                    width: '420px'
                });
            }
        );
    }
    
    this.ss_name_toggle = function(code, flag) { /* AXL */
        
        var e = jQuery('#ss_name_wrapper_'+ code);
        
        e.toggle(flag);
        if(! e.is(':hidden')) {
            
            jQuery('#ss_name_'+ code).val('').focus();
            
        } else STEP_3.ss_save_full_flag[code] = false;
    }
    
    this.ss_check = function(code) { /* AXL */
        
        GBL.loader();
                
        jQuery.post(
            DOCROOT +'sensory/async_scoresheet_check',
            {
                rta_id : Q.rta_id,
                code : code,
                t : (new Date).getTime()
            },
            function(r) {

                GBL.loader(false);

                if(r.exists) STEP_3.ss_name_toggle(code, true);
                else {

                    Popup.dialog({
                        title : 'ERROR',
                        message : 'Score-sheet is empty. This operation cannot be made.',
                        buttons: ['Okay', 'Cancel'],
                        width: '420px'
                    });
                }

            }, 'json'
        );
    }
    
    this.ss_search = function() { /* AXL */
        
        var field = jQuery('#ss_search_field');
        
        if(field.val().trim().length < 2) {
            
           field.focus();
           return;
        }
        
        var result = jQuery('#ss_search_result_inner');
        
        jQuery('#ss_search_result').show();
        result.html('<img alt="processing..." src="'+ DOCROOT +'media/images/loader2.gif" /> <span style="color: #999">searching...</span>');
        
        jQuery.post(
        
            DOCROOT +'sensory/async_scoresheet_search',
            {
                search : field.val(),
                t : (new Date).getTime()
            },
            function(r){ result.html(r); }
        );
    }
    
    this.ss_search_keypressed = function(e) { /* AXL */
        
        var key = GBL.get_keypressed(e);
        if(key == 13) this.ss_search();
    }
    
    this.ss_load = function(id, rta_id, code) { /* AXL */
        
        /* START: Hide all except selection. */
        jQuery("div[id^='ss_result_itemcode_wrapper_']").each(function(){
            
            var tmp = jQuery(this).attr('id').split('_');
            tmp = tmp[tmp.length - 1];
            
            if(parseInt(tmp, 10) != parseInt(id, 10)) jQuery(this).hide();            
        });
        /* END: Hide all except selection. */
        
        var fieldw = jQuery('#ss_result_itemcode_wrapper_'+ id), html = '';
        
        if(this.ss_code.length > 1 && (! code)) {
            
            if(fieldw && fieldw.length == 0) {
                
                for(var x=0; x<this.ss_code.length; x++) { html += '<option value="'+ this.ss_code[x] +'">'+ this.ss_code[x] +'</option>'; }
                
                jQuery('#ss_result_item_'+ id).append(
                    '<div id="ss_result_itemcode_wrapper_'+ id +'" style="margin-left: 20px; color: #666">Select <b><u>code</u></b> where to load: <select onchange="STEP_3.ss_load('+ id +','+ rta_id +',jQuery(this).val())" id="ss_result_itemcode_'+ id +'"><option value="">Choose</option>'+ html +'</select></div>'
                );

            } else { if(fieldw.is(':hidden')) fieldw.show(); }
            
            return;
            
        } else if(this.ss_code.length == 1) code = STEP_3.ss_code[0];
        
        GBL.loader();
        
        jQuery.post(
        
            DOCROOT +'sensory/async_scoresheet_load',
            {
                rta_id_src : rta_id,
                rta_id_dst : Q.rta_id,
                id : id,
                code: code,
                t : (new Date).getTime()
            },
            function(r){
                
                GBL.loader(false);
                
                STEP_3.ss_result_toggle(false);                
                if(! r.same) STEP_3.screen_fill(code, r.html, r.count, true);
                
            }, 'json'
        );
    }
    
    this.ss_result_toggle = function(flag) { /* AXL */
        
        jQuery('#ss_search_field').val('').focus();
        jQuery('#ss_search_result').toggle(flag);
    }
}