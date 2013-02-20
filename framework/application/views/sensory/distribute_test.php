<?php defined('BASEPATH') or exit('No direct script access allowed')?>

<div id="section_wrapper">    
    <?php include_once APPPATH . 'views/menu.inc.php'?>

    <div id="section_wrapper_inner">
        <div id="seat_preview" style="position: absolute; display: none; width: 500px; min-height: 300px; border: 1px solid #000; background: #FFF">
            <table cellpadding="0" cellspacing="0" width="100%">
                <tr><td align="right">[<a onclick="SEAT.seat_preview_close()" href="javascript:;"><b>x</b></a>]</td></tr>

                <tr><td><div></div></td></tr>

            </table>
        </div>

        <div style="clear: both">
            <?php include_once 'distribute_test/step_' . $step . '.php'?>
        </div>
    </div>
    
</div>