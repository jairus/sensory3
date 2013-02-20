<?php defined('BASEPATH') or exit('No direct script access allowed')?>
<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

extract($item);

$rl = html_entity_decode($rl, ENT_QUOTES, 'UTF-8');
parse_str($rl, $range_and_label);

$range_arr = array();
$label_arr = array();

foreach($range_and_label as $range => $label) { 
    
    $range_arr = array_merge($range_arr, explode(',', $range));
    $label_arr[] = $label;
}

/* Set ordering. */
array_multisort ($range_arr, (($ordering == 'asc') ? SORT_ASC : SORT_DESC));

$rl_reordered = array();
foreach($range_arr as $x) {
    
    foreach($range_and_label as $rl_range => $rl_label) {

        if(in_array($x, explode(',', $rl_range))) {
            $rl_reordered[$rl_label][] = $x;
        }
    }
}

$item_next = $screen['items'][$ctr];

$type_ctr = $type . '_' . $ctr;

$answer = (int) $_SESSION['EXAM'][$screen_code][$screen_count]['items'][$ctr - 1]->axl;
?>
<script type="text/javascript">
jQuery.extend(
    
    EXAM.axl, {
        
        <?php echo $type_ctr?> : function() {

                if(! EXAM.answers[<?php echo $ctr - 1?>].axl) {
            
                    Popup.dialog({
                        title : 'ERROR',
                        message : '<div>Please select a range from the "<b>SQS main</b>".</div>',
                        buttons: ['Okay', 'Cancel'],
                        width: '420px'
                    });

                    return false;

                } else return true;
        }
    }
);

jQuery(function(){ EXAM.answers[<?php echo ($ctr - 1)?>] = { 'type' : '<?php echo $type?>', 'axl' : <?php echo $answer?> }; });
</script>
<div>
    <table cellpadding="0" cellspacing="0" align="center">
        <tr>
            <?php
            $x = 0;
            foreach($rl_reordered as $label => $range) {
                
                $x++;
                xy_screen_and_item_string_html($label);
                
                if($x < $partition) $border = ' style="border-right: 1px dashed #CCC"';
                else $border = '';
                
                ?>
                <td <?php echo $border?>>                    
                    <div style="text-align: center"><b style="font-size: 12px"><?php echo $label?></b></div>
                    
                        <table cellpadding="0" cellspacing="0">
                            <tr>
                                <?php
                                foreach($range as $value) {
                                    
                                    if($answer == $value) $style = '; background-color : #FDF7F7; border : 1px solid #EB9999';
                                    else $style = '; background-color : #FFF; border : 1px solid #FFF';
                                    
                                    ?>
                                    <td align="center" title="<?php echo $label, ': ', $value?>">
                                        <div id="<?php echo $type_ctr, '_', $value?>" onclick="SQS.main_pickbox(this, <?php echo $ctr?>)"
                                             style="margin: 15px 25px 5px 25px;
                                             height: 50px; width: 50px;
                                             cursor: default<?php echo $style?>">
                                            <div style="margin-top: 15px"><?php echo $value?></div>
                                        </div>
                                    </td>
                                    <?php
                                }
                                ?>
                            </tr>
                        </table>
                   
                </td>
                <?php
            }
            ?>
        </tr>
        <?php
        if($item_next['type'] != 'sqs_attribute_heading') {
        ?>
    </table>
</div>
<?php
} else echo '<tr>';
?>