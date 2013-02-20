<?php defined('BASEPATH') or exit('No direct script access allowed')?>
<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

$item_total = $x = count($screen['items']);
do {

    $x--;

    if($screen['items'][$x]['type'] == 'sqs_attribute_heading') {

        $item_sqs_attribute_heading = $screen['items'][$x];
        //$item_sqs_attribute_heading_position = ($x + 1); /* Is actually the "$ctr" of this (sqs_attribute_heading) item. */
        $partition = $screen['items'][$x]['partition'];

        break;
    }

} while($x > 0);

if(empty($item_sqs_attribute_heading)) {
    
    echo 'ERROR: An item was NOT loaded.<br /><br />SQS Attributes can only be loaded under "SQS Attribute Headers".';
    return;
}

extract($item);

$heading = explode('[=ROW=]',  $item_sqs_attribute_heading['heading']);
$attributes = explode('[=ROW=]', $attr);
    
$middle = ceil($partition / 2);
xy_screen_and_item_string_html($label);

$answer = $_SESSION['EXAM'][$screen_code][$screen_count]['items'][$ctr - 1]->axl;
if(empty($answer)) $answer_js = array();
$answer_js = json_encode($answer);

$type_ctr = $type . '_' . $ctr;
?>
<script type="text/javascript">
jQuery.extend(
    
    EXAM.axl, {
        
        <?php echo $type_ctr?> : function() {
                
                var ctr = <?php echo $ctr?>, unanswered_total = 0;
                
                if(! EXAM.answers[ctr - 1].axl) {
                    
                    Popup.dialog({
                        title : 'ERROR',
                        message : '<div>Please select an answer from the "SQS attribute : <b><?php echo $label?></b>".</div>',
                        buttons: ['Okay', 'Cancel'],
                        width: '420px'
                    });
                    
                    return false;
                }
                
                jQuery.each(EXAM.answers[ctr - 1].axl, function(key, value) {
                    
                    if(value.answer == 0 && EXAM.attributes[ctr - 1][key] != '---') {
                        unanswered_total++;
                        
                        Popup.dialog({
                            title : 'ERROR',
                            message : '<div>Please select an answer from "<?php echo $label?> : <b>'+ value.attr +'</b>".</div>',
                            buttons: ['Okay', 'Cancel'],
                            width: '420px'
                        });
                        
                        /* Breaking out from jQuery.each()'s loop. */
                        return false;
                    }
                });
                
                if(unanswered_total == 0) return true;
                else return false;
        }
    }
);

jQuery(function(){
    
    EXAM.attributes[<?php echo ($ctr - 1)?>] = <?php echo json_encode($attributes)?>;
    EXAM.answers[<?php echo ($ctr - 1)?>] = { 'type' : '<?php echo $type?>', 'axl' : <?php echo $answer_js?> };
});
</script>
<div style="margin-bottom: <?php echo (($ctr < $item_total) ? 30 : 0)?>px">
    <table cellpadding="0" cellspacing="0" width="100%">
        
        <tr>
            <?php        
            for($x=1; $x<=$partition; $x++) {

                ?><td width="100" height="25">&nbsp;</td><?php
                if($x == $middle) {
                    
                    ?><td width="100" align="center"><b><?php echo $label?></b></td><?php
                }
            }
            ?>
        </tr>
        <?php
        $a = 0;
        foreach($attributes as $attr) {
            
            $a++;
            ?><tr><?php
            
            for($x=1; $x<=$partition; $x++) {
                
                $attr_id = (($attr == '---') ? 'blank' : xy_make_id($attr));
                $label_id = xy_make_id($label);
                
                $name = $type_ctr . '_' . $label_id . '__' . $a . '_' . $attr_id;
                $id = $type_ctr . '_' . $label_id . '__' . $a . '_' . $x . '_' . $attr_id;
                
                $attr_index = ($a - 1);
                
                ?>
                <td width="300" align="center" height="25">
                    <input<?php echo (($answer->$attr_index->answer == $x) ? ' checked="checked"' : '')?> class="alignment_<?php echo $x?>" value="<?php echo $x, ':', $attr_id?>" name="<?php echo $name?>" id="<?php echo $id?>" onclick="SQS.attribute_checkbox(this, <?php echo $ctr?>)" type="checkbox" />
                </td>
                <?php
                
                if($x == $middle) {

                    ?>
                    <td width="300" align="center" height="20">
                        <?php
                        if($attr == '---') { ?><input id="<?php echo $name?>_field" onkeyup="SQS.attribute_textfield(this, <?php echo $ctr?>)" onkeydown="SQS.attribute_textfield(this, <?php echo $ctr?>)" style="margin-top: 1px" type="text" value="<?php echo $answer->$attr_index->attr?>" /><?php }
                        else { 
                            
                            xy_screen_and_item_string_decode($attr);
                            ?><?php echo $attr?><?php
                            
                        }
                        ?>
                    </td>
                    <?php
                }
            }

            ?></tr><?php
        }
        ?>        
    </table>
</div>