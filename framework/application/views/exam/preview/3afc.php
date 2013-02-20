<?php defined('BASEPATH') or exit('No direct script access allowed')?>
<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

/*
 * BAA, ABA, AAB
 * Controls     = A
 * Experiments  = B
 **/

extract($item);

$codes = array($experiments[0], $controls[0], $controls[1]);
xy_screen_and_item_string_html($i);

$type_ctr = $type . '_' . $ctr;
?>
<style type="text/css">
div[id^='<?php echo $type_ctr?>_choice_'] {
    margin-top: 15px;
    border: 1px solid #EFEFEF;
    height: 50px;
    width: 70px;
    cursor: pointer
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
<div>
    <div><?php echo $i?></div>
    
    <div style="text-align: center">
        <table cellpadding="0" cellspacing="0">
            <tr>
            <?php
            foreach($codes as $code) {

                ?>
                <td width="200" height="100" align="center">
                    
                    <div title="<?php echo $code?>" id="<?php echo $type_ctr?>_choice_<?php echo $code?>">
                        <div style="margin-top: 15px"><?php echo $code?></div>
                    </div>
                    
                </td>
                <?php
            }
            
            if($nodiff == 'true') {
                    
                ?>
                <td width="200" height="100" align="center">

                    <div style="width: 80px" title="same" id="<?php echo $type_ctr?>_choice_nodiff">
                        <div style="margin-top: 8px">No Difference</div>
                    </div>

                </td>
                <?php
            }
            ?>
            </tr>
        </table>
        
    </div>
    
    <?php
    if($nodiff_i != '') {
        
        xy_screen_and_item_string_html($nodiff_i);
        
        ?>
        <div><b style="font-family: Arial"><u><i>Instruction</i></u></b>: <?php echo $nodiff_i?></div>
        <?php
        
    }
    ?>
    
</div>