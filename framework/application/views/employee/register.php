<?php defined('BASEPATH') or exit('No direct script access allowed')?>

<?php
$month_arr = xy_month_array();
list($thisyear) = explode('-', $this->config->config['XY']->TODAY);
?>
<div id="register_wrapper">
    
    <div id="register_icon_wrapper">
        <table cellpadding="0" cellpadding="0">
            <tr>
                <td><div id="register_icon"></div></td>
                <td><span style="color: #006600; font: 12px 'Lucida Grande'">Please fill-up the fields with <b class="mandatory">*</b>.</span></td>
            </tr>
        </table>
    </div>
    
    <table cellpadding="0" cellspacing="0">
        <tr><td align="right"><b>Employee #</b></td></tr>
        <tr><td align="right"><input id="employee_no" type="text" onkeyup="return GBL.numOnly(event)" onkeydown="return GBL.numOnly(event)" maxlength="20" /></td></tr>
        <tr><td><b>Name</b><b class="mandatory">*</b></td></tr>
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
        <tr><td><b>Birthdate</b><b class="mandatory">*</b></td></tr>
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
                            for($x = 1950, $y = ($thisyear - 5); $x<=$y; $x++) {
                                ?><option value="<?php echo $x?>"><?php echo $x?></option><?php
                            }
                            ?>
                        </select>
                    </td>
                    <td class="padLf5"><span id="age"></span></td>
                </tr>
            </table>
            
            </td>
        </tr>
        <tr><td><b>Gender</b><b class="mandatory">*</b></td></tr>
        <tr><td>
            <input type="radio" name="icon" value="male" /><img title="Male" alt="Male" class="gender" src="<?php echo $blankGIF?>" />
            <input type="radio" name="icon" value="female" /><img title="Female" alt="Female" class="gender female" src="<?php echo $blankGIF?>" />
            </td>
        </tr>
        <tr><td><b>SBU/Division</b><b class="mandatory">*</b> <span class="tip">(Strategic Business Unit)</span></td></tr>
        <tr><td>
            <div class="fltLf">
            <select id="sbu" onchange="GBL.toggle_other_field(this,'sbu_other_wrapper','sbu_other')">
                <option value="">Select:</option>
                <?php echo $sbu?>
                <option value="other">Others</option>
            </select>
            </div>
            <div class="fltLf hidden padLf2" id="sbu_other_wrapper"><input type="text" id="sbu_other" maxlength="50" /></div>
            </td>
        </tr>
        <tr><td><b>Location</b><b class="mandatory">*</b></td></tr>
        <tr><td>
            <div class="fltLf">
            <select id="sbu_loc" onchange="GBL.toggle_other_field(this,'sbu_loc_other_wrapper','sbu_loc_other')">
                <option value="">Select:</option>
                <?php echo $sbu_locations?>                
                <option value="other">Others</option>
            </select>
            </div>
            <div class="fltLf hidden padLf2" id="sbu_loc_other_wrapper"><input type="text" id="sbu_loc_other" maxlength="50" /></div>
            </td>
        </tr>
        <tr><td><b>Department</b><b class="mandatory">*</b></td></tr>
        <tr><td>
                <select id="dept">
                    <option value="">Select:</option>
                    <?php echo $departments?>
                </select>
            </td>
        </tr>
        <tr><td><b>Office email address</b><b class="mandatory">*</b></td></tr>
        <tr><td><input id="office_email" type="text" style="width: 250px" maxlength="100" /></td></tr>
        
        <tr><td class="padTp8">Contact numbers:</td></tr>
        <tr><td>
                <table cellpadding="0" cellspacing="0">
                    <tr><td><b>Mobile</b><b class="mandatory">*</b></td>
                        <td class="padLf2">Local</td>
                    </tr>
                    <tr><td><input id="mobile_no" type="text" maxlength="15" /></td>
                        <td class="padLf2"><input id="local_no" type="text" maxlength="15" /></td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <tr><td class="padTp4">
                <table cellpadding="0" cellspacing="0">
                    <tr><td><input style="width: auto; padding: 3px; font: 14px 'Lucida Grande'" type="button" value="Register" onclick="EMPLOYEE.register(this)" /></td>
                        <td class="padLf5"><a href="<?php echo $this->config->item('XY')->DOCROOT?>employee/login"><b>Log-in.</b></a></td>
                    </tr>
                </table>                
            </td>
        </tr>
    </table>
</div>