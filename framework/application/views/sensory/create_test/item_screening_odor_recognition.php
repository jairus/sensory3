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
    
    $codes = explode(',', $item['codes']);

} else $codes = array();

$codes = json_encode($codes);
?>
<script type="text/javascript">         
var ITEM = new function() {
    
    this.id = <?php echo (double) $item_id?>;
    this.type = '<?php echo $item_type?>';
    this.instruction = '<?php echo $instruction?>';
    this.codes = [];

    this.ok = function() {
        
        var i = jQuery('#'+ this.type +'_instruction');
        if(i.val().trim() == '') {
            
            i.focus();
            return;
        }
        
        var codes = [];
        jQuery('#'+ ITEM.type +'_codes_wrapper li b').each(function(){
            
            codes.push(jQuery(this).html());
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
    
    this.randomize = function() {
        
        var codes = this.codes;
        
        var positions = [], ordering = [], x = 0, html = '';
        
        while(positions.length < this.codes.length) {
            
            var position = Math.floor(Math.random() * this.codes.length);
            
            if(jQuery.inArray(position, positions) == -1) {
                positions.push(position);
            }
        }
        
        for(x=0; x<positions.length; x++) { for(var y=0; y<codes.length; y++) { if(positions[x] == y) { ordering.push(codes[y]); } } }
        
        for(x=0; x<ordering.length; x++) { html += '<li><b>'+ ordering[x] +'</b></li>'; }
        if(html != '') jQuery('#'+ ITEM.type +'_codes_wrapper').html(html);
    }
 }
 
 jQuery(function(){
    
    <?php
    if($codes != '[]') echo 'ITEM.codes = ' . $codes . ';';
    else echo 'ITEM.codes = Q.codes_1;';
    ?>
    
    SCREEN.itemlabel = '<?php echo $item['header']?>';
    
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
    
    if(ITEM.codes.length) {
        
        var html = '';
        for(var x=0; x<ITEM.codes.length; x++) { html += '<li><b>'+ ITEM.codes[x] +'</b></li>'; }        
        if(html != '') jQuery('#'+ ITEM.type +'_codes_wrapper').html(html);
    }    
 });
 </script>
 <div style="clear: both; text-align: left">
     
     <table cellpadding="0" cellspacing="0">
         <tr><td title="needs to be filled-in"><b>Instruction</b><b class="mandatory">*</b><br /><a title="default" style="font: 12px Verdana" id="<?php echo $item_type?>_default_instruction_trigger" href="javascript:;">Apply system default</a></td></tr>
         <tr><td><textarea id="<?php echo $item_type?>_instruction" style="height: 120px; width: 485px; font: 12px Verdana; color: #333"><?php echo $item['i']?></textarea></td></tr>
         
         <tr><td style="padding-top: 5px">
             <div><b>Sample codes:</b></div>
             <table cellpadding="0" cellspacing="0">
                 <tr><td valign="top"><input type="button" value="Change Order" onclick="ITEM.randomize()" /></td>
                     <td style="padding-left: 5px">
                         
                         <ul id="<?php echo $item_type?>_codes_wrapper" style="padding: 0; margin: 0; list-style: none"></ul>
                     </td>
                 </tr>
             </table>
             </td>
         </tr>
     </table>
     
 </div>