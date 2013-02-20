<?php defined('BASEPATH') or exit('No direct script access allowed')?>
<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

array_walk($item, 'xy_screen_and_item_string_html');
extract($item);

if($attr) $attributes = explode('[=ROW=]', $attr);
else {
    
    echo 'No attribute was set.';
    return;
}

$answer = $_SESSION['EXAM'][$screen_code][$screen_count]['items'][$ctr - 1]->axl;
if(! is_object($answer)) $answer = new stdClass;

$type_ctr = $type . '_' . $ctr;
?>
<script type="text/javascript">
jQuery(function(){
    
    EXAM.answers[<?php echo $ctr - 1?>] = { 'type' : '<?php echo $type?>', 'axl' : <?php echo json_encode($answer)?> };
    
    jQuery("input[id^='<?php echo $type_ctr?>_']").each(function(){
        
        var tmp = EXAM.ljc__extract_id(jQuery(this).attr('id'));
        var type_ctr = tmp[0]; var attr = tmp[1]; var index = tmp[2];
        
        if(! EXAM.answers[<?php echo $ctr - 1?>]['axl'][type_ctr]) EXAM.answers[<?php echo $ctr - 1?>]['axl'][type_ctr] = {};
        if(! EXAM.answers[<?php echo $ctr - 1?>]['axl'][type_ctr][attr]) EXAM.answers[<?php echo $ctr - 1?>]['axl'][type_ctr][attr] = {};
        if(! EXAM.answers[<?php echo $ctr - 1?>]['axl'][type_ctr][attr][index - 1]) EXAM.answers[<?php echo $ctr - 1?>]['axl'][type_ctr][attr][index - 1] = false;
    });
    
    jQuery("input[id^='<?php echo $type_ctr?>_']").click(function(){
        
        var tmp = EXAM.ljc__extract_id(jQuery(this).attr('id'));
        var type_ctr = tmp[0]; var attr = tmp[1]; var index = tmp[2];
        
        EXAM.answers[<?php echo $ctr - 1?>]['axl'][type_ctr][attr][index - 1] = jQuery(this).is(':checked');
        EXAM.session_updater_peritem(<?php echo $ctr?>);        
    });
});

jQuery.extend(
    
    EXAM.axl, {
        
        <?php echo $type_ctr?> : function() {
                
                var total = 0;
                
                jQuery.each(EXAM.answers[<?php echo $ctr - 1?>].axl.<?php echo $type_ctr?>, function(key, value){
                    
                    total = 0; jQuery.each(value, function(key2, value2){ if(value2 == 1) total++; });
                    
                    if(total == 0) {
                        
                        Popup.dialog({
                            title : 'ERROR',
                            message : '<div>Please select atleast one (1) answer for "<?php echo ($header ? $header : $type_ctr)?>\'s <b>'+ GBL.ucwords(key) +'</b> attribute".</div>',
                            buttons: ['Okay', 'Cancel'],
                            width: '420px'
                        });
                        
                        return false; /* Break from this loop. */
                    }
                });
                
                if(total == 0) return false;
                else return true;                
        }
    }
);
</script>
<div style="margin-left: auto; margin-right: auto; width: 60%">
    <?php    
    if($header != '') { ?><div style="margin-bottom: 5px"><div style="display: inline; background: #333; color: #FFF; padding: 5px 10px 5px 10px"><b><?php echo $header?></b></div></div><?php }
    
    for($x=0, $y=count($attributes); $x<$y; $x++) {
        
        list($tmp, $items) = explode('[=SETTING=]', $attributes[$x]);
        $items = explode('[row]', $items);
        
        list($label, $setting) = explode('[=LABEL=]', $tmp);
        $label_id = xy_make_id($label);
        
        $setting = explode(' ', $setting);
        
        ksort($items);
        $tmp = array();
        if($setting[0] == 'asc') {
            
            $titems = count($items);
            for($xy=$titems; $xy>=1; $xy--) { $tmp[] = $items[$xy - 1]; }            
            $items = $tmp;
        }
        
        if($setting[1] == 'Horizontal') $style = 'float: left; ';
        else $style = '';
        ?>
        <div style="margin-bottom: <?php echo ((($x + 1) < $y) ? 20 : 0)?>px">
            
            <div><b><?php echo $label?></b></div>
            <?php            
            for($x2=0, $y2 = count($items); $x2<$y2; $x2++) {
                
                $item = $items[$x2];
                $id = $type_ctr . '___' . $label_id . '__' . ($x2 + 1);
                
                $answer_tmp = $answer->$type_ctr->$label_id;
                
                if($setting[1] == 'Horizontal' && (($x2 + 1) < $y2)) { 
                    
                    $style = str_replace('padding-right: 10px', '', $style);
                    $style .= 'padding-right: 10px';                    
                }
                
                ?><div style="<?php echo $style?>" title="<?php echo $label, ': ', $item?>"><input id="<?php echo $id?>" type="checkbox"<?php echo (($answer_tmp->$x2 == 1) ? ' checked="checked"' : '')?> /> <label for="<?php echo $id?>"><?php echo $item?></label></div><?php
            }
            
            if($setting[1] == 'Horizontal') echo '<div style="clear: both"></div>';
            ?>
        </div>
        <?php
    }
    ?>
</div>