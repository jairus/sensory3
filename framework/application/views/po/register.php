<?php defined('BASEPATH') or exit('No direct script access allowed')?>

<?php
$month_arr = xy_month_array();
list($thisyear) = explode('-', $this->config->config['XY']->TODAY);
?>
<div>
    <table cellpadding="0" cellspacing="0">
        <tr><td align="right"><b>Employee #</b></td></tr>
        <tr><td align="right"><input id="employee_no" type="text" onkeyup="return GBL.numOnly(event)" onkeydown="return GBL.numOnly(event)" maxlength="20" /></td></tr>
        <tr><td><b class="mandatory">*</b> <b>Name</b></td></tr>
        <tr><td>
            <table cellpadding="0" cellspacing="0">
                <tr><td>Firstname</td>
                    <td class="padLf2">Middlename</td>
                    <td class="padLf2">Lastname</td>
                </tr>
                <tr><td><input id="fname" type="text" maxlength="50" /></td>
                    <td class="padLf2"><input id="mname" type="text" maxlength="50" /></td>
                    <td class="padLf2"><input id="lname" type="text" maxlength="50" /></td>
                </tr>
            </table>
            </td>
        </tr>
        <tr><td><b class="mandatory">*</b> <b>Birthdate</b></td></tr>
        <tr><td>
            <table cellpadding="0" cellspacing="0">
                <tr><td>
                    <select id="bd_month">
                        <option value="">Month:</option>
                        <?php                
                        for($x = 0, $y = count($month_arr); $x<$y; $x++) {

                            $xmonth = ((($x + 1) < 10) ? '0' : '') . ($x + 1);
                            ?><option value="<?php echo $xmonth?>"><?php echo $month_arr[$x]?></option><?php
                        }
                        ?>
                    </select>
                    </td>
                    <td class="padLf2"><select id="bd_day"></select></td>
                    <td class="padLf2">
                        <select id="bd_year">
                            <option value="">Year:</option>
                            <?php
                            for($x = 1930, $y = ($thisyear - 13); $x<=$y; $x++) {
                                ?><option value="<?php echo $x?>"><?php echo $x?></option><?php
                            }
                            ?>
                        </select>
                    </td>
                    <td class="padLf2"><span id="age"></span></td>
                </tr>
            </table>
            
            </td>
        </tr>
        <tr><td><b class="mandatory">*</b> <b>Gender</b></td></tr>
        <tr><td>
            <input type="radio" name="icon" value="male" /><img title="Male" alt="Male" class="gender" src="<?php echo $blankGIF?>" />
            <input type="radio" name="icon" value="female" /><img title="Female" alt="Female" class="gender female" src="<?php echo $blankGIF?>" />
            </td>
        </tr>
        <tr><td><b class="mandatory">*</b> <b>SBU/Division</b> <span class="tip">(Strategic Business Unit)</span></td></tr>
        <tr><td>
            <div class="fltLf">
            <select id="sbu" onchange="GBL.toggle_other_field(this,'sbu_other_wrapper','sbu_other')">
                <?php echo $sbu?>
                <option value="">Others</option>
            </select>
            </div>
            <div class="fltLf hidden padLf2" id="sbu_other_wrapper"><input type="text" id="sbu_other" maxlength="50" /></div>
            </td>
        </tr>
        <tr><td><b class="mandatory">*</b> <b>Location</b></td></tr>
        <tr><td>
            <div class="fltLf">
            <select id="sbu_loc" onchange="GBL.toggle_other_field(this,'sbu_loc_other_wrapper','sbu_loc_other')">
                <?php echo $sbu_locations?>                
                <option value="">Others</option>
            </select>
            </div>
            <div class="fltLf hidden padLf2" id="sbu_loc_other_wrapper"><input type="text" id="sbu_loc_other" maxlength="50" /></div>
            </td>
        </tr>
        <tr><td><b class="mandatory">*</b> <b>Department</b></td></tr>
        <tr><td><input id="dept" type="text" style="width: 250px" maxlength="50" /></td></tr>
        <tr><td><b class="mandatory">*</b> <b>Office e-Mail Address</b></td></tr>
        <tr><td><input id="office_email" type="text" style="width: 250px" maxlength="100" /></td></tr>
        <tr><td><b class="mandatory">*</b> <b>Mobile #</b></td></tr>
        <tr><td><input id="mobile_no" type="text" maxlength="15" /></td></tr>
        <tr><td><b>Local #</b></td></tr>
        <tr><td><input id="local_no" type="text" maxlength="15" /></td></tr>
        <tr><td class="padTp2">
                <ul>
                    <li><div class="btnWrapper" style="width: 90px"><input style="width: 90px" class="btn" type="button" value="Register" onclick="EMPLOYEE.register(this)" /></div></li>
                    <li style="padding: 8px 0 0 5px"><a href="<?php echo $this->config->config['XY']->DOCROOT?>avail/login"><b>Log-in.</b></a></li>
                </ul>
            </td>
        </tr>
    </table>
</div>