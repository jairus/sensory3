<?php defined('BASEPATH') or exit('No direct script access allowed')?>

<div id="login_wrapper">
    
    <div id="login_icon_wrapper" class="login_icon_wrapper_green">
        <table cellpadding="0" cellpadding="0">
            <tr>
                <td><div id="login_icon" class="login_icon login_icon_green"></div></td>
                <td><span style="color: #006600">Please sign-in by filling-up the required <b>fields</b> below.</span></td>
            </tr>
        </table>
    </div>
    
    <div><b>Name</b><b class="mandatory">*</b></div>
    <div>
        <table cellpadding="0" cellspacing="0">
            <tr><td>Firstname</td>
                <td class="padLf2">Lastname</td>
            </tr>
            <tr><td><input id="fname" type="text" /></td>
                <td class="padLf2"><input id="lname" type="text" /></td>
            </tr>
        </table>
    </div>
    <div><b>Age</b><b class="mandatory">*</b></div>
    <div><input type="text" id="age" style="width: 30px; text-align: right" /></div>
    <div>
        <div><b>Gender</b><b class="mandatory">*</b></div>
        <div>
            <input type="radio" name="icon" value="male" /><img title="Male" alt="Male" class="gender" src="<?php echo $blankGIF?>" />
            <input type="radio" name="icon" value="female" /><img title="Female" alt="Female" class="gender female" src="<?php echo $blankGIF?>" />
        </div>
    </div>
    <div class="padTp8"><input id="login" type="button" value="Enter" onclick="NE.submit()" /></div>
</div>