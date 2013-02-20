<?php defined('BASEPATH') or exit('No direct script access allowed')?>
<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

array_walk($item, 'xy_screen_and_item_string_html');
extract($item);

$choices = explode('[=ROW=]', $choices);

$answer = $_SESSION['EXAM'][$screen_code][$screen_count]['items'][$ctr - 1]->axl;
if(! $answer) $answer = new stdClass;

$type_ctr = $type . '_' . $ctr;

for($x=0, $y = count($choices); $x<$y; $x++) {
    
    $choice = $choices[$x]; $index = $x + 1;
    $html_choice .= '<div><input name="' . $type_ctr . '_choice" id="' . $type_ctr . '_choice_' . $index . '" type="checkbox" /> <label for="' . $type_ctr . '_choice_' . $index . '">' . $choice . '</label></div>';
}

if($question) $question = '<div id="' . $type_ctr . '_question"><b>' . $question . '</b></div>';    
$html .= $question . $html_choice;
?>
<script type="text/javascript">
jQuery(function(){

    EXAM.answers[<?php echo $ctr - 1?>] = { 'type' : '<?php echo $type?>', 'axl' : <?php echo json_encode($answer)?> };
    
    /* START: Initialize picked answers. */
    jQuery("input[name='<?php echo $type_ctr?>_choice']").each(function(index){ if(EXAM.answers[<?php echo $ctr - 1?>]['axl'][index]) { jQuery(this).attr('checked', true); } });
    /* END: Initialize picked answers. */
    
    jQuery("input[name='<?php echo $type_ctr?>_choice']").click(function(){
        
        var index = parseInt(jQuery(this).attr('id').replace('<?php echo $type_ctr?>_choice_', ''), 10);
        EXAM.answers[<?php echo $ctr - 1?>]['axl'][index - 1] = jQuery(this).is(':checked');
        
        EXAM.session_updater_peritem(<?php echo $ctr?>);
    });
    
    console.log(EXAM.answers[<?php echo $ctr - 1?>]['axl']);
});

jQuery.extend(
    
    EXAM.axl, {
        
        <?php echo $type_ctr?> : function() {
                
                var total = 0; jQuery("input[name='<?php echo $type_ctr?>_choice']").each(function(){ if(jQuery(this).is(':checked')) total++; });
                
                if(total == 0) {

                    Popup.dialog({
                        title : 'ERROR',
                        message : '<div>Please select answer for "Multiple Choice: <b>'+ jQuery('#<?php echo $type_ctr?>_question b').html() +'</b>".</div>',
                        buttons: ['Okay', 'Cancel'],
                        width: '420px'
                    });

                    return false; /* Break from this loop. */

                } else return true;
        }
    }
);
</script>
<div style="margin-left: auto; margin-right: auto; width: 60%">
    
    <div style="margin: 10px 0 40px 0"><?php echo $html?></div>
    
</div>