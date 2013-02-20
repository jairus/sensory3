<?php defined('BASEPATH') or exit('No direct script access allowed')?>
<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

extract($item);
$heading = explode('[=ROW=]', $heading);
$type_ctr = $type . '_' . $ctr;

//$item_next = $screen['items'][$ctr];
$item_prev = $screen['items'][$ctr - 2];
$middle = ceil($item_prev['partition'] / 2);

if($item_prev['type'] != 'sqs_main') {
?>
<div>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <?php
            }
            
            $index = 0;
            for($x=1; $x<=$item_prev['partition']; $x++) {
                
                if($item_prev['partition']%2 !=0) {
                    
                    if($x == $middle) $index--;
                    $header = $heading[$index];
                    
                    if($x == $middle) { $header = ''; }
                    
                } else $header = $heading[$index];
                
                xy_screen_and_item_string_decode($header);
                
                ?>
                <td width="300" align="center"<?php echo $border?>>
                    <?php
                    if($header != '') {
                    ?>
                    <div><label for="<?php echo $type_ctr?>_choice_<?php echo ($index + 1)?>"><b style="font-size: 12px"><?php echo $header?></b></label></div>
                    <div><input name="<?php echo $type_ctr?>_choice" id="<?php echo $type_ctr?>_choice_<?php echo ($index + 1)?>" type="checkbox" /></div>
                    <?php
                    } else echo '&nbsp;';
                    ?>
                </td>
                <?php

                $index++;
            }
            
            /*$x = 0;
            foreach($heading as $header) {
                
                $x++;
                if($x < $partition) $border = ' style="border-right: 1px dashed #CCC"';
                else $border = '';
                
                ?>
                <td width="300" align="center"<?php echo $border?>>
                    <div><label for="<?php echo $type_ctr?>_choice_<?php echo $x?>"><b style="font-size: 12px"><?php echo $header?></b></label></div>
                    <div><input name="<?php echo $type_ctr?>_choice" id="<?php echo $type_ctr?>_choice_<?php echo $x?>" type="checkbox" /></div>
                </td>
                <?php
            }*/
            ?>            
        </tr>        
    </table>
</div>