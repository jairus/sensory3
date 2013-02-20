<?php defined('BASEPATH') or exit('No direct script access allowed')?>

<div id="login_wrapper">
    
    <div id="login_icon_wrapper" class="login_icon_wrapper_green">
        <table cellpadding="0" cellpadding="0">
            <tr>
                <td><div id="login_icon" class="login_icon login_icon_green"></div></td>
                <td><span style="color: #006600">Please sign-in first with your <b>username</b> and <b>password</b>.</span></td>
            </tr>
        </table>
    </div>
    
    <div>(Administrator) <b>Username</b><b class="mandatory">*</b></div>
    <div class="padBtmTp2"><input style="width: 200px" id="username" type="text" maxlength="100" onkeypress="ADMIN.keypress_login(event)" /></div>

    <div><b>Password</b><b class="mandatory">*</b></div>
    <div class="padBtmTp2"><input style="width: 200px" id="password" type="password" maxlength="13" onkeypress="ADMIN.keypress_login(event)" /></div>
    <div><input id="login" style="width: 90px" type="button" value="Log-in" onclick="ADMIN.login()" /></div>
</div>