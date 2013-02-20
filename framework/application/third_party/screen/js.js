var SCREEN = new function() {
    
    this.selection_code = 0;
    this.selection_count = 0;
    this.save_flag = {}; /* Becomes true when there are changes to the "screen session" per code. */
    this.count = 0;
    //this.item_name = '';
    //this.details = {};
    
    this.samplename_toggle = function(code, edit) {
        
        var e = jQuery('#samplename_'+ code);

        if(! edit) {
            
            if(! e.html().replace(/(<([^>]+)>)/ig, "").length) {
                
                e.html(' <u>Click</u> to <b>change</b>');
            }
            
            e.attr('contentEditable', false)
                .css('background', '#FFFFFF')
                .css('border', '0');
                
        } else {
            
            e.attr('contentEditable', true)
                .css('background', '#EFEFEF')            
                .css('border', '1px dashed #CCC')
                .focus();
        }
    }

    this.samplename_keypressed = function(e, code) {

        var key = GBL.get_keypressed(e);
        if(key == 13) {

            this.samplename_toggle(code, false);
            jQuery('#screencopy_code_'+ code).focus(); /* Just to lose focus from the editable DIV. */
        }
    }

    this.screenlabel_toggle = function(code, count, edit) {

        var e = jQuery('#screenlabel_'+ code +'_'+ count);

        if(! edit) {
            
            if(! e.html().replace(/(<([^>]+)>)/ig, "").length) {
                
                e.html(' <u>Click</u> to <b>change</b> label');
            }
            
            e.attr('contentEditable', false)
                .css('background', '#FFFFFF')
                .css('border', '0');

        } else {
            
            e.attr('contentEditable', true)
                .css('background', '#EFEFEF')            
                .css('border', '1px dashed #CCC')
                .focus();
        }
    }

    this.screenlabel_keypressed = function(e, code, count) {

        var key = GBL.get_keypressed(e);
        if(key == 13) {

            this.screenlabel_toggle(code, count, false);            
            this.update(code, count);
            jQuery('#screenlabel_'+ code +'_'+ count +'_visibility').focus(); /* Just to lose focus from the editable DIV. */
        }
    }

    this.screen_add = function(code) {
        
        var count = jQuery("tr[id^='tr_"+ code +"_']").length;
        var ctr = 1;

        jQuery("tr[id^='tr_"+ code + "_'] td").each(function(){

            if(ctr == 1 || ctr == 3) { jQuery(this).attr('rowspan', (count + 1)); }
            ctr++;
        });

        var sno = jQuery('#screennumber_'+ code +'_'+ count);
        if(sno && sno.length) { sno = parseInt(sno.html(), 10); }
        else { sno = 0; }

        sno++;
        
        var option = '<div style="text-align: center; font-size: 20px"><b id="screennumber_'+ code +'_'+ (count + 1) +'">'+ sno +'</b></div>';
            option += '<div style="padding: 5px">';
                option += '<div><a id="screenitemae_'+ code +'_'+ (count + 1) +'_trigger" href="javascript:SCREEN.item_ae_pick(\''+ code +'\','+ (count + 1) +')"><img src="'+ DOCROOT +'media/images/16x16/item-add.png" /> Add item</a></div>';
                option += '<div><a id="screenitemsort_'+ code +'_'+ (count + 1) +'_trigger" href="javascript:SCREEN.item_sort_init(\''+ code +'\','+ (count + 1) +')"><img src="'+ DOCROOT +'media/images/16x16/sort.png" /> Sort items</a></div>';
                option += '<div><a id="screendel_'+ code +'_'+ (count + 1) +'_trigger" href="javascript:SCREEN.screen_del(\''+ code +'\','+ (count + 1) +')"><img src="'+ DOCROOT +'media/images/16x16/delete.png" /> Delete</a></div>';
                option += '<div><a id="screenpreview_'+ code +'_'+ (count + 1) +'_trigger" target="_blank" href="'+ DOCROOT + 'exam/preview/'+ Q.rta_id +'/'+ code +'/'+ count +'"><img src="'+ DOCROOT +'media/images/16x16/preview.png" /> Preview</a></div>';
            option += '</div>';

        var label = '<div class="fntWrap" style="width: 153px; position: absolute; min-height: 16px; margin: 5px; padding: 2px; font: 12px Verdana" onkeypress="SCREEN.screenlabel_keypressed(event,\''+ code +'\','+ (count + 1) +')" onblur="SCREEN.screenlabel_toggle(\''+ code +'\','+ (count + 1) +',false); SCREEN.update(\''+ code +'\','+ (count + 1) +')" onclick="SCREEN.screenlabel_toggle(\''+ code +'\','+ (count + 1) +',true)" id="screenlabel_'+ code +'_'+ (count + 1) +'"> <u>Click</u> to <b>change</b> label</div>';
        
        var html = '<tr id="tr_'+ code +'_'+ (count + 1) +'"><td valign="top">'+ option +'</td>';
            html += '<td><ul style="padding: 5px; list-style: none; margin: 0; width: 260px" id="ul_'+ code +'_'+ (count + 1) +'"></ul></td><td>&nbsp;</td><td valign="top">'+ label +'</td><td align="center"><input id="screenlabel_'+ code +'_'+ (count + 1) +'_visibility" type="checkbox" onclick="SCREEN.update(\''+ code +'\','+ (count + 1) +')" /></td></tr>';
        
        jQuery('#tr_'+ code +'_'+ count).after(html); /* Append after the previous element. */
        
        this.update(code, (count + 1));
    }
    
    this.screen_del = function(code, count, confirmed) {
        
        if(! confirmed) {
            
            Popup.dialog({
                title : 'DELETE',
                message : 'Are you sure you want to <b>delete</b> this screen?<br /><br /><br />',
                buttons: ['Yes', 'No, I Cancel'],
                buttonClick: function(button) {
                    if(button == 'Yes') { SCREEN.screen_del(code, count, 1); }
                },
                width: '420px'
            });
            
        } else {
            
            jQuery('#tr_'+ code +'_'+ count).remove();
            this.update(code, count, 'delete');

            var ctr = 1;
            jQuery("tr[id^='tr_"+ code +"_'] td").each(function(){

                if(ctr == 1 || ctr == 3) { jQuery(this).attr('rowspan', jQuery("tr[id^='tr_"+ code + "_']").length); }
                ctr++;
            });

            /* START: Reset counter. */
            ctr = 1;
            jQuery("tr[id^='tr_"+ code +"_']").each(function(){

                var tmp = jQuery(this).attr('id');
                tmp = tmp.split('_');
                var count_old = tmp[tmp.length - 1];

                if(ctr > 1) {

                    jQuery('#screennumber_'+ code +'_'+ count_old).attr('id', 'screennumber_'+ code +'_'+ ctr).html(ctr - 1);

                    jQuery('#screenitemae_'+ code +'_'+ count_old +'_trigger')
                        .attr('id', 'screenitemae_'+ code +'_'+ ctr +'_trigger')
                        .attr('href', 'javascript:SCREEN.item_ae_pick(\''+ code +'\','+ ctr +')');
                    
                    jQuery('#screenitemsort_'+ code +'_'+ count_old +'_trigger')
                        .attr('id', 'screenitemsort_'+ code +'_'+ ctr +'_trigger')
                        .attr('href', 'javascript:SCREEN.item_sort_init(\''+ code +'\','+ ctr +')');
                        
                    jQuery('#screendel_'+ code +'_'+ count_old +'_trigger')
                        .attr('href', 'javascript:SCREEN.screen_del(\''+ code +'\','+ ctr +')')
                        .attr('id', 'screendel_'+ code +'_'+ ctr +'_trigger');
                        
                    jQuery('#screenpreview_'+ code +'_'+ count_old +'_trigger')
                        .attr('href', DOCROOT+ 'exam/preview/'+ Q.rta_id +'/'+ code +'/'+ (ctr - 1))
                        .attr('id', 'screenpreview_'+ code +'_'+ ctr +'_trigger');
                        
                    jQuery('#screenlabel_'+ code +'_'+ count_old +'_trigger')
                        .attr('onclick', 'SCREEN.screenlabel_toggle(\''+ code +'\','+ ctr +')')
                        .attr('id', 'screenlabel_'+ code +'_'+ ctr +'_trigger');
                    
                    jQuery('#screenlabel_'+ code +'_'+ count_old)
                        .attr('id', 'screenlabel_'+ code +'_'+ ctr)
                        .attr('onkeypress', 'SCREEN.screenlabel_keypressed(event,\''+ code +'\','+ ctr +')')
                        .attr('onblur', 'SCREEN.screenlabel_toggle(\''+ code +'\','+ ctr +',false); SCREEN.update(\''+ code +'\','+ ctr +')')
                        .attr('onclick', 'SCREEN.screenlabel_toggle(\''+ code +'\','+ ctr +',true)');

                    jQuery('#screenlabel_'+ code +'_'+ count_old +'_visibility')
                        .attr('id', 'screenlabel_'+ code +'_'+ ctr +'_visibility')
                        .attr('onclick', 'SCREEN.update(\''+ code +'\','+ ctr +')');
                        
                    /* START: Reset counter for Items. */
                    jQuery('.item_'+ code +'_'+ count_old +'_del_trigger').each(function(){
            
                        var tmp = jQuery(this).attr('href');
                        tmp = tmp.substr(0, tmp.lastIndexOf(',') + 1);

                        jQuery(this)
                            .attr('href', tmp + ctr +')')
                            .attr('class', 'item_'+ code +'_'+ ctr +'_del_trigger');
                    });
                    
                    jQuery('.item_'+ code +'_'+ count_old +'_edit_trigger').each(function(){
            
                        var tmp = jQuery(this).attr('href');
                        tmp = tmp.substr(0, tmp.lastIndexOf(',') + 1);

                        jQuery(this)
                            .attr('href', tmp + ctr +')')
                            .attr('class', 'item_'+ code +'_'+ ctr +'_edit_trigger');
                    });
                    
                    jQuery("li[id^='screenitem_"+ code +'_'+ count_old +"_']").each(function(){
                        
                        var tmp = jQuery(this).attr('id').split('_');
                        var item_ctr = parseInt(tmp[tmp.length - 1], 10);
                        
                        jQuery(this).attr('id', 'screenitem_'+ code +'_'+ ctr +'_'+ item_ctr);                        
                    });
                    /* END: Reset counter for Items. */
                    
                    jQuery('#ul_'+ code +'_'+ count_old).attr('id', 'ul_'+ code +'_'+ ctr);                    
                }

                jQuery(this).attr('id', 'tr_'+ code +'_'+ ctr);            
                ctr++;

            });
            /* END: Reset counter. */
        }
    }
    
    this.sort_init = function(code) {
        
        this.selection_code = code; /* For this.sort() */
        
        var items = '', index = 0;
        jQuery("tr[id^='tr_"+ code +"_']").each(function(){
            
            var tmp = jQuery(this).attr('id');
            tmp = tmp.split('_');
            var count = parseInt(tmp[tmp.length - 1], 10);
            
            var label = jQuery('#screenlabel_'+ code +'_'+ count);
            
            if(count > 1) {
                
                index++;
                items += '<li id="screensort_'+ index +'_'+ label.attr('detail') +'" class="ui-state-default">'+ label.html() +'</li>';
            }
        });
        
        if(items != '') {
            
            items = '<ul id="screen_list">'+ items +'</ul>';
        
            if(! POPUPJS.obj) POPUPJS.obj = new Popup.Window('popupjs_wrapper');
            POPUPJS.obj.hide();
            POPUPJS.obj.show();

            jQuery('#popupjs_wrapper').css('width', '420px');
            jQuery('#popupjs_btn_ok').attr('onclick', 'SCREEN.sort()');
            jQuery('#popupjs_wrapper .popup_title').html('Screen Sort');
            jQuery('#popupjs_wrapper .popup_content p').html(items);

            jQuery('#screen_list').attr('style', 'list-style-type: none; margin: 0; padding: 0; width: 100%; min-height: 100px');
            jQuery('#screen_list li').attr('style', 'margin: 0 2px 2px 2px; padding: 2px; padding-left: 1.5em; height: 18px; cursor: default');

            jQuery("#screen_list").sortable();
            
        } else {
            
            Popup.dialog({
                title : 'ERROR',
                message : '<div>There are no screens to sort.</div>',
                buttons: ['Okay', 'Cancel'],
                width: '350px'
            });
        }
    }

    this.sort = function() {
        
        var screens = [];
        jQuery('#screen_list li').each(function(){
            
            var id = jQuery(this).attr('id').replace('screensort_', '');
            screens.push(id +'='+ jQuery(this).html().replace(/\&/g, '[=AND=]'));
        });
        
        jQuery.post(
            DOCROOT +'sensory/async_screen_sort',
            {
                rta_id : Q.rta_id,
                screens : screens.toString().replace(/,/g, '&'),
                screen_code : SCREEN.selection_code,
                t : (new Date).getTime()
            },
            function(r){
                
                POPUPJS.obj.hide();
                
                SCREEN.clear_table(r, SCREEN.selection_code);
                
                jQuery('#tr_'+ SCREEN.selection_code +'_1').after(r.html);

                SCREEN.save_flag[SCREEN.selection_code] = ((r.flag == 'true') ? true : false);
                jQuery('#screen_ae_and_cancel_wrapper_'+ SCREEN.selection_code).toggle(SCREEN.save_flag[SCREEN.selection_code]);
                
                SCREEN.selection_code = 0;
                
            }, 'json'
        );
    }
    
    this.preview = function(code) {
        
        //window.location.href = DOCROOT +'exam/preview/'+ Q.rta_id +'/'+ code;
        
        Popup.dialog({
            title : 'UNDER CONSTRUCTION',
            message : '<img src="'+ DOCROOT +'media/images/uc.png"> This <u>feature</u> is still <b>under construction</b>.<div style="margin: 10px 0 0 280px">~:)</div>',
            buttons: ['Okay', 'Cancel'],
            buttonClick: function(button) {

            },
            width: '350px'
        });
    }
    
    this.update = function(code, count, command) {
        
        if(! command) command = 'add';
        
        var args = {
            
            rta_id : Q.rta_id,
            type : 'screen',
            command : command,
            screen_code : code,
            screen_count : count,
            t : (new Date).getTime()                
        };
        
        if(command != 'delete') {
            
            var screen_title_or_label_value = jQuery('#screenlabel_'+ code +'_'+ count).html().replace(/(<([^>]+)>)/ig, "");
            if(screen_title_or_label_value == 'Click to change label') screen_title_or_label_value = '';
        
            jQuery.extend(args, {
                screen_title_or_label_value : screen_title_or_label_value,
                screen_title_or_label_visibility : ((jQuery('#screenlabel_'+ code +'_'+ count +'_visibility').is(':checked') == true) ? 'shown' : 'hidden')
            });
        }
        
        jQuery.post(DOCROOT +'screen/async_session_update', args, function(r){
            if(r) {
                
                SCREEN.save_flag[code] = r.flag;
                jQuery('#screen_ae_and_cancel_wrapper_'+ code).toggle(r.flag);
            }
        }, 'json');
    }
    
    this.clear = function(code, confirmed) {
        
        var total = jQuery("tr[id^='tr_"+ code +"_']").length;
        
        if(total == 1) {
            
            Popup.dialog({
                title : 'ERROR',
                message : 'There are no screens to remove.',
                buttons: ['Okay', 'Cancel'],
                width: '420px'
            });
            
            return;
        }
        
        if(! confirmed) {
            
            Popup.dialog({
                title : 'CONFIRM',
                message : 'Are you sure you want to clear all <b>'+ (total - 1) +'</b> screens?',
                buttons: ['Yes', 'No, I Cancel'],
                buttonClick: function(button) {
                    
                    if(button == 'Yes') SCREEN.clear(code, 1);
                },
                width: '420px'
            });
            
        } else {
            
            jQuery.post(
                DOCROOT +'sensory/async_screen_clear',
                {
                    rta_id : Q.rta_id,
                    screen_code : code,
                    t : (new Date).getTime()
                },
                function(r){

                    SCREEN.clear_table(r, code);

                    SCREEN.save_flag[code] = r.flag;
                    jQuery('#screen_ae_and_cancel_wrapper_'+ code).toggle(SCREEN.save_flag[code]);

                }, 'json'
            );
        }
    }
    
    this.clear_table = function(r, code) {
        
        var ctr = 0;
        jQuery('#tr_'+ code +'_1 td').each(function(){

            ctr++;
            if(ctr == 1 || ctr == 3) { jQuery(this).attr('rowspan', r.count + 1); }                        
        });

        /* START: Clear. */
        jQuery("tr[id^='tr_"+ code +"_']").each(function(){

            var id = jQuery(this).attr('id').split('_');
            id = parseInt(id[id.length - 1], 10);                        
            if(id > 1) jQuery(this).remove();                        
        }); /* END: Clear. */
    }
    
    this.item_del = function(id, code, count, confirmed) {
        
        this.selection_count = count;
        
        if(! confirmed) {
            
            Popup.dialog({
                title : 'DELETE',
                message : 'Are you sure you want to <b>delete</b> this item?<br /><br /><br />',
                buttons: ['Yes', 'No, I Cancel'],
                buttonClick: function(button) {
                    if(button == 'Yes') { SCREEN.item_del(id, code, count, 1); }
                },
                width: '420px'
            });
            
        } else {
            
            GBL.loader();
            
            jQuery.post(
                DOCROOT +'screen/async_session_update',
                {
                    rta_id : Q.rta_id,
                    type : 'item',
                    command : 'delete',
                    item_id : id,
                    screen_code : code,
                    screen_count : count,
                    t : (new Date).getTime()
                },
                function(r) {
                    
                    if(r) {
                        
                        jQuery('#screenitem_'+ code +'_'+ count +'_'+ id).remove();
                        
                        SCREEN.item_del_reset_index(code, count);
                        
                        SCREEN.save_flag[code] = r.flag;
                        jQuery('#screen_ae_and_cancel_wrapper_'+ code).toggle(r.flag);
                    }
                    
                    GBL.loader(false);
                }, 'json'
            );
        }        
    }
    
    this.item_del_reset_index = function(code, count) {
        
        var ctr = 0, items = jQuery("li[id^='screenitem_"+ code +'_'+ count +"_']");
        
        if(items.length) {
        
            items.each(function(){

                ctr++;

                jQuery('.item_'+ code +'_'+ count +'_del_trigger', jQuery(this))
                    .attr('href', 'javascript:SCREEN.item_del('+ ctr +',\''+ code +'\','+ count +')');

                var tmp = jQuery('.item_'+ code +'_'+ count +'_edit_trigger', jQuery(this)).attr('href');
                tmp = tmp.split(',');

                jQuery('.item_'+ code +'_'+ count +'_edit_trigger', jQuery(this))
                    .attr('href', tmp[0]+','+ ctr +','+ tmp[2] +','+ tmp[3]);

                jQuery(this).attr('id', 'screenitem_'+ code +'_'+ count +'_'+ ctr);
            });
        }
    }
    
    this.item_ae_pick = function(code, count) {
        
        var options = '';
        
        this.selection_code = code;
        this.selection_count = count;
        
        if(Q.type_of_test == 'affective') {
            
            options += '<div><a title="liking" style="font: 12px Verdana" href="javascript:SCREEN.item_ae_picked(\'liking\')">Liking</a></div>';
            options += '<div><a title="compatibility" style="font: 12px Verdana" href="javascript:SCREEN.item_ae_picked(\'compatibility\')">Compatibility</a></div>';
            options += '<div><a title="jar" style="font: 12px Verdana" href="javascript:SCREEN.item_ae_picked(\'jar\')">JAR</a></div>';
            options += '<div><a title="ranking for preference" style="font: 12px Verdana" href="javascript:SCREEN.item_ae_picked(\'ranking_for_preference\')">Ranking for Preference</a></div>';
            options += '<div><a title="paired preference" style="font: 12px Verdana" href="javascript:SCREEN.item_ae_picked(\'paired_preference\')">Paired Preference</a></div>';

        }
        else
        if(Q.type_of_test == 'analytical') {
            
            options += '<div><a title="triangle" style="font: 12px Verdana" href="javascript:SCREEN.item_ae_picked(\'triangle\')">Triangle</a></div>';
            options += '<div><a title="same/no difference" style="font: 12px Verdana" href="javascript:SCREEN.item_ae_picked(\'same_or_different\')">Same / Different</a></div>';
            options += '<div><a title="duo-trio" style="font: 12px Verdana" href="javascript:SCREEN.item_ae_picked(\'duo_trio\')">Duo-Trio</a></div>';
            options += '<div><a title="2afc" style="font: 12px Verdana" href="javascript:SCREEN.item_ae_picked(\'2afc\')">2-AFC</a></div>';
            options += '<div><a title="3afc" style="font: 12px Verdana" href="javascript:SCREEN.item_ae_picked(\'3afc\')">3-AFC</a></div>';
            options += '<div><a title="sqs main" style="font: 12px Verdana" href="javascript:SCREEN.item_ae_picked(\'sqs_main\')">SQS Main</a></div>';
            options += '<div><a title="sqs attribute headers" style="font: 12px Verdana" href="javascript:SCREEN.item_ae_picked(\'sqs_attribute_heading\')">SQS Attribute Headers</a></div>';
            options += '<div><a title="sqs attribute" style="font: 12px Verdana" href="javascript:SCREEN.item_ae_picked(\'sqs_attribute\')">SQS Attribute</a></div>';
            options += '<div><a title="descriptive" style="font: 12px Verdana" href="javascript:SCREEN.item_ae_picked(\'descriptive\')">Descriptive</a></div>';
            options += '<div><a title="ranking for intensity" style="font: 12px Verdana" href="javascript:SCREEN.item_ae_picked(\'ranking_for_intensity\')">Ranking for Intensity</a></div>';
            
            options += '<div style="margin: 5px 0 5px 0; border: 1px solid #CC5454; border-right: 0; border-left: 0; padding: 5px 0 5px 0">';
            
            options += '<div>Screening: Triangle Test</div>';
            options += '<div><a title="screening: basic taste recognition" style="font: 12px Verdana" href="javascript:SCREEN.item_ae_picked(\'screening_basic_taste_recognition\')">Screening: Basic Taste Recognition</a></div>';
            options += '<div>Screening: Basic Taste Ranking for Intensity</div>';
            options += '<div><a title="screening: odor recognition" style="font: 12px Verdana" href="javascript:SCREEN.item_ae_picked(\'screening_odor_recognition\')">Screening: Odor Recognition</a></div>';
            options += '<div>Screening: Hardness Ranking</div>';
            
            options += '</div>';
        }
        
        options += '<div><a title="comment" style="font: 12px Verdana" href="javascript:SCREEN.item_ae_picked(\'comment\')">Comment</a></div>';
        options += '<div><a title="instruction" style="font: 12px Verdana" href="javascript:SCREEN.item_ae_picked(\'instruction\')">Instruction/Message</a></div>';
        options += '<div><a title="pause/break" style="font: 12px Verdana" href="javascript:SCREEN.item_ae_picked(\'pause_break\')">Pause/Break</a></div>';
        options += '<div><a title="multiple choice" style="font: 12px Verdana" href="javascript:SCREEN.item_ae_picked(\'multiple_choice\')">Multiple Choice</a></div>';
        options += '<div><a title="cata" style="font: 12px Verdana" href="javascript:SCREEN.item_ae_picked(\'cata\')">CATA</a></div>';

        if(! POPUPJS.obj) POPUPJS.obj = new Popup.Window('popupjs_wrapper');
        POPUPJS.obj.hide();
        POPUPJS.obj.show();
        
        jQuery('#popupjs_wrapper').css('width', '490px');
        jQuery('#popupjs_wrapper .popup_content p').html(options);
        jQuery('#popupjs_wrapper .popup_title').html('Select Item to Add');
        jQuery('#popupjs_btn_ok').hide();
    }
    
    this.item_ae_picked = function(item, id, code, count) {
        
        GBL.loader();
        
        var title = {
            'liking'                    : 'Liking',
            'compatibility'             : 'Compatibility',
            'jar'                       : 'JAR',
            'ranking_for_preference'    : 'Ranking for Preference',
            'paired_preference'         : 'Paired Preference',
            
            'comment'                   : 'Comment',
            'instruction'               : 'Instruction/Message',
            'pause_break'               : 'Pause/Break',
            'multiple_choice'           : 'Multiple Choice',
            'cata'                      : 'CATA (Check All That Apply)',
            
            'ranking_for_intensity'     : 'Ranking for Intensity',            
            'triangle'                  : 'Triangle',
            'same_or_different'         : 'Same / Different',
            'duo_trio'                  : 'Duo-Trio',
            '2afc'                      : '2-AFC',
            '3afc'                      : '3-AFC',
            'descriptive'               : 'Descriptive',
            'sqs_main'                  : 'SQS Main',
            'sqs_attribute'             : 'SQS Attribute',
            'sqs_attribute_heading'     : 'SQS Attribute Headers',
            
            'screening_basic_taste_recognition' : 'Screening: Basic Taste Recognition',
            'screening_odor_recognition'        : 'Screening: Odor Recognition'            
        };
        
        var width = {
            liking                      : '700px',
            compatibility               : '700px',
            jar                         : '700px',
            descriptive                 : '560px'
        };
        
        var popupjs_width = (width[item]) ? width[item] : '500px';
        
        if(! id) id = 0;
        
        var args = {
            rta_id : Q.rta_id,
            item_id : id,
            item_type : item,
            docroot : DOCROOT,            
            t : (new Date).getTime()
        };
        
        if(id > 0) {
            
            jQuery.extend(args, { screen_code : code, screen_count : (count - 1) });
            this.selection_count = count;
            this.selection_code = code;
            
        } else {
            
            jQuery.extend(args, { screen_code : this.selection_code, screen_count : (this.selection_count - 1) });
        }
        
        if( item == 'liking' ||
            item == 'compatibility' ||
            item == 'jar' ) { 
            
            jQuery.extend(args, { library : STEP_3.library[item] });
        }
        
        jQuery('#popupjs_wrapper .popup_title').html('Select Item to Add');
        jQuery.post(
            DOCROOT + APPPATH +'views/sensory/create_test/item_'+ item +'.php',
            args,
            function(r) {

                if(! POPUPJS.obj) POPUPJS.obj = new Popup.Window('popupjs_wrapper');
                POPUPJS.obj.hide();
                POPUPJS.obj.show();
                
                var trigger = '<a title="edit" href="javascript:SCREEN.itemlabel_toggle('+ id +',\''+ code +'\','+ count +')"><img src="'+ DOCROOT +'media/images/16x16/edit.png" /></a>' +
                '<div id="itemlabel_field_wrapper" style="display: none; position: absolute; padding: 5px 7px 7px 5px; background: #FFF; border: 1px solid #CC5454; border-right: 2px solid #CC5454; border-bottom: 2px solid #CC5454">' +
                '<div style="color: #000; margin-bottom: 2px"><b>Change :</b></div><input id="itemlabel_field" type="text" style="width: 250px" maxlength="50" /> <input id="itemlabel_field_trigger" type="button" value="Go" onclick="SCREEN.itemlabel_save('+ id +',\''+ code +'\','+ count +')" />' +
                ' <input type="button" onclick="SCREEN.itemlabel_toggle()" value="Cancel" /></div>';
                
                var content = '';
                if(id > 0) {
                    
                    //screen_total = jQuery('#tbl_screen tr').length - 2
                    var screen_total = jQuery("tr[id^='tr_"+ code +"_']").length - 1, html_copy = '', html_copy_to = '';
                    
                    if(screen_total > 0) {

                        for(var x=1; x<=screen_total; x++) { html_copy_to += '<option value="'+ x +'">'+ x +'</option>'; }
                        html_copy_to = '<select id="screencopy_to"><option value="">Select:</option>'+ html_copy_to +'</select>';
                    }

                    html_copy = '<select id="screencopy_option"><option value="copy">Copy to</option><option value="move">Move to</option></select>';
                    content = '<div style="float: right; margin-bottom: 10px"><img src="'+ DOCROOT +'media/images/16x16/copy.png" /> '+ html_copy +' screen '+ html_copy_to +' <input type="button" value="Go" onclick="SCREEN.item_copy_or_move()" /></div>';
                }
                
                jQuery('#popupjs_wrapper .popup_title').html(trigger +'<span>'+ title[item] +'</span>');
                jQuery('#popupjs_wrapper .popup_content p').html(content + r);
                
                jQuery('#popupjs_wrapper').css('width', popupjs_width);
                jQuery('#popupjs_btn_ok').attr('onclick', 'ITEM.ok()');
                
                GBL.loader(false);
            }
        );
    }
    
    this.itemlabel = '';
    this.itemlabel_wrapper = '';
    this.itemlabel_toggle = function(id, code, count) {
        
        var wrapper = jQuery('#itemlabel_field_wrapper');
        
        wrapper.toggle(function() {
            
            var field = jQuery('#itemlabel_field');
            
            if(! field.is(':hidden')) {
                
                jQuery.post(
                    DOCROOT +'sensory/async_itemlabel_fetch',
                    {
                        rta_id : Q.rta_id,
                        screen_code : code,
                        screen_count : count,
                        id : id,
                        t : (new Date).getTime()
                    },
                    function(r) {
                        
                        if(field.val().trim() == '') field.val(r).select();
                        field.focus();
                    }
                );
            }
        });
    }
    
    this.itemlabel_save = function(id, code, count) {
        
        var label = jQuery('#itemlabel_field');
        if(label.val().trim() == '') {
            
            label.focus();
            return;
        }
        
        this.itemlabel = label.val();
        this.itemlabel_wrapper = '#screenitem_'+ code +'_'+ count +'_'+ id + ' .item_'+ code +'_'+ count +'_edit_trigger span';
        this.itemlabel_toggle();        
    }
    
    this.item_sort_init = function(code, count) {
        
        this.selection_code = code; /* For this.item_sort() */
        this.selection_count = count; /* For this.item_sort() */
        
        var wrapper = jQuery('#ul_'+ code +'_'+ count);
        
        var items = '', index = 0;
        jQuery('li span', wrapper).each(function() {
            
            if(jQuery(this).html().trim().length) {
                
                index++;
                items += '<li id="screenitemsort_'+ index +'" class="ui-state-default">'+ jQuery(this).html() +'</li>';
            }
        });
        
        if(items != '') {
            
            items = '<ul id="screenitem_list">'+ items +'</ul>';
        
            if(! POPUPJS.obj) POPUPJS.obj = new Popup.Window('popupjs_wrapper');
            POPUPJS.obj.hide();
            POPUPJS.obj.show();

            jQuery('#popupjs_wrapper').css('width', '420px');
            jQuery('#popupjs_btn_ok').attr('onclick', 'SCREEN.item_sort()');
            jQuery('#popupjs_wrapper .popup_title').html('Item Sort');
            jQuery('#popupjs_wrapper .popup_content p').html(items);

            jQuery('#screenitem_list').attr('style', 'list-style-type: none; margin: 0; padding: 0; width: 100%; min-height: 100px');
            jQuery('#screenitem_list li').attr('style', 'margin: 0 2px 2px 2px; padding: 2px; padding-left: 1.5em; height: 18px; cursor: default');

            jQuery("#screenitem_list").sortable();
            
        } else {
            
            Popup.dialog({
                title : 'ERROR',
                message : '<div>There are no items to sort.</div>',
                buttons: ['Okay', 'Cancel'],
                width: '350px'
            });
        }
    }
    
    this.item_sort = function() {
        
        POPUPJS.obj.hide();
        
        var items = [];
        jQuery('#screenitem_list li').each(function(){
            
            //index++;
            items.push(jQuery(this).attr('id').replace('screenitemsort_', '')); // +'='+ jQuery(this).html()
            
        });
        
        if(items.length) {
            
            jQuery.post(
                DOCROOT +'sensory/async_screenitem_sort',
                {
                    rta_id : Q.rta_id,
                    items : items.toString(),//.replace(/,/g, '&'),
                    screen_code : SCREEN.selection_code,
                    screen_count : (SCREEN.selection_count - 1),
                    t : (new Date).getTime()
                },
                function(r){

                    POPUPJS.obj.hide();
                    
                    jQuery('#ul_'+ SCREEN.selection_code +'_'+ SCREEN.selection_count).html(r.html);
                    
                    SCREEN.save_flag[SCREEN.selection_code] = r.flag;
                    jQuery('#screen_ae_and_cancel_wrapper_'+ SCREEN.selection_code).toggle(SCREEN.save_flag[SCREEN.selection_code]);
                    
                    SCREEN.selection_code = 0;
                    SCREEN.selection_count = 0;
                    
                }, 'json'
            );
        }
    }
    
    this.item_copy_or_move = function() {
        
        var command = jQuery('#screencopy_option').val();
        var screen = parseInt(jQuery('#screencopy_to').val(), 10);
        
        if(screen > 0) {
            
            GBL.loader();
            
            jQuery.post(
                DOCROOT +'screen/async_session_update',
                {
                    rta_id : Q.rta_id,
                    type : 'item',
                    command : command,
                    item_id : ITEM.id,
                    screen_code : SCREEN.selection_code,
                    screen_count : SCREEN.selection_count,
                    copy_or_move_to : screen,
                    t : (new Date).getTime()
                },
                function(r) {
                    
                    if(! r.error) {
                        
                        jQuery('#ul_'+ SCREEN.selection_code +'_'+ (screen + 1)).append(r.html);

                        if(command == 'move') {

                            jQuery('#screenitem_'+ SCREEN.selection_code +'_'+ SCREEN.selection_count +'_'+ ITEM.id).remove();
                            
                            SCREEN.item_del_reset_index(SCREEN.selection_code, SCREEN.selection_count);

                            SCREEN.selection_count = (screen + 1);
                            ITEM.id = (r.target_screen_item_total + 1);
                        }

                        SCREEN.save_flag[SCREEN.selection_code] = r.flag;
                        jQuery('#screen_ae_and_cancel_wrapper_'+ SCREEN.selection_code).toggle(SCREEN.save_flag[SCREEN.selection_code]);
                        
                    } else {
                        
                        Popup.dialog({
                            title : 'ERROR',
                            message : 'Cannot copy to it\'s destination.<br /><br />'+ r.error,
                            buttons: ['Okay'],
                            buttonClick: function() {

                                jQuery('#popupjs_btn_ok').attr('onclick', 'ITEM.ok()');
                                POPUPJS.overlay_show();
                                
                            },
                            width: '420px'
                        });
                    }
                    
                    GBL.loader(false);
                    
                }, 'json'
            );
        }
    }
}