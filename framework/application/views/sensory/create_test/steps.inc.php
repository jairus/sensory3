<?php defined('BASEPATH') or exit('No direct script access allowed')?>

<div id="q_steps_wrapper" style="margin-bottom: 20px">
    <table cellpadding="0" cellspacing="0">
        <tr>
            <?php
            for($x=0; $x<4; $x++) { ?><td<?php echo (($x == 0) ? '' : ' class="padLf5"')?>><div<?php echo (($step == ($x + 1)) ? '' : ' title="go to STEP#' . ($x + 1) . '" onclick="GBL.go(\'sensory/create_test/' . $rta->id . '/?step=' . ($x + 1) . '\')"')?> class="q_step _<?php echo ($x + 1)?><?php echo (($step == ($x + 1)) ? '' : '_inactive')?>"></div></td><?php }
            ?>            
            <td class="padLf5"><?php echo $step_headers[$step - 1]?></td>
        </tr>
    </table>
    <div id="q_steps_wrapper_inner">
        
        <span style="color: #CCC">
            <?php
            for($x=0; $x<4; $x++) { echo (($step == ($x + 1)) ? '<span style="color: #777">&bull;</span>' : '&bull;'); }
            ?>
        </span>
        <span title="<?php echo strtoupper($rta->type_of_test)?>" style="font-weight: bold; color: #555">ID#<?php echo str_pad($rta->id, 6, '0', STR_PAD_LEFT)?>: <?php echo '<span style="color: #A23030">', $rta->samples_name, '</span> - ', $spec1, (($spec2 != '') ? (' - ' . $spec2) : '')?></span>
        
        &nbsp;&nbsp;<a title="edit RTA#<?php echo $rta->id?>" href="<?php echo xy_url('rta/edit_by_admin/' . $rta->id)?>"><img src="<?php echo xy_url('media/images/16x16/edit.png')?>" /></a>
    </div>
</div>