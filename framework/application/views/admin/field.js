var FIELD = new function() {
    
    this.button = null;
    this.editable_field_orig_color = [];
    
    this.mark_to_edit = function(obj, id) {
        
        obj = jQuery(obj);
        var checker = jQuery('#checkbox_edit_'+ id);
        
        if(checker.val().trim() != obj.val().trim()) {
            checker.attr('checked', true);
            obj.css('color', '#006600');
            this.editable_field_orig_color[id] = '#006600';
        }
        else {
            checker.attr('checked', false);
            obj.css('color', '#000');
            this.editable_field_orig_color[id] = '#000';
        }
    }
    
    this.mark_to_delete = function(obj, id) {
        
        var color = (this.editable_field_orig_color[id]) ? this.editable_field_orig_color[id] : '#000';
        
        var field = jQuery('#field_'+ id);
        obj = jQuery(obj);

        if(obj.attr('checked') == 'checked') {
            field.css('color', '#990000');
            field.attr('disabled', true);
        }
        else {
            field.css('color', color);
            field.attr('disabled', false);
        }
    }
    
    this.submit = function(confirmed) {
        
        var to_delete = [];
        var to_edit = [];
        
        jQuery("input[id^='checkbox_']:checked").each(function(){
            
            var field = jQuery(this).attr('id');
            var tmp = field.split('_');
            var id = tmp[tmp.length - 1];
            
            if(field.indexOf('delete_') > -1) { to_delete.push(id); }
            if(field.indexOf('edit_') > -1) { to_edit[id] = jQuery('#field_'+ id).val(); }
            
        });
        
        if(to_delete.length && ! confirmed) {
            
            Popup.dialog({
                title : 'CONFIRM',
                message : 'Are you sure you want to <b>delete</b> the selected items?',
                buttons: ['Okay', 'No, I cancel'],
                buttonClick : function(button) {
                    
                    if(button == 'Okay') {
                        FIELD.submit(true);
                    }
                },
                width: '420px'
            });
        }
        else {
            
            var to_add = [];
            jQuery("input[id^='field_add_']").each(function(){
                to_add.push(jQuery(this).val());
            });
            
            if(to_edit.length == 0 && to_add.length == 0 && ! to_delete.length) {
                
                Popup.dialog({
                    title : 'OOOPS !!! NO CHANGES',
                    message : 'You have not changed anything yet.',
                    buttons: ['Okay', 'Cancel'],
                    width: '420px'
                });

                return;
            }
            
            var tmp_edit = "";
            
            if(to_edit.length) {
                
                jQuery.each(to_edit, function(i, item){
                    
                    if(item) tmp_edit += '&'+ i +'='+ item; //to_edit[i]
                });
                
                if(tmp_edit != '') tmp_edit = tmp_edit.substring(1);                
            }
            
            GBL.loader();
            
            jQuery.post(
                DOCROOT +'admin/async_field',
                {
                    to_delete   : to_delete.toString(),
                    to_edit     : tmp_edit,
                    to_add      : to_add.toString(),
                    section     : SECTION,
                    target      : TARGET,
                    t           : (new Date).getTime()
                },
                function(r) {

                    if(r) { 
                        
                        Popup.dialog({
                            title : r.title,
                            message : r.msg,
                            buttons: ['Okay', 'Cancel'],
                            buttonClick : function() {
                                
                                window.location.href = r.go;
                            },
                            width: '420px'
                        });
                    }

                    GBL.loader(false);
                },
                'json'
            );
        }
    }
    
    this.del_chk_all = function() {
        
        var on = jQuery('#del_chk_all').is(':checked');
        jQuery('input[name=del_chk]').each(function(){
            
            jQuery(this).attr('checked', on);
        });
    }
    
    this.add_field_count = 0;
    this.add_field_cancel = function(id) {
        
        jQuery('#tr_'+ id).remove();
    }
    
    this.add_field_create = function() {
        
        if(this.add_field_count <= 0) this.add_field_count = (jQuery('#table_data tr').length - 1);
        this.add_field_count++;
        
        var html = '<tr id="tr_'+ this.add_field_count +'"><td align="center"><b>'+ this.add_field_count +'</b></td><td style="padding: 2px"><input id="field_add_'+ this.add_field_count +'" type="text" maxlength="100" style="width: 500px" /></td><td style="padding-left: 5px" colspan="3"><a title="cancel" href="javascript:FIELD.add_field_cancel('+ this.add_field_count +')">Cancel</a></td></tr>';
        jQuery('#table_data').append(html).show();
        
        jQuery('#field_add_'+ this.add_field_count).focus();
    }
}