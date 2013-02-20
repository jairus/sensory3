<?php defined('BASEPATH') or exit('No direct script access allowed')?>

<div id="section_wrapper">    
    <?php include_once APPPATH . 'views/menu.inc.php'?>

    <div id="section_wrapper_inner">
        <?php
        if($page == '') $page = 'step_' . $step;
        include_once 'create_test/' . $page . '.php'
        ?>
    </div>    
</div>