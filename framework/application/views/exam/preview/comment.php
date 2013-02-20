<?php defined('BASEPATH') or exit('No direct script access allowed')?>
<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

array_walk($item, 'xy_screen_and_item_string_html');
extract($item);
?>
<div>
    <?php if($label != '') { ?><div><?php echo $label?></div><?php } ?>
    <div><textarea id="<?php echo $type, '_', $ctr?>_field"></textarea></div>
</div>