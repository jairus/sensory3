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
            item_id : this.id,
            t : (new Date).getTime(),
            screen_code : SCREEN.selection_code,
            screen_count : SCREEN.selection_count,
            rta_id : Q.rta_id,

            item : this.type,
            i : i.val(),
            nodiff : jQuery('#'+ this.type +'_nodiff').is(':checked')
        };
        
        var nodiff_instruction = jQuery('#'+ ITEM.type +'_nodiff_instruction');
        if(! nodiff_instruction.is(':hidden')) {
            
            if(nodiff_instruction.val().length) {
                
                jQuery.extend(args, { nodiff_i : nodiff_instruction.val() });
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
    
    jQuery('#'+ ITEM.type +'_nodiff_instruction_wrapper').toggle(<?php echo $item['nodiff']?>);
    
    jQuery('#'+ ITEM.type +'_nodiff').click(function(){
        
        if(jQuery(this).is(':checked')) {
            
            jQuery('#'+ ITEM.type +'_nodiff_instruction_wrapper').show();
            jQuery('#'+ ITEM.type +'_nodiff_instruction').focus();
            
        } else jQuery('#'+ ITEM.type +'_nodiff_instruction_wrapper').hide();
        
    });
    
    jQuery('label').disableSelection();
});
</script>
<div style="clear: both; text-align: left">
    <table cellspacing="0" cellpadding="0" width="100%">
        
        <tr><td title="needs to be filled-in"><b>Instruction</b><b class="mandatory">*</b><br /><a title="default" style="font: 12px Verdana" id="<?php echo $item_type?>_default_instruction_trigger" href="javascript:;">Apply system default</a></td></tr>
        <tr><td><textarea id="<?php echo $item_type?>_instruction" style="height: 120px; width: 485px; font: 12px Verdana; color: #333"><?php echo $item['i']?></textarea></td></tr>
        
        <tr><td style="padding-top: 5px">
                
                <div><input type="checkbox" id="<?php echo $item_type?>_nodiff"<?php echo (($item['nodiff'] == 'true') ? ' checked="checked"' : '')?> /> <label for="<?php echo $item_type?>_nodiff">No difference</label>.</div>
                <div id="<?php echo $item_type?>_nodiff_instruction_wrapper" style="padding-left: 24px">
                    <div>Instruction</div>
                    <div><textarea id="<?php echo $item_type?>_nodiff_instruction"><?php echo $item['nodiff_i']?></textarea></div>
                </div>
                
            </td>
        </tr>
    </table>
</div>