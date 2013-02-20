jQuery(function(){
    
    jQuery('input[name=instruct]').click(function(){

       if(jQuery(this).val() == 'yes') {

           jQuery('#instruction_wrapper').show();
           jQuery('#instruction').focus();

       } else jQuery('#instruction_wrapper').hide();
    });    
});

var STEP_1 = new function(){
    
    this.submit = function() {
        
        var i = '';
        if(jQuery('input[name=instruct]:checked').val() == 'yes') {
            i = jQuery('#instruction').val().trim();
        }
        
        GBL.loader();
        jQuery.post(
            DOCROOT +'sensory/async_ct_step_1',
            {
                rta_id : Q.rta_id,
                i : i,
                t : (new Date).getTime()
            },
            function(){ GBL.go('sensory/create_test/'+ Q.rta_id +'/?step=2'); } /* Proceed to step 2. */
        );
    }
    
}