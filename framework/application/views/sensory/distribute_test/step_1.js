jQuery(function(){
    
    jQuery('#content_tbl td > div.menu').click(function(){
        
        var content_wrapper = jQuery('#content_tr td div');
        var menu = jQuery(this).attr('id');
        
        jQuery('#content_tbl td > div.menu').each(function(){
            jQuery(this).css('font-weight', 'normal');
        });
        
        jQuery(this).css('font-weight', 'bold');
        
        if(content_wrapper.is(':hidden')) {
            
            content_wrapper.toggle(function(){ STEP_1.content_fill(menu); });
            
        } else STEP_1.content_fill(menu);
        
    });
    
    jQuery('#content_tbl #content').bind('keyup keydown', function(){
        
        var content = jQuery(this);
        var menu = '';
        
        jQuery('#content_tbl td > div.menu').each(function(){
            
            if(jQuery(this).css('font-weight') == 700) {
                
                menu = jQuery(this).attr('id');
            }
        });
        
        setTimeout(STEP_1.content_flag_apply, 100, content, menu);        
        STEP_1.content_tmp[menu] = content.val();        
    });
    
    STEP_1.content_flag_init();
    
    jQuery("#rtas_wrapper").sortable({
            
        receive: function(e, ui) { sortableIn = 1; },
        over: function(e, ui) { sortableIn = 1; },
        out: function(e, ui) { sortableIn = 0; },
        beforeStop: function(e, ui) {
           if(sortableIn == 0) { if(ui.item.html().indexOf('Pause/Break') > -1) { ui.item.remove(); } }
        }
    });

    jQuery("#pause_wrapper").draggable({
        connectToSortable: "#rtas_wrapper",
        helper: "clone",
        revert: "invalid"
    });
    
    jQuery("#date").datepicker();
    jQuery('#ui-datepicker-div').css('font-size', '12px');
    
    jQuery('#pick_date_trigger').click(function(){

        if(jQuery('#date').val().trim() == '') {

            jQuery('#date').focus();
            return;
        }

        jQuery('#content_tr #content').val('');
        STEP_1.content_flag_init();

        GBL.loader();

        jQuery.post(
            DOCROOT +'sensory/async_dt_rta_sequence',
            {
                date : jQuery('#date').val(),
                t : (new Date).getTime()
            },
            function(r){

                GBL.loader(false);
                
                jQuery('#rtas_wrapper').html('');
                
                if(r.rta && r.rta.length) {

                    jQuery('#rtas_wrapper').html(r.rta);

                } else {

                    Popup.dialog({
                        title : 'BLANK',
                        message : 'No result(s) found for this date.',
                        buttons: ['Okay', 'Cancel'],
                        width: '420px'
                    });
                }
                
                STEP_1.content = r.content;

                var tmp = jQuery('#date').val().split('/');
                DT.date = tmp[2] +'-'+ tmp[0] +'-'+ tmp[1]; /* Store the new selected date. */
            },
            'json'
        );
    });
});

var STEP_1 = new function() {
    
    this.content = {};
    this.content_tmp = {};
    this.content_flag = {};
    
    this.content_fill = function(menu) {
        
        var content = jQuery('#content_tbl #content');
        
        var c = (! this.content[menu] ||
                 ! this.content[menu].length ||
                 this.content_flag[menu] == true) ? this.content_tmp[menu] : this.content[menu];
        
        content.val(c).focus();
        content.css('color', (this.content_flag[menu]) ? '#006600' : '#000000');        
    }
    
    this.content_flag_apply = function(content, menu) {
        
        var flag = (STEP_1.content[menu] != STEP_1.content_tmp[menu]) ? true : false;
        
        STEP_1.content_flag[menu] = flag;
        content.css('color', (flag) ? '#006600' : '#000000');
    };
    
    this.content_flag_init = function() {
        
        this.content_flag['menu_1'] = false;
        this.content_flag['menu_2'] = false;
        this.content_flag['menu_3'] = false;
    }
    
    this.submit = function() {
        
        var sequence = [], id = 0;
            
        jQuery('#rtas_wrapper li').each(function(){
            
            if(jQuery(this).attr('id') && jQuery(this).attr('id').length) {
                
                id = jQuery(this).attr('id').replace('rta_', '').split('__');
                id = id[0];
                
            } else id = 0; /* Pause/Break. */

            sequence.push(id);
        });
        
        if(! STEP_1.content_tmp['menu_1'] || ! STEP_1.content_tmp['menu_1'].length) STEP_1.content_tmp['menu_1'] = STEP_1.content['menu_1'];
        if(! STEP_1.content_tmp['menu_2'] || ! STEP_1.content_tmp['menu_2'].length) STEP_1.content_tmp['menu_2'] = STEP_1.content['menu_2'];
        if(! STEP_1.content_tmp['menu_3'] || ! STEP_1.content_tmp['menu_3'].length) STEP_1.content_tmp['menu_3'] = STEP_1.content['menu_3'];

        jQuery.post(
            DOCROOT +'sensory/async_dt_save_step_1',
            {
                welcome_text : STEP_1.content_tmp['menu_1'],
                instruction : STEP_1.content_tmp['menu_2'],
                ty_text : STEP_1.content_tmp['menu_3'],
                sequence : sequence.toString(),
                date : jQuery('#date').val(),
                t : (new Date).getTime()
            },
            function() { GBL.go('sensory/distribute_test/2/'+ DT.date); }
        );
    }
}