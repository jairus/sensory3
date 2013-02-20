<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
if(isset($_POST)) { extract($_POST); }
$item = array();
$can_initiate_sqs_attr = false;
if($item_id > 0) {
    
    $item = $_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items'][$item_id - 1];
    function xy_screen_and_item_string_decode(&$str) { $str = str_replace(array('[quote]', '[nl]', '[comma]'), array("'", "\n", ","), $str); }
    array_walk($item, 'xy_screen_and_item_string_decode');    
    $can_initiate_sqs_attr = true;
    
    $partition = $item['partition'];
    
    if($item['heading']) $heading = explode('[=ROW=]', $item['heading']);
    
} else { /* Adding NEW ITEM. */
    
    $current_screen_item_count = count($_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items']);
    if($current_screen_item_count > 0) {

        $tmp = $_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items'][$current_screen_item_count - 1];
        if($tmp['type'] == 'sqs_main') {
            
            $can_initiate_sqs_attr = true;
            $partition = $tmp['partition'];
        }
        
    } else {

        if(! empty($_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count - 1])) {

            $previous_screen_item_count = count($_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count - 1]['items']);
            $tmp = $_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count - 1]['items'][$previous_screen_item_count - 1];
            if($tmp['type'] == 'sqs_main') {
                
                $can_initiate_sqs_attr = true;
                $partition = $tmp['partition'];
            }
        }
    }
    
    $partition = 4; /* Sep 09, 2012 - Partition is 4 by default. Does not vary with the SQS attribute anymore. */
    
    /* Heading default. */
    $heading[] = 'NOT NEARLY ENOUGH';
    $heading[] = 'NOT ENOUGH';
    $heading[] = 'TOO MUCH';
    $heading[] = 'MUCH TOO MUCH';
}

if($can_initiate_sqs_attr) {

    ?>
    <script type="text/javascript">
    var ITEM = new function() {

        this.id = <?php echo (double) $item_id?>;
        this.type = '<?php echo $item_type?>';

        this.item_count = <?php echo (int) $attr_count?>;
        this.edit_flag = false;
        this.edit_id = 0;
        this.partition = <?php echo (int) $partition?>;

        this.ok = function() {

            var head_attr = [];
            for(var x=1; x<=this.partition; x++) {
                
                var h = jQuery('#'+ this.type +'_value_'+ x).val();                
                if(h.trim() != '') head_attr.push(h.replace(/,/g, '[comma]').replace(/\&/g, '[and]'));                
            }

            if(head_attr.length != this.partition) {

                for(var x=1; x<=this.partition; x++) {

                    if(jQuery('#'+ this.type +'_value_'+ x).val().trim() == '') {

                        jQuery('#'+ this.type +'_value_'+ x).focus();
                        break;
                    }
                }
                
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
                partition : this.partition,
                heading : head_attr.toString().replace(/,/g, '[=ROW=]')
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

        SCREEN.itemlabel = '<?php echo $item['header']?>';
        jQuery('#'+ ITEM.type +'_btn_cancel').hide();

    });
    </script>
    <div style="clear: both; text-align: left">

        <table cellspacing="0" cellpadding="0" width="100%">
            <tr><td>
                <table cellpadding="1" cellspacing="0">
                    <tr><td valign="top"><b>Heading</b></td></tr>
                    <tr><td valign="top" style="padding-left: 15px">
                        <?php
                        if(isset($partition)) { /* When adding new item. Else, auto-filled by JS. */

                            for($x=1; $x<=$partition; $x++) {

                                ?><div style="margin-bottom: 2px"><?php echo $x?> <input style="width: 300px" id="<?php echo $item_type?>_value_<?php echo $x?>" type="text" value="<?php echo $heading[$x - 1]?>" /></div><?php
                            }
                        }
                        ?>
                        </td>
                    </tr>
                </table>
                </td>
            </tr>
        </table>
    </div>
    <?php
} else {
    
    ?>
    <script type="text/javascript">
    var ITEM = new function() { this.ok = function() { POPUPJS.obj.hide(); } }    
    jQuery(function(){ jQuery('#popupjs_wrapper .popup_title img').hide(); });
    </script>
    <div style="clear: both; text-align: center"><b>NOTE:</b> "SQS Attribute Headers" cannot be created. This item should be created just after an "SQS Main".</div>
    <?php
}
?>