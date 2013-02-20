<?php defined('BASEPATH') or exit('No direct script access allowed')?>
<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

array_walk($item, 'xy_screen_and_item_string_html');
extract($item);
$attributes = explode('[=ROW=]', $attr);

$type_ctr = $type . '_' . $ctr;
?>
<div id="<?php echo $type_ctr?>_wrapper" style="margin-bottom: <?php echo ((($ctr + 1) > 1) ? 20 : 0)?>px">
    <?php    
    if($header != '') {
        ?>
        <div style="margin-bottom: 5px">
            <div style="display: inline; background: #333; color: #FFF; padding: 5px 10px 5px 10px"><b><?php echo $header?></b></div>        
        </div>
        <?php
    }
    
    for($x=0, $y=count($attributes); $x<$y; $x++) {
        
        list($tmp, $items) = explode('[=SETTING=]', $attributes[$x]);
        //$items = explode('[=ITEM=]', $items);
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
                $id = $type_ctr . '_' . $label_id . '_' . ($x2 + 1);
                
                if($setting[1] == 'Horizontal' && (($x2 + 1) < $y2)) { 
                    
                    $style = str_replace('padding-right: 10px', '', $style);
                    $style .= 'padding-right: 10px';                    
                }
                
                ?><div style="<?php echo $style?>" title="<?php echo $label, ': ', $item?>"><input id="<?php echo $id?>" type="checkbox" /> <label for="<?php echo $id?>"><?php echo $item?></label></div><?php
            }
            
            if($setting[1] == 'Horizontal') echo '<div style="clear: both"></div>';
            ?>
        </div>
        <?php
    }
    ?>
</div>