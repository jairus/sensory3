var DT = new function() {
    
    this.step = 1;
    this.seat = [];
    this.date = '';
    
    this.toggle_trigger = function(id) {
        
        var tmp = id.split('_');
        var seat = jQuery('#'+ id +'_seat').val().trim();
        
        jQuery('#'+ id +'_trigger').attr('disabled', ((DT.seat[tmp[tmp.length - 1]] == seat) ? true : false));
    }
    
    /*this.station_get = function(id) {
        
        var tmp = id.split('_');
        var rta_id = tmp[tmp.length - 1];
        
        jQuery.post(
            DOCROOT +'sensory/async_station_get',
            {
                date : DT.date,
                rta_id : rta_id,
                batch : jQuery('#batch').val().trim(),
                t : (new Date).getTime()
            },
            function(r) {
                
                jQuery('#'+ id +'_seat').val(r);
            }
        );
    }*/
}