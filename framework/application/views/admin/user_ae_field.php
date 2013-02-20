<?php defined('BASEPATH') or exit('No direct script access allowed')?>

<table cellpadding="0" cellspacing="0">
    <tr><td colspan="3">

            <table cellpadding="0" cellspacing="0" width="100%">
                <tr><td align="left"><b>Level</b><b class="mandatory">*</b></td>
                    <td align="left" class="padLf5">Employee #</td>
                    <td align="right"><b>Birthdate</b><b class="mandatory">*</b></td>
                </tr>
                <tr><td align="left" class="padBtmTp2">
                        <select id="level">
                            <option value="">Select:</option>
                            <?php
                            foreach($level as $key => $value) {

                                ?><option value="<?php echo $key?>"<?php echo (($key == $user->level) ? ' selected="selected"' : '')?>><?php echo $value?></option><?php
                            }
                            ?>
                        </select>
                    </td>
                    <td align="left" class="padLf2"><input id="employee_no" type="text" value="<?php echo $user->employee_no?>" style="text-align: right; width: 70px" onkeypress="return GBL.numOnly(event)" /></td>                    
                    <td align="right" width="275"><input type="text" style="width: 100px" id="birthdate" value="<?php echo $user->birthdate?>" /></td>                        
                </tr>
            </table>
            
        </td>
    </tr>
    <tr><td colspan="3" style="padding-top: 10px">Immediate Superior</td></tr>
    <tr><td colspan="3" style="padding-bottom: 10px"><?php echo $superior_choices_html?></td></tr>
    <tr><td><b>Firstname</b><b class="mandatory">*</b></td>
        <td class="padLf2"><b>Middlename</b><b class="mandatory">*</b></td>
        <td class="padLf2"><b>Lastname</b><b class="mandatory">*</b></td>
    </tr>
    <tr><td><input type="text" id="fname" value="<?php echo $user->firstname?>" /></td>
        <td class="padLf2"><input id="mname" type="text" value="<?php echo $user->middlename?>" /></td>
        <td class="padLf2"><input id="lname" type="text" value="<?php echo $user->lastname?>" /></td>
    </tr>

    <tr><td>Email address</td>
        <td class="padLf2">Username</td>
        <td class="padLf2"><b>Password</b><b class="mandatory">*</b></td>
    </tr>
    <tr><td><input type="text" id="email" value="<?php echo $user->email?>" /></td>
        <td class="padLf2"><input type="text" id="username" value="<?php echo $user->username?>" /></td>
        <td class="padLf2"><input id="password" type="text" maxlength="15" value="<?php echo $user->password?>" /></td>
    </tr>

    <!--tr><td colspan="3" class="padTp2">

            <table cellpadding="0" cellspacing="0">
                <tr><td><div class="btnWrapper" style="width: 170px"><input class="btn" style="width: 170px" type="button" value="Add this User" onclick="USER_ADD.submit(this)" /></div></td>
                    <td class="padLf5"><a title="Cancel" href="<?php //echo xy_doc_root()?>admin/user"><b>Cancel.</b></a></td>
                </tr>
            </table>

        </td>
    </tr-->
</table>