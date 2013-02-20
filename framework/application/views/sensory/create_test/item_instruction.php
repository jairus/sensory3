<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

include_once 'procedures.inc.php';

if(isset($_POST)) { extract($_POST); }
$item = array();
if($item_id > 0) {
    
    $item = $_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items'][$item_id - 1];
    
    array_walk($item, 'xy_ssstring_for_view_byref');    
}

$content = array(
    'liking'        => array('Liking', 'Evaluate the sample according to the ff attributes. Place a check on the box corresponding to your answer.' .
        "\n\n" . 'Choose only ONE answer'),
    'preference'    => array('Preference', 'Taste the sample on the left first and the sample on the right second. After tasting the samples, encircle the code number of the sample you prefer.' .
        "\n\n" . 'Please choose one.'),
    'ranking'       => array('Ranking', 'Please rank the samples in the order of acceptability. Rank the most acceptable sample first and the least acceptable as third. Do not assign the same rank to two samples. Write the code on the blanks under "Sample Code"' .
        "\n\n" . 'Evaluate the samples in the following order:'),
    'triangle'      => array('Triangle', 'Taste samples from left to right. Two are identical; determine which is the odd sample. Encircle the code number of the odd sample.' .
        "\n\n" . 'You must make a choice.'),
    '2afc'          => array('2-AFC', 'Taste the sample on the left first and the sample on the right second. After tasting the samples, encircle the code number of the _____ sample.' .
        "\n\n" . 'Which sample is _____?'),
    '3afc'          => array('3-AFC', 'Taste samples from left to right. Within the group of three, choose the _____ sample. Encircle the code number of the _____ sample.' .
        "\n\n" . 'Choose the _____ sample.'),
    'other'         => array('Other', $item['i'])
);

?>
<script type="text/javascript">
var ITEM = new function() {
    
    this.id = <?php echo (double) $item_id?>;
    this.default_instructions = <?php echo json_encode($content)?>;
    
    this.ok = function() {
        
        var i = jQuery('#<?php echo $item_type?>_instruction');
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

            item : 'instruction',                
            i : i.val()                
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
    
    jQuery('#<?php echo $item_type?>_default_trigger').change(function(){
    
        var html = '';
        if(jQuery(this).val() != '') {

            var x = ITEM.default_instructions[jQuery(this).val()];
            html = x[1];
        }

        jQuery('#<?php echo $item_type?>_instruction').val(html).focus();
    });
});
</script>
<div style="clear: both; text-align: left">
    <div>System <b>default</b>:
        <select id="<?php echo $item_type?>_default_trigger">
            <option value="">Select:</option>
            <?php
            foreach($content as $key => $value) {
                ?>
                <option value="<?php echo $key?>"><?php echo $value[0]?></option>
                <?php
            }
            ?>            
        </select>
    </div>
    
    <div><textarea style="width: 485px; font: 12px Verdana; color: #333" id="<?php echo $item_type?>_instruction"><?php echo $item['i']?></textarea></div>
</div>