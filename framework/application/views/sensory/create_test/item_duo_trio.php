<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

include_once 'procedures.inc.php';

if(isset($_POST)) { extract($_POST); }
$item = array();
if($item_id > 0) {
    
    $item = $_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items'][$item_id - 1];
    $instruction = $item['i'];
    
    array_walk($item, 'xy_ssstring_for_view_byref');
}
?>
<script type="text/javascript">
var ITEM = new function() { 
    
    this.id = <?php echo (double) $item_id?>;
    this.instruction = '<?php echo $instruction?>';
    this.type = '<?php echo $item_type?>';
    
    this.ok = function() {
        
        var i = jQuery('#'+ this.type +'_instruction');
        if(i.val().trim() == '') {
            
            i.focus();
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
            i : i.val()
        };

        /*var ref_code = jQuery("input[name='"+ ITEM.type +"_ref_code']");
        if(ref_code && ref_code.length) {
            
            if(jQuery("input[name='"+ ITEM.type +"_ref_code']:checked").val()) {
                
                jQuery.extend(args, { ref_code_value : jQuery("input[name='"+ ITEM.type +"_ref_code']:checked").val() });
            } else {
                
                jQuery('#'+ this.type +'_refcode_label')
                    .css('background', '#CC0000')
                    .css('color', '#FFCC00')
                    .css('padding', '5px');
                return;
            }
        }
        */
        if(jQuery("input[name='"+ ITEM.type +"_ref_code_position']:checked").val()) {
            
            jQuery.extend(args, { ref_code_position : jQuery("input[name='"+ ITEM.type +"_ref_code_position']:checked").val() });
            
        } else {
            
            jQuery('#'+ this.type +'_refcode_position_label')
                .css('background', '#CC0000')
                .css('color', '#FFCC00')
                .css('padding', '5px');
            return;
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
}

jQuery(function(){
    
    SCREEN.itemlabel = '<?php echo addslashes($item['header'])?>';
    
    jQuery('#'+ ITEM.type +'_default_instruction_trigger').click(function(){
        
        var tmp = '', caption1 = 'Apply system default', caption2 = 'Cancel system default';
        if(jQuery(this).html() == caption1) {
            
            jQuery(this).html(caption2);
            tmp = STEP_3.default_instructions[ITEM.type];
        }
        else
        if(jQuery(this).html() == caption2) {
            
            jQuery(this).html(caption1);
            tmp = ITEM.instruction.replace(/\[nl\]/g, "\n");
        }
        
        jQuery('#'+ ITEM.type +'_instruction').val(tmp).focus();
        
    });
    
    var html = '', x = 0;
    if(Q.codes_1.length) {
        
        for(x=0; x<Q.codes_1.length; x++) { html += '<div><input type="radio" name="'+ ITEM.type +'_ref_code" id="'+ ITEM.type +'_ref_code_'+ Q.codes_1[x] +'" value="'+ Q.codes_1[x] +'"'+ ((ITEM.ref_code == Q.codes_1[x]) ? ' checked="checked"' : '') +' /> <label for="'+ ITEM.type +'_ref_code_'+ Q.codes_1[x] +'"><b style="font: bold 24px Verdana">'+ Q.codes_1[x] +'</label></div>'; }
        jQuery('#'+ ITEM.type +'_primary_code_wrapper').html(html);
    }
    
    /*if(Q.codes_2.length) {
        
        for(x=0; x<Q.codes_2.length; x++) { html_2 += '<div><input type="radio" name="'+ ITEM.type +'_ref_code" id="'+ ITEM.type +'_ref_code_'+ Q.codes_2[x] +'" value="'+ Q.codes_2[x] +'"'+ ((ITEM.ref_code == Q.codes_2[x]) ? ' checked="checked"' : '') +' /> <label for="'+ ITEM.type +'_ref_code_'+ Q.codes_2[x] +'"><b style="font: bold 24px Verdana">'+ Q.codes_2[x] +'</b></label></div>'; }
        jQuery('#'+ ITEM.type +'_secondary_code_wrapper').html(html_2);
    }*/
    
    /*jQuery("input[name='"+ ITEM.type +"_ref_code']").click(function(){
        
        jQuery('#'+ ITEM.type +'_refcode_label')
            .css('background', '#FFF')
            .css('color', '#000')
            .css('padding', '0');            
    });*/
    
    jQuery("input[name='"+ ITEM.type +"_ref_code_position']").click(function(){
        
        jQuery('#'+ ITEM.type +'_refcode_position_label')
            .css('background', '#FFF')
            .css('color', '#000')
            .css('padding', '0');            
    });
});
</script>
<div style="clear: both; text-align: left">

    <table cellspacing="0" cellpadding="0" width="100%">
        
        <tr><td title="needs to be filled-in"><b>Instruction</b><b class="mandatory">*</b><br /><a title="default" style="font: 12px Verdana" id="<?php echo $item_type?>_default_instruction_trigger" href="javascript:;">Apply system default</a></td></tr>
        <tr><td><textarea id="<?php echo $item_type?>_instruction" style="height: 120px; width: 485px; font: 12px Verdana; color: #333"><?php echo $item['i']?></textarea></td></tr>

        <tr><td style="padding: 5px 0 10px 0">

            <table cellpadding="0" cellspacing="0">
                <tr>
                    <td><div id="<?php echo $item_type?>_refcode_position_label" style="display: inline">Set the reference's position:</div></td>
                    <td>
                        <input type="radio" name="<?php echo $item_type?>_ref_code_position" id="<?php echo $item_type?>_ref_code_position_left" value="left"<?php echo (($item['ref_code_position'] == 'left') ? ' checked="checked"' : '')?> /> <label for="<?php echo $item_type?>_ref_code_position_left"><b style="font: bold 12px Verdana">Left</b></label>
                        <input type="radio" name="<?php echo $item_type?>_ref_code_position" id="<?php echo $item_type?>_ref_code_position_center" value="center"<?php echo (($item['ref_code_position'] == 'center') ? ' checked="checked"' : '')?> /> <label for="<?php echo $item_type?>_ref_code_position_center"><b style="font: bold 12px Verdana">Center</b></label>
                        <input type="radio" name="<?php echo $item_type?>_ref_code_position" id="<?php echo $item_type?>_ref_code_position_right" value="right"<?php echo (($item['ref_code_position'] == 'right') ? ' checked="checked"' : '')?> /> <label for="<?php echo $item_type?>_ref_code_position_right"><b style="font: bold 12px Verdana">Right</b></label>
                    </td>
                </tr>
            </table>

            </td>
        </tr>
    </table>
</div>