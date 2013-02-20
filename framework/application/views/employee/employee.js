jQuery(function(){
    
    if(SECTION == 'register') {
        
        jQuery('#sbu_other').bind('keydown keyup paste', function(){

            var item = -1;

            if(SBU.length) {

                var str = jQuery.trim(jQuery(this).val().toLowerCase());

                for(var x=0; x<SBU.length; x++) {

                    var tmp = SBU[x].toLowerCase();
                    if(tmp.indexOf(str) > -1) {

                        item = x;
                        break;
                    }
                }

                /* Must select the last option item ("Others") when no result. */
                if(item == -1 || str == '') {item = (jQuery('#sbu option').length - 1);}
                jQuery('#sbu')[0].selectedIndex = item;
            }
        });

        jQuery('#sbu_loc_other').bind('keydown keyup paste', function(){

            var item = -1;

            if(SBU_LOC.length) {

                var str = jQuery.trim(jQuery(this).val().toLowerCase());

                for(var x=0; x<SBU_LOC.length; x++) {

                    var tmp = SBU_LOC[x].toLowerCase();                
                    if(tmp.indexOf(str) > -1) {

                        item = x;
                        break;
                    }
                }

                /* Must select the last option item ("Others") when no result. */
                if(item == -1 || str == '') {item = (jQuery('#sbu_loc option').length - 1);}            
                jQuery('#sbu_loc')[0].selectedIndex = item;
            }
        });
    }
    else
    if(SECTION == 'login') {
        
        jQuery('#search').bind('keyup paste', function(){
            var search = jQuery.trim(jQuery(this).val());
            if(search.length > 2) {
                
                jQuery.post(
                    DOCROOT +'employee/async_search_name',
                    {search : search,
                        t : (new Date).getTime()
                    },
                    function(r){
                        if(r) {
                            jQuery('#search_result_div').html(r);
                        }
                    }
                );
            } else jQuery('#search_result_div').html('');
        });
        
        jQuery('#search_div').hide();
        jQuery('#search_trigger').click(function(){
            jQuery('#search_div').toggle(function(){
                
                if(! jQuery('#search_div').is(':hidden')) {
                
                    jQuery('#search').val('').focus();
                    jQuery('#search_result_div').html('');
                }
            });
            
            
        });
    }
});

var EMPLOYEE = new function() {
    
    this.button = null;
    this.data = null;
    
    this.register = function(obj) {
        
        this.button = obj;
        
        var employee_no = jQuery('#employee_no');
        var fname = jQuery('#fname');
        var mname = jQuery('#mname');
        var lname = jQuery('#lname');
        var bd_month = jQuery('#bd_month');
        var bd_day = jQuery('#bd_day');
        var bd_year = jQuery('#bd_year');
        var icon = jQuery('input[name=icon]:checked').val();
        var sbu = jQuery('#sbu');
        var sbu_other = jQuery('#sbu_other');
        var sbu_loc = jQuery('#sbu_loc');
        var sbu_loc_other = jQuery('#sbu_loc_other');
        var dept = jQuery('#dept');
        var office_email = jQuery('#office_email');
        var mobile_no = jQuery('#mobile_no');
        var local_no = jQuery('#local_no');
        var pattern = /^[a-zA-Z\.\ ]+$/;
        
        var args = 'employee_no='+ employee_no.val();
        args += '&fname='+ fname.val();
        args += '&mname='+ mname.val();
        args += '&lname='+ lname.val();
        args += '&bd_month='+ bd_month.val();
        args += '&bd_day='+ bd_day.val();
        args += '&bd_year='+ bd_year.val();
        args += '&icon='+ icon;
        args += '&sbu='+ sbu.val();
        args += '&sbu_other='+ sbu_other.val();
        args += '&sbu_loc='+ sbu_loc.val();
        args += '&sbu_loc_other='+ sbu_loc_other.val();
        args += '&dept='+ dept.val();
        args += '&office_email='+ office_email.val();
        args += '&mobile_no='+ mobile_no.val();
        args += '&local_no='+ local_no.val();
        
        this.data = args;
        
        if(GBL.blank(fname)) {
            
            Popup.dialog({
                title : 'OOOPS !!! BLANK',
                message : 'Please fill-up the "<b>firstname</b>" field.',
                buttons: ['Okay', 'Cancel'],
                buttonClick: function() { fname.focus(); },
                width: '420px'
            });
            
            return;
            
        } else {
            
            if(pattern.test(fname.val()) == false) {
                
                Popup.dialog({
                    title : 'OOOPS !!! INVALID',
                    message : 'The "<b>firstname</b>" must not contain <b>invalid characters</b>.',
                    buttons: ['Okay', 'Cancel'],
                    buttonClick: function() { fname.focus(); },
                    width: '420px'
                });
                
                return;
            }
        }
        
        if(GBL.blank(mname)) {
            
            Popup.dialog({
                title : 'OOOPS !!! BLANK',
                message : 'Please fill-up the "<b>middlename</b>" field.',
                buttons: ['Okay', 'Cancel'],
                buttonClick: function() { mname.focus(); },
                width: '420px'
            });
            
            return;
            
        } else {
            
            if(pattern.test(mname.val()) == false) {
                
                Popup.dialog({
                    title : 'OOOPS !!! INVALID',
                    message : 'The "<b>middlename</b>" must not contain <b>invalid characters</b>.',
                    buttons: ['Okay', 'Cancel'],
                    buttonClick: function() { mname.focus(); },
                    width: '420px'
                });

                return;
            }
        }
        
        if(GBL.blank(lname)) {
            
            Popup.dialog({
                title : 'OOOPS !!! BLANK',
                message : 'Please fill-up the "<b>lastname</b>" field.',
                buttons: ['Okay', 'Cancel'],
                buttonClick: function() { lname.focus(); },
                width: '420px'
            });

            return;
            
        } else {
            
            if(pattern.test(lname.val()) == false) {
                
                Popup.dialog({
                    title : 'OOOPS !!! INVALID',
                    message : 'The "<b>lastname</b>" must not contain <b>invalid characters</b>.',
                    buttons: ['Okay', 'Cancel'],
                    buttonClick: function() { lname.focus(); },
                    width: '420px'
                });

                return;
            }
        }
        
        if(GBL.blank(bd_month)) {
            
            Popup.dialog({
                title : 'OOOPS !!! BLANK',
                message : 'Please fill-up the "<b>month of birth</b>".',
                buttons: ['Okay', 'Cancel'],
                buttonClick: function() { bd_month.focus(); },
                width: '420px'
            });

            return;
            
        }
        
        if(GBL.blank(bd_day)) {
            
            Popup.dialog({
                title : 'OOOPS !!! BLANK',
                message : 'Please fill-up the "<b>day of birth</b>".',
                buttons: ['Okay', 'Cancel'],
                buttonClick: function() { bd_day.focus(); },
                width: '420px'
            });

            return;

        }
        
        if(GBL.blank(bd_year)) {
            
            Popup.dialog({
                title : 'OOOPS !!! BLANK',
                message : 'Please fill-up the "<b>year of birth</b>".',
                buttons: ['Okay', 'Cancel'],
                buttonClick: function() { bd_year.focus(); },
                width: '420px'
            });

            return;
            
        }
        
        if(icon == undefined) {
            
            Popup.dialog({
                title : 'OOOPS !!! BLANK',
                message : 'Please choose the appropriate "<b>gender</b>".',
                buttons: ['Okay', 'Cancel'],
                width: '420px'
            });

            return;
            
        }
        
        if(GBL.blank(sbu)) {
            
            Popup.dialog({
                title : 'OOOPS !!! BLANK',
                message : 'Please select from "<b>SBU</b>" field.',
                buttons: ['Okay', 'Cancel'],
                width: '420px'
            });

            return;
        }
        else
        if(sbu.val() == 'other' && GBL.blank(sbu_other)) {
            
            Popup.dialog({
                title : 'OOOPS !!! BLANK',
                message : 'Please fill-up the "<b>SBU</b>" field.',
                buttons: ['Okay', 'Cancel'],
                buttonClick: function() { sbu_other.focus(); },
                width: '420px'
            });

            return;
        }
        
        if(GBL.blank(sbu_loc)) {
            
            Popup.dialog({
                title : 'OOOPS !!! BLANK',
                message : 'Please select from "<b>location</b>" field.',
                buttons: ['Okay', 'Cancel'],
                width: '420px'
            });

            return;
        }
        else
        if(sbu_loc.val() == 'other' && GBL.blank(sbu_loc_other)) {
            
            Popup.dialog({
                title : 'OOOPS !!! BLANK',
                message : 'Please fill-up the "<b>location</b>" field.',
                buttons: ['Okay', 'Cancel'],
                buttonClick: function() { sbu_loc_other.focus(); },
                width: '420px'
            });

            return;
            
        }
        
        if(GBL.blank(dept)) {
            
            Popup.dialog({
                title : 'OOOPS !!! BLANK',
                message : 'Please select from "<b>department</b>" field.',
                buttons: ['Okay', 'Cancel'],
                buttonClick: function() { dept.focus(); },
                width: '420px'
            });

            return;
            
        }
        
        if(GBL.blank(office_email)) {
            
            Popup.dialog({
                title : 'OOOPS !!! BLANK',
                message : 'Please fill-up the "<b>office email</b>" field.',
                buttons: ['Okay', 'Cancel'],
                buttonClick: function() { office_email.focus(); },
                width: '420px'
            });

            return;
            
        } else {
            
            pattern = /^[a-zA-Z0-9._-]{3,}@[a-zA-Z0-9-.]+\.[a-zA-Z.]{2,6}$/;
            
            if(pattern.test(office_email.val()) == false) {
                
                Popup.dialog({
                    title : 'OOOPS !!! INVALID',
                    message : 'Please make the email in <b>correct</b> format.',
                    buttons: ['Okay', 'Cancel'],
                    buttonClick: function() { office_email.focus(); },
                    width: '420px'
                });

                return;
                
            }
        }
        
        if(GBL.blank(mobile_no)) {
            
            Popup.dialog({
                title : 'OOOPS !!! BLANK',
                message : 'Please fill-up the "<b>mobile #</b>" field.',
                buttons: ['Okay', 'Cancel'],
                buttonClick: function() { mobile_no.focus(); },
                width: '420px'
            });

            return;
            
        }
        
        if(GBL.blank(employee_no)) {
            
            Popup.dialog({
                title : 'OOOPS !!! BLANK',
                message : 'Are you sure you want to register this User without the "<b>employee #</b>"?',
                buttons: ['Yes', 'No, I cancel'],
                buttonClick: function(button) {
                    
                    if(button == 'Yes') EMPLOYEE.register_process();
                    else employee_no.focus();
                    
                },
                width: '420px'
            });

            return;
            
        } else {
            
            this.register_process();
        }
    }
    
    this.register_process = function() {
        
        GBL.loader();
        
        jQuery.post(DOCROOT +'employee/async_register',
            {   data : this.data,
                t : (new Date).getTime()
            },
            function(r) {
                
                GBL.loader(false);
                
                if(r.confirm) { /* When similar user. */
                    
                    /* Tell PHP that action was already confirmed, so proceed. */
                    EMPLOYEE.data += '&similar_user_confirmation_done=1';
                    
                    Popup.dialog({
                        title : 'CONFIRM',
                        message : r.msg,
                        buttons: ['Yes', 'No, I Cancel'],
                        buttonClick: function(button) { if(button == 'Yes') EMPLOYEE.register_process(); },
                        width: '420px'
                    });
                    
                } else {
                    
                    Popup.dialog({
                        title : 'SUCCESS',
                        message : r.msg,
                        buttons: ['Okay'],
                        buttonClick: function() { window.location.href = r.go; },
                        width: '420px'
                    });
                }
            },
            'json'
        );
    }
    
    this.select_name = function(id, eid) {
        
        if(eid == 0) eid = '';
        jQuery('#employee_no').val(eid);
        
        jQuery('input[name=search_result_name]').each(function(){
           if(jQuery(this).val() == id) {
               jQuery(this).attr('checked', true);
           } 
        });
    }
    
    this.login = function(btn) {
        
        var employee_no = jQuery('#employee_no').val();
        
        var srn = jQuery('input[name=search_result_name]');
        var rid = 0; /* Record ID. */
        if(srn && srn.length) {
            
            rid = jQuery('input[name=search_result_name]:checked').val();
        }
        
        GBL.loader();
        
        jQuery.post(
            DOCROOT +'employee/async_login',
            {
                rid : rid,
                eid : employee_no,
                password : jQuery('#password').val(),
                t : (new Date).getTime()
            },
            function(r){
                
                if(r.title != undefined) {
                    
                    GBL.loader(false);
                    
                    jQuery('#login_icon_wrapper span').css('color', '#FFFFFF').html(r.msg);
                    jQuery('#login_icon_wrapper').attr('class', 'login_icon_wrapper_red');
                    jQuery('#login_icon').removeClass('login_icon_green').addClass('login_icon_red');
                    
                } else window.location.href = DOCROOT +'home';
            },
            'json'
        );
    }
    
    this.keypress_login = function(e) {
        
        var key = GBL.get_keypressed(e);
        if(key == 13) {
            this.login(jQuery('#login'));
        }
    }
}