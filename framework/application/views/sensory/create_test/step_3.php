<?php defined('BASEPATH') or exit('No direct script access allowed')?>

<div class="popup" id="popupjs_wrapper" style="display: none; width: 24em; text-align: left; font-size: 14px">
    <div class="popup_title">Screen Management</div> 
    <div class="popup_content"> 
        <p style="margin-top: 5px"></p>

        <div class="popup_buttons" style="font-size: 14px">
            <button id="popupjs_btn_ok" class="default">Okay</button><button id="popupjs_btn_cancel" style="margin-left: 2px" class="close_popup">Cancel</button>
        </div>
    </div>
</div>

<?php include_once APPPATH . 'views/sensory/create_test/steps.inc.php'?>

<table cellpadding="0" cellspacing="0">

    <tr><td>
            
            <div><b>Scoresheet library</b></div>
            <div>
                
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <td><img src="<?php echo xy_url('media/images/16x16/search.png')?>" /> Search:</td>
                        <td style="padding-left: 5px">
                            <input id="ss_search_field" type="text" onkeypress="return STEP_3.ss_search_keypressed(event)" />
                            <input type="button" value="Go" onclick="STEP_3.ss_search()" />
                        </td>
                    </tr>
                </table>
                
                <div id="ss_search_result">
                    <div style="position: relative; float: right; left: +105px; background: #990000">
                        <div style="padding: 2px 5px 2px 5px"><a title="close" href="javascript:STEP_3.ss_result_toggle(false)">Close</a></div>
                    </div>
                    <div id="ss_search_result_inner"></div>
                </div>
                
            </div>
            
            <div>                
                <?php                
                /*
                 * If NOT Triangle, Paired Preference Only, Ranking for Preference Only, Duo-Trio
                 */
                
                if($rta->type_of_test == 'affective') {
                    
                    $check = in_array($rta->specific_2, $one_ss_only);
                    $specific_as_code = 'affective_' . $rta->specific_2;
                    
                } elseif($rta->type_of_test == 'analytical') {
                    
                    $check = in_array($rta->specific_1, $one_ss_only);
                    $specific_as_code = 'analytical_' . $rta->specific_1;
                }
                
                if(! $check) { //array(1, 11, 12, 13) //in_array($rta->specific_2, $one_ss_only)  
                    
                    ?>                    
                    <table style="margin-top: 20px" id="tbl_screen" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr><th width="180"><div>Codes</div></th>
                            <th width="90"><div>Screen #</div></th>
                            <th width="325"><div>Type/Item(s)</div></th>
                            <th width="170"><div>Details</div></th>
                            <th width="190" style="border-right: 0"><div>Title/label</div></th>
                            <th width="50" style="border-left: 0"><div>Show?</div></th>
                            <th width="130"><div>Options</div></th>
                        </tr>
                        <?php
                        for($x=0, $y = count($codes); $x<$y; $x++) {

                            $code = $codes[$x];
                            
                            if(isset($_SESSION['SCREEN'][$rta->id][$code])) {
                                
                                $screenz = $_SESSION['SCREEN'][$rta->id][$code];
                                $screen_total = count($_SESSION['SCREEN'][$rta->id][$code]);
                                
                                $rowspan = $screen_total;
                                $rowspan++;
                                
                                $rowspan = ' rowspan="' . $rowspan . '" ';
                                
                            } else {
                                
                                $screen_total = 0;
                                $rowspan = '';
                            }
                            
                            $product_name = $product_names[$x + 1];
                            if($product_name == '') $product_name = ' <u>Click</u> to <b>change</b>';
                            ?>
                            <tr id="tr_<?php echo $code?>_1">
                                <td valign="top"<?php echo $rowspan?>>
                                    
                                    <div style="text-align: center; font-size: 20px"><b><?php echo $code?></b></div>
                                    <div class="fntWrap" style="width: 125px; position: absolute; min-height: 16px; margin: 5px; padding: 2px; font: 12px Verdana" onkeypress="SCREEN.samplename_keypressed(event,<?php echo $code?>)" onblur="SCREEN.samplename_toggle(<?php echo $code?>,false)" onclick="SCREEN.samplename_toggle(<?php echo $code?>,true)" id="samplename_<?php echo $code?>"><?php echo $product_name?></div>
                                    
                                </td>
                                <td colspan="5">&nbsp;</td>
                                <td valign="top"<?php echo $rowspan?>>
                                    <div style="padding: 5px">
                                        <div><a href="javascript:SCREEN.screen_add(<?php echo $code?>)"><img src="<?php echo xy_url('media/images/16x16/screen-add.png')?>" /> <b>Add</b> screen</a></div>
                                        <div><a target="_blank" href="<?php echo xy_url('exam/preview/' . $rta->id . '/' . $code)?>"><img src="<?php echo xy_url('media/images/16x16/preview.png')?>" /> Preview exam</a></div>
                                        <div><a href="javascript:SCREEN.sort_init(<?php echo $code?>)"><img src="<?php echo xy_url('media/images/16x16/sort.png')?>" /> <b>Sort</b> screens</a></div>
                                        <div><a href="javascript:SCREEN.clear(<?php echo $code?>)"><img src="<?php echo xy_url('media/images/16x16/clear.png')?>" /> <b>Clear</b></a></div>
                                        <div style="margin-top: 5px">
                                            <div><b>Copy</b> screens of:</div>
                                            <div style="margin-top: 2px">
                                                <select id="screencopy_code_<?php echo $code?>">
                                                    <option value="">Select:</option>
                                                    <?php
                                                    foreach($codes as $c) {
                                                        if($code != $c) {
                                                            ?><option value="<?php echo $c?>"><?php echo $c?></option><?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                <input id="screencopy_trigger_<?php echo $code?>" onclick="STEP_3.screen_copy(<?php echo $code?>)" type="button" style="font: 12px Verdana; width: 30px" value="Go" />
                                            </div>
                                        </div>
                                        
                                        <div style="margin: 2px 0 50px 0">
                                            <a title="save" href="javascript:STEP_3.ss_save(<?php echo $code?>)"><img src="<?php echo xy_url('media/images/16x16/save.png')?>" /> <b>Save</b> score sheet</a>

                                            <div id="ss_name_wrapper_<?php echo $code?>" style="display: none">
                                                <div id="ss_name_wrapper_inner_<?php echo $code?>">

                                                    <div style="margin-bottom: 2px"><b>Name <b class="mandatory">*</b></b></div>
                                                    <div><input type="text" id="ss_name_<?php echo $code?>" /></div>
                                                    <div style="float: right; margin-top: 2px"><input type="button" value="Save" onclick="STEP_3.ss_save('<?php echo $code?>',true)" /> <input type="button" value="Cancel" onclick="STEP_3.ss_name_toggle(<?php echo $code?>,false)" /></div>
                                                    <div style="clear: both"></div>

                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div id="screen_ae_and_cancel_wrapper_<?php echo $code?>" style="margin-top: 2px">
                                            <input id="screen_ae_trigger_<?php echo $code?>" type="button" value="Save" onclick="STEP_3.screen_ae(<?php echo $code?>)" />
                                            <input id="screen_ae_cancel_<?php echo $code?>" type="button" value="Reset" onclick="STEP_3.screen_reset(<?php echo $code?>)" />
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php                            
                            if($screen_total > 0) {
                                
                                $count = 1;
                                foreach($screenz as $number => $detail) {
                                    
                                    $count++;
                                    
                                    xy_screen_and_item_string_decode($detail['screen_title_or_label_value']);                                    
                                    $label = $detail['screen_title_or_label_value'];
                                    if($label == '') $label = ' <u>Click</u> to <b>change</b> label';
                                    
                                    $option = '<div style="text-align: center; font-size: 20px"><b id="screennumber_' . $code . '_' . $count . '">' . $number . '</b></div>' .
                                    '<div style="padding: 5px">' .
                                        '<div><a id="screenitemae_' . $code . '_' . $count . '_trigger" href="javascript:SCREEN.item_ae_pick(\'' . $code . '\',' . $count . ')"><img src="' . xy_url('media/images/16x16/item-add.png') . '" /> Add item</a></div>' .
                                        '<div><a id="screenitemsort_' . $code . '_' . $count . '_trigger" href="javascript:SCREEN.item_sort_init(\'' . $code . '\',' . $count . ')"><img src="' . xy_url('media/images/16x16/sort.png') . '" /> Sort items</a></div>' .
                                        '<div><a id="screendel_' . $code . '_' . $count . '_trigger" href="javascript:SCREEN.screen_del(\'' . $code . '\',' . $count . ')"><img src="' . xy_url('media/images/16x16/delete.png') . '" /> Delete</a></div>' .
                                        '<div><a id="screenpreview_' . $code . '_' . $count . '_trigger" target="_blank" href="' . xy_url('exam/preview/' . $rta->id . '/' . $code . '/' . ($count - 1)) . '"><img src="' . xy_url('media/images/16x16/preview.png') . '" /> Preview</a></div>' .
                                    '</div>';
                                    
                                    $screen_title_or_label_visibility = $detail['screen_title_or_label_visibility'];
                                    $visibility = (($screen_title_or_label_visibility == 'shown') ? ' checked="checked"' : '');
                                    
                                    ?>
                                    <tr id="tr_<?php echo $code, '_', $count?>">
                                        <td valign="top" nowrap="nowrap"><?php echo $option?></td>
                                        <td style="padding: 5px"><ul style="padding: 0; list-style: none; margin: 0" id="ul_<?php echo $code, '_', $count?>">
                                            <?php
                                            if(! empty($detail['items'])) {

                                                foreach($detail['items'] as $item_no => $item) {
                                                    
                                                    $item_no++;
                                                    
                                                    $item_label = (($item['header'] == '') ? strtoupper($item['type']) : $item['header']);
                                                    xy_screen_and_item_string_decode($item_label);
                                                    
                                                    ?><li style="padding: 0; margin: 0; width: 250px" id="screenitem_<?php echo $code, '_', $count, '_', $item_no?>"><a title="delete" class="item_<?php echo $code, '_', $count?>_del_trigger" href="javascript:SCREEN.item_del(<?php echo $item_no, ',', $code, ',', $count?>)"><img src="<?php echo xy_url('media/images/16x16/delete.png')?>" /></a> <a title="edit" class="item_<?php echo $code, '_', $count?>_edit_trigger" href="javascript:SCREEN.item_ae_picked('<?php echo $item['type']?>',<?php echo $item_no?>,<?php echo $code?>,<?php echo $count?>)" style="font: 12px Verdana"><span><?php echo $item_label?></span></a></li><?php
                                                }
                                            }
                                            ?>
                                            </ul>
                                        </td>
                                        <td><div style="padding: 5px; color: #777"><?php echo wordwrap($detail['details'], 20, '<br />', true)?></div></td>
                                        <td valign="top">
                                            
                                            <div class="fntWrap" style="width: 153px; position: absolute; min-height: 16px; margin: 5px; padding: 2px; font: 12px Verdana" onkeypress="SCREEN.screenlabel_keypressed(event,<?php echo $code?>,<?php echo $count?>)" onblur="SCREEN.screenlabel_toggle(<?php echo $code?>,<?php echo $count?>,false); SCREEN.update(<?php echo $code?>,<?php echo $count?>)" onclick="SCREEN.screenlabel_toggle(<?php echo $code?>,<?php echo $count?>,true)" id="screenlabel_<?php echo $code?>_<?php echo $count?>" detail="<?php echo $detail['id']?>"><?php echo $label?></div>
                                            
                                        </td>
                                        <td align="center"><input id="screenlabel_<?php echo $code, '_', $count?>_visibility" type="checkbox"<?php echo $visibility?> onclick="SCREEN.update(<?php echo $code, ',', $count?>)" /></td>
                                    </tr>
                                    <?php
                                    
                                }
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                    <?php
                } else {
                    
                    ?>
                    <table style="margin-top: 20px" id="tbl_screen" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr><th width="180">&nbsp;</th>
                            <th width="90"><div>Screen #</div></th>
                            <th width="325"><div>Type/Item(s)</div></th>
                            <th width="170"><div>Details</div></th>
                            <th width="190" style="border-right: 0"><div>Title/label</div></th>
                            <th width="50" style="border-left: 0"><div>Show?</div></th>
                            <th width="130"><div>Options</div></th>
                        </tr>
                        <?php
                        if(isset($_SESSION['SCREEN'][$rta->id][$specific_as_code])) {
                                
                            $screens = $_SESSION['SCREEN'][$rta->id][$specific_as_code];
                            $screen_total = count($_SESSION['SCREEN'][$rta->id][$specific_as_code]);

                            $rowspan = $screen_total;
                            $rowspan++;

                            $rowspan = ' rowspan="' . $rowspan . '" ';

                        } else {

                            $screen_total = 0;
                            $rowspan = '';
                        }
                        ?>
                        <tr id="tr_<?php echo $specific_as_code?>_1">
                            
                            <td<?php echo $rowspan?>>&nbsp;</td>
                            <td colspan="5">&nbsp;</td>
                            
                            <td valign="top"<?php echo $rowspan?>>
                                <div style="padding: 5px">
                                    <div><a href="javascript:SCREEN.screen_add('<?php echo $specific_as_code?>')"><img src="<?php echo xy_url('media/images/16x16/screen-add.png')?>" /> <b>Add</b> screen</a></div>
                                    <div><a target="_blank" href="<?php echo xy_url('exam/preview/' . $rta->id . '/' . $specific_as_code)?>"><img src="<?php echo xy_url('media/images/16x16/preview.png')?>" /> Preview exam</a></div>
                                    <div><a href="javascript:SCREEN.sort_init('<?php echo $specific_as_code?>')"><img src="<?php echo xy_url('media/images/16x16/sort.png')?>" /> <b>Sort</b> screens</a></div>
                                    <div><a href="javascript:SCREEN.clear('<?php echo $specific_as_code?>')"><img src="<?php echo xy_url('media/images/16x16/clear.png')?>" /> <b>Clear</b></a></div>
                                    
                                    <div style="margin-bottom: 50px">
                                        <a href="javascript:STEP_3.ss_save('<?php echo $specific_as_code?>')"><img src="<?php echo xy_url('media/images/16x16/save.png')?>" /> <b>Save</b> score sheet</a>
                                        
                                        <div id="ss_name_wrapper_<?php echo $specific_as_code?>" style="display: none">
                                            <div id="ss_name_wrapper_inner_<?php echo $specific_as_code?>">
                                                
                                                <div style="margin-bottom: 2px"><b>Name <b class="mandatory">*</b></b></div>
                                                <div><input type="text" id="ss_name_<?php echo $specific_as_code?>" /></div>
                                                <div style="float: right; margin-top: 2px"><input type="button" value="Save" onclick="STEP_3.ss_save('<?php echo $specific_as_code?>',true)" /> <input type="button" value="Cancel" onclick="STEP_3.ss_name_toggle('<?php echo $specific_as_code?>',false)" /></div>
                                                <div style="clear: both"></div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div id="screen_ae_and_cancel_wrapper_<?php echo $specific_as_code?>" style="margin-top: 2px">
                                        <input id="screen_ae_trigger_<?php echo $specific_as_code?>" type="button" value="Save" onclick="STEP_3.screen_ae('<?php echo $specific_as_code?>')" />
                                        <input id="screen_ae_cancel_<?php echo $specific_as_code?>" type="button" value="Reset" onclick="STEP_3.screen_reset('<?php echo $specific_as_code?>')" />
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php
                        
                        if($screen_total > 0) {
                                
                            $count = 1;
                            $code = $specific_as_code;
                            
                            foreach($screens as $number => $detail) {

                                $count++;
                                
                                xy_screen_and_item_string_decode($detail['screen_title_or_label_value']);                                
                                $label = $detail['screen_title_or_label_value'];
                                if($label == '') $label = ' <u>Click</u> to <b>change</b> label';

                                $option = '<div style="text-align: center; font-size: 20px"><b id="screennumber_' . $code . '_' . $count . '">' . $number . '</b></div>' .
                                '<div style="padding: 5px">' .
                                    '<div><a id="screenitemae_' . $code . '_' . $count . '_trigger" href="javascript:SCREEN.item_ae_pick(\'' . $code . '\',' . $count . ')"><img src="' . xy_url('media/images/16x16/item-add.png') . '" /> Add item</a></div>' .
                                    '<div><a id="screenitemsort_' . $code . '_' . $count . '_trigger" href="javascript:SCREEN.item_sort_init(\'' . $code . '\',' . $count . ')"><img src="' . xy_url('media/images/16x16/sort.png') . '" /> Sort items</a></div>' .
                                    '<div><a id="screendel_' . $code . '_' . $count . '_trigger" href="javascript:SCREEN.screen_del(\'' . $code . '\',' . $count . ')"><img src="' . xy_url('media/images/16x16/delete.png') . '" /> Delete</a></div>' .
                                    '<div><a id="screenpreview_' . $code . '_' . $count . '_trigger" target="_blank" href="' . xy_url('exam/preview/' . $rta->id . '/' . $code . '/' . ($count - 1)) . '"><img src="' . xy_url('media/images/16x16/preview.png') . '" /> Preview</a></div>' .
                                '</div>';

                                $screen_title_or_label_visibility = $detail['screen_title_or_label_visibility'];
                                $visibility = (($screen_title_or_label_visibility == 'shown') ? ' checked="checked"' : '');

                                ?>
                                <tr id="tr_<?php echo $code, '_', $count?>">
                                    <td valign="top" nowrap="nowrap"><?php echo $option?></td>
                                    <td style="padding: 5px"><ul style="padding: 0; list-style: none; margin: 0" id="ul_<?php echo $code, '_', $count?>">
                                        <?php
                                        if(! empty($detail['items'])) {

                                            foreach($detail['items'] as $item_no => $item) {

                                                $item_no++;

                                                $item_label = (($item['header'] == '') ? strtoupper($item['type']) : $item['header']);
                                                xy_screen_and_item_string_decode($item_label);
                                                
                                                ?><li style="padding: 0; margin: 0" id="screenitem_<?php echo $code, '_', $count, '_', $item_no?>"><a title="delete" class="item_<?php echo $code, '_', $count?>_del_trigger" href="javascript:SCREEN.item_del(<?php echo $item_no, ',\'', $code, '\',', $count?>)"><img src="<?php echo xy_url('media/images/16x16/delete.png')?>" /></a> <a title="edit" class="item_<?php echo $code, '_', $count?>_edit_trigger" href="javascript:SCREEN.item_ae_picked('<?php echo $item['type']?>',<?php echo $item_no?>,'<?php echo $code?>',<?php echo $count?>)" style="font: 12px Verdana"><span><?php echo $item_label?></span></a></li><?php
                                            }
                                        }
                                        ?>
                                        </ul>
                                    </td>
                                    <td><div style="padding: 5px; color: #777"><?php echo wordwrap($detail['details'], 20, '<br />', true)?></div></td>
                                    <td valign="top">
                                        
                                        <div class="fntWrap" style="width: 153px; position: absolute; min-height: 16px; margin: 5px; padding: 2px; font: 12px Verdana" onkeypress="SCREEN.screenlabel_keypressed(event,'<?php echo $code?>',<?php echo $count?>)" onblur="SCREEN.screenlabel_toggle('<?php echo $code?>',<?php echo $count?>,false); SCREEN.update('<?php echo $code?>',<?php echo $count?>)" onclick="SCREEN.screenlabel_toggle('<?php echo $code?>',<?php echo $count?>,true)" id="screenlabel_<?php echo $code?>_<?php echo $count?>" detail="<?php echo $detail['id']?>"><?php echo $label?></div>

                                    </td>
                                    <td align="center"><input id="screenlabel_<?php echo $code, '_', $count?>_visibility" type="checkbox"<?php echo $visibility?> onclick="SCREEN.update('<?php echo $code?>',<?php echo $count?>)" /></td>
                                </tr>
                                <?php

                            }
                        }
                        ?>
                    </tbody>
                    </table>
                    <?php
                    
                }
                ?>
            </div>
            
        </td>
    </tr>
    
    
</table>
<div style="margin-top: 20px; float: right">
    <input type="button" value="Back" onclick="window.location.href='<?php echo xy_url('sensory/create_test/' . $rta->id . '/?step=2')?>'" />
    <input id="step_4_trigger" type="button" value="Next > Step 4" />
    <a href="<?php echo xy_url('sensory/create_test/' . $rta->id . '/?step=4')?>">Skip</a>
</div>