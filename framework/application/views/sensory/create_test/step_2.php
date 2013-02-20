<?php defined('BASEPATH') or exit('No direct script access allowed')?>

<?php include_once APPPATH . 'views/sensory/create_test/steps.inc.php'?>

<table cellspacing="0" cellpadding="0">
    <tr><td align="right"><b>No. of Respondents</b></td>
        <td style="padding: 0 2px 0 2px">:</td>
        <td>
            <input id="respondents" type="text" style="width: 30px; text-align: right" onkeypress="return GBL.numOnly(event)" maxlength="3" value="<?php echo $q->respondents?>" />
        </td>
    </tr>
    
    <tr><td colspan="3">&nbsp;</td></tr>
    
    <tr><td align="right">No. of test dates</td>
        <td style="padding: 0 2px 0 2px">:</td>
        <td><b><?php echo (substr_count($rta->schedule, ',') + 1)?></b></td>
    </tr>
    
    <tr><td colspan="3">&nbsp;</td></tr>
    
    <tr><td align="right" valign="top"><b>No. of Batch</b></td>
        <td valign="top" style="padding: 0 2px 0 2px">:</td>
        <td valign="top">

            <input id="batch" type="text" style="width: 30px; text-align: right" onkeypress="return GBL.numOnly(event)" maxlength="3" value="<?php echo $q->batch?>" />
            <div id="batch_value_wrapper" style="padding-top: 2px">
                <?php
                for($x=0; $x<$q->batch; $x++) {

                    ?><div style="margin-bottom: 2px"><input name="batch_value_content" type="text" value="<?php echo $batch_content[$x]?>" /></div><?php
                }
                ?>
            </div>
        </td>
    </tr>
    
    <tr><td colspan="3">&nbsp;</td></tr>
    
    <tr><td align="right">Flow</td>
        <td style="padding: 0 2px 0 2px">:</td>                            
        <td><input style="margin: 0" type="checkbox" name="" id="flow"<?php echo (($q->flow == 'both') ? ' checked="checked"' : '')?> /> <label for="flow">Can go back to previous screens.</label></td>
    </tr>
    
    <tr><td colspan="3">&nbsp;</td></tr>
    
    <tr><td align="right" valign="top">Panelist registration</td>
        <td valign="top" style="padding-left: 2px">:</td>
        <td valign="top">

            <table cellspacing="0" cellpadding="0">
                <tr><td><input style="margin: 0" type="checkbox" name="panelist_reg" id="employee"<?php echo (($q->registration == 'both' || $q->registration == 'e') ? ' checked="checked"' : '')?> /> <label for="employee">Employee</label></td></tr>
                <tr><td><input style="margin: 0" type="checkbox" name="panelist_reg" id="nemployee"<?php echo (($q->registration == 'both' || $q->registration == 'ne') ? ' checked="checked"' : '')?> /> <label for="nemployee">Non-employee</label></td></tr>
            </table>

        </td>
    </tr>
    
    <tr><td colspan="3">&nbsp;</td></tr>
    
    <tr><td align="right" valign="top">
            <div><b>Assign Codes</b></div>
            <div><input type="button" value="Generate" onclick="STEP_2.code_generate(<?php echo $rta->no_of_samples?>,<?php echo (int) $with_2ndary_code?>)" />
                <a title="clear all codes" href="javascript:STEP_2.code_clear()"><img src="<?php echo xy_url('media/images/16x16/clear.png')?>" /></a>
            </div>
            <div id="code_restore_wrapper"><input type="button" value="Revert" onclick="STEP_2.code_restore(<?php echo $rta->no_of_samples?>)" /></div>
        </td>
        <td valign="top" style="padding-left: 2px">:</td>
        <td valign="top">

            <table id="table_data" cellspacing="0" cellpadding="0" style="margin-top: 4px">
                <tr><th><div>#</div></th>
                    <th><div>Primary <b class="mandatory">*</b></div></th>
                    <?php
                    if($with_2ndary_code) {

                        $style1 = '';
                        $style2 = ' class="mandatory"'; 
                    } else {

                        $style1 = ' style="color: #CCC"'; 
                        $style2 = ' style="color: #CCC; font: bold 14px Verdana"'; 
                    }
                    ?>
                    <th><div<?php echo $style1?>>Secondary <b<?php echo $style2?>>*</b></div></th>
                    <th><div>Tag Control</div></td>
                    <th><div>Product/Sample Name</div></td>
                </tr>
                <?php                            
                if($q->codes != '') { parse_str($q->codes, $codes); }

                for($x=1; $x<=$rta->no_of_samples; $x++) {

                    ?>
                    <tr><td><div class="bold"><?php echo $x?></div></td>
                        <td><div><input id="code_1_<?php echo $x?>" value="<?php echo $codes['code_1_' . $x]?>" style="width: 70px; text-align: right" type="text" onkeypress="return GBL.numOnly(event)" /></div></td>
                        <?php

                        if($with_2ndary_code) {

                            ?><td><div><input id="code_2_<?php echo $x?>" value="<?php echo $codes['code_2_' . $x]?>" style="width: 70px; text-align: right" type="text" onkeypress="return GBL.numOnly(event)" /></div></td><?php
                        } else {

                            ?><td><div><input style="width: 70px; text-align: right" type="text" disabled="disabled" /></div></td><?php
                        }
                        ?>
                        <td align="center"><div><input name="tag_control_trigger" value="<?php echo $x?>" type="radio"<?php echo (($control_codes_index == $x) ? ' checked="checked"' : '')?> /></div></td>
                        <td><div><input id="product_name_<?php echo $x?>" type="text" value="<?php echo $product_names[$x]?>" maxlength="50" style="width: 200px" /></div></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </td>
    </tr>
</table>
        

<div style="margin-top: 20px; float: right">
    <input type="button" value="Back" onclick="window.location.href='<?php echo xy_url('sensory/create_test/' . $rta->id . '/?step=1')?>'" />
    <input type="button" value="Next > Step 3" onclick="STEP_2.submit()" />
    <a href="<?php echo xy_url('sensory/create_test/' . $rta->id . '/?step=3')?>">Skip</a>
</div>