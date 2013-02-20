<?php defined('BASEPATH') or exit('No direct script access allowed')?>

<?php
if($this->session->level != 3) {

    ?>
    <div id="section_wrapper">
        <?php echo $rta_pending?>
        <div style="padding-top: 10px"><?php echo $alerts?></div>
    </div>
    <?php

} else {

    ?>
    <div id="employee_wrapper">
        <?php
        if($ip_hooked) {
            
            ?>
            <div style="margin-top: 60px">
                <a href="<?php echo xy_url('exam')?>"><b style="font: 32px 'Lucida Grande'">Take the exam!</b></a>
            </div>
            <?php
            
        } else {
            
            ?>
            <div style="margin-top: 40px">
                <b style="font: 32px 'Lucida Grande'">This station is not properly configured, contact the Administrator.</b>
            </div>
            <?php
        }
        ?>
    </div>
    <?php
}
?>
