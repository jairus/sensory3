var AFFECTIVE  = new function() {
   
   this.toggle_scale = function(scale) {
       
       if(scale > 10) {
           
           jQuery('#scale_fields').html('<div style="text-align: left">Limit exceeded. Please choose a smaller number.</div>');
           return;
       }
       
       var label = [];
       var html = '';
       var start = 1;
       var label_number = scale;
       
       if(SENSORY.item_name == 'liking') {
           
           label[9] = 'Like extremely';
           label[8] = 'Like very much';
           label[7] = 'Like moderately';
           label[6] = 'Like slightly';
           label[5] = 'Neither like nor dislike';
           label[4] = 'Dislike slightly';
           label[3] = 'Dislike moderately';
           label[2] = 'Dislike very much';
           label[1] = 'Dislike extremely';
       }
       else
       if(SENSORY.item_name == 'compatibility') {
           
           label[9] = 'Extremely compatible';
           label[8] = 'Very much compatible';
           label[7] = 'Moderately compatible';
           label[6] = 'Slightly compatible';
           label[5] = 'Neither compatible nor incompatible';
           label[4] = 'Slightly incompatible';
           label[3] = 'Moderately incompatible';
           label[2] = 'Very much incompatible';
           label[1] = 'Extremely incompatible';
       }     
       
       if(scale == 7) start = 2;
       else if(scale == 5) start = 3;
       
       if(scale == 9 || scale == 7 || scale == 5) {
           
           if(scale == 7) scale = 8;
           if(scale == 5) scale = 7;
           
       }
       else
       if(scale == 2) {
           
           if(SENSORY.item_name == 'liking') {
               label[2] = 'Like';               
               label[1] = 'Dislike';
           }
           else
           if(SENSORY.item_name == 'compatibility') {
               
               label[2] = 'Compatible';
               label[1] = 'Inompatible';
           }
       }
       else
       if(scale == 3) {
           
           if(SENSORY.item_name == 'liking') {
               label[3] = 'Like';
               label[2] = 'Neither';
               label[1] = 'Dislike';
           }
           else
           if(SENSORY.item_name == 'compatibility') {
               
               label[3] = 'Compatible';
               label[2] = 'Neither compatible nor incompatible';
               label[1] = 'Inompatible';
           }           
       }
       else label = [];
       
       var x;
       var scale_data = jQuery('#scale_other_data').val();
       var row = [];
       if(scale_data != '') {row = scale_data.split('&');}
       
       if( label_number != 9 &&
           label_number != 7 &&
           label_number != 5 &&
           label_number != 3 &&
           label_number != 2 ) {
           
           /* Custom values. */           
           for(x=scale; x>=1; x--) {

               var tmp = 'scale_'+ x;
               var field = '';
               var value = '';
               var key = 'scale_'+ x;

               for(var y in row) {

                   if(row[y].indexOf(tmp) > -1) {

                       field = row[y].split('=');
                       value = field[1];                           
                   }
               }

               html += '<div style="padding-bottom: 1px">'+ x +' <input id="edit_'+ key +'" onkeyup="SENSORY.item_data_storeso()" type="text" value="'+ value +'" /></div>';                   
           }
       }
       else {
           
           /* Deafult values. */
           for(x=scale; x>=start; x--) {
           
               var caption = (label[x]) ? label[x] : '';

               html += '<div style="padding-bottom: 1px">'+ label_number +' <input id="edit_scale_'+ label_number +'" onkeyup="SENSORY.item_data_storeso()" type="text" value="'+ caption +'" /></div>';
               label_number--;
           }
       }
       
       jQuery('#scale_fields').html(html);
   }
   
   this.toggle_scale_jar = function(scale) {
       
       if(scale > 10) {
           
           jQuery('#scale_fields').html('<div style="text-align: left">Limit exceeded. Please choose a smaller number.</div>');
           return;
       }
       
       var label = [];
       var html = '';
       var start = 1;
       
       if(scale == 5) {
           
           label[1] = 'Too little';
           label[2] = 'Somewhat too little';
           label[3] = 'Just About Right';
           label[4] = 'Somewhat too much';
           label[5] = 'Too much';
       }
       else
       if(scale == 3) {
           label[1] = 'Too little';
           label[2] = 'Just About Right';
           label[3] = 'Somewhat too much';
           
       }
       else label = [];
       
       var x;
       var scale_data = jQuery('#scale_other_data').val();
       var row = [];
       if(scale_data != '') {row = scale_data.split('&');}
       
       if( scale != 5 &&
           scale != 3 ) {

           for(x=1; x<=scale; x++) {

               var tmp = 'scale_'+ x;
               var field = '';
               var value = '';
               var key = 'scale_'+ x;

               for(var y in row) {

                   if(row[y].indexOf(tmp) > -1) {

                       field = row[y].split('=');
                       value = field[1];                           
                   }
               }

               html += '<div style="padding-bottom: 1px">'+ x +' <input id="edit_'+ key +'" onkeyup="SENSORY.item_data_storeso()" type="text" value="'+ value +'" /></div>';                   
           }
       }
       else {
           
           for(x=start; x<=scale; x++) {
           
               var caption = (label[x]) ? label[x] : '';

               html += '<div style="padding-bottom: 1px">'+ x +' <input id="edit_scale_'+ x +'" onkeyup="SENSORY.item_data_storeso()" type="text" value="'+ caption +'" /></div>';               
           }
       }
       
       jQuery('#scale_fields').html(html);
   }
}