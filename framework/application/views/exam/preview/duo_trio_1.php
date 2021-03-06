<?php defined('BASEPATH') or exit('No direct script access allowed')?>
<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

extract($item);

/* Duo - Trio has no 2ndary codes, only Primary. */
$tmp = array();
$tcode = count($code_1);
$code_1_tmp = $code_1;

if($ref_code_position == 'left') { $position = 0; }
else
if($ref_code_position == 'center') { $position = floor($tcode / 2); }
else
if($ref_code_position == 'right') { $position = $tcode - 1; }

$tmp[$position] = 'REFERENCE'; //$ref_code_value;

for($x=0; $x<$tcode; $x++) {
    
    if($code_1_tmp[$x] == $ref_code_value) unset($code_1_tmp[$x]);
}

for($x=0; $x<$tcode; $x++) {
    
    if($x != $position) {
        
        foreach($code_1_tmp as $code) {
            
            if(!in_array($code, $tmp)) {
                
                $tmp[$x] = $code;
            }
        }
    }
}

$code_1 = $tmp;

$type_ctr = $type . '_' . $ctr;
?>
<style type="text/css">
div[id^=<?php echo $type_ctr?>_choice_] {
    margin-top: 15px;
    border: 1px solid #EFEFEF;
    height: 50px;
    width: 90px;
    cursor: default
}
</style>
<script type="text/javascript">
jQuery(function(){
    
    jQuery("div[id^='<?php echo $type_ctr?>_choice_']").click(function(){
        
        jQuery("div[id^='<?php echo $type_ctr?>_choice_']")
            .css('background', '#FFF')
            .css('border', '1px solid #EFEFEF');
        
        jQuery(this)
            .css('background', '#FDF7F7')
            .css('border', '1px solid #EB9999');
    });
    
});
</script>
<div id="<?php echo $type_ctr?>_wrapper">
    <div><?php echo str_replace('[nl]', '<br />', $i)?></div>
    
    <table cellpadding="0" cellspacing="0">
        <tr>
            <?php
            $x = 0;
            while($x < $tcode) {
                
                $code = $code_1[$x];
                $x++;
                
                ?>
                <td width="200" height="100" align="center">

                    <div title="<?php echo $code?>" id="<?php echo $type_ctr?>_choice_<?php echo $code?>">
                        <div style="margin-top: 15px"><?php echo $code?></div>
                    </div>

                </td>
                <?php
            }            
            ?>
        </tr>
    </table>
    
</div>