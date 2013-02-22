jQuery(function(){
    
    jQuery('#calendar_wrapper').css('width', jQuery('#calendar_wrapper').parents('div').width());
    
    if(jQuery.url().param('target') == 'week') {
        
        jQuery('#week_number').change(function(){

            var caption = '';
            var tmp = CALENDAR.grouped_by_week_number[jQuery(this).val()];

            if(tmp.length > 1) caption = tmp[0] +' <b>to</b> '+ tmp[tmp.length - 1];
            else caption = tmp[0];

            jQuery('#week_number_days').html('<b>Dates:</b> '+ caption +'.');

            GBL.loader();
            
            jQuery.post(DOCROOT +'calendar/async_get_rtas_by_week',
                {   
                    //date : tmp.toString(),
                    week_no : jQuery(this).val(),
                    date_end : CALENDAR.date_end,
                    date_subject : CALENDAR.date_subject,
                    t : (new Date).getTime()
                },
                function(r){
                    
                    /* START: Clear fields. */
                    var tot = ['analytical', 'affective', 'micro', 'physico_chem'];
                    var group = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];                

                    jQuery.each(tot, function(x1, v1){

                        jQuery.each(group, function(x2, v2){

                            jQuery('#'+ tot[x1] +'_'+ group[x2]).html(''); 
                        });
                    });
                    /* END: Clear fields. */

                    if(r.data.length) {

                        for(var x=0; x<r.data.length; x++) {

                            var tmp = r.data[x];
                            jQuery('#'+ tmp.tot +'_'+ tmp.group).append('<a title="'+ tmp.data.samples_name +'" href="'+ DOCROOT +'admin/rta_view/'+ tmp.data.id +'"><b>'+ tmp.data.samples_name +'</b></a><br />');
                        }
                    }
                    
                    GBL.loader(false);
                },
                'json'
            );

        });

        jQuery('#week_number').val(CALENDAR.week_no).trigger('change');
    }
});

var CALENDAR = new function() {
    
    this.grouped_by_week_number = [];
    this.date_end = '';
    this.date_subject = '';
    this.week_no = '01';
}