<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

include_once 'procedures.inc.php';

if(isset($_POST)) { extract($_POST); }
$item = array();

$attr_default = array(
    
    /*'overall' => array('label' => 'Overall'),
    'appearance' => array('label' => 'Appearance'),
    'color_outer' => array('label' => 'Color (Outer)'),
    'color_inner' => array('label' => 'Color (Inner)'),
    'crispiness' => array('label' => 'Crispiness'),
    'juiciness' => array('label' => 'Juiciness'),
    'meat_texture' => array('label' => 'Meat Texture'),
    'overall_flavor_blend' => array('label' => 'Overall Flavor Blend'),
    'overall_saltiness' => array('label' => 'Overall Saltiness')*/
    
);

$attr_custom = array();
if(! empty($library)) {
    
    foreach($library as $lib) { $attr_custom[strtolower(str_replace(' ', '_', $lib['label']))] = $lib; }    
    $attr_default = array_merge($attr_default, $attr_custom);
}

$attr_selection = array();
if($item_id > 0) {
    
    $item = $_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items'][$item_id - 1];
    
    $attr = trim($item['attr']);

    if($attr != '') {
    
        if(substr_count($attr, '[=ROW=]')) $attr = explode('[=ROW=]', $attr);
        else $attr = array($attr);

        foreach($attr as $row) {

            list($tmp, $detail) = explode('[=SETTING=]', $row);
            list($label, $setting) = explode('[=LABEL=]', $tmp);

            $id = strtolower(str_replace(array(' ','(', ')'), array('_', '', ''), $label));

            $attr_selection[$id] = array(
                'id' => (double) $attr_default[$id]['id'],
                'label' => $attr_default[$id]['label'],
                'detail' => str_replace('[=ITEM=]', '[row]', $detail),
                'setting' => $setting
            );
        }
    }
}

/*echo '<pre>';
print_r($attr_default);
print_r($attr_selection);
print_r($attr_custom);
echo '</pre>';
*/
//$selection = array_keys($attr_selection);
//$attr_custom_arr = array_keys($attr_custom);

foreach($attr_default as $id => $data) {
    
    if(! in_array($id, array_keys($attr_selection))) {
        
        if(in_array($id, array_keys($attr_custom))) $event_ondblclick = ' ondblclick="ITEM.library_ae_field(' . $data['id'] . ',\'' . $id . '\')" ';
        else $event_ondblclick = '';
        
        $library_items .= '<li' . $event_ondblclick . ' class="ui-state-highlight" id="' . $item_type . '_attr_' . $id . '">' . $data['label'] . '</li>';
    }
}

//echo '<pre>';
//print_r($attr_selection);

if(! empty($attr_selection)) {
    
    foreach($attr_selection as $id => $data) {

        if(in_array($id, array_keys($attr_custom))) {

            $js_ondblclick .= "ITEM.ondblclick['" . $item_type . '_attr_' . $id . "']=\"ITEM.library_ae_field(" . $data['id'] . ",'" . $id . "')\";\n\n";
        }

        $event = "ITEM.attr_edit('" . $item_type . "_attr_" . $id . "')";
        $attr_items .= '<li class="ui-state-highlight" id="' . $item_type . '_attr_' . $id . '" ondblclick="' . $event . '" detail="' . $attr_selection[$id]['detail'] . '"><b>' . $attr_selection[$id]['label'] . '</b> ' . $attr_selection[$id]['setting'] . ' Point Scale</li>';
    }
}
?>
<style type="text/css">
.connectedSortable {
    padding: 0;
    margin: 0;
    list-style: none;
    min-height: 200px;
}
.connectedSortable li {
    padding: 0;
    margin: 0;
    list-style: none;
    margin: 1px 0 1px 0;
    padding: 2px 5px 2px 5px;
    cursor: default
}

#<?php echo $item_type?>_library_manage_wrapper { 
    width: 180px;
    height: 135px;
    display: none
}
</style>
<script type="text/javascript">
var ITEM = new function() {
    
    this.id = <?php echo (double) $item_id?>;
    this.detail = null;
    this.item_type = '<?php echo $item_type?>';
    this.screen_code = '<?php echo (double) $screen_code?>';
    this.screen_count = <?php echo (double) $screen_count?>;
    this.ondblclick = [];
    
    this.ok = function() {
        
        var args = {
            type : 'item',
            item_id : ITEM.id,
            t : (new Date).getTime(),
            screen_code : SCREEN.selection_code,
            screen_count : SCREEN.selection_count,
            rta_id : Q.rta_id,

            item : this.item_type,
            attr : this.attr_selection().toString().replace(/,/g, '[=ROW=]')
        };
        
        if(SCREEN.itemlabel.trim() != '') jQuery.extend(args, { header : SCREEN.itemlabel });
        
        GBL.loader();
        
        jQuery.post(
            DOCROOT +'screen/async_session_update',
            args,
            function(r) {
                
                POPUPJS.obj.hide();
                GBL.loader(false);
                
                if(r) {
                    
                    if(ITEM.id == 0) { jQuery('#ul_'+ SCREEN.selection_code +'_'+ SCREEN.selection_count).append(r.html); }
                    SCREEN.save_flag[SCREEN.selection_code] = r.flag;
                    jQuery('#screen_ae_and_cancel_wrapper_'+ SCREEN.selection_code).toggle(r.flag);
                    
                    if(SCREEN.itemlabel != '') {
                        
                        jQuery(SCREEN.itemlabel_wrapper).html(SCREEN.itemlabel);
                        SCREEN.itemlabel = '';
                        SCREEN.itemlabel_wrapper = '';
                    }
                    
                } else { Popup.alert('<b>An ERROR has occured</b>.<br /><br />There\'s no response from your recent request.', { title : 'ERROR' }); }
            }, 'json'
        );
    }
    
    this.attr_selection = function() {
        
        var items = [];
        jQuery('#'+ this.item_type +'_attr_wrapper li').each(function(){
            
            var label = jQuery('b', jQuery(this)).html();
            
            var detail = jQuery(this).attr('detail');
            if(detail && detail.length) detail.replace(/\[row\]/g, '[=ITEM=]');
            else detail = '';
            
            var structure = jQuery(this).html(); /* <b>Appearance</b> desc Vertical 9 Point Scale. */
            var item = jQuery('b', jQuery(this)).html(); /* Appearance */
            
            var tmp = structure.stripTags(); /* Appearance desc Vertical 9 Point Scale. */
            tmp = tmp.replace(item, '').replace(/Point Scale/i, '').trim(); /* desc Vertical 9 */
        
            items.push(label +'[=LABEL=]'+ tmp +'[=SETTING=]'+ detail);
        });
        
        return items;
    }
    
    this.attr_edit = function(id) {
        
        jQuery("li[id^='"+ ITEM.item_type +"_attr_']").css('border', '1px solid #FCEFA1');
        jQuery('#'+ id).css('border', '1px solid #FFCC00');
        
        var structure = jQuery('#'+ id).html(); /* <b>Appearance</b> desc Vertical 9 Point Scale. */
        var item = jQuery('b', jQuery('#'+ id)).html(); /* Appearance */
        var detail = jQuery('#'+ id).attr('detail');
        if(detail) {
            
            detail = detail.replace(/\[dquote\]/g, '&quot;').replace(/\[quote\]/g, "&#39;");
            this.detail = detail.split('[row]');
            
        } else this.detail = [];
        
        var tmp = structure.stripTags(); /* Appearance desc Vertical 9 Point Scale. */
        tmp = tmp.replace(item, '').replace(/Point Scale/i, '').trim(); /* desc Vertical 9 */
        
        var setting = tmp.split(' ');
        var scale = parseInt(setting[2], 10);
        
        var html = '<table cellpadding="0" cellspacing="0"><tr>';
        html += '<td valign="top"><div style="padding-bottom: 5px"><b>'+ item +'</b></div>'+ this.scale_fields(scale) +'</td>';
        html += '<td valign="top" style="padding-left: 5px">'+ this.scale_select(scale) + this.order_select(setting[0]) + this.orientation_select(setting[1]) +
                '<div><input onclick="ITEM.attr_update(\''+ id +'\')" type="button" value="update" />' + 
                ' <input onclick="ITEM.attr_cancel(\''+ id +'\')" type="button" value="cancel" />' +
                '</div></td>';
        html += '</tr></table>'; 
        
        jQuery('#'+ ITEM.item_type +'_edit_attr_wrapper').html(html);
    }
    
    this.attr_update = function(id) {
        
        var item = jQuery('b', jQuery('#'+ id)).html(); /* Appearance */
        var order = jQuery('#'+ ITEM.item_type +'_edit_order').val();
        var orientation = jQuery('#'+ ITEM.item_type +'_edit_orientation').val();
        
        var scale = 0;
        
        if(jQuery('#'+ ITEM.item_type +'_edit_scale').val() != 'other') {
            
            jQuery('#'+ ITEM.item_type +'_edit_scale_other').val('');
            scale = jQuery('#'+ ITEM.item_type +'_edit_scale').val();
            
        } else scale = jQuery('#'+ ITEM.item_type +'_edit_scale_other').val();
        
        var html = '<b>'+ item +'</b> '+ order +' '+ ((orientation == 'h') ? 'Horizontal' : 'Vertical') +' '+ scale +' Point Scale';
        
        var detail = [];
        jQuery("input[id^='"+ ITEM.item_type +"_edit_scale_field_']").each(function(){
            
            detail.push(jQuery(this).val().replace(/,/g, '[comma]').replace(/"/g, '[dquote]').replace(/'/, "[quote]"));
        });
        
        jQuery('#'+ id).attr('detail', detail.toString().replace(/,/g, '[row]').replace(/\[comma\]/g, ',')).html(html);
        
    }
    
    this.attr_cancel = function(id) {
        
        jQuery('#'+ this.item_type +'_edit_attr_wrapper').html('');
        jQuery('#'+ id).css('border', '1px solid #FCEFA1');
    }
    
    this.scale_fields = function(scale) {
        
        var style = '';
        var scale_value = '';
        var html = '';
        var y = 0;
        var event = '';
        
        if(jQuery('#'+ ITEM.item_type +'_edit_scale_other').is(':hidden') == false) {
            
            event = ' onkeydown="ITEM.scale_init_data_update('+ scale +')" onkeyup="ITEM.scale_init_data_update('+ scale +')" ';            
        }
        
        for(var x=scale; x>=1; x--) {
            
            style = (x > 1) ? ' style="margin-bottom: 2px"' : '';
            scale_value = (this.detail[y]) ? this.detail[y] : '';
            y++;
            html += '<div'+ style +'>'+ x +' <input '+ event +' type="text" value="'+ scale_value +'" id="'+ ITEM.item_type +'_edit_scale_field_'+ x +'"></div>';
        }
        
        html = '<div id="'+ ITEM.item_type +'_edit_scale_field_wrapper">'+ html +'</div>';
        
        return html;
    }
    
    this.scale_fields_regenerate = function() {
        
        var scale = 0;
        
        if(jQuery('#'+ ITEM.item_type +'_edit_scale_other').is(':hidden')) {
            
            jQuery('#'+ ITEM.item_type +'_edit_scale_other').val('');
            scale = jQuery('#'+ ITEM.item_type +'_edit_scale').val();
            
        } else scale = jQuery('#'+ ITEM.item_type +'_edit_scale_other').val();
        
        STEP_3.scale_toggle(ITEM.item_type, scale);
        /*if(jQuery('#'+ ITEM.item_type +'_edit_scale_other').is(':hidden') == false) {
            
            STEP_3.scale_toggle(ITEM.item_type, scale);
            
        } else jQuery('#'+ ITEM.item_type +'_edit_scale_field_wrapper').html(this.scale_fields(scale));
        */
    }
    
    this.scale_select = function(scale) {
        
        var scale_arr = [];
            scale_arr[0] = 9;
            scale_arr[1] = 7;
            scale_arr[2] = 5;
            scale_arr[3] = 3;
            scale_arr[4] = 2;
        
        var display = 'none', value = '', selected = '';
        if(jQuery.inArray(scale, scale_arr) <= -1) {
            
            display = 'block';
            value = ' value="'+ scale +'" ';
            selected = ' selected="selected" ';
        }
        
        var html  = '<select onchange="GBL.toggle_other_field(this,\''+ ITEM.item_type +'_edit_scale_other_wrapper\',\''+ ITEM.item_type +'_edit_scale_other\'); ITEM.scale_fields_regenerate()" id="'+ ITEM.item_type +'_edit_scale">' +
            '<option value="">Select:</option>';
        for(var x=0; x<scale_arr.length; x++) {
            html += '<option'+ ((scale == scale_arr[x]) ? ' selected="selected"' : '') +' value="'+ scale_arr[x] +'">'+ scale_arr[x] +' Point Scale</option>';
        } html += '<option value="other"'+ selected +'>Other</option></select>';
        
        var scale_other_field = '<div id="'+ ITEM.item_type +'_edit_scale_other_wrapper" style="display: '+ display +'; margin-top: 2px"><input '+ value +' onkeypress="return GBL.numOnly(event)" onkeydown="ITEM.scale_fields_regenerate()" onkeyup="ITEM.scale_fields_regenerate()" type="text" style="width: 30px; text-align: right" maxlength="2" id="'+ ITEM.item_type +'_edit_scale_other"></div>';
        
        html = '<div style="padding-bottom: 5px"><b>Scale</b></div><div>'+ html + scale_other_field +'</div>';
        
        return html;
    }
    
    this.order_select = function(order) {
        
        var html = '<select id="'+ ITEM.item_type +'_edit_order">' +
        '<option'+ ((order == 'asc') ? ' selected="selected"' : '') +' value="asc">Ascending</option>' +
        '<option'+ ((order == 'desc') ? ' selected="selected"' : '') +' value="desc">Descending</option>' +
        '</select>';
        
        html = '<div style="padding: 5px 0 5px 0"><b>Ordering</b></div><div>'+ html +'</div>';
        
        return html;
    }
    
    this.orientation_select = function(orientation) {
        
        var html = '<select id="'+ ITEM.item_type +'_edit_orientation">' +
        '<option'+ ((orientation == 'Horizontal') ? ' selected="selected"' : '') +' value="h">Horizontal</option>' +
        '<option'+ ((orientation == 'Vertical') ? ' selected="selected"' : '') +' value="v">Vertical</option></select>';
        
        html = '<div style="padding: 5px 0 5px 0"><b>Orientation</b></div><div>'+ html +'</div>';
        
        return html;
    }
    
    this.scale_init_data_update = function() {
        
        var initial_detail = [];
        jQuery("input[id^='"+ ITEM.item_type +"_edit_scale_field_']").each(function(){
            initial_detail.push(jQuery(this).val().replace(/,/g, '[comma]'));
        });
        
        if(initial_detail.length) {
            
            jQuery('#'+ ITEM.item_type +'_scale_init_data').attr('detail', initial_detail.toString().replace(/,/g, '[row]').replace(/\[comma\]/g, ','));
        }        
    }
    
    this.library_item_id = 0;
    this.library_item_idstr = '';
    this.library_ae = function() {
        
        var label = jQuery('#'+ ITEM.item_type +'_library_ae_field');
        
        if(label.val() == '') {
            
            label.focus();
            return;
        }
        
        GBL.loader();
        
        jQuery.post(
            DOCROOT +'sensory/async_library_ae',
            {
                id : ITEM.library_item_id,
                type : ITEM.item_type,
                label : label.val(),
                t : (new Date).getTime()
            },
            function(r){
                
                GBL.loader(false);
                
                if(r) {
                    
                    var tmp = r.item.split('_'), idstr = tmp[0].replace(/ /g, '_').toLowerCase();
                    var html = '<li class="ui-state-highlight" id="'+ ITEM.item_type +'_attr_'+ idstr +'" ondblclick="ITEM.library_ae_field('+ tmp[1] +',\''+ idstr +'\')">'+ tmp[0] +'</li>';

                    var wrapper = jQuery('#'+ ITEM.item_type +'_library_wrapper');
                    var wrapper_outer = jQuery('#'+ ITEM.item_type +'_library_wrapper_outer');
                    
                    if(ITEM.library_item_id > 0) {
                        
                        jQuery('#'+ ITEM.item_type +'_attr_'+ ITEM.library_item_idstr)
                            .attr('id', ITEM.item_type +'_attr_'+ idstr)
                            .attr('ondblclick', "ITEM.library_ae_field("+ ITEM.library_item_id +",'"+ idstr +"')")
                            .html(tmp[0]);
                            
                            STEP_3.library[ITEM.item_type] = r.library[ITEM.item_type];
                            
                    } else {
                        
                        wrapper.append(html);
                        /* START: Push newly created item in the Library. */
                        if(STEP_3.library[ITEM.item_type] == undefined) STEP_3.library[ITEM.item_type] = [];
                        STEP_3.library[ITEM.item_type].push({'id' : tmp[1], 'label' : tmp[0]});
                        /* END: Push newly created item in the Library. */
                    }
                    
                    wrapper_outer.scrollTop(wrapper_outer[0].scrollHeight);
                    
                    label.val('');
                }
            }, 'json'
        );
    }
    
    this.library_ae_field = function(id, idstr) {
        
        ITEM.library_item_id = id;
        ITEM.library_item_idstr = idstr;
        
        var label = jQuery('#'+ ITEM.item_type +'_attr_'+ idstr).html();
        var wrapper = jQuery("#"+ ITEM.item_type +"_library_manage_wrapper");
        
        if(wrapper.is(':hidden') == false) { /* When fields are already visible. */

            jQuery('#'+ ITEM.item_type +'_library_ae_field').val(label).focus();
            jQuery('#'+ ITEM.item_type +'_btn_ae').val('Save');
            jQuery('#'+ ITEM.item_type +'_btn_delete').show();

        } else {
            
            /* Show fields and initialize data. */
            jQuery("#"+ ITEM.item_type +"_library_manage_wrapper").toggle(function(){

                jQuery('#'+ ITEM.item_type +'_library_ae_field').val(label).focus();
                jQuery('#'+ ITEM.item_type +'_btn_ae').val('Save');
                jQuery('#'+ ITEM.item_type +'_btn_delete').show();
            });
        }
    }
    
    this.library_del = function(confirmed) {
        
        if(! confirmed) {
            
            jQuery.post(
                DOCROOT +'sensory/async_library_del_check',
                {
                    id : ITEM.library_item_id,
                    type : ITEM.item_type,
                    label : jQuery('#'+ ITEM.item_type +'_attr_'+ ITEM.library_item_idstr).html(),
                    t : (new Date).getTime()
                },
                function(r) {
                    
                    if(r > 0) { /* Has attachment. */
                        
                        Popup.dialog({
                            title : 'ERROR',
                            message : 'This is currently attached to <b>'+ r +'</b> item'+ ((r > 1) ? 's' : '') +' in your screen.<br /><br />Deleting this item is <b>not</b> possible.',
                            buttons: ['Okay'],
                            buttonClick: function(button) {

                                POPUPJS.overlay_show();
                                jQuery('#popupjs_btn_ok').attr('onclick', 'ITEM.ok()'); /* Restore event of the first Popup. */
                            },
                            width: '420px'
                        });
                        
                    } else {
                        
                        Popup.dialog({
                            title : 'DELETE',
                            message : 'You can no longer retrieve this item after it was <b>deleted</b>.<br /><br />Do you want to proceed?',
                            buttons: ['Yes', 'No, I Cancel'],
                            buttonClick: function(button) {

                                if(button == 'Yes') { ITEM.library_del(1); }
                                POPUPJS.overlay_show();

                                jQuery('#popupjs_btn_ok').attr('onclick', 'ITEM.ok()'); /* Restore event of the first Popup. */
                            },
                            width: '420px'
                        });
                    }
                }, 'json'
            );
            
        } else {
            
            jQuery.post(
                DOCROOT +'sensory/async_library_del',
                {
                    id : ITEM.library_item_id,
                    type : ITEM.item_type,
                    t : (new Date).getTime()
                },
                function(r) {
                    
                    if(r) {
                        
                        jQuery('#'+ ITEM.item_type +'_attr_'+ ITEM.library_item_idstr).remove();
                        jQuery('#'+ ITEM.item_type +'_library_ae_field').val('').focus();

                        ITEM.library_item_id = 0;
                        ITEM.library_item_idstr = '';
                        jQuery('#'+ ITEM.item_type +'_btn_ae').val('Add');
                        jQuery('#'+ ITEM.item_type +'_btn_delete').hide();
                    
                        STEP_3.library[ITEM.item_type] = r.library[ITEM.item_type];                        
                    }
                    
                }, 'json'
            );
        }
        
    }
    
    this.library_cancel = function() {
        
        if(jQuery('#'+ ITEM.item_type +'_library_manage_wrapper').is(':hidden') == false) {
            
            jQuery('#'+ ITEM.item_type +'_library_manage_trigger').trigger('click');
        }
    }
}

jQuery(function(){
    
    <?php echo $js_ondblclick?>
    
    SCREEN.itemlabel = '<?php echo addslashes($item['header'])?>';
    
    jQuery('#'+ ITEM.item_type +'_btn_delete').hide();
    jQuery("input[name='"+ ITEM.item_type +"_scale']").click(function(){ 
        
        
        jQuery('#'+ ITEM.item_type +'_edit_scale_field_wrapper').remove();
        
        jQuery('#'+ ITEM.item_type +'_scale_other').val('');    
        STEP_3.scale_toggle(ITEM.item_type, jQuery(this).val());
    });

    jQuery('#'+ ITEM.item_type +'_scale_other').bind('keyup keydown paste focus', function(){

        jQuery("input[name='"+ ITEM.item_type +"_scale'][value=other]").attr('checked', true);
        STEP_3.scale_toggle(ITEM.item_type, jQuery(this).val());
    });

    STEP_3.scale_toggle(ITEM.item_type, jQuery("input[name='"+ ITEM.item_type +"_scale']:checked").val());

    jQuery('#'+ ITEM.item_type +'_attr_wrapper, #'+ ITEM.item_type +'_library_wrapper').sortable({ connectWith: ".connectedSortable" }).disableSelection();
    jQuery('#'+ ITEM.item_type +'_attr_wrapper').sortable({
        receive: function(ev, ui) {

            var label = ui.item.html();
            var scale = (jQuery("input[name='"+ ITEM.item_type +"_scale']:checked").val());

            if(scale == 'other') scale = jQuery('#'+ ITEM.item_type +'_scale_other').val();
            if(! scale) scale = 9;

            var orientation = (jQuery("input[name='"+ ITEM.item_type +"_orientation']:checked").val()) ? jQuery("input[name='<?php echo $item_type?>_orientation']:checked").val() : 'v';
            var ordering = jQuery('#'+ ITEM.item_type +'_order').val();

            if(label.indexOf('</b>') > -1) { /* If inside the "attr_wrapper" sortable area. */

                /* Needs to restore the original label/caption. */
                label = label.split('</b>');

                var so = jQuery.trim(label[1]).split(' ');
                scale = so[2];
                orientation = so[1][0].toLowerCase();
                ordering = so[0];
                label = label[0].replace('<b>', '');
            }

            /* Caption to display with the item's name in the sortable area. */
            var attr = ordering +' '+ ((orientation == 'v') ? 'Vertical' : 'Horizontal') +' '+ scale +' Point Scale';
            ui.item.html('<b>'+ label +'</b> '+ attr);
            
            if(ui.item.attr('ondblclick')) ITEM.ondblclick[ui.item.attr('id')] = ui.item.attr('ondblclick');
            ui.item.attr('ondblclick', "ITEM.attr_edit('"+ ui.item.attr('id') +"')");

            var init_data = jQuery('#'+ ITEM.item_type +'_scale_init_data').val();

            var tmp = ''; for(var x=1; x<scale; x++) { tmp += '[row]'; }
            if(tmp == init_data) init_data = '';

            if(init_data == '') {

                if(jQuery('#'+ ITEM.item_type +'_rad_scale_other').is(':checked')) {

                   var initial_detail = [];
                   if(parseInt(jQuery('#'+ ITEM.item_type +'_scale_other').val(), 10) > 0) {

                       jQuery("input[id^='"+ ITEM.item_type +"_edit_scale_field_']").each(function(){ initial_detail.push(jQuery(this).val().replace(/,/g, '[comma]')); });
                   }

                   if(initial_detail.length) { ui.item.attr('detail', initial_detail.toString().replace(/,/g, '[row]').replace(/\[comma\]/g, ',')); }

                }
            } else ui.item.attr('detail', init_data);

            ITEM.attr_edit(ui.item.attr('id'));
            ITEM.library_cancel();
        }
    });

    jQuery('#'+ ITEM.item_type +'_library_wrapper').sortable({
        receive: function(ev, ui) {

            var label = jQuery('b', ui.item).html();
            if(label) ui.item.html(label);

            if(ITEM.ondblclick[ui.item.attr('id')]) ui.item.attr('ondblclick', ITEM.ondblclick[ui.item.attr('id')]); /* Restore event when custom item. */
            else ui.item.removeAttr('ondblclick'); /* Remove event when default item. */

            ui.item.css('border', '1px solid #FCEFA1').removeAttr('detail');            
            jQuery('#'+ ITEM.item_type +'_edit_attr_wrapper').html('');
        }    
    });
    
    jQuery('#'+ ITEM.item_type +'_library_manage_trigger').click(function(){
        
        var field = jQuery('#'+ ITEM.item_type +'_library_ae_field');
        field.val('');
        
        jQuery('#'+ ITEM.item_type +'_library_manage_wrapper').toggle(function(){
            
            if(jQuery(this).is(':hidden') == false) { field.focus(); }
            else {
                
                ITEM.library_item_id = 0;
                ITEM.library_item_idstr = '';                
                jQuery('#'+ ITEM.item_type +'_btn_ae').val('Add');
                jQuery('#'+ ITEM.item_type +'_btn_delete').hide();
            }
        });
    });
});
</script>

<input type="hidden" id="<?php echo $item_type?>_scale_init_data" />
<div style="clear: both; text-align: left">
    <table cellspacing="0" cellpadding="0" width="100%">    
        <tr><td width="250" valign="top">

                <table cellspacing="0" cellpadding="0">

                    <tr><td valign="top" align="right">Orientation:</td>
                        <td>
                            <div><input type="radio" name="orientation" id="orientation_h" value="h" /> <label for="orientation_h">Horizontal</label></div>
                            <div><input type="radio" name="orientation" id="orientation_v" value="v" checked="checked" /> <label for="orientation_v">Vertical</label></div>                        
                        </td>
                    </tr>

                    <tr><td style="padding: 5px 0 5px 0" align="right">Ordering:</td>
                        <td style="padding: 5px 0 5px 5px">
                            <select id="<?php echo $item_type?>_order">
                                <option value="asc">Ascending</option>
                                <option value="desc" selected="selected">Descending</option>
                            </select>
                        </td>
                    </tr>

                    <tr><td valign="top" align="right">Scale:</td>
                        <td>
                            <div id="9"><input type="radio" name="<?php echo $item_type?>_scale" id="<?php echo $item_type?>_rad_scale_9" value="9" checked="checked" /> <label for="<?php echo $item_type?>_rad_scale_9">9 Point Scale</label></div>
                            <div id="7"><input type="radio" name="<?php echo $item_type?>_scale" id="<?php echo $item_type?>_rad_scale_7" value="7" /> <label for="<?php echo $item_type?>_rad_scale_7">7 Point Scale</label></div>
                            <div id="5"><input type="radio" name="<?php echo $item_type?>_scale" id="<?php echo $item_type?>_rad_scale_5" value="5" /> <label for="<?php echo $item_type?>_rad_scale_5">5 Point Scale</label></div>
                            <div id="3"><input type="radio" name="<?php echo $item_type?>_scale" id="<?php echo $item_type?>_rad_scale_3" value="3" /> <label for="<?php echo $item_type?>_rad_scale_3">3 Point Scale</label></div>
                            <div id="2"><input type="radio" name="<?php echo $item_type?>_scale" id="<?php echo $item_type?>_rad_scale_2" value="2" /> <label for="<?php echo $item_type?>_rad_scale_2">2 Point Scale</label></div>
                            <div>
                                <input type="radio" name="<?php echo $item_type?>_scale" id="<?php echo $item_type?>_rad_scale_other" value="other" /> <label for="<?php echo $item_type?>_rad_scale_other">Other</label>
                                <input type="text" id="<?php echo $item_type?>_scale_other" value="" style="width: 50px; text-align: right" onkeyup="return GBL.numOnly(event)" onkeydown="return GBL.numOnly(event)" />
                            </div>
                        </td>
                    </tr>

                </table>            
            </td>
            <td valign="top">
                <div style="border: 1px solid #CCC; width: 425px; height: 242px; padding: 5px; overflow: auto">
                    <div id="<?php echo $item_type?>_edit_attr_wrapper"></div>
                </div>
            </td>
        </tr>
        
        <tr><td colspan="2">
            
                <table cellspacing="0" cellpadding="0">

                    <tr><th style="height: 25px">Attributes</th>
                        <th style="height: 25px; padding-left: 5px">Library</th>
                    </tr>
                    <tr><td valign="top" width="350">
                            <div style="overflow: auto; padding: 1px; height: 202px; border: 1px solid #CCC; width: 350px">
                                <ul class="connectedSortable" id="<?php echo $item_type?>_attr_wrapper"><?php echo $attr_items?></ul>                            
                            </div>
                        </td>
                        <td valign="top" style="padding-left: 5px; width: 100%" align="left">

                            <div id="<?php echo $item_type?>_library_wrapper_outer" style="overflow: auto; width: 100%; height: 205px">
                                <ul class="connectedSortable" id="<?php echo $item_type?>_library_wrapper"><?php echo $library_items?></ul>
                            </div>

                        </td>
                        <td style="padding-left: 5px" valign="top">
                            <a href="javascript:;" id="<?php echo $item_type?>_library_manage_trigger" title="add to library"><img alt="library" src="<?php echo $docroot . 'media/images/16x16/library.png'?>" /></a>
                            <div id="<?php echo $item_type?>_library_manage_wrapper">
                                <div>Label</div>
                                <div><input id="<?php echo $item_type?>_library_ae_field" type="text" style="width: 165px" /></div>
                                <div>
                                    <table cellpadding="0" cellspacing="0">
                                        <tr><td><input id="<?php echo $item_type?>_btn_ae" type="button" value="Add" onclick="ITEM.library_ae()" /></td>
                                            <td style="padding-left: 2px"><input id="<?php echo $item_type?>_btn_delete" type="button" value="Delete" onclick="ITEM.library_del()" /></td>
                                            <td style="padding-left: 2px"><input type="button" value="Cancel" onclick="ITEM.library_cancel()" /></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>                            
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    
    </table>
</div>