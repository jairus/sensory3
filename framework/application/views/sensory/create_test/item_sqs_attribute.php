<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

include_once 'procedures.inc.php';

if(isset($_POST)) { extract($_POST); }

if($item_id > 0) {
    
    $item = $_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items'][$item_id - 1];
    
    array_walk($item, 'xy_ssstring_for_view_byref');
    
    if($item['attr']) {
        
        $attr = explode('[=ROW=]', $item['attr']);
        array_walk($attr, 'xy_ssstring_for_view_byref');
    }    
}

$default_labels = array(
    'Appearance' => array(
        'Color',
        'Breading uniformity',
        'Uncooked flour',
        'Meat doneness',
        'Skin intactness',
        'Skin blisters',
        'Skin oiliness',
        'Skin sogginess',
        'Burnt particles'
    ),
    'Aroma' => array(
        'Raw',
        'Burnt',
        'Rancid'
    ),
    'Flavor' => array(
        'Fresh fried chicken',
        'Saltiness',
        'Seasoning taste',
        'Raw',
        'Burnt',
        'Rancid'
    ),
    'Texture' => array(
        'Skin crispiness',
        'Meat juiciness',
        'Meat tenderness',
        'Gumminess',
        'Toughness',
        'Fibrousness'
    )
);

$default_label_flag = in_array($item['label'], array_keys($default_labels));
$default_label_other_flag = false;

if($default_label_flag) {
    
    if(is_array($attr)) {
        
        $default_labels[$item['label']] = array_merge($default_labels[$item['label']], $attr);
        $default_labels[$item['label']] = array_unique($default_labels[$item['label']]); /* Remove duplicate entries. */        
        $default_labels[$item['label']] = array_merge($default_labels[$item['label']]); /* Reset keys. */
        
        $attr = $default_labels[$item['label']];        
    }
    
} else {
    
    if(is_array($attr)) {
        
        $default_label_other_flag = true;
        $default_labels['other'] = $attr;
        
        $attr = $default_labels['other'];
    }
}

$attr_count = count($attr);

if(! empty($attr)) {

    for($x=0; $x<$attr_count; $x++) {

        $xy = ($x + 1);

        $attr_html .= '
            <div style="margin-top: 2px;" id="' . $item_type . '_attr_' . $xy . '">
                <a title="delete" href="javascript:ITEM.remove(' . $xy . ')"><img src="' . $docroot . '/media/images/16x16/delete.png" /></a>
                <a title="edit" href="javascript:ITEM.edit_init(' . $xy . ')"><img src="' . $docroot . '/media/images/16x16/edit.png" /></a>
                <span>' . str_replace('[and]', '&', $attr[$x]) . '</span>
            </div>';
    }

} else $attr_html = '<span style="color: #777">(empty)</span>';
?>
<script type="text/javascript">
var ITEM = new function() {

    this.id = <?php echo (double) $item_id?>;
    this.type = '<?php echo $item_type?>';

    this.item_count = <?php echo (int) $attr_count?>;
    this.edit_flag = false;
    this.edit_id = 0;
    this.default_labels = <?php echo json_encode($default_labels)?>;
    
    this.ok = function() {

        var attr_arr = [];
        if(jQuery('#'+ this.type +'_attr_wrapper').html().trim().stripTags() != '(empty)') {

            for(var x=1; x<=jQuery('#'+ this.type +'_attr_wrapper > div').length; x++) {

                attr_arr.push(jQuery('#'+ this.type +'_attr_'+ x +' span').html().replace(/,/g, '[comma]').replace(/\&amp\;/g, '[and]'));

            }                
        }

        if(! attr_arr.length) {

            jQuery('#'+ this.type +'_attr_content').focus();
            return;
        }

        var args = {
            type : 'item',
            item_id : ITEM.id,
            t : (new Date).getTime(),
            screen_code : SCREEN.selection_code,
            screen_count : SCREEN.selection_count,
            rta_id : Q.rta_id,

            item : this.type,
            attr : attr_arr.toString().replace(/,/g, '[=ROW=]')
        };

        if(jQuery('#'+ this.type +'_label').val() != 'other') {
            
            jQuery.extend(args, { label : jQuery('#'+ this.type +'_label').val() });
            
        } else {
            
            var other = jQuery('#'+ this.type +'_label_other');
            
            if(other.val().trim() == '') {
                
                other.focus();
                return;
                
            } else {
                
                jQuery.extend(args, { label : other.val() });
            }
        }
        
        
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

    this.add = function() {

        var content = jQuery('#'+ this.type +'_attr_content');
        
        if(jQuery('#'+ this.type +'_label').val() != 'other') {
            
            if(content.val().trim() == '') {

                content.focus();
                return;
            }
        }
        
        if(this.edit_flag) {

            jQuery('#'+ this.type +'_attr_'+ this.edit_id +' span').html((content.val() == '') ? '---' : content.val());
            this.edit_cancel();            
        }
        else {

            if(this.item_count == 0) {

                jQuery('#'+ this.type +'_attr_wrapper').html('');
            }

            this.item_count++;
            jQuery('#'+ this.type +'_attr_wrapper').append('<div id="'+ this.type +'_attr_'+ this.item_count +'" style="margin-top: 2px"><a title="delete" href="javascript:ITEM.remove('+ this.item_count +')"><img src="'+ DOCROOT +'media/images/16x16/delete.png" /></a> <a title="edit" href="javascript:ITEM.edit_init('+ this.item_count +')"><img src="'+ DOCROOT +'media/images/16x16/edit.png" /></a> <span>'+ (content.val() == '' ? '---' : content.val()) +'</span></div>');
        }

        content.val('');
        content.focus();
    }

    this.add_keypressed = function(e) {

        var key = GBL.get_keypressed(e);
        if(key == 13 && jQuery('#'+ this.type +'_btn_ae').val() == 'add') this.add();
    }

    this.edit_init = function(id) {

        this.edit_flag = true;
        this.edit_id = id;
        
        var content = jQuery('#'+ this.type +'_attr_'+ id +' span').html().replace(/\&amp\;/g, '&');
        if(content == '---') content = '';
        
        jQuery('#'+ this.type +'_btn_ae').val('update');
        jQuery('#'+ this.type +'_btn_cancel').show();
        jQuery('#'+ this.type +'_attr_content').val(content);
        jQuery('#'+ this.type +'_attr_content').focus();
    }

    this.edit_cancel = function() {

        this.edit_flag = false;
        this.edit_id = 0;

        jQuery('#'+ this.type +'_btn_ae').val('add');
        jQuery('#'+ this.type +'_btn_cancel').hide();

        jQuery('#'+ this.type +'_attr_content').val('').focus();
    }

    this.remove = function(id) {

        jQuery('#'+ this.type +'_attr_'+ id).remove();            
        if(this.edit_flag) this.edit_cancel();

        this.item_count--;
        if(this.item_count == 0) {

            jQuery('#'+ this.type +'_attr_wrapper').html('<span style="color: #777">(empty)</span>');
        }
    }
}

jQuery(function(){

    SCREEN.itemlabel = '<?php echo addslashes($item['header'])?>';

    jQuery('#'+ ITEM.type +'_btn_cancel').hide();
    
    jQuery('#'+ ITEM.type +'_label').bind('change', function(){
        
        var labels = ITEM.default_labels[jQuery(this).val()], attr_html = '';
        
        if(labels) {
            
            ITEM.item_count = labels.length;
            
            for(var x=0; x<labels.length; x++) {

                var label = labels[x];
                var xy = (x + 1);

                attr_html += '<div style="margin-top: 2px;" id="'+ ITEM.type +'_attr_'+ xy +'">'+            
                    '<a title="delete" href="javascript:ITEM.remove('+ xy +')"><img src="'+ DOCROOT +'/media/images/16x16/delete.png" /></a> '+
                    '<a title="edit" href="javascript:ITEM.edit_init('+ xy +')"><img src="'+ DOCROOT +'/media/images/16x16/edit.png" /></a>'+
                    '<span>'+ label +'</span>'+
                '</div>';

            }
        }
        
        jQuery('#'+ ITEM.type +'_attr_wrapper').html(attr_html);
        
        //alert(ITEM.default_labels[jQuery(this).val()]);
        
    });
});
</script>
<div style="clear: both; text-align: left">

    <table cellspacing="0" cellpadding="0" width="100%">
        <tr><td>
                <table cellspacing="0" cellpadding="0">
                    <tr><td><b>Label</b></td>
                        <td style="padding-left: 5px">
                            <div class="fltLf">
                            <select id="<?php echo $item_type?>_label" onchange="GBL.toggle_other_field(this,'<?php echo $item_type?>_label_other_wrapper','<?php echo $item_type?>_label_other')">
                                <?php
                                echo $item['label'];
                                foreach($default_labels as $label => $values) {
                                    
                                    if($label == 'other') $label_html = 'Other';
                                    else $label_html = $label;
                                    
                                    ?><option value="<?php echo $label?>"<?php echo (($label == $item['label'] || ($label == 'other' && $default_label_flag == false))   ? ' selected="selected"' : '')?>><?php echo $label_html?></option><?php
                                }
                                
                                if(! $default_label_other_flag) {
                                    
                                    ?>
                                    <option value="other"<?php echo (($default_label_flag == false) ? ' selected="selected"' : '')?>>Other</option>
                                    <?php
                                }
                                ?>
                            </select>
                            </div>
                            
                            <div id="<?php echo $item_type?>_label_other_wrapper" class="fltLf hidden padLf2" style="display: <?php echo (($default_label_flag == false) ? 'block' : 'none')?>;">
                                <input id="<?php echo $item_type?>_label_other" type="text" style="width: 300px" value="<?php echo (($default_label_flag == false) ? $item['label'] : '')?>" />
                            </div>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr><td><b>Attribute<b class="mandatory">*</b></b></td></tr>
        <tr><td><input id="<?php echo $item_type?>_attr_content" type="text" style="width: 300px" onkeypress="ITEM.add_keypressed(event)" /> <input id="<?php echo $item_type?>_btn_ae" type="button" onclick="ITEM.add()" value="add" />
                <input id="<?php echo $item_type?>_btn_cancel" type="button" onclick="ITEM.edit_cancel()" value="cancel" />
            </td>
        </tr>
        <tr><td style="padding-left: 20px">
            <div><b>Attribute(s):</b></div>
            <div id="<?php echo $item_type?>_attr_wrapper"><?php echo $attr_html?></div>
            </td>
        </tr>
    </table>
</div>
