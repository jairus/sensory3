var ADMIN =  new function() {
    
    this.login = function() {
        
        var un = jQuery('#username');
        var pw = jQuery('#password');
        
        if(GBL.blank(un)) {
            
            Popup.dialog({
                title : 'ERROR',
                message : 'Please enter your <b>username</b>.',
                buttons: ['Okay', 'Cancel'],
                buttonClick: function() { un.focus(); },
                width: '420px'
            });

            return;
        }
        else
        if(GBL.blank(pw)) {
            
            Popup.dialog({
                title : 'ERROR',
                message : 'Please enter your <b>password</b>.',
                buttons: ['Okay', 'Cancel'],
                buttonClick: function() { pw.focus(); },
                width: '420px'
            });

            return;
        }
        
        GBL.loader();
        
        jQuery.post(
            DOCROOT +'admin/async_login',
            {   username : un.val(),
                password : pw.val(),
                t : (new Date).getTime()
            },
            function(r) {
                
                if(! r.go) {

                    jQuery('#login_icon_wrapper span').css('color', '#FFFFFF').html(r.msg);
                    jQuery('#login_icon_wrapper').attr('class', 'login_icon_wrapper_red');
                    jQuery('#login_icon').removeClass('login_icon_green').addClass('login_icon_red');

                } else window.location.href = DOCROOT + r.go;
                
                GBL.loader(false);
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
    
    this.user_id = 0;
    this.do_user_delete = function(confirmed, name) {
        
        if(! confirmed) { 
            
            Popup.dialog({
                title : 'CONFIRM',
                message : 'Are you sure you want to <b>delete</b> "'+ name +'" ?',
                buttons: ['Okay', 'Cancel'],
                buttonClick : function(button) { if(button == 'Okay') { ADMIN.do_user_delete(true, name); } },
                width: '420px'
            });
            
            return;
        } 
        
        jQuery.post(
            DOCROOT +'admin/async_user_delete',
            {
                id : this.user_id,
                t : (new Date).getTime()
            },
            function() {
                
                GBL.loader(false);
                jQuery('#xy_list tr#'+ ADMIN.user_id).remove();
            }
        );        
    }
    
    this.do_user_ae = function(id) {
        
        if(! id) id = 0;
        
        ADMIN.user_id = id;
        GBL.loader();
        
        jQuery.post(
            DOCROOT +'admin/async_user_ae_field',
            {
                id : id,
                t : (new Date).getTime()
            },
            function(r) {
                
                GBL.loader(false);
                
                if(! POPUPJS.obj) POPUPJS.obj = new Popup.Window('popupjs_wrapper');
                POPUPJS.obj.hide();
                POPUPJS.obj.show();
                
                jQuery('#popupjs_wrapper .popup_content p').html(r);
                jQuery('#popupjs_wrapper .popup_title').html((id > 0) ? 'Edit this User' : 'Add new User');
                
                jQuery('#birthdate').datepicker();
                jQuery('#ui-datepicker-div').css('font-size', '12px');
            }
        );
    }
}

jQuery(function(){
    
    jQuery('#popupjs_wrapper #popupjs_btn_ok').click(function(){

        var level = jQuery('#level');
        var fname = jQuery('#fname');
        var mname = jQuery('#mname');
        var lname = jQuery('#lname');
        var password = jQuery('#password');
        var username = jQuery('#username');
        var email = jQuery('#email');
        var birthdate = jQuery('#birthdate');
        
        if(level.val().trim() == '') {

            level.focus();
            return;
        }
        else
        if(birthdate.val().trim() == '') {
            
            birthdate.focus();
            return;
        }
        else
        if(fname.val().trim() == '') {

            fname.focus();
            return;
        }
        else
        if(mname.val().trim() == '') {

            mname.focus();
            return;
        }
        else
        if(lname.val().trim() == '') {

            lname.focus();
            return;
        }
        else
        if(password.val().trim() == '') {

            password.focus();
            return;
        }

        GBL.loader();

        jQuery.post(
            DOCROOT + 'admin/async_user_ae',
            {
                id          : ADMIN.user_id,
                level       : level.val(),
                birthdate   : birthdate.val(),
                superior    : jQuery('#superior_choice').val(),
                employee_no : jQuery('#employee_no').val(),
                username    : username.val(),
                fname       : fname.val(),
                mname       : mname.val(),
                lname       : lname.val(),
                password    : password.val(),
                email       : email.val(),
                t           : (new Date).getTime()
            },
            function(r) {

                GBL.loader(false);
                POPUPJS.obj.hide();

                if(r) {

                    Popup.dialog({
                        title : 'SUCCESS',
                        message : 'You have successfully '+ ((ADMIN.user_id > 0) ? ' updated this User' : ' added a new User') +'.',
                        buttons: ['Okay', 'Cancel'],
                        buttonClick : function() {

                            if(ADMIN.user_id > 0) {

                                jQuery('#xy_list tr#'+ ADMIN.user_id +' td').each(function(){
                                    
                                    if(jQuery(this).attr('aria-describedby') == 'xy_list_employee_no') jQuery(this).attr('title', r.employee_no).html(r.employee_no);
                                    else
                                    if(jQuery(this).attr('aria-describedby') == 'xy_list_level') jQuery(this).attr('title', r.level).html(r.level);
                                    else
                                    if(jQuery(this).attr('aria-describedby') == 'xy_list_lastname') jQuery(this).attr('title', r.lastname +', '+ r.firstname +' '+ r.middlename).html('<b>'+ r.lastname +'</b>, '+ r.firstname +' '+ r.middlename);
                                    else
                                    if(jQuery(this).attr('aria-describedby') == 'xy_list_username') jQuery(this).attr('title', r.username).html(r.username);
                                    else
                                    if(jQuery(this).attr('aria-describedby') == 'xy_list_birthdate') jQuery(this).attr('title', r.birthdate).html(r.birthdate);
                                    else
                                    if(jQuery(this).attr('aria-describedby') == 'xy_list_password') jQuery(this).attr('title', r.password).html(r.password);

                                });

                            } else jQuery("#xy_list").trigger("reloadGrid");
                        },
                        width: '420px'
                    });                                
                }
            }, 'json'
        );
    });
});