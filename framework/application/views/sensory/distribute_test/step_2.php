<?php defined('BASEPATH') or exit('No direct script access allowed')?>

<div style="min-height: 350px">
    <table cellpadding="0" cellspacing="0">
        <tr><td style="padding-right: 5px; vertical-align: top">
                <div><b>RTA list</b></div>
                <ul id="rtas_wrapper" style="width: 150px"><?php echo $sequence?></ul>
            </td>
            <td align="center" style="padding: 2px  0 0 5px; vertical-align: top; width: 100px; border-left: 1px solid #CCC; border-right: 1px solid #CCC">
                <div><b>Batch / Time</b></div>
                <div>
                    <select id="batch">
                    <?php foreach($batch as $time) { ?><option value="<?php echo $time?>"><?php echo $time?></option><?php }?>
                    </select>
                </div>
            </td>
            <td style="padding-left: 5px; width: 400px; vertical-align: top">
                <div><b>Actual</b></div>
                <table id="rta_assignment_wrapper" cellpadding="0" cellspacing="0" width="100%"></table>
            </td>

            <td style="padding-left: 20px; vertical-align: top">
                <div>Sensorium 1</div>
                <ul class="seat_wrapper" style="width: 440px">
                    <?php
                    for($x=1; $x<=16; $x++) {

                        ?>
                        <li id="s1_<?php echo $x?>" class="ui-state-default">
                            <table cellpadding="0" cellspacing="0">
                                <tr><td width="80">
                                        3 Done<br />
                                        2 On-going
                                    </td>
                                    <td valign="top"><b><?php echo $x?></b></td>
                                </tr>
                            </table>
                        </li><?php
                    }
                    ?>
                </ul>

                <div style="clear: both">Sensorium 2</div>
                <ul class="seat_wrapper" style="width: 440px">
                    <?php
                    for($x=1; $x<=16; $x++) {

                        ?><li id="s2_<?php echo $x?>" class="ui-state-default"><b><?php echo $x?></b> <span style="color: #888">[<?php echo ($x + 16)?>]</span></li><?php
                    }
                    ?>
                </ul>
            </td>
        </tr>
    </table>
</div>

<div><input type="button" value="Back" onclick="GBL.go('sensory/distribute_test/1/<?php echo $date?>')" /></div>