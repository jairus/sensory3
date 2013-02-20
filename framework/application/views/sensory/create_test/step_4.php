<?php defined('BASEPATH') or exit('No direct script access allowed')?>

<?php
parse_str($q->code_control, $controls);
$controls = array_values($controls);

$code_combination_html = '';

for($x=1; $x<=$q->respondents; $x++) {

    $text = $code_combination->$x;
    
    if($text) {
        
        $text = str_replace(array($controls[0], $controls[1]), array('<span style="color: #CC0000">' . $controls[0] . '</span>', '<span style="color: #CC0000">' . $controls[1] . '</span>'), $text);
        $code_combination_html .= '<li id="li_' . $x . '_' . str_replace(' ', '_', $code_combination->$x) . '" title="' . $code_combination->$x . '" style="font-size: 12px; padding: 2px 15px 2px 15px; text-align: center; cursor: pointer" class="ui-state-highlight">' . $text . '</li>';
    }
    
    //$respondent_numbering_html .= '<tr><td id="code_numbering_' . $x . '" style="font-size: 12px; text-align: right"><div>' . $x . '</div></td></tr>';
    
    $respondent_numbering_html .= '<li id="code_numbering_' . $x . '" style="font-size: 12px; text-align: right">' . $x . '</li>';
}
?>

<div class="popup" id="popupjs_wrapper" style="display: none; text-align: left; font-size: 14px"> 
    <div class="popup_title">List of Codes</div> 
    <div class="popup_content">
        
        <div style="margin-bottom: 10px; border-bottom: 1px solid #CCC; padding: 10px; background: #EFEFEF">Option: <input type="checkbox" id="checkall_trigger" /> <label for="checkall_trigger"><b>Check all</b></label> <span style="margin-left: 30px" id="nof_selection">0</span> / <b><?php echo $q->respondents?></b></div>
        
        <p style="overflow: auto"></p>

        <div class="popup_buttons" style="font-size: 14px">
            <button id="popupjs_btn_ok" class="default">Okay</button>
        </div>
    </div>
</div>

<?php include_once APPPATH . 'views/sensory/create_test/steps.inc.php'?>

<table cellspacing="0" cellpadding="0">
    
    <tr><td valign="top">
            
            <table cellpadding="0" cellspacing="0">
                <tr><td valign="top" style="padding-right: 5px">
                        <div style="margin: 10px 0 3px 0"><b>Combinations</b></div>
                        <div><input id="auto_fill_trigger" type="button" value="Auto fill Seats" style="width: 142px" /></div>
                        <div style="height: 50px">
                            
                            <input id="distribution_field_trigger" type="checkbox" /> <label for="distribution_field_trigger">Multi-select</label>
                            <div id="clear_selections_trigger_wrapper" style="padding-left: 25px"><a title="clear selections" href="javascript:SEAT.code_distributor_clear_selection()"><img src="<?php echo xy_url('media/images/16x16/clear.png')?>" /> Clear</a></div>
                            
                        </div>
                        
                        <table cellpadding="0" cellspacing="0">
                            <tr><td valign="top" width="25" align="left">
                                    <ul style="list-style: none; margin: 0; padding: 0">
                                        <?php echo $respondent_numbering_html?>
                                    </ul>
                                </td>
                                <td valign="top" style="padding-left: 5px">
                                    <ul class="connectedSortable" id="code_distributor_wrapper">
                                        <?php echo $code_combination_html?>
                                    </ul>
                                </td>
                            </tr>
                        </table>
                        
                    </td>
                    <td valign="top" style="border-left: 1px solid #CCC; border-right: 1px solid #CCC; padding: 0 5px 0 5px">
                        <div style="width: 115px"><b>Batch/Time</b></div>
                        <div>
                            Pick <select id="batch" onchange="STEP_4.code_distribution_load()">
                                <?php $x = 0; foreach($batches as $batch) { $x++; ?><option value="<?php echo $x?>"><?php echo $batch?></option><?php } ?>
                            </select>
                        </div>
                        
                        <div id="distribution_field_wrapper">
                            
                            <div><b>Location</b></div>
                            <div>
                                <select id="sensorium">
                                    <option value="1">Sensorium 1</option>
                                    <option value="2">Sensorium 2</option>
                                </select>
                            </div>
                        
                            <div><b>Seats <b class="mandatory">*</b></b></div>
                            <div><input id="distribution_field" style="width: 99px; text-align: right" type="text" /></div>
                            <div style="float: right; padding-right: 2px">
                                <input id="seat_fill_trigger" type="button" value="Fill" />
                                
                            </div>
                            
                        </div>
                        
                        <div style="margin-top: 50px">
                            <ul id="code_standby_wrapper"></ul>
                        </div>
                        
                    </td>
                    <td valign="top" style="padding-left: 5px">
                        
                        
                        <table cellpadding="0" cellspacing="0">
                            <tr><td>
                                    <div><b>Sensorium 1</b></div>
                                    <div><a title="clear stations" href="javascript:SEAT.clear(1)"><img src="<?php echo xy_url('media/images/16x16/clear.png')?>" /> Clear</a></div>
                                </td>
                                <td style="padding-left: 10px">
                                    
                                    <div><b>Sensorium 2</b></div>
                                    <div><a title="clear stations" href="javascript:SEAT.clear(2)"><img src="<?php echo xy_url('media/images/16x16/clear.png')?>" /> Clear</a></div>
                                    
                                </td>
                            </tr>
                            <tr><td>
                                    <ul id="code_receiver_wrapper_1" style="">
                                    <?php
                                    for($x=1; $x<=16; $x++) {

                                        ?>
                                        <li class="connectedSortable" id="code_receiver_1_<?php echo $x?>" style="">
                                            <?php echo $x?>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                    </ul>                                    
                                </td>
                                <td style="padding-left: 10px">
                                    <ul id="code_receiver_wrapper_2" style="">
                                    <?php
                                    for($x=1; $x<=16; $x++) {

                                        ?>
                                        <li class="connectedSortable" id="code_receiver_2_<?php echo $x?>" style="">
                                            <?php echo $x?>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                    </ul>
                                    
                                </td>
                            </tr>
                        </table>
                        
                        
                        <ul style="clear: both"></ul>
                        <table style="clear: both"></table>
                        
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<div style="clear: both; margin-top: 20px; float: right">
    <input type="button" value="Back" onclick="GBL.go('sensory/create_test/<?php echo $rta->id?>/?step=3')" />
    <input type="button" value="Save" onclick="STEP_4.submit()" /> <i style="color: #777">(Final Step)</i>
</div>