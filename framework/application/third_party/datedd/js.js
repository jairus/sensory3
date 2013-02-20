if( typeof(MONTH_OBJ) == 'undefined' ||
    typeof(DAY_OBJ) == 'undefined' ||
    typeof(YEAR_OBJ) == 'undefined' ||
    typeof(DAY_DEF) == 'undefined' ||
    typeof(TODAY) == 'undefined') {

    /*if(typeof(MONTH_OBJ) == 'undefined') console.log('MONTH_OBJ : Month\'s selectbox element is undefined.');
    if(typeof(DAY_OBJ) == 'undefined') console.log('DAY_OBJ : Day\'s selectbox element is undefined.');
    if(typeof(YEAR_OBJ) == 'undefined') console.log('YEAR_OBJ : Year\'s selectbox element is undefined.');
    if(typeof(DAY_DEF) == 'undefined') console.log('DAY_DEF : Default day is undefined.');
    if(typeof(TODAY) == 'undefined') console.log('TODAY : Today string is undefined.');*/
}

else {
    
    jQuery(function(){

        xyDATE_DD.init(MONTH_OBJ, DAY_OBJ, YEAR_OBJ);

        xyDATE_DD.month_obj.change(function(){        
            xyDATE_DD.change_day();
        });

        xyDATE_DD.year_obj.change(function(){
            xyDATE_DD.change_day();
        });

        jQuery('#'+ MONTH_OBJ +', #'+ DAY_OBJ +', #'+ YEAR_OBJ).bind('change keyup keydown', function(){
            xyDATE_DD.calculate_age();
            //alert(xyDATE_DD.age);
        });

        xyDATE_DD.change_day();
        
        /* Select the fefault day on first load. */
        if(DAY_DEF > 0) { xyDATE_DD.day_obj[0].selectedIndex = parseInt(DAY_DEF, 10); }
    });

    var xyDATE_DD = new function() {

        this.month_obj = null;
        this.day_obj = null;
        this.year_obj = null;

        this.age = null;

        this.init = function(month, day, year) {

            this.month_obj = jQuery('#'+ month);
            this.day_obj = jQuery('#'+ day);
            this.year_obj = jQuery('#'+ year);
        }

        this.change_day = function() {

            var bd_month = this.month_obj.val();
            var bd_year = this.year_obj.val();
            if(bd_year == '') {
                
                this.day_obj[0].selectedIndex = 0;
                return;
            }

            var bd_day_end = 31;
            if( bd_month == '04' ||
                bd_month == '07' ||
                bd_month == '09' ||
                bd_month == '11') { bd_day_end = 30; }

            bd_day_end = ((bd_month == '02') ? ((bd_year%4 == 0) ? 29 : 28 ) : bd_day_end);

            var bd_day = ((this.day_obj.val() != '') ? ((this.day_obj.val() > bd_day_end) ? bd_day_end : this.day_obj.val()) : '');
            
            var html = '';
            for(var x=1; x<=bd_day_end; x++) {

                var xday = ((x<10) ? '0' : '') + x; /* Prepend "0". */
                html += '<option value="'+ xday +'"'+ ((bd_day == xday) ? ' selected="selected"' : '') +'>'+ x +'</option>';                
            }

            this.day_obj.html('<option value="">Day:</option>').append(html);
        }

        this.calculate_age = function() {

            if(this.month_obj.val() == '' || this.day_obj.val() == '' || this.year_obj.val() == '') {

                /* Don't do anything when either one of these is blank. */
                return;
            }

            var tmp = TODAY.split('-');

            var age = parseInt(tmp[0], 10) - parseInt(this.year_obj.val(), 10);

            if(parseInt(tmp[1], 10) == parseInt(this.month_obj.val(), 10)) {

                if(! (parseInt(tmp[2], 10) >= parseInt(this.day_obj.val(), 10))) {
                    age--;
                }
            }

            if(parseInt(tmp[1], 10) < parseInt(this.month_obj.val(), 10)) {
                age--;
            }

            this.age = age;
        }
    }
}