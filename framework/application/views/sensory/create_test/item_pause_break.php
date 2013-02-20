<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

include_once 'procedures.inc.php';

if(isset($_POST)) { extract($_POST); }
$item = array();
if($item_id > 0) {
    
    $item = $_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items'][$item_id - 1];
    
    array_walk($item, 'xy_ssstring_for_view_byref');
    
    $text = $item['text'];
    $timer_value = $item['timer_value'];
    $timer_visibility = $item['timer_visibility'];
    $photo = $item['photo'];
    $url_append = '/' . $photo;
    
} else {
    
    $timer_visibility = 'shown';
    $screen_code = $screen_count = 0;
}

//echo '<pre>';
//print_r($item);
//echo '</pre>';

$upload_url = $docroot . 'screen/async_pausebreak_photo_upload/' . $item_id . $url_append;
?>
<script type="text/javascript">         
var ITEM = new function() {
    
    this.id = <?php echo (double) $item_id?>;
    this.screen_code = '<?php echo $screen_code?>';
    this.screen_count = <?php echo $screen_count?>;
    this.type = '<?php echo $item_type?>';
    
    this.photo = '<?php echo $photo?>';
    this.photo_del_flag = false;
    
    this.ok = function() {
        
        if(jQuery('#'+ this.type +'_photo').val().trim() != '') {
            
            GBL.loader();
            
            /* START: Attach event that will be triggered when iframe is loaded
             * (in this case: after the actual file upload). */
            jQuery('#'+ this.type +'_ifra_upload').bind('load', function() {
                
                var response = jQuery(this).contents().find("body").html();
                ITEM.photo = response;
                ITEM.submit();
                
                GBL.loader(false);
            });
    
            jQuery('#'+ this.type +'_upload').submit();

        } else this.submit();
    }
    
    this.submit = function() {
        
        if(this.photo_del_flag) { this.photo = ''; }
        
        var args = {            
            type : 'item',
            item_id : ITEM.id,
            t : (new Date).getTime(),
            screen_code : SCREEN.selection_code,
            screen_count : SCREEN.selection_count,
            rta_id : Q.rta_id,
            
            item : this.type,
            timer_visibility : jQuery("input[name='"+ this.type +"_timer_voption']:checked").val()            
        };
        
        /* Just don't include fields with blank values since
         * they will be converted with "null" values on "json_encode()".
         * */
        if(this.photo != '') { jQuery.extend(args, { photo : ITEM.photo }); }
        if(jQuery('#'+ this.type +'_text').val().trim().length) { jQuery.extend(args, { text : jQuery('#'+ this.type +'_text').val() }); }
        if(jQuery('#'+ this.type +'_timer_value').val().trim().length) { jQuery.extend(args, { timer_value : jQuery('#'+ this.type +'_timer_value').val() }); }
        
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
                    
                    if(ITEM.photo_del_flag) { ITEM.photo_del_file(); }
                    
                } else { Popup.alert('<b>An ERROR has occured</b>.<br /><br />There\'s no response from your recent request.', { title : 'ERROR' }); }
            }, 'json'
        );
    }
    
    this.photo_upload = function() {
        
        if(jQuery('#'+ this.type +'_photo').val().trim() != '') {
            
            GBL.loader();
            
            /* START: Attach event that will be triggered when iframe is loaded
             * (in this case: after the actual file upload). */
            jQuery('#'+ this.type +'_ifra_upload').bind('load', function() {

                //alert(jQuery("#ifra_upload")[0].contentDocument.body.innerHTML);
                var response = jQuery(this).contents().find("body").html();
                
                jQuery('#'+ ITEM.type +'_img_preview_wrapper').show();
                jQuery('#'+ ITEM.type +'_img').attr('src', DOCROOT +'TEMP/'+ response +'_preview.jpg?t='+ (new Date).getTime());
                jQuery('#'+ ITEM.type +'_photo').val('');
                
                ITEM.photo = response;
                
                GBL.loader(false);
            });
    
            jQuery('#'+ this.type +'_upload').submit();
        }
    }
    
    this.photo_del = function(confirmed) {
        
        if(! confirmed) {
            
            Popup.dialog({
                title : 'DELETE',
                message : 'Are you sure you want to <b>delete</b> this photo?',
                buttons: ['Yes', 'No, I Cancel'],
                buttonClick: function(button) {
                    
                    if(button == 'Yes') { ITEM.photo_del(1); }
                    
                    POPUPJS.overlay_show();
                    jQuery('#popupjs_btn_ok').attr('onclick', 'ITEM.ok()'); /* Restore event of the first Popup. */                                
                },
                width: '420px'
            });
            
        } else {
            
            ITEM.photo_del_flag = true;
            jQuery('#'+ ITEM.type +'_img_preview_wrapper').hide();
            jQuery('#'+ ITEM.type +'_img').attr('src', '').attr('alt', ' ');
        }
    }
    
    /* Deletes the actual image in the File System. */
    this.photo_del_file = function() {
        
        jQuery.post(
            DOCROOT +'screen/async_pausebreak_photo_delete',
            {
                item_id : ITEM.id,
                photo : ITEM.photo,
                screen_code : ITEM.screen_code,
                screen_count : ITEM.screen_count,
                t : (new Date).getTime()
            },
            function() { //STEP_3.screen_ae(ITEM.screen_code);
            
            }
        );
    }
}

jQuery(function(){
    
    SCREEN.itemlabel = '<?php echo addslashes($item['header'])?>';
});
</script>
<div style="clear: both; text-align: left">
    
    <div>Enter a <b>text</b> that will <u>appear</u> on screen:</div>

    <div><textarea style="width: 485px; font: 12px Verdana; color: #333" id="<?php echo $item_type?>_text"><?php echo $text?></textarea></div>
    
    <table cellpadding="0" cellspacing="0">
        <tr><td valign="top">
                <div>Browse a <b>photo</b> from your computer:</div>
                <form target="<?php echo $item_type?>_ifra_upload" style="margin: 0; padding: 0" action="<?php echo $upload_url?>" id="<?php echo $item_type?>_upload" method="post" enctype="multipart/form-data">
                    <input name="<?php echo $item_type?>_photo" id="<?php echo $item_type?>_photo" type="file" />
                    <input type="button" value="upload" onclick="ITEM.photo_upload()" />
                </form>
                <iframe id="<?php echo $item_type?>_ifra_upload" name="<?php echo $item_type?>_ifra_upload" style="border: 0; margin: 0; display: none"></iframe>
            </td>
            <td valign="top">
                <div style="padding: 2px 5px 5px 5px">
                    <?php                    
                    if($photo) {

                        $attr = ' src="' . $docroot . 'TEMP/' . $photo . '_preview.jpg?t=' . time() . '" ';
                        
                    } else {
                        
                        $attr = ' alt="" ';
                        $display = '; display: none';
                    }
                    ?>
                    <div id="<?php echo $item_type?>_img_preview_wrapper" style="margin-bottom: 2px<?php echo $display?>"><b>Preview <a title="delete" href="javascript:ITEM.photo_del()"><img src="<?php echo $docroot?>media/images/16x16/delete.png" /></a>:</b></div>
                    <img id="<?php echo $item_type?>_img" <?php echo $attr?> />
                </div>
            </td>
            
        </tr>        
    </table>
    
    <div style="margin: 20px 0 20px 0">
        <div>Include <b>timer</b> <span style="color: #777">(a countdown timer that will appear on screen depending on your setting)</span>:</div>
        <table cellpadding="0" cellspacing="0" style="margin-left: 20px">
            <tr><td align="right">In <u>seconds</u></td>
                <td style="padding-left: 5px"><input type="text" id="<?php echo $item_type?>_timer_value" maxlength="3" style="width: 50px; text-align: right" value="<?php echo $timer_value?>" /></td>
            </tr>
            <tr><td align="right"><b>Show?</b></td>
                <td><input type="radio" name="<?php echo $item_type?>_timer_voption" id="<?php echo $item_type?>_timer_voption1" value="shown"<?php echo (($timer_visibility == 'shown') ? ' checked="checked"' : '')?> /> <label for="<?php echo $item_type?>_timer_voption1">Yes</label>
                    <input type="radio" name="<?php echo $item_type?>_timer_voption" id="<?php echo $item_type?>_timer_voption2" value="hidden"<?php echo (($timer_visibility == 'hidden') ? ' checked="checked"' : '')?> /> <label for="<?php echo $item_type?>_timer_voption2">No</label>
                </td>
            </tr>
        </table>
    </div>
    
</div>