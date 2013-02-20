jQuery(function(){
    
    HOME.admin_forward_ip();
    
    jQuery('#employee_wrapper').centerElement().css('top', '70px');
});

var HOME = new function() {
    
    this.admin_forward_ip = function() {
        
        jQuery.post( DOCROOT +'home/async_admin_forward_ip', { t : (new Date).getTime() } );
        
        setTimeout("HOME.admin_forward_ip()", 3000);
    }
}