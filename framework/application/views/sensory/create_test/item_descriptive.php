<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

include_once 'procedures.inc.php';

if(isset($_POST)) { extract($_POST); }
$item = $interval = array();
if($item_id > 0) {
    
    $item = $_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items'][$item_id - 1];
    if(is_array($item)) array_walk($item, 'xy_ssstring_for_view_byref');
    
    $interval = array();
    if($item['interval_type'] == 'digit') {
        
    } else {
        
        
        $item['interval'] = str_replace('[=FIELD=]', '[field]', $item['interval']);
        //print_r($item['interval']);
        //parse_str($item['interval'], $interval);
        
        $tmp = explode('&', $item['interval']);
        
        for($x=0, $y = count($tmp); $x<$y; $x++) {
            
            $tmp2 = explode('=', $tmp[$x]);
            $interval[$tmp2[0]] = str_replace('[field]', '[=FIELD=]', $tmp2[1]);
            //$interval[$x] =
        }
        
        //$tmp = explode('&', $item['interval']);
        
    }
    
    //print_r($interval);
    //exit();
}
?>
<style type="text/css">
#<?php echo $item_type?>_interval_digit_wrapper, #<?php echo $item_type?>_interval_custom_wrapper {
    margin-top: 2px
}
</style>
<script type="text/javascript">
var ITEM = new function() {
    
    this.id = <?php echo (double) $item_id?>;
    this.type = '<?php echo $item_type?>';
    
    this.item_count = <?php echo count($interval)?>;
    this.edit_flag = false;
    this.edit_id = 0;
    this.interval_type = '<?php echo $item['interval_type']?>';
    
    this.ok = function() {
        
        var length = jQuery('#'+ this.type +'_length');
        if(! length.val().trim().length) {
            
            length.focus();
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
            length : length.val()
        };
        
        
        if(jQuery('#'+ this.type +'_label').val().trim().length) {
            
            jQuery.extend(args, { label : jQuery('#'+ this.type +'_label').val() });
        }
        
        var interval_type = jQuery('#'+ this.type +'_interval_type').val();
        
        if(interval_type == 'digit') {
            
            var d = jQuery('#'+ this.type +'_interval_digit_data');
            if(d.val().trim() != '') {
                
                jQuery.extend(args, { interval : d.val() });
            } else {
                
                d.focus();
                return;
            }
        }
        else
        if(interval_type == 'custom') {
            
            var vl = jQuery("div[id^='"+ this.type +"_interval_custom_vl_']");
            var vl_arr = [];
            
            if(vl.length) {
                
                for(var x=1; x<=vl.length; x++) {
                    
                    var v = jQuery('#'+ this.type +'_v_'+ x).html();
                    var l = jQuery('#'+ this.type +'_l_'+ x).html().replace(/,/g, '[comma]').replace(/\&amp\;/g, '[and]');
                    var l_option = jQuery('#'+ this.type +'_l_option_'+ x).html();
                    
                    vl_arr.push(v +'='+ l +'[=FIELD=]'+ l_option);
                }
            } else {
                
                jQuery('#'+ this.type +'_interval_custom_value').focus();
                return;
            }

            if(vl_arr.length) jQuery.extend(args, { interval : vl_arr.toString().replace(/,/g, '&') });
            
        } else {
            
            return;
        }
        
        jQuery.extend(args, { interval_type : interval_type });
        
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
        
        var v = jQuery('#'+ this.type +'_interval_custom_value');
        var l = jQuery('#'+ this.type +'_interval_custom_label');
        
        if(v.val().trim() == '') {
            
            v.focus();
            return;
        }
        
        var sh = jQuery('#'+ this.type +'_interval_custom_label_option').is(':checked');
        if(sh) sh = 'shown';
        else sh = 'hidden';
            
        if(this.edit_flag) {
            
            jQuery('#'+ this.type +'_v_'+ this.edit_id).html(jQuery('#'+ this.type +'_interval_custom_value').val());
            jQuery('#'+ this.type +'_l_'+ this.edit_id).html(jQuery('#'+ this.type +'_interval_custom_label').val());
            jQuery('#'+ this.type +'_l_option_'+ this.edit_id).html((jQuery('#'+ this.type +'_interval_custom_label_option').is(':checked')) ? 'shown' : 'hidden');
            
            this.edit_cancel();
        }
        else {
            
            this.item_count++;
            jQuery('#'+ this.type +'_interval_custom_data_wrapper').append('<div id="'+ this.type +'_interval_custom_vl_'+ this.item_count +'" style="margin-top: 2px"><a href="javascript:ITEM.remove('+ this.item_count +')"><img src="'+ DOCROOT +'media/images/16x16/delete.png" /></a> <a href="javascript:ITEM.edit_init('+ this.item_count +')"><img src="'+ DOCROOT +'media/images/16x16/edit.png" /></a> <span id="'+ this.type +'_v_'+ this.item_count +'">'+ v.val() +'</span> <span id="'+ this.type +'_l_'+ this.item_count +'">'+ l.val() +'</span> <sup style="color: #777" id="'+ this.type +'_l_option_'+ this.item_count +'">'+ sh +'</sup></div>');
        }
        
        v.val(''); v.focus(); l.val('');
    }
    
    this.edit_init = function(id) {
        
        this.edit_flag = true;
        this.edit_id = id;
        
        jQuery('#'+ this.type +'_btn_ae').val('update');
        jQuery('#'+ this.type +'_btn_cancel').show();
        
        jQuery('#'+ this.type +'_interval_custom_value').val(jQuery('#'+ this.type +'_v_'+ id).html());
        jQuery('#'+ this.type +'_interval_custom_label').val(jQuery('#'+ this.type +'_l_'+ id).html().replace(/\&amp;/g, '&')).focus();
        jQuery('#'+ this.type +'_interval_custom_label_option').attr('checked', (jQuery('#'+ this.type +'_l_option_'+ id).html() == 'shown') ? true : false);
    }
    
    this.edit_cancel = function() {
        
        this.edit_flag = false;
        this.edit_id = 0;
        
        jQuery('#'+ this.type +'_btn_ae').val('add');
        jQuery('#'+ this.type +'_btn_cancel').hide();
        
        jQuery('#'+ this.type +'_interval_custom_value').val('').focus();
        jQuery('#'+ this.type +'_interval_custom_label').val('');
    }
    
    this.remove = function(id) {
        
        jQuery('#'+ this.type +'_interval_custom_vl_'+ id).remove();
        jQuery('#'+ this.type +'_interval_custom_value').focus();
        
        if(this.edit_flag) this.edit_cancel();
        
        this.item_count--;
    }    
}

jQuery(function(){
    
    SCREEN.itemlabel = '<?php echo addslashes($item['header'])?>';
    
    jQuery('#'+ ITEM.type +'_interval_digit_wrapper').toggle((ITEM.interval_type == 'digit') ? true : false);
    jQuery('#'+ ITEM.type +'_interval_custom_wrapper').toggle((ITEM.interval_type == 'custom') ? true : false);
    
    jQuery('#'+ ITEM.type +'_interval_type').change(function(){
        
        var interval_type = jQuery(this).val();
        
        jQuery('#'+ ITEM.type +'_interval_'+ interval_type +'_wrapper').show();
        
        if(interval_type == 'digit') { 
            
            jQuery('#'+ ITEM.type +'_interval_digit_value').focus();
            jQuery('#'+ ITEM.type +'_interval_custom_wrapper').hide();
        }
        else
        if(interval_type == 'custom') {
            
            jQuery('#'+ ITEM.type +'_interval_custom_value').focus();
            jQuery('#'+ ITEM.type +'_interval_digit_wrapper').hide();
        }
        else {
            
            jQuery('#'+ ITEM.type +'_interval_digit_wrapper').hide();
            jQuery('#'+ ITEM.type +'_interval_custom_wrapper').hide();
        }
    });
    
    jQuery('#'+ ITEM.type +'_btn_cancel').hide();
});
</script>
<div style="clear: both; text-align: left">

    <table cellspacing="0" cellpadding="0" width="100%">
        <tr><td>Label</td></tr>
        <tr><td><input id="<?php echo $item_type?>_label" type="text" style="width: 300px" value="<?php echo $item['label']?>" /></td></tr>
        <tr><td valign="top">
                <table cellpadding="1" cellspacing="0">
                    <tr><td align="right"><b>Length</b><b class="mandatory">*</b></td>
                        <td><input id="<?php echo $item_type?>_length" type="text" style="width: 50px" value="<?php echo $item['length']?>" /> <span style="color: #777">Default starting point is "0".</span></td>
                    </tr>
                    <tr><td valign="top" align="right"><b>Interval</b><b class="mandatory">*</b></td>
                        <td valign="top">
                            <select id="<?php echo $item_type?>_interval_type">
                                <option value="">Select:</option>
                                <option value="digit"<?php echo (($item['interval_type'] == 'digit') ? ' selected="selected"' : '')?>>Digit</option>
                                <option value="custom"<?php echo (($item['interval_type'] == 'custom') ? ' selected="selected"' : '')?>>Custom</option>
                            </select>

                            <div id="<?php echo $item_type?>_interval_digit_wrapper">
                                <input id="<?php echo $item_type?>_interval_digit_data" type="text" value="<?php echo $item['interval']?>" style="width: 70px" />
                            </div>

                            <div id="<?php echo $item_type?>_interval_custom_wrapper">
                                <table cellpadding="0" cellspacing="0">
                                    <tr><td>Value</td>
                                        <td style="padding-left: 2px">Label</td>
                                        <td style="padding-left: 2px">Show/Hide Label</td>
                                    </tr>
                                    <tr><td><input id="<?php echo $item_type?>_interval_custom_value" type="text" style="width: 50px; text-align: right" /></td>
                                        <td style="padding-left: 2px"><input id="<?php echo $item_type?>_interval_custom_label" type="text" /></td>
                                        <td style="padding-left: 2px" align="center"><input id="<?php echo $item_type?>_interval_custom_label_option" type="checkbox" checked="checked" /></td>
                                        <td style="padding-left: 2px">
                                            <input id="<?php echo $item_type?>_btn_ae" type="button" value="add" onclick="ITEM.add()" />
                                            <input id="<?php echo $item_type?>_btn_cancel" type="button" value="cancel" onclick="ITEM.edit_cancel()" />
                                        </td>
                                    </tr>
                                </table>
                                <div id="<?php echo $item_type?>_interval_custom_data_wrapper">
                                
                                
                                    <?php
                                    if(! empty($interval)) {
                                        
                                        $ctr = 0;
                                        foreach($interval as $value => $vl) {
                                            //echo $value,'<br/>';
                                            list($label, $visibility) = explode('[=FIELD=]', $vl);
                                            $ctr++;
                                            ?>
                                            <div style="margin-top: 2px;" id="<?php echo $item_type?>_interval_custom_vl_<?php echo $ctr?>">
                                                <a href="javascript:ITEM.remove(<?php echo $ctr?>)"><img src="<?php echo $docroot?>/media/images/16x16/delete.png" /></a>
                                                <a href="javascript:ITEM.edit_init(<?php echo $ctr?>)"><img src="<?php echo $docroot?>/media/images/16x16/edit.png" /></a>
                                                <span id="<?php echo $item_type?>_v_<?php echo $ctr?>"><?php echo $value?></span> <span id="<?php echo $item_type?>_l_<?php echo $ctr?>"><?php echo str_replace('[and]', '&amp;', $label)?></span>
                                                <sup id="<?php echo $item_type?>_l_option_<?php echo $ctr?>" style="color: #777"><?php echo $visibility?></sup>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>