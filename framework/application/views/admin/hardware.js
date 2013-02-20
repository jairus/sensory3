jQuery(function(){
    
    jQuery('#update').click(function(){ HWARE.submit(); });    
    setInterval("HWARE.station_state()", 3000);    
});

var HWARE = new function() {
    
    this.station_state = function() {
        
        jQuery.post(
            DOCROOT +'admin/async_station_state_get', { t : (new Date).getTime() },
            function(r) {
                
                var s1 = r.one, s2 = r.two, x = 0;
                jQuery.each(s1, function(x){ jQuery('#sensorium_1_status_'+ s1[x].number).html( ((s1[x].state == 1) ? 'online' : 'offline') ).css('color', ((s1[x].state == 1) ? '#009900' : '#999999')); });                
                jQuery.each(s2, function(x){ jQuery('#sensorium_2_status_'+ s2[x].number).html( ((s2[x].state == 1) ? 'online' : 'offline') ).css('color', ((s2[x].state == 1) ? '#009900' : '#999999')); });
                
            }, 'json'
        );
    }
    
    this.submit = function() {
        
        var s1 = [], s2 = [];
        
        jQuery("input[id^='sensorium_1_']").each(function(){
            
            var id = jQuery(this).attr('id').split('_'); id = id[id.length - 1];
            s1.push('sn'+ id +"="+ jQuery(this).val().trim());
        });
        
        jQuery("input[id^='sensorium_2_']").each(function(){
            
            var id = jQuery(this).attr('id').split('_'); id = id[id.length - 1];
            s2.push('sn'+ id +"="+ jQuery(this).val().trim());
        });
        
        GBL.loader();
        
        jQuery.ajax({
            type: 'POST',
            url: DOCROOT +'admin/async_hardware',
            data: { 
                
                sensorium1 : s1.toString().replace(/,/g, '&'),
                sensorium2 : s2.toString().replace(/,/g, '&'),
                t : (new Date).getTime()
            },
            success: function() {
                
                GBL.loader(false);
                
                jQuery.each(s1, function(key, value){
                    
                    var tmp = value.split('='); var no = tmp[0].replace('sn', ''); var val = tmp[1];
                    if(val == '') { jQuery('#sensorium_1_status_'+ no).html('offline').css('color', '#999999'); }
                });
                
                jQuery.each(s2, function(key, value){
                    
                    var tmp = value.split('='); var no = tmp[0].replace('sn', ''); var val = tmp[1];
                    if(val == '') { jQuery('#sensorium_2_status_'+ no).html('offline').css('color', '#999999'); }
                });
            },
            error : function(xhr) {
                
                /* If not reachable. */
                if(xhr.status == 0) {
                    
                    GBL.loader(false);
                    HWARE.submit();
                }
                
            } /* Recall. */            
        });
    }
}