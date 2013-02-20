<?php defined('BASEPATH') or exit('No direct script access allowed')?>

<div>Pick a date:</div>
<div style="padding-bottom: 10px">
    <input type="text" id="date" value="<?php echo $today?>" />
    <input id="pick_date_trigger" type="button" value="Go" />
</div>


<table id="content_tbl" cellpadding="0" cellspacing="0">
    <tr><td><div id="menu_1" class="menu">Welcome Text</div></td>
        <td class="padLf5"><div id="menu_2" class="menu">General instruction</div></td>
        <td class="padLf5"><div id="menu_3" class="menu">Thank you Text</div></td>
    </tr>
    <tr id="content_tr"><td colspan="3" width="500"><div style="display: none"><textarea id="content" style="width: 486px"></textarea></div></td></tr>
</table>

<div>
    
    <div style="margin: 10px 0 5px 0"><b>RTA Sequence :</b></div>
    <table cellpadding="0" cellspacing="0">
        <tr><td valign="top">
                <ul id="rtas_wrapper" style="width: 200px">

                    <?php
                    if($sequence != '') echo $sequence;
                    /*else { foreach($rta as $row) { ?><li id="rta_item_<?php echo $row->rta_id?>" class="ui-state-highlight rta_item_<?php echo $row->rta_id?>"><?php echo $row->rta_name?></li><?php } }*/
                    ?>

                </ul>
            </td>
            <td valign="top" style="padding-left: 50px">
                <ul style="width: 200px; list-style: none; margin: 0; padding: 0">
                    <li style="margin: 1px 0 1px 0; padding: 2px 5px 2px 5px; cursor: default" id="pause_wrapper" class="ui-state-highlight"><b>Pause/Break</b></li>
                </ul>
            </td>
        </tr>
    </table>
    
</div>

<div style="margin-top: 30px; float: right">
    <input type="button" value="Next" onclick="STEP_1.submit()" />
</div>