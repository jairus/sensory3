jQuery(function(){
    
    /* START: Align "attribute checkboxes" with the "sqs header checkboxes". */
    var sqs_attribute_heading = jQuery("input[id^='sqs_attribute_heading_'][type='checkbox']");
    if(sqs_attribute_heading && sqs_attribute_heading.length) {
        
        sqs_attribute_heading.each(function(){
        
            var choice = jQuery(this).attr('id').split('_');
            choice = choice[choice.length - 1];

            var left = (jQuery(this).offset().left - 4);

            jQuery(".alignment_"+ choice).each(function(){

                jQuery(this)
                    .css('position', 'absolute')
                    .css('left', left)
                    .css('top', jQuery(this).offset().top - 6);            
            });
        });
    }
    /* END: Align "attribute checkboxes" with the "sqs header checkboxes". */
});

var SQS = new function() {
    
    this.attribute_textfield = function(choice, ctr) {
        
        EXAM.ctr = ctr;
        choice = jQuery(choice);

        /* i.e. (See "[]" enclosed. ) sqs_attribute_3_aroma__[1_2]_raw = attribute index , partition number. */
        var tmp = choice.attr('id').split('__');
        tmp = tmp[1].split('_');
        var attr_i = parseInt(tmp[0], 10) - 1;

        /* Get the corresponding checkbox for this text field. */
        var checkbox = jQuery("input[name='"+ choice.attr('id').replace('_field', '') +"']:checked");

        if(checkbox.length) {

            if(choice.val().trim().length) {

                EXAM.answers[ctr - 1]['axl'][attr_i].attr = choice.val().trim();

            } else {

                EXAM.answers[ctr - 1]['axl'][attr_i] = { 'answer' : 0, 'attr' : '' };
                checkbox.attr('checked', false);
            }
        }
    }

    this.attribute_checkbox = function(choice, ctr) {
        
        EXAM.ctr = ctr;
        
        var attr_selection = {};
        choice = jQuery(choice);
        
        /* Uncheck other selection with the same name. */
        jQuery("input[name='"+ choice.attr('name') +"']").each(function(){ if(jQuery(this).attr('id') != choice.attr('id')) jQuery(this).attr('checked', false); });
        
        var tmp = choice.attr('id').split('__');
        var answered = 0;
        
        var field = jQuery('#'+ choice.attr('name') +'_field');
        if(field && field.length) {
            
            if(! field.val().trim().length) {
                
                
                Popup.dialog({
                    title : 'ERROR',
                    message : '<div>You cannot select an item with a blank attribute.<br /><br />Please fill-up the corresponding field.</div>',
                    buttons: ['Okay', 'Cancel'],
                    width: '420px',
                    buttonClick : function() {
                        
                        field.focus();
                        choice.attr('checked', false);
                    }
                });
                
                return;
            }
        }
        
        var textfield_total = 0;
        jQuery.each(EXAM.attributes[ctr - 1], function(key, value){ if(value == '---') textfield_total++; });
        
        jQuery("input[id^='"+ tmp[0] +"__']").each(function(){
            
            // sqs_attribute_3_aroma__1_2_raw = attribute index , partition number
            
            var tmp = jQuery(this).attr('id').split('__');
            tmp = tmp[1].split('_');
            
            var attr_i = parseInt(tmp[0], 10) - 1;
            
            var attr_text = EXAM.attributes[ctr - 1][attr_i];
            if(attr_text == '---') attr_text = '';
            
            var partition_no = parseInt(tmp[1], 10);
            
            if(jQuery(this).is(':checked')) {
                
                /* If attribute is a text field. */
                var field = jQuery('#'+ jQuery(this).attr('name') +'_field');
                if(field && field.length) {
                    
                    if(field.val().trim().length) {
                        
                        attr_selection[attr_i] = { 'answer' : partition_no, 'attr' : field.val().trim() };
                        textfield_total--;
                    }
                    
                /* Else, if normal attribute (readonly). */
                } else { attr_selection[attr_i] = { 'answer' : partition_no, 'attr' : attr_text }; }
                
                answered++;
                
            } else {
                
                if(typeof attr_selection[attr_i] == 'undefined' || (! attr_selection[attr_i].answer)) {
                    
                    EXAM.answers[ctr - 1]['axl'] = attr_selection[attr_i] = { 'answer' : 0, 'attr' : attr_text };
                    
                }                
            }
        });
        
        EXAM.answers[ctr - 1]['axl'] = attr_selection;
        
        if(answered == (EXAM.attributes[ctr - 1].length - textfield_total)) {
            
            EXAM.session_updater_peritem(ctr);
        }
    }
    
    this.attribute_heading_checkbox = function(choice, ctr) {
        
        choice = jQuery(choice);

        jQuery("input[name='"+ choice.attr('name') +"']").each(function(){

            if(jQuery(this).attr('id') != choice.attr('id')) { jQuery(this).attr('checked', false); }
        });
        
        EXAM.answers[ctr - 1]['axl'] = (choice.is(':checked') ? choice.val().trim() : '');
        EXAM.session_updater_peritem(ctr);
    }
    
    this.main_pickbox = function(choice, ctr) {
        
        choice = jQuery(choice);
        
        jQuery("div[id^='sqs_main_"+ ctr +"_']").each(function(){
        
            if(jQuery(this).attr('id') != choice.attr('id')) { jQuery(this).css('background-color', '#FFF').css('border-color', '#FFF'); }
        });
        
        choice.css('background-color', '#FDF7F7').css('border-color', '#EB9999');

        EXAM.answers[ctr - 1]['axl'] = choice.html().stripTags().trim();
        EXAM.session_updater_peritem(ctr);
    }
}