<?php defined('BASEPATH') or exit('No direct script access allowed')?>

<div id="login_wrapper">
    
    <div id="login_icon_wrapper" class="login_icon_wrapper_green">
        <table cellpadding="0" cellpadding="0">
            <tr>
                <td><div id="login_icon" class="login_icon login_icon_green"></div></td>
                <td><span style="color: #006600">Please sign-in first with your <b>employee #</b> and <b>password</b>.</span></td>
            </tr>
        </table>
    </div>
    <div>Employee #</div>
    <div class="padBtmTp2">
        <input id="employee_no" type="text"  onkeyup="return GBL.numOnly(event)" onkeydown="return GBL.numOnly(event)" maxlength="20" onkeypress="EMPLOYEE.keypress_login(event)" />
        <img style="cursor: pointer" id="search_trigger" title="Forgot #, or No employee # yet?" alt="Forgot #, or No employee # yet?" class="tooltip" src="<?php echo xy_url('media/images/tip.png')?>" />
    </div>
    <div id="search_div" style="margin: 10px 0 10px 20px; border: 1px dashed #CCC; padding: 5px 10px 5px 10px; background: #EFEFEF">
        <div style="font: 12px 'Lucida Grande'">Search <b>name</b> <span class="tip">( i.e. "<b>Luna Buwan</b>" )</span></div>
        <div class="padBtmTp2"><input style="width: 270px" id="search" type="text" maxlength="100" /></div>
        <div id="search_result_div"></div>
    </div>
    <div><b>Password</b><b class="mandatory">*</b></div>
    <div class="padBtmTp2"><input title="Birthdate(yyyy-mm-dd)" id="password" type="password" maxlength="13" onkeypress="EMPLOYEE.keypress_login(event)" /></div>
    <div>
        <table cellpaddding="0" cellspacing="0">
            <tr>
                <td><input id="login" type="button" value="Log-in" onclick="EMPLOYEE.login(this)" /></td>
                <td style="padding: 8px 0 0 5px"><a href="<?php echo $this->config->item('XY')->DOCROOT?>employee/register"><b>Register a new User.</b></a></td>
            </tr>
        </ul>
    </div>

</div>