jQuery(function(){    
    jQuery('#week_number').change(function(){
        
        var caption = '';
        var tmp = grouped_by_week_number[jQuery(this).val()];
        
        if(tmp.length > 1) caption = tmp[0] +' <b>to</b> '+ tmp[tmp.length - 1];
        else caption = tmp[0];
        
        jQuery('#week_number_days').html('<b>Dates:</b> '+ caption +'.');
        
        
        jQuery.post(DOCROOT +'calendar/async_get_rtas_by_week',
            {   
                date : tmp.toString(),
                date_end : date_end,
                date_subject : date_subject,
                t : (new Date).getTime()
            },
            function(r){
                
                /* START: Clear fields. */
                var tot = ['analytical', 'affective', 'micro', 'physico_chem'];
                var group = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];                
                for(var x1 in tot) { for(var x2 in group) { jQuery('#'+ tot[x1] +'_'+ group[x2]).html(''); } }
                /* END: Clear fields. */
                
                if(r.data.length) {
                    
                    for(var x=0; x<r.data.length; x++) {

                        var tmp = r.data[x];
                        jQuery('#'+ tmp.tot +'_'+ tmp.group).append('<a title="'+ tmp.data.samples_name +'" href="'+ DOCROOT +'admin/rta_view/'+ tmp.data.id +'"><b>'+ tmp.data.samples_name +'</b></a><br />');
                    }
                }
            },
            'json'
        );
        
    });
    
    jQuery('#week_number').trigger('change');
});