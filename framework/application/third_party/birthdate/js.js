jQuery(function(){
    
    jQuery('#bd_month').change(function(){
        
        var bd_month = jQuery(this).val();
        var bd_year = jQuery('#bd_year').val();
        if(bd_year == '') bd_year = (new Date).getFullYear();

        var bd_day_end = 31;
        if( bd_month == '04' ||
            bd_month == '07' ||
            bd_month == '09' ||
            bd_month == '11') {bd_day_end = 30;}
        
        bd_day_end = ((bd_month == '02') ? ((bd_year%4 == 0) ? 29 : 28 ) : bd_day_end);

        var bd_day = ((jQuery('#bd_day').val() != '') ? ((jQuery('#bd_day').val() > bd_day_end) ? bd_day_end : jQuery('#bd_day').val()) : '');
        if(BD_DAY > 0) bd_day = BD_DAY;
        var html = ((bd_month == '00') ? '<option value="">Month:</option>' : '');

        for(var x=1; x<=bd_day_end; x++) {
            
            var xday = ((x<10) ? '0' : '') + x; /* Prepend "0". */
            html += '<option value="'+ xday +'"'+ ((bd_day == xday) ? ' selected="selected"' : '') +'>'+ x +'</option>';
        }
        
        jQuery('#bd_day').html((BD_DAY > 0) ? '' : '<option value="">Day:</option>').append(html);
    });
    
    jQuery('#bd_year').change(function(){
        
        var bd_month = jQuery('#bd_month').val();
        var bd_year = jQuery(this).val();
        if(bd_year == '') bd_year = (new Date).getFullYear();

        var bd_day_end = 31;
        if( bd_month == '04' ||
            bd_month == '07' ||
            bd_month == '09' ||
            bd_month == '11') {bd_day_end = 30;}
        
        bd_day_end = ((bd_month == '02') ? ((bd_year%4 == 0) ? 29 : 28 ) : bd_day_end);

        var bd_day = ((jQuery('#bd_day').val() != '') ? ((jQuery('#bd_day').val() > bd_day_end) ? bd_day_end : jQuery('#bd_day').val()) : '');
        if(BD_DAY > 0) bd_day = BD_DAY;
        var html = ((bd_month == '00') ? '<option value="">Month:</option>' : '');

        for(var x=1; x<=bd_day_end; x++) {
            
            var xday = ((x<10) ? '0' : '') + x; /* Prepend "0". */
            html += '<option value="'+ xday +'"'+ ((bd_day == xday) ? ' selected="selected"' : '') +'>'+ x +'</option>';
        }
        
        jQuery('#bd_day').html('<option value="">Day:</option>').append(html);
    });
    
    jQuery('#bd_year, #bd_month, #bd_day').bind('change keyup keydown', function(){
        xyBD.ageCalc();
    });
    
    jQuery('#bd_month').trigger('change');
});

var xyBD = new function() {
    
    this.age = null;
    
    this.ageCalc = function() {
        
        var bd_month = jQuery('#bd_month');
        var bd_day = jQuery('#bd_day');
        var bd_year = jQuery('#bd_year');
        
        if(bd_month.val() == '' || bd_day.val() == '' || bd_year.val() == '') {
            
            /* Don't do anything when either one of these is blank. */
            return;
        }
        
        var tmp = TODAY.split('-');
        
        var age = parseInt(tmp[0], 10) - parseInt(bd_year.val(), 10);
        
        if(parseInt(tmp[1], 10) == parseInt(bd_month.val(), 10)) {
            
            if(! (parseInt(tmp[2], 10) >= parseInt(bd_day.val(), 10))) {
                age--;
            }
        }
        
        if(parseInt(tmp[1], 10) < parseInt(bd_month.val(), 10)) {
            age--;
        }
        
        jQuery('#age').html('Age: <b>'+ age +'</b>');
        
        this.age = age;
    }
}