<?php
$codes = array(669, 360);
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
    <style type="text/css">
        
        @import url('popupjs/css.css');
        @import url("../framework/application/third_party/jquery.ui.1.8/smoothness/jquery-ui-1.8.16.custom.css");
        
        body, td {
            font: 12px Arial;
        }
    </style>
    <script type="text/javascript" src="../media/js/jquery.min.1.6.2.js"></script>
    <script type="text/javascript" src="../media/js/globals.js"></script>
    
    <script type="text/javascript" src="../framework/application/third_party/jquery.ui.1.8/jquery-ui-1.8.16.custom.min.js"></script>
    <script type="text/javascript" src="../framework/application/third_party/jquery.ui.1.8/jquery.cookie.js"></script>

    <script type="text/javascript" src="popupjs/1_prototype.js"></script>
    <script type="text/javascript" src="popupjs/2_effects.js"></script>
    <script type="text/javascript" src="popupjs/3_dragdrop.js"></script>
    <script type="text/javascript" src="popupjs/4_lowpro.js"></script>
    <script type="text/javascript" src="popupjs/5_popup.js"></script>
    <script type="text/javascript" src="popupjs/6_js.js"></script>
    
    <script type="text/javascript" src="js.js"></script>
</head>
<body>

    <div class="popup" id="screenedit_popup" style="display: none; width: 24em;"> 
      <div class="popup_title">Screen Management</div> 
      <div class="popup_content"> 
        <p>hello</p>
        
        <div class="popup_buttons">
            <button class="close_popup default">Okay</button><button style="margin-left: 2px" class="close_popup">Cancel</button>
        </div>
        
      </div>
    </div>
    
    <table id="tbl" border="1" cellpadding="0" cellspacing="0">
        <tr><th>Codes</th>
            <th>Screen #</th>
            <th>Type/Item(s)</th>
            <th>Details</th>
            <th>Title/label</th>
            <th>Include<br />label?</th>
            <th>Options</th>
        </tr>
        <?php
        for($x=0, $y = count($codes); $x<$y; $x++) {
            
            $code = $codes[$x];
            
            ?>
            <tr id="tr_<?php echo $code?>_1">
                <td valign="top">
                    <div style="text-align: center; font-size: 20px"><b><?php echo $code?></b></div>
                    <div>
                        <div onclick="SCREEN.samplename_toggle(<?php echo $code?>)" id="samplename_<?php echo $code?>_trigger">Sample name</div>
                        <div style="display: none" id="samplename_<?php echo $code?>_field_wrapper"><input id="samplename_<?php echo $code?>_field" onkeypress="SCREEN.samplename_keypressed(event)" type="text" /></div>
                    </div>
                </td>
                <td colspan="5">&nbsp;</td>
                <td valign="top">
                    <a href="javascript:SCREEN.screen_add(<?php echo $code?>)">Add screen</a><br />
                    <a href="javascript:SCREEN.screen_edit(<?php echo $code?>)">Edit</a>
                </td>
            </tr>
            <?php
        }
        ?>
        </table>
    </body>
</html>
