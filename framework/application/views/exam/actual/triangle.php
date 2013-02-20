<?php defined('BASEPATH') or exit('No direct script access allowed')?>
<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

/*
 * BAA, ABA, AAB, ABB, BAB, BBA
 * Controls     = A
 * Experiments  = B
 **/

array_walk($item, 'xy_screen_and_item_string_html');
extract($item);

$codes = $code_choice;
$answer = $_SESSION['EXAM'][$screen_code][$screen_count]['items'][$ctr - 1]->axl;

$type_ctr = $type . '_' . $ctr;
?>
<script type="text/javascript">
jQuery(function(){
    
    EXAM.answers[<?php echo $ctr - 1?>] = { 'type' : '<?php echo $type?>', 'axl' : '<?php echo $answer?>' };
    
    jQuery("div[id^='<?php echo $type_ctr?>_choice_']").click(function(){
        
        jQuery("div[id^='<?php echo $type_ctr?>_choice_']")
            .css('background', '#FFF')
            .css('border', '1px solid #EFEFEF');
        
        jQuery(this)
            .css('background', '#FDF7F7')
            .css('border', '1px solid #EB9999');
            
        EXAM.answers[<?php echo $ctr - 1?>]['axl'] = jQuery.trim(jQuery('div', jQuery(this)).html());
        EXAM.session_updater_peritem(<?php echo $ctr?>);
    });    
});

jQuery.extend(
    
    EXAM.axl, {
        
        <?php echo $type_ctr?> : function() {
                
                if(! EXAM.answers[<?php echo $ctr - 1?>].axl) {
                    
                    Popup.dialog({
                        title : 'ERROR',
                        message : '<div>Please select an answer for "Triangle item# <?php echo $ctr?>".</div>',
                        buttons: ['Okay', 'Cancel'],
                        width: '420px'
                    });
                    
                    return false;
                }
                
                return true;                
        }
    }
);
</script>
<div style="margin-left: auto; margin-right: auto; width: 60%">
    <div style="font-size: 18px; font-weight: bold; width: 700px; color: #777"><?php echo $i?></div>
    
    <div style="margin-left: auto; margin-right: auto; margin-top: 50px; width: 60%">
        <table cellpadding="0" cellspacing="0">
            <tr>
            <?php
            foreach($codes as $code) {
                
                if($answer == $code) $border = 'background: #FDF7F7; border: 1px solid #EB9999';
                else $border = 'background: #FFF; border: 1px solid #EFEFEF';
                
                ?>
                <td style="text-align: center">
                    
                    <div title="<?php echo $code?>" id="<?php echo $type_ctr?>_choice_<?php echo $code?>" style="display: inline-block; margin: 10px; padding: 10px; cursor: pointer; <?php echo $border?>">
                        <div style="font-size: 30px; font-weight: bold"><?php echo $code?></div>
                    </div>
                    
                </td>
                <?php
            }
            
            ?>
            </tr>
        </table>
        
    </div>
</div>