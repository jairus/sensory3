<?php defined('BASEPATH') or exit('No direct script access allowed')?>
<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

extract($item);

if(substr_count($length, '-')) {

    list($min, $max) = explode('-', $length);
    
} else {

    $min = 0;
    $max = $length;
}

$float_key = false;
$js_labels = $max;
$slider_data = array();
$slider_labels = array();

$slider_data[0] = array('label' => 0, 'visibility' => 'shown');

if($interval_type == 'custom') {
    
    //parse_str($interval, $interval_arr);
    
    $interval_arr = array();
    $item['interval'] = str_replace('[=FIELD=]', '[field]', $item['interval']);
    $tmp = explode('&', $item['interval']);

    for($x=0, $y = count($tmp); $x<$y; $x++) {

        $tmp2 = explode('=', $tmp[$x]);
        $interval_arr[$tmp2[0]] = str_replace('[field]', '[=FIELD=]', $tmp2[1]);
    }

    foreach($interval_arr as $key => $value) {
        
        list($lbl, $visibility) = explode('[=FIELD=]', $value);
        
        $slider_labels[] = $lbl;
        $slider_data[$key] = array('label' => $lbl, 'visibility' => $visibility);//(($visibility == 'shown') ? $label : '');
        
    }
    
    $slider_data[$max] = array('label' => $max, 'visibility' => 'shown');
    
    $tmp = array_keys($interval_arr);
    foreach($tmp as $key) {
        if(is_float($key)) {
            
            $float_key = true;
            $js_labels = ($max * 2);
            break;
        }
    }
    
    echo '<pre>';
    print_r($interval_arr);
    
    //print_r($slider_data);
    echo '</pre>';
}
else
if($interval_type == 'digit') {
    
    $divisor = (double) $interval;
    if($divisor > 0) {

        $quotient =  $length / $divisor;

        for($x=1; $x<=$divisor; $x++) {

            $sum += $quotient;
            
            $slider_labels[] = (string) $sum;
            $slider_data[$sum] = array('label'=> $sum, 'visibility' => 'shown');
        }
    }
}

$type_ctr = $type . '_' . $ctr;
?>
<script type="text/javascript">
var <?php echo $type_ctr?>_labels = <?php echo json_encode($slider_labels)?>;

jQuery(function(){
    
    jQuery('select#<?php echo $type_ctr?>_slider').selectToUISlider({
        labels: <?php echo $js_labels?>,
        
        sliderOptions: {
            stop: function(e, ui){
                console.log('<?php echo $type_ctr?> '+ ui.value)
            }
        }
    }).hide();
    
    jQuery('#<?php echo $type_ctr?>_slider_wrapper ol li').each(function(){

        var subj = jQuery('>span', jQuery(this)).html();

        if(jQuery.inArray(subj, <?php echo $type_ctr?>_labels) > -1) {
            
        } else { jQuery('>span', jQuery(this)).css('border', '0'); }
    });
});
</script>
<div style="margin-top: <?php echo (($ctr > 1) ? 70 : 10)?>px; width: 95%">
    <?php if($label != '') { ?><div style="padding-bottom: 5px"><b><?php echo $label?></b></div><?php } ?>    
    <div id="<?php echo $type_ctr?>_slider_wrapper">
        
    <select id="<?php echo $type_ctr?>_slider">        
        <?php        
        /* value = bottom label in the slider, text = tooltip in the slider. */
        
        for($x=$min; $x<=$max; $x+=0.5) {
            
            if(is_float($x)) $x_tmp = strval($x);
            else $x_tmp = $x;
            
            if(in_array($x_tmp, array_keys($slider_data))) {
                
                if($slider_data[$x_tmp]['visibility'] == 'shown') {
                    
                    /*<option value="<?php echo $slider_data[$x_tmp]['label']?>"><?php echo $x_tmp?></option>*/
                    
                    ?><option value="<?php echo $slider_data[$x_tmp]['label']?>"><?php echo $slider_data[$x_tmp]['label']?></option><?php
                    
                } else {
                    
                    ?><option value=""><?php echo $slider_data[$x_tmp]['label']?></option><?php
                }
                
            } else {
                
                ?><option value="">---</option><?php
                
                /*?><option value=""><?php echo $x_tmp?></option><?php */
            
            }
        }
        
        /*foreach($slider_data as $x => $v) {
            
            $x_tmp = str_replace('_', '.', $x);
            
            if(in_array($x, array_keys($slider_data))) {
                
                if($slider_data[$x]['visibility'] == 'shown') {
                    
                    ?><option value="<?php echo $slider_data[$x]['label']?>"><?php echo $x_tmp?></option><?php
                    
                } else {
                    
                    ?><option value=""><?php echo $slider_data[$x]['label']?></option><?php
                }
                
            } else { ?><option value=""><?php echo $x_tmp?></option><?php }
        }*/
        ?>
    </select>
    </div>
</div>