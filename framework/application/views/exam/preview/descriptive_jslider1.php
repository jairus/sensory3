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
    //$kery_arr = array();
    
    foreach($tmp as $key) {
        
        if(substr_count($key, '.')) {
            
            if(! $float_key) {
                $float_key = true;
                $js_labels = ($max * 2);
                break;
            }
            
            //$tmp2 = explode('.', $key);
            //$kery_arr[] = $tmp2[1];
        }
    }
    
    
    //array_multisort($kery_arr, SORT_ASC);
    //$js_labels = ($max * 5);
    //echo '<pre>';
    
    //print_r($kery_arr);
    //echo '</pre>';
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

echo '<pre>';
print_r($slider_data);
print_r($slider_labels);
echo '</pre>';

$type_ctr = $type . '_' . $ctr;
?>

	<style type="text/css" media="screen">

	 .layout-slider { margin-bottom: 60px; width: 90%; }
	 
	</style>
<script type="text/javascript"> 
var slider = '<?php echo $type_ctr?>_slider';

jQuery(function(){
    
    jQuery("#"+ slider).slider(
        {
            from: 0,
            to: 150,
            //heterogeneity: ['50/5', '75/15'],
            
            scale: <?php echo json_encode($slider_labels)?>,
            limits: false,
            step: 1,
            dimension: '',
            skin: "blue"
        }
    );
});
</script>
<div style="margin-top: <?php echo (($ctr > 1) ? 70 : 10)?>px; width: 95%; clear: both">
    <?php if($label != '') { ?><div style="padding-bottom: 5px"><b><?php echo $label?></b></div><?php } ?>    
    <div id="<?php echo $type_ctr?>_slider_wrapper">
       
        <div class="layout-slider">
            <input id="<?php echo $type_ctr?>_slider" type="slider" value="0" />
        </div>
    </div>
</div>