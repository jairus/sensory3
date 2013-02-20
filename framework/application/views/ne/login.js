var NE = new function() {
    
    this.submit = function() {
        
        var fname = jQuery('#fname');
        var lname = jQuery('#lname');
        var age = jQuery('#age');
        var icon = jQuery('input[name=icon]:checked').val();
        var pattern = /^[a-zA-Z\.\ ]+$/;
        
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
        
        if(GBL.blank(age)) {
            
            Popup.dialog({
                title : 'OOOPS !!! BLANK',
                message : 'Please enter your "<b>age</b>".',
                buttons: ['Okay', 'Cancel'],
                buttonClick: function() { age.focus(); },
                width: '420px'
            });
            
            return;
            
        } else {
            
            if(age.val() <= 13) {
                
                Popup.dialog({
                    title : 'OOOPS !!! INVALID',
                    message : 'You are <b>too young</b> for this, please enter your real age.',
                    buttons: ['Okay', 'Cancel'],
                    buttonClick: function() { age.focus(); },
                    width: '420px'
                });

                return;
            }
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
        
        GBL.loader();
        
        jQuery.post(DOCROOT +'ne/async_login',
            {   
                fname : fname.val(),
                lname : lname.val(),                
                age : age.val(),
                gender : icon,
                t : (new Date).getTime()
            },
            function(r) {
                
                window.location.href = DOCROOT +'home';
                
            }, 'json'
        );
    }
}