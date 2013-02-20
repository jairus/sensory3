<?php defined('BASEPATH') or exit('No direct script access allowed')?>
<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

array_walk($item, 'xy_screen_and_item_string_html');
extract($item);

$answer = $_SESSION['EXAM'][$screen_code][$screen_count]['items'][$ctr - 1]->axl;

$type_ctr = $type . '_' . $ctr;
?>
<script type="text/javascript">
jQuery.extend(
    
    EXAM.axl, {
        
        <?php echo $type_ctr?> : function() {
                
                var ctr = <?php echo $ctr?>;
                var required = <?php echo (($required == 'yes') ? 'true' : 'false')?>;
                var comment = jQuery('#<?php echo $type_ctr?>_field');

                if(required && ! comment.val().length) {

                    Popup.dialog({
                        title : 'ERROR',
                        message : '<div>Please enter something in the <b>comment</b> field.</div>',
                        buttons: ['Okay', 'Cancel'],
                        width: '420px',
                        buttonClick : function() { comment.focus(); }
                    });

                    return false;

                } else {

                    EXAM.answers[ctr - 1] = { 'type' : '<?php echo $type?>', 'axl' : comment.val().trim() };
                    return true;
                }
                
        }
    }
);

jQuery(function(){ EXAM.answers[<?php echo ($ctr - 1)?>] = { 'type' : '<?php echo $type?>', 'axl' : '<?php echo $answer?>' }; });
</script>
<div style="margin-left: auto; margin-right: auto; width: 40%">
    <div style="text-align: left">
        <?php if($label != '') { ?><div style="margin-bottom: 5px"><?php echo $label?></div><?php } ?>
        <div><textarea onkeyup="EXAM.comment_field_onkey(this,<?php echo $ctr?>)" onkeydown="EXAM.comment_field_onkey(this,<?php echo $ctr?>)" id="<?php echo $type_ctr?>_field"><?php echo $answer?></textarea></div>
    </div>
</div>