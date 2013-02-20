<?php
$user_level = array(
    1 => 'Administrator',
    2 => 'Project Owner',
    3 => 'Employee',
    4 => 'Immediate Superior',
    5 => 'Non-employee',
    6 => 'Multi Level (2)',
    7 => 'Multi Level (3)');

if(! empty($this->session)) {
    if($this->session->level == 1) $redirect = 'admin';
    elseif(in_array($this->session->level, array(2, 6, 7))) $redirect = 'po';
    elseif($this->session->level == 3) $redirect = 'employee';
    elseif($this->session->level == 5) $redirect = 'ne';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "media/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php $this->document->loadHead()?>
</head>
<body bgcolor="#F7F7F7">
<!--div id="xyDIALOG"></div-->
<div id="main">
    <div id="wrapper">
        
        <div style="padding: 5px; margin-top: 1px; background: #FFF; border: 1px solid #DDD; display: inline-block">
            <div class="buttons">
                <?php
                if(! empty($this->session)) {?>
                    
                    <a href="<?php echo xy_url('home')?>" class="negative">
                        <img src="<?php echo xy_url('media/images/16x16/dashboard.png')?>" alt="jollibee" />
                        Dashboard
                    </a>
                
                    <?php
                    if($this->session->level == 1) {
                        ?>                        
                        <a href="<?php echo xy_url('admin/rta')?>" class="negative">
                            <img src="<?php echo xy_url('media/images/16x16/sensory.png')?>" alt="jollibee" />
                            Sensory
                        </a>

                        <a href="<?php echo xy_url('admin/user')?>" class="negative">
                            <img src="<?php echo xy_url('media/images/16x16/user-mgt.png')?>" alt="jollibee" />
                            User Mgt
                        </a>

                        <a href="<?php echo xy_url('admin/field')?>" class="negative">
                            <img src="<?php echo xy_url('media/images/16x16/field-mgt.png')?>" alt="jollibee" />
                            Field Mgt
                        </a>

                        <a href="<?php echo xy_url('admin/hardware')?>" class="negative">
                            <img src="<?php echo xy_url('media/images/16x16/hardware.png')?>" alt="jollibee" />
                            Hardware Settings
                        </a>
                        <?php
                    }
                    else
                    if($this->session->level == 2 || $this->session->level == 6 || $this->session->level == 7) {
                        
                        ?>
                        <a href="<?php echo xy_url('po/rta_by_owner')?>" class="negative">
                            <img src="<?php echo xy_url('media/images/16x16/rta.png')?>" alt="jollibee" />
                            RTA
                        </a>                        
                        <?php                        
                    }
                    
                    if($this->session->level != 5) {
                        ?>
                        <a href="<?php echo xy_url('home/logout/?r=' . base64_encode($redirect))?>" class="negative">
                            <img src="<?php echo xy_url('media/images/16x16/log-out.png')?>" alt="jollibee" />
                            Log-Out
                        </a>
                        <?php
                    }
                } else {
                    
                    if($this->configXY->URI[0] != 'ne') {
                        
                        ?>
                        <a href="<?php echo xy_url('employee/login')?>" class="negative">
                            <img src="<?php echo xy_url('media/images/16x16/key.png')?>" alt="jollibee" />
                            Log-In
                        </a>
                        <?php
                    }
                    
                    if($this->configXY->URI[0] == 'employee') {
                        
                        ?>
                        <a href="<?php echo xy_url('employee/register')?>" class="negative">
                            <img src="<?php echo xy_url('media/images/16x16/sign-up.png')?>" alt="jollibee" />
                            Register
                        </a>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
        <?php
        if(! empty($this->session)) {

            ?>
            <div style="padding: 5px 0 5px 5px">
                <div id="user_welcome_wrapper_outer">
                    <table id="user_welcome_wrapper" cellpadding="0" cellspacing="0">
                        <tr><td><span style="font: 12px 'Lucida Grande'"><i>Hi</i></span></td>
                            <td class="padLf5"><img src="<?php echo xy_url('media/images/16x16/user.gif')?>" /></td>
                            <td class="padLf5"><b><?php echo $this->session->firstname?></b></td>
                            <td class="padLf5"><span style="font: 10px 'Lucida Grande'; color: #BBB">(<?php echo $user_level[$this->session->level]?>)</span></td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php
        }
        ?>
        <div style="padding: 30px 0 100px 0"><?php echo $content?></div>
    </div>
</div>
</body>
</html>