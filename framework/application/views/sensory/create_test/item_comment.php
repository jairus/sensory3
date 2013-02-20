<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

include_once 'procedures.inc.php';

if(isset($_POST)) { extract($_POST); }
$item = array();
if($item_id > 0) {
    
    $item = $_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items'][$item_id - 1];
    
    array_walk($item, 'xy_ssstring_for_view_byref');
    
    $label = $item['label'];
    $required = $item['required'];

} else $required = 'no';
?>
<script type="text/javascript">
var ITEM = new function() {
    
    this.id = <?php echo (double) $item_id?>;
    this.type = '<?php echo $item_type?>';
    
    this.ok = function() {
        
        var args = {
            type : 'item',
            item_id : ITEM.id,
            t : (new Date).getTime(),
            screen_code : SCREEN.selection_code,
            screen_count : SCREEN.selection_count,
            rta_id : Q.rta_id,

            item : this.type,
            required : jQuery("input[name='"+ ITEM.type +"_roption']:checked").val()
        };
        
        if(jQuery('#'+ ITEM.type +'_label_field').val().trim().length) {
            jQuery.extend(args, { label : jQuery('#'+ ITEM.type +'_label_field').val() });
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
    
    var d = [];
    d['affective'] = 'What do you like or dislike about the product?';
    d['other'] =  '<?php echo addslashes($label)?>';

    var dlabel_trigger = jQuery('#<?php echo $item_type?>_default_label_trigger');
    var dlabel_field = jQuery('#<?php echo $item_type?>_label_field');
    
    dlabel_trigger.change(function(){ if(jQuery(this).val() != '') { dlabel_field.val(d[jQuery(this).val()].replace(/\&quot\;/g, '"')).focus(); } });
    
    if(d['affective'] == dlabel_field.val().trim()) dlabel_trigger[0].selectedIndex = 1;
    else dlabel_trigger[0].selectedIndex = 2;
    
    dlabel_trigger.trigger('change');
});
</script>
<div style="clear: both; text-align: left">
    
    <div><b>Label</b>: System default
        <select id="<?php echo $item_type?>_default_label_trigger">
            <option value="">Select:</option>
            <option value="affective">Affective</option>
            <option value="other">Other</option>
        </select>
    </div>

    <div style="color: #777">This label will be visible just on top of the comment field.</div>
    <div style="padding-top: 2px"><input type="text" id="<?php echo $item_type?>_label_field" maxlength="250" style="width: 450px" value="<?php echo $label?>" /></div>
    
    <div style="padding-top: 10px"><b>Option</b>: Is the field required to be filled-in?</div>
    <div></div>
    <div>
        <input type="radio" name="<?php echo $item_type?>_roption" id="<?php echo $item_type?>_roption1" value="yes"<?php echo (($required == 'yes') ? ' checked="checked"' : '')?> /> <label for="<?php echo $item_type?>_roption1">Yes</label>
        <input type="radio" name="<?php echo $item_type?>_roption" id="<?php echo $item_type?>_roption2" value="no"<?php echo (($required == 'no') ? ' checked="checked"' : '')?> /> <label for="<?php echo $item_type?>_roption2">No</label>
    </div>
    
</div>