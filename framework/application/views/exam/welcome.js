jQuery(function(){
   
   jQuery('#proceed').click(function(){
       
       var url = jQuery.url(), date = url.segment(4), rta_id = 0, code = 0, batch = '';
       
       if(EXAM.queue) {
           
           jQuery.each(EXAM.queue.data, function(key, value){
               
               /* Take the first data in the loop. */
               rta_id = value.rta;
               code = value.code;
               batch = value.batch.replace(' ', '-');
               
               return false; /* Break from this loop. */
           });

           GBL.go('exam/actual/'+ rta_id +'/'+ code +'/?batch='+ batch +'&date='+ date);
       }
   });
   
   if(! EXAM.queue.data.length) {
       
       Popup.dialog({
            title : 'System Message',
            message : '<div>There are no questionnaires assigned for this station yet.</div>',
            buttons: ['Okay', 'Cancel'],
            width: '420px'
       });
       
       jQuery('.popup .popup_buttons').hide();
       jQuery('#proceed').hide();
   }               
});

var EXAM = new function() { this.queue = null; };