<?php defined('BASEPATH') or exit('No direct script access allowed')?>
<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

array_walk($item, 'xy_screen_and_item_string_html');
extract($item);

$item_total = count($_SESSION['EXAM'][$screen_code][$screen_count]['items']);
?>
<script type="text/javascript">
jQuery(function(){ EXAM.answers[<?php echo $ctr - 1?>] = { 'type' : '<?php echo $type?>', 'axl' : '' }; });
</script>

<div style="margin-left: auto; margin-right: auto; margin-bottom: <?php echo (($ctr < $item_total) ? 50 : 0)?>px; margin-top: <?php echo (($ctr > 1) ? 50 : 0)?>px; width: 60%">
    
    <div style="font-size: 18px; font-weight: bold; width: 700px"><?php echo $i?></div>
    
</div>