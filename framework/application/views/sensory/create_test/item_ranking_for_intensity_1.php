<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
if(isset($_POST)) { extract($_POST); }
$item = array();
if($item_id > 0) {

    $item = $_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items'][$item_id - 1];
    
    $instruction = $item['i'];    
    function xy_screen_and_item_string_decode(&$str) { $str = str_replace(array('[quote]', '[nl]'), array("'", "\n"), $str); }
    array_walk($item, 'xy_screen_and_item_string_decode');
    
    $codes = explode(',', $item['codes']);
    $nodiff = (($item['nodiff'] == 'true') ? ' checked="checked"' : '');
    
} else $codes = array();

$codes = json_encode($codes);
?>
<style type="text/css">
#<?php echo $item_type?>_code_wrapper { list-style-type: none; margin: 0; padding: 0; clear: both }
#<?php echo $item_type?>_code_wrapper li { margin: 3px 3px 3px 0; padding-top: 20px; float: left; width: 100px; height: 90px; font-size: 4em; text-align: center; cursor: default }

#<?php echo $item_type?>_rank_wrapper { list-style-type: none; margin: 0; padding: 0; }
#<?php echo $item_type?>_rank_wrapper li { color: #A23030; margin: 3px 3px 3px 0; padding: 1px; float: left; width: 100px; font-size: 1em; text-align: center }
</style>
<script type="text/javascript">
var ITEM = new function() {
    
    this.id = <?php echo (double) $item_id?>;
    this.instruction = '<?php echo $instruction?>';
    this.codes = [];
    this.type = '<?php echo $item_type?>';
    
    this.ok = function() {
        
        var i = jQuery('#'+ this.type +'_instruction'), codes = [];
        if(i.val().trim() == '') {
            
            i.focus();
            return;
        }
        
        jQuery('#'+ this.type +'_code_wrapper li').each(function(){
            if(jQuery(this).html() != '') codes.push(jQuery(this).html());
        });
        
        var args = {
            type : 'item',
            item_id : ITEM.id,
            t : (new Date).getTime(),
            screen_code : SCREEN.selection_code,
            screen_count : SCREEN.selection_count,
            rta_id : Q.rta_id,

            item : this.type,
            i : i.val(),
            codes : codes.toString()
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
}

jQuery(function(){
    
    <?php
    if($codes == '[]') echo "ITEM.codes = Q.codes_1;";
    else echo "ITEM.codes = " . $codes . ";";
    ?>
    
    SCREEN.itemlabel = '<?php echo $item['header']?>';
    
    var codes = '';
    for(var x=0; x<ITEM.codes.length; x++) {
        
        codes += '<li class="ui-state-default">'+ ITEM.codes[x] +'</li>';
    }
    if(codes != '') jQuery('#'+ ITEM.type +'_code_wrapper').append(codes);
    jQuery('#'+ ITEM.type +'_code_wrapper').sortable({ axis: 'x' }).disableSelection();
    
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
});
</script>
<div style="clear: both; text-align: left">
    <table cellspacing="0" cellpadding="0">
        <tr><td title="needs to be filled-in"><b>Instruction</b><b class="mandatory">*</b><br /><a title="default" style="font: 12px Verdana" id="<?php echo $item_type?>_default_instruction_trigger" href="javascript:;">Apply system default</a></td></tr>
        <tr><td>
                <textarea id="<?php echo $item_type?>_instruction" style="height: 120px; width: 485px; font: 12px Verdana; color: #333"><?php echo $item['i']?></textarea>
            </td>
        </tr>
        <tr><td>Codes <b>ordering</b> to appear on screen <span style="color: #999">(drag the boxes to change)</span>:</span></td></tr>
        <tr><td height="100" valign="top">
                <table cellspacing="0" cellpadding="0" align="center">
                    <tr><td>

                        <ul id="<?php echo $item_type?>_rank_wrapper">
                            <li>1st</li>
                            <li>2nd</li>
                            <li>3rd</li>
                        </ul>
                        <ul id="<?php echo $item_type?>_code_wrapper"></ul>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <!--tr><td title="no difference" style="padding-top: 5px"><input type="checkbox" name="<?php echo $item_type?>_nodiff" id="<?php echo $item_type?>_nodiff"<?php echo $nodiff?> /> <label for="<?php echo $item_type?>_nodiff">No difference</label>.</td></tr-->
    </table>
</div>