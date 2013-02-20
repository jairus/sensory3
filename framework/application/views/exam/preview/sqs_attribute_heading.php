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

//$middle = ceil($item_prev['partition'] / 2);
//print_r($_SESSION['SCREEN'][$rta_id][$screen_code]);

//$item = $_SESSION['SCREEN'][$rta_id][$screen_code][$screen_count]['items'][$item_id - 1];

//print_r($screen['items']);
//echo $ctr - 2;
//echo $screen['items'][$ctr];

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
                <td width="300" align="center"<?php echo $border?>>
                    <?php
                    if($header != '') {
                        ?>
                        <div><label for="<?php echo $type_ctr?>_choice_<?php echo $index?>"><b style="font-size: 12px"><?php echo $header?></b></label></div>
                        <div><input name="<?php echo $type_ctr?>_choice" id="<?php echo $type_ctr?>_choice_<?php echo $index?>" type="checkbox" /></div>
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