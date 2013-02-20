<?php defined('BASEPATH') or exit('No direct script access allowed')?>
<div style="padding-top: 13px; clear: both; width: 520px">
    
    <table cellpadding="0" cellspacing="0">
        <tr><td>
                
                <b>Sensorium 1</b>
                <table>
                    <?php
                    for($x=1; $x<=16; $x++) {

                        ?>
                        <tr>
                            <td align="right"><?php echo $x?></td><td><input id="sensorium_1_<?php echo $x?>" type="text" maxlength="20" value="<?php echo $sensorium1[$x]?>" /><td>
                            <td><span id="sensorium_1_status_<?php echo $x?>" style="color: #999">offline</span></td>
                        </tr>
                        <?php
                    }
                    ?>

                </table>
            </td>
            <td style="padding-left: 40px">
                
                <b>Sensorium 2</b>
                <table>
                    <?php
                    for($x=1; $x<=16; $x++) {

                        ?>
                        <tr>
                            <td align="right"><?php echo $x?></td><td><input id="sensorium_2_<?php echo $x?>" type="text" maxlength="20" value="<?php echo $sensorium2[$x]?>" /><td>
                            <td><span id="sensorium_2_status_<?php echo $x?>" style="color: #999">offline</span></td>
                        </tr>
                        <?php
                    }
                    ?>

                </table>
            </td>
        </tr>
    </table>
    
    <div style="text-align: right; padding-top: 15px"><input id="update" type="button" value="Update Stations" /></div>
</div>