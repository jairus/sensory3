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
?>
<script type="text/javascript">
jQuery(function(){
    
    jQuery("div[id^='<?php echo $type_ctr?>_']").click(function(){
        
        jQuery("div[id^='<?php echo $type_ctr?>_']")
            .css('background', '#FFF')
            .css('border', '1px solid #FFF');
        
        jQuery(this)
            .css('background', '#FDF7F7')
            .css('border', '1px solid #EB9999');
    });
    
});
</script>
<div>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <?php
            $x = 0;
            foreach($rl_reordered as $label => $range) {
                
                $x++;
                xy_screen_and_item_string_html($label);
                
                if($x < $partition) $border = ' style="border-right: 1px dashed #CCC"';
                else $border = '';
                
                ?>
                <td width="300" align="center"<?php echo $border?>>                    
                    <div><b style="font-size: 12px"><?php echo $label?></b></div>
                    <div>
                        <table cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <?php
                                foreach($range as $value) {
                                    
                                    ?>
                                    <td align="center" title="<?php echo $label, ': ', $value?>">
                                        <div id="<?php echo $type_ctr, '_', $value?>" style="margin-top: 15px; border: 1px solid #FFF; height: 50px; width: 50px; cursor: default">
                                            <div style="margin-top: 15px"><?php echo $value?></div>
                                        </div>
                                    </td>
                                    <?php
                                }
                                ?>
                            </tr>
                        </table>
                    </div>                    
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
}
?>