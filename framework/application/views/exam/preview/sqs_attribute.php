<?php defined('BASEPATH') or exit('No direct script access allowed')?>
<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

$item_total = $x = count($screen['items']);
do {

    $x--;

    if($screen['items'][$x]['type'] == 'sqs_attribute_heading') {

        $item_sqs_attribute_heading = $screen['items'][$x];
        $item_sqs_attribute_heading_position = ($x + 1);
        $partition = $screen['items'][$x]['partition'];

        break;
    }

} while($x > 0);

if(empty($item_sqs_attribute_heading)) {
    
    echo 'ERROR: An item was NOT loaded.<br /><br />SQS Attributes can only be loaded under "SQS Attribute Headers".';
    return;
}

extract($item);

$heading = explode('[=ROW=]',  $item_sqs_attribute_heading['heading']);
$attributes = explode('[=ROW=]', $attr);


//if($partition % 2 == 0) $middle = ceil($partition / 2);
//else 
    
$middle = ceil($partition / 2);

$type_ctr = $type . '_' . $ctr;
?>
<script type="text/javascript">
jQuery(function(){
    
    jQuery("input[id^='sqs_attribute_heading_<?php echo $item_sqs_attribute_heading_position?>_choice_']").each(function(){
        
        var choice = jQuery(this).attr('id').split('_');
        choice = choice[choice.length - 1];
        
        var left = (jQuery(this).offset().left - 4);
        
        jQuery("input[id^='<?php echo $type_ctr?>_"+ choice +"_']").each(function(){
            
            jQuery(this)
                .css('position', 'absolute')
                .css('left', left)
                .css('top', jQuery(this).offset().top - 12);            
        });            
    });
});

function <?php echo $type_ctr?>_selection_pick(obj, name) {
    
    jQuery("input[name='"+ name +"']").each(function(){        
        if(jQuery(obj).attr('id') != jQuery(this).attr('id')) { jQuery(this).attr('checked', false); }
    });
}
</script>
<div style="margin-bottom: <?php echo (($ctr < $item_total) ? 30 : 0)?>px">
    <table cellpadding="0" cellspacing="0" width="100%">
        
        <tr>
            <?php        
            for($x=1; $x<=$partition; $x++) {

                ?><td width="100" height="25">&nbsp;</td><?php
                if($x == $middle) {
                    
                    xy_screen_and_item_string_html($label);
                    ?><td width="100" align="center"><b><?php echo $label?></b></td><?php
                }
            }
            ?>
        </tr>
        <?php
        
        foreach($attributes as $attr) {

            ?><tr><?php
            
            for($x=1; $x<=$partition; $x++) {
                
                $name = xy_make_id($label) . '__' . (($attr == '---') ? 'blank' : xy_make_id($attr));
                
                ?>
                <td width="300" align="center" height="25"><input onclick="<?php echo $type_ctr?>_selection_pick(this,'<?php echo $type_ctr, '_', $name?>')" name="<?php echo $type_ctr, '_', $name?>" id="<?php echo $type_ctr, '_', $x, '_', $name?>" type="checkbox" /></td>
                <?php
                
                if($x == $middle) {

                    ?>
                    <td width="300" align="center" height="20">
                        <?php
                        if($attr == '---') { ?><input id="<?php echo $type_ctr, '_', $name?>_field" style="margin-top: 1px" type="text" /><?php }
                        else { 
                            
                            xy_screen_and_item_string_decode($attr);
                            ?><?php echo $attr?><?php
                            
                        }
                        ?>
                    </td>
                    <?php
                }
            }

            ?></tr><?php
        }
        ?>        
    </table>
</div>