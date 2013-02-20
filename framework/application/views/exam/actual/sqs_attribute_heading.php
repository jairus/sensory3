<?php defined('BASEPATH') or exit('No direct script access allowed')?>
<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

extract($item);
$heading = explode('[=ROW=]', $heading);
$type_ctr = $type . '_' . $ctr;

$item_prev = $screen['items'][$ctr - 2];
if(! empty($item_prev)) { $partition = $item_prev['partition']; }
$middle = ceil($partition / 2);

$tmp = $_SESSION['EXAM'][$screen_code][$screen_count]['items'][$ctr - 1]->axl;
if($tmp) { list($answer, $text) = explode(':', $_SESSION['EXAM'][$screen_code][$screen_count]['items'][$ctr - 1]->axl); }

if($answer) $answer_js = $answer . ':' . $text;
else $answer_js = '';

if($item_prev['type'] != 'sqs_main') {
?>
<div>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <?php
            }
            
            $index = 0;
            for($x=1; $x<=$partition; $x++) {
                
                $header = '';
                
                if($x == 1 || $x == 2 || $x == ($partition - 1) || $x == $partition) {
                    
                    $header = $heading[$index];
                    xy_screen_and_item_string_html($header);
                    
                    $index++;
                }
                
                ?>
                <td width="300" align="center">
                    <?php
                    if($header != '') {
                        ?>
                        <div><label for="<?php echo $type_ctr?>_choice_<?php echo $index?>"><b style="font-size: 12px"><?php echo $header?></b></label></div>
                        <div><input<?php echo (($answer == $index) ? ' checked="checked"' : '')?> name="<?php echo $type_ctr?>_choice" id="<?php echo $type_ctr?>_choice_<?php echo $index?>" value="<?php echo $index, ':', xy_make_id($header)?>" type="checkbox" onclick="SQS.attribute_heading_checkbox(this, <?php echo $ctr?>)" /></div>
                        <?php
                    } else echo '&nbsp;';
                    ?>
                </td>
                <?php
            }
            ?>            
        </tr>        
    </table>
</div>
<script type="text/javascript">
jQuery.extend(
    
    EXAM.axl, {
        
        <?php echo $type_ctr?> : function() {

                if(! EXAM.answers[<?php echo $ctr - 1?>].axl) {
            
                    Popup.dialog({
                        title : 'ERROR',
                        message : '<div>Please select an answer from the "<b>SQS headers</b>".</div>',
                        buttons: ['Okay', 'Cancel'],
                        width: '420px'
                    });

                    return false;

                } else return true;
        }
    }
);

jQuery(function(){ EXAM.answers[<?php echo ($ctr - 1)?>] = { 'type' : '<?php echo $type?>', 'axl' : '<?php echo $answer_js?>' }; });
</script>