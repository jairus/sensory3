var PO = new function() {
    
    this.login = function(btn) {
        
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
            DOCROOT +'po/async_login',
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
}