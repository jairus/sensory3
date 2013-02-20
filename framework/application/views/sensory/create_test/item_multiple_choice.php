<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

include_once 'procedures.inc.php';

if(isset($_POST)) { extract($_POST); }
$item = array();
if($item_id > 0) {
    
    $item = $_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items'][$item_id - 1];
    
    array_walk($item, 'xy_ssstring_for_view_byref');
    
    $question = $item['question'];
    
    $tmp = explode('[=ROW=]', $item['choices']);
    $item_count = count($tmp);
    $ctr = 0;
    $choices = '';
    foreach($tmp as $choice) {
        
        $ctr++;
        $choices .= '<div id="' . $item_type . '_choice_' . $ctr . '" style="margin-top: 2px"><a title="delete" href="javascript:ITEM.remove(' . $ctr . ')"><img src="' . $docroot . 'media/images/16x16/delete.png"></a> <a title="edit" href="javascript:ITEM.edit_init(' . $ctr . ')"><img src="' . $docroot . 'media/images/16x16/edit.png"></a> <span><b>' . stripslashes($choice) . '</b></span></div>';
    }
    
} else $choices = '<span style="color: #777">(empty)</span>';
?>
<script type="text/javascript">
var ITEM = new function() {
    
    this.id = <?php echo (double) $item_id?>;
    this.item_count = <?php echo (int) $item_count?>;
    this.edit_flag = false;
    this.edit_id = 0;
    this.type = '<?php echo $item_type?>';
    
    this.add = function() {
        
        var content = jQuery('#'+ this.type +'_choice_content');
    
        if(content.val().trim() == '') {
            
            content.focus();
            return;
        }
        
        if(this.edit_flag) {
            
            jQuery('#'+ this.type +'_choice_'+ this.edit_id +' span').html('<b>'+ content.val() +'</b>');
            this.edit_cancel();
        }
        else {
            
            if(this.item_count == 0) jQuery('#'+ this.type +'_choices_wrapper').html('');
            
            this.item_count++;
            jQuery('#'+ this.type +'_choices_wrapper').append('<div id="'+ this.type +'_choice_'+ this.item_count +'" style="margin-top: 2px"><a title="delete" href="javascript:ITEM.remove('+ this.item_count +')"><img src="'+ DOCROOT +'media/images/16x16/delete.png"></a> <a title="edit" href="javascript:ITEM.edit_init('+ this.item_count +')"><img src="'+ DOCROOT +'media/images/16x16/edit.png"></a> <span><b>'+ content.val() +'</b></span></div>');
        }
        
        content.val('');
    }
    
    this.add_keypressed = function(e) {
        
        var key = GBL.get_keypressed(e);
        if(key == 13 && jQuery('#'+ this.type +'_btn_ae').val() == 'add') this.add();
    }
    
    this.edit_init = function(id) {
        
        this.edit_flag = true;
        this.edit_id = id;
        
        jQuery('#'+ this.type +'_btn_ae').val('update');
        jQuery('#'+ this.type +'_btn_cancel').show();
        jQuery('#'+ this.type +'_choice_content').val(jQuery('#'+ this.type +'_choice_'+ id +' span').html().replace(/(<([^>]+)>)/ig, ""));
        jQuery('#'+ this.type +'_choice_content').focus();
        
    }
    
    this.edit_cancel = function() {
        
        this.edit_flag = false;
        this.edit_id = 0;

        jQuery('#'+ this.type +'_btn_ae').val('add');
        jQuery('#'+ this.type +'_btn_cancel').hide();
        
        jQuery('#'+ this.type +'_choice_content').val('');
    }
    
    this.remove = function(id) {
        
        jQuery('#'+ this.type +'_choice_'+ id).remove();
        this.item_count--;
        
        if(this.item_count == 0) jQuery('#'+ this.type +'_choices_wrapper').html('<span style="color: #777">(empty)</span>');
        
    }
    
    this.ok = function() {
        
        var q = jQuery('#'+ this.type +'_question'), choices = [];
        if(q.val().trim() == '') {
            
            q.focus();
            return;
        }

        if(this.item_count == 0) {
            
            jQuery('#'+ this.type +'_choice_content').val('').focus();
            return;
        }
        
        jQuery('#'+ this.type +'_choices_wrapper span b').each(function(){
            choices.push(jQuery(this).html().replace(/,/g, '[=COMMA=]'));
            this.item_count++;
        });
        
        var args = {
            type : 'item',
            item_id : ITEM.id,
            t : (new Date).getTime(),
            screen_code : SCREEN.selection_code,
            screen_count : SCREEN.selection_count,
            rta_id : Q.rta_id,

            item : this.type,
            question : q.val(),
            choices : choices.toString().replace(/,/g, '[=ROW=]').replace(/\[\=COMMA\=\]/g, ',')
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
    
    SCREEN.itemlabel = '<?php echo addslashes($item['header'])?>';
    
    jQuery('#'+ ITEM.type +'_btn_cancel').hide();
});
</script>
<div style="clear: both; text-align: left">

<table cellspacing="0" cellpadding="0">
    
    <tr><td valign="top">
            <div>Enter the <b>question</b> to appear for this "Multiple Choice" <span style="color: #777">(up to 100 characters only)</span>:</div>
            <div style="margin: 2px 0 5px 0"><input maxlength="100" id="<?php echo $item_type?>_question" type="text" style="width: 485px" value="<?php echo $question?>" /></div>
            
            <div>Add an <u>item</u> where to choose from:</div>
            
            <div style="margin: 2px 0 5px 20px">
                
                <input maxlength="50" type="text" style="width: 300px" id="<?php echo $item_type?>_choice_content" onkeypress="ITEM.add_keypressed(event)" /> <input id="<?php echo $item_type?>_btn_ae" type="button" onclick="ITEM.add()" value="add" />
                <input id="<?php echo $item_type?>_btn_cancel" type="button" onclick="ITEM.edit_cancel()" value="cancel" />
                
            </div>
            
            <div style="padding-left: 20px">
                <div><b>Choices</b>:</div>
    
                <div id="<?php echo $item_type?>_choices_wrapper" style="padding-left: 10px">
                    <?php echo $choices?>
                </div>
            </div>
        </td>
    </tr>
</table>
</div>