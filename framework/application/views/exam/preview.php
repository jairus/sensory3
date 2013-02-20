<?php defined('BASEPATH') or exit('No direct script access allowed')?>
<?php
if(isset($screen)) extract($screen);
xy_screen_and_item_string_decode($screen_title_or_label_value);

$data['code_1'] = $code_1;
$data['code_2'] = $code_2;
$data['controls'] = $controls;
$data['experiments'] = $experiments;

function xy_qitem_assign($item, $screen_total, $screen, $data, $rta_id, $screen_code) {
    
    static $ctr = 0;
    $ctr++;
    
    extract($data);
    
    $func = 'xy_qitem_' . $item['type'];
    if(function_exists($func)) { echo '<div style="margin: ' . (($ctr > 1) ? 20 : 0) . 'px 0 20px 0">', call_user_func($func, $item, $ctr), '</div>'; }
    else {
        
        $file = APPPATH . 'views/exam/preview/' . $item['type'] . '.php';
        if(file_exists($file)) include $file;                
    }
}

function xy_qitem_instruction($item, $index) {
    
    array_walk($item, 'xy_screen_and_item_string_html');
    extract($item);
    
    if($i) {
        
        //$i = str_replace('[nl]', '<br />', $i);
        $html = '<div>' . $i . '</div>';        
    }
    
    return $html;
}

function xy_qitem_multiple_choice($item, $index) {
    
    array_walk($item, 'xy_screen_and_item_string_html');
    extract($item);
    
    //$choices = explode('[=ROW=]', str_replace('[quote]', "'", $choices));
    $choices = explode('[=ROW=]', $choices);
    
    $ctr = 0;
    foreach($choices as $choice) {
        
        $ctr++;
        $html_choice .= '<div><input name="' . $type . '_' . $index . '_choice" id="' . $type . '_' . $index . '_choice_' . $ctr . '" type="radio" /> <label for="' . $type . '_' . $index . '_choice_' . $ctr . '">' . $choice . '</label></div>';
    }
    
    if($question) $question = '<div><b>' . $question . '</b></div>';    
    $html .= $question . $html_choice;
    
    return $html;
}

function xy_qitem_cata($item, $index) {
    
    array_walk($item, 'xy_screen_and_item_string_html');
    extract($item);
    
    //$choices = explode('[=ROW=]', str_replace('[quote]', "'", $choices));
    $choices = explode('[=ROW=]', $choices);
    
    $ctr = 0;
    foreach($choices as $choice) {
        
        $ctr++;
        $html_choice .= '<div><input name="' . $type . '_' . $index . '_choice" id="' . $type . '_' . $index . '_choice_' . $ctr . '" type="checkbox" /> <label for="' . $type . '_' . $index . '_choice_' . $ctr . '">' . $choice . '</label></div>';
    }
    
    if($question) $question = '<div><b>' . $question . '</b></div>';    
    $html .= $question . $html_choice;
    
    return $html;
}
?>
<script type="text/javascript">
jQuery(function(){
    
    var func = jQuery('#btn_next').attr('onclick');
    jQuery('#btn_next').removeAttr('onclick');
    jQuery('#btn_next').click(function(){
        
        <?php
        $item_total = count($screen['items']);
        for($x=0; $x<$item_total; $x++) {
            
            $item = $screen['items'][$x];
            $item_type = $item['type'] . '_' . ($x + 1);
            
            if($item['type'] == 'comment') {
                
                if($item['required'] == 'yes') {
                    
                    ?>
                    var comment = jQuery('#<?php echo $item_type?>_field');
                    if(comment.val().trim() == '') {
                        
                        Popup.dialog({
                            title : 'ERROR',
                            message : '<div>Please enter a comment in the field provided for you.<br /><br /><b><?php echo $item['label']?></b></div>',
                            buttons: ['Okay', 'Cancel'],
                            buttonClick: function() {
                                comment.focus();
                            },
                            width: '420px'
                        });
                        
                        return;
                    }
                    <?php
                }
            }
            else
            if($item['type'] == 'sqs_main') {
                
                ?>
                var ctr = 0;
                jQuery("div[id^='<?php echo $item_type?>_']").each(function(){
                    if(jQuery(this).attr('style').indexOf('rgb(235, 153, 153)') > -1) ctr++;
                });
                
                if(ctr == 0) {

                    Popup.dialog({
                        title : 'ERROR',
                        message : '<div>Please select a range from "SQS Main".</div>',
                        buttons: ['Okay', 'Cancel'],
                        width: '420px'
                    });
                    return;
                }
                <?php
            }
        }
        ?>
        eval(func);
    });
});
</script>
<div style="margin-bottom: 10px; font: bold 24px Verdana"><?php echo (($screen_title_or_label_visibility == 'shown') ? $screen_title_or_label_value : '')?></div>
<div style="border: 2px solid #CCC; padding: 10px">
    <div style="min-height: 500px; width: inherit">
    <?php
    if(($total = count($items)) > 0) {
        
        for($x=0; $x<$total; $x++) {
            
            $item = $items[$x];
            xy_qitem_assign($item, $screen_total, $screen, $data, $rta_id, $screen_code);
        }
    }
    ?>
    </div>
    <div>
        <div style="float: left"><?php echo $button_prev?></div>
        <div style="float: right"><?php echo $button_next?></div>
        <div style="clear: both"></div>
    </div>
</div>