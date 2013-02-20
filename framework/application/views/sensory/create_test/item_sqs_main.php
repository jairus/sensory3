<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

include_once 'procedures.inc.php';

if(isset($_POST)) { extract($_POST); }
$item = $rl = array();
if($item_id > 0) {
    
    $item = $_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items'][$item_id - 1];
    
    $item['rl'] = html_entity_decode($item['rl'], ENT_QUOTES, 'UTF-8');
    parse_str($item['rl'], $rl);
    
    //array_walk($item, 'xy_ssstring_for_view_byref');
    
    //$rl = explode('&', $item['rl']);
    
    //print_r($rl_tmp);
}
?>
<script type="text/javascript">
var ITEM = new function() {
    
    this.id = <?php echo (double) $item_id?>;
    this.type = '<?php echo $item_type?>';
    
    this.scale = 0;
    
    this.ok = function() {
        
        var scale = jQuery("input[id^='"+ this.type +"_scale_']:checked").val();

        if(scale == 'other') {
            
            var o = jQuery('#'+ this.type +'_scale_other_field');
            if(o.val().trim() == '') {
                
                o.focus();
                return;
                
            } else scale = o.val();
        }
        
        var partition = jQuery('#'+ this.type +'_partition');
        if(partition.val().trim() == '') {
            
            partition.focus();
            return;
            
        } else partition = partition.val();
        
        var rl_arr = [], check = [];
        for(var x=1; x<=partition; x++) {
            
            var r = jQuery('#'+ this.type +'_range_'+ x).val().replace(/,/g, '[comma]').replace(/\&/g, '[and]');
            var v = jQuery('#'+ this.type +'_label_'+ x).val().replace(/,/g, '[comma]').replace(/\&/g, '[and]');
            
            rl_arr.push(r +'='+ v);
            check.push('=');
        }
        
        if(rl_arr.toString() == check.toString()) {
            
            jQuery('#'+ this.type +'_range_1').focus();
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
            scale : scale,
            partition : partition,
            rl : rl_arr.toString().replace(/,/g, '&').replace(/\[comma\]/g, ','),
            ordering : jQuery('#'+ this.type +'_scale_ordering').val()
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
    
    this.toggle_fields = function() {
        
        var partition = jQuery('#'+ this.type +'_partition').val();
        var html_h1 = '';
        
        var range = [];
        range['10_4_1'] = '1,2';
        range['10_4_2'] = '3,4,5';
        range['10_4_3'] = '6,7,8';
        range['10_4_4'] = '9,10';
        
        range['10_2_1'] = '1,2,3,4,5';
        range['10_2_2'] = '6,7,8,9,10';
        
        range['4_2_1'] = '1,2';
        range['4_2_2'] = '3,4';
        
        var label_h1 = [];
        label_h1['10_4_1'] = 'Reject';
        label_h1['10_4_2'] = 'Unacceptable';
        label_h1['10_4_3'] = 'Acceptable';
        label_h1['10_4_4'] = 'Match';
        
        /* START: Clear fields. */
        jQuery('#'+ this.type +'_heading_fields_wrapper').html('');
        /* END: Clear fields. */
        
        if(ITEM.scale > 0 && partition > 0) {
            
            //alert(partition +'>='+ ITEM.scale);
            if(parseInt(partition, 10) > parseInt(ITEM.scale, 10)) {
                
                //xyDIALOG.message('ERROR !!!', 'The <b>partition</b> must not be equal or larger than the <b>scale</b>.', '#partition');
                //return;
            }
            
            for(var x=1; x<=partition; x++) {
                
                var range_caption = ((range[ITEM.scale +'_'+ partition +'_'+ x]) ? range[ITEM.scale +'_'+ partition +'_'+ x] : '');
                var label_caption_h1 = ((label_h1[ITEM.scale +'_'+ partition +'_'+ x]) ? label_h1[ITEM.scale +'_'+ partition +'_'+ x] : '');
                
                html_h1 += '<tr><td><input id="'+ this.type +'_range_'+ x +'" type="text" style="width: 70px" value="'+ range_caption +'" /></td><td><input id="'+ this.type +'_label_'+ x +'" type="text" value="'+ label_caption_h1 +'" /></td></tr>';
            }
            
            if(html_h1 != '') {
                
                jQuery('#'+ this.type +'_heading_fields_wrapper').html('<table cellpadding="1" cellspacing="0"><tr><td>Range</td><td>Label</td></tr>'+ html_h1 +'</table>');
            }
        }
    }
}

jQuery(function(){
    
    //jQuery('img[title]').qtip({ style: { name: 'cream', tip: true } });
    
    SCREEN.itemlabel = '<?php echo addslashes($item['header'])?>';
    
    jQuery("input[name='"+ ITEM.type +"_scale']").click(function(){ 
        
        jQuery('#'+ ITEM.type +'_scale_other_field').val('');
        
        if(jQuery(this).val() == 'other') {
            
            jQuery('#'+ ITEM.type +'_scale_other_field').focus();
            
        } else ITEM.scale = jQuery(this).val();
        
        
        if(jQuery(this).val() >= 4) jQuery('#'+ ITEM.type +'_partition').val(4);
        else jQuery('#'+ ITEM.type +'_partition').val('');
        
        ITEM.toggle_fields();
    });
    
    jQuery('#'+ ITEM.type +'_scale_other_field').bind('keyup keydown paste focus', function(){
        
        jQuery('#'+ ITEM.type +'_scale_other').attr('checked', true);
        ITEM.scale = jQuery(this).val();
        
        if(jQuery(this).val() >= 4) jQuery('#'+ ITEM.type +'_partition').val(4);
        else jQuery('#'+ ITEM.type +'_partition').val('');
        
        ITEM.toggle_fields();
    });
    
    jQuery('#'+ ITEM.type +'_partition').bind('keyup', function(){ ITEM.toggle_fields(); });    
    
});

</script>
<div style="clear: both; text-align: left">

    <table cellspacing="0" cellpadding="0" width="100%">
        <tr><td>
                <table cellpadding="0" cellspacing="0">
                    <tr><td valign="top">

                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    <td valign="top"><b>Scale<b class="mandatory">*</b>:</b></td>
                                    <td valign="top">
                                        <div><input type="radio" name="<?php echo $item_type?>_scale" id="<?php echo $item_type?>_scale_10" value="10"<?php echo (($item['scale'] == 10) ? ' checked="checked"' : '')?> /> <label for="<?php echo $item_type?>_scale_10">10 Point Scale</label></div>
                                        <div><input type="radio" name="<?php echo $item_type?>_scale" id="<?php echo $item_type?>_scale_4" value="4"<?php echo (($item['scale'] == 4) ? ' checked="checked"' : '')?> /> <label for="<?php echo $item_type?>_scale_4">4 Point Scale</label></div>

                                        <div>
                                            <input type="radio" name="<?php echo $item_type?>_scale" id="<?php echo $item_type?>_scale_other" value="other"<?php echo ((! in_array($item['scale'], array(4, 10))) ? ' checked="checked"' : '')?> /> <label for="<?php echo $item_type?>_scale_other">Other</label>
                                            <input type="text" id="<?php echo $item_type?>_scale_other_field" style="width: 50px; text-align: right" onkeyup="return GBL.numOnly(event)" onkeydown="return GBL.numOnly(event)" value="<?php echo ((! in_array($item['scale'], array(4, 10))) ? $item['scale'] : '')?>" />
                                        </div>
                                    </td>
                                </tr>
                            </table>

                        </td>
                        <td valign="top" style="padding-left: 2px">
                            <table cellpadding="1" cellspacing="0">
                                <tr><td valign="top" width="200"><b>Heading</b><b class="mandatory">*</b></td></tr>
                                <tr><td valign="top" id="<?php echo $item_type?>_heading_fields_wrapper">
                                    <?php
                                    if($item['partition'] > 0) {
                                        ?>
                                        <table cellspacing="0" cellpadding="1">
                                            <tbody>
                                                <tr><td>Range <img alt="Separate range by comma. i.e. 1,2,3 ..." title="Separate range by comma. i.e. 1,2,3 ..." src="<?php echo $docroot . 'media/images/tip.png'?>" /></td>
                                                    <td>Label</td>
                                                </tr>
                                                <?php
                                                $x = 0;
                                                foreach($rl as $range => $label) {
                                                    
                                                    $x++;
                                                    $range = xy_ssstring_for_view($range);
                                                    $label = xy_ssstring_for_view($label);
                                                    
                                                    ?>
                                                    <tr><td><input type="text" value="<?php echo $range?>" style="width: 70px" id="<?php echo $item_type?>_range_<?php echo $x?>" /></td>
                                                        <td><input type="text" value="<?php echo $label?>" id="<?php echo $item_type?>_label_<?php echo $x?>" /></td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>                                                
                                            </tbody>
                                        </table>
                                        <?php
                                    }
                                    ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr><td colspan="2" style="padding-top: 5px">
                        <b>Scale ordering<b class="mandatory">*</b>:</b>
                        <select id="<?php echo $item_type?>_scale_ordering">
                            <option value="asc"<?php echo (($item['ordering'] == 'asc') ? ' selected="selected"' : '')?>>Ascending</option>
                            <option value="desc"<?php echo (($item['ordering'] == 'desc') ? ' selected="selected"' : '')?>>Descending</option>
                        </select>
                        </td>
                    </tr>
                    <tr><td colspan="2"><div style="padding-top: 2px"><b>Partition<b class="mandatory">*</b>:</b> <input id="<?php echo $item_type?>_partition" type="text" style="width: 50px; text-align: right" onkeyup="return GBL.numOnly(event)" onkeydown="return GBL.numOnly(event)" value="<?php echo $item['partition']?>" /></div></td></tr>
                </table>            
            </td>
        </tr>    
        
    </table>
</div>