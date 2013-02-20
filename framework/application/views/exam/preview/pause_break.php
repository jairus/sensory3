<?php defined('BASEPATH') or exit('No direct script access allowed')?>
<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

array_walk($item, 'xy_screen_and_item_string_html');
extract($item);

$type_ctr = $type . '_' . $ctr;
?>
<script type="text/javascript">
var <?php echo $type_ctr?> = new function(){
    
    this.type = '<?php echo $type_ctr?>';
    this.timer_visibility = '<?php echo $timer_visibility?>';
    this.timer_value = '<?php echo $timer_value?>';
    this.screen_total = <?php echo (int) $screen_total?>;
    
    this.timer_change = function() {
        
        var subj = 'exam/preview/';
        var url = window.location.href;
        var tmp = '', url_target = '';
        
        tmp = url.substr(url.indexOf(subj) + subj.length);
        tmp = tmp.split('/');
        var step = parseInt(tmp[2], 10);
        if(isNaN(step)) step = 1;
        
        url_target = DOCROOT + subj + tmp[0] +'/';
        
        if(this.timer_value > 1) {
            
            this.timer_value--;
            jQuery('#'+ this.type +'_timer_value_wrapper').html('<span style="font: bold 50px Verdana">'+ this.timer_value +'</span><br />seconds left');

            setTimeout("<?php echo $type_ctr?>.timer_change()", 1000);
            
        } else {
            
            if(step < this.screen_total) window.location.href = url_target + tmp[1] +'/' + (step + 1);
            else {
                
                if(EXAM.one_ss_only == true) window.close();
                else  window.location.href = url_target + EXAM.screen_code_next +'/1';
            }
        }
    }
}

jQuery(function(){
    
    jQuery('#btn_next').hide();
    if(<?php echo $type_ctr?>.timer_value > 0) { <?php echo $type_ctr?>.timer_change(); }
});
</script>
<div style="min-height: inherit; <?php echo (($photo != '') ? 'background:url(' . str_repeat('../', 4) . 'TEMP/' . $photo . '_resized.jpg) top center no-repeat' : '')?>">
    
    <?php
    if($text != '') { ?><div style="float: left"><div style="background: #FFF; color: #000; display: inline; padding: 5px"><?php echo $text?></div></div><?php }
    
    if($timer_visibility == 'shown') {

        ?>
        <div style="float: right">
            <div style="text-align: center" id="<?php echo $type_ctr?>_timer_value_wrapper"><?php echo $timer_value?></div>
        </div>
        <?php
    }
    ?>
    
    <div style="clear: both"></div>    
</div>