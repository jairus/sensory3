<?php defined('BASEPATH') or exit('No direct script access allowed')?>

<style type="text/css">
.ranking_rank_wrapper {
    
    clear: both;
    list-style: none;
    width: 426px;    
    padding: 0;
    margin: 0;

}
.ranking_rank_wrapper li {
    
    margin: 5px;
    padding: 5px;
    width: 120px;
    float: left;
    font-weight: bold;
    text-align: center

}

#ranking_code_wrapper { 
    
    clear: both;
    list-style-type: none;
    margin: 0;
    padding: 0;
    float: left;
    /*margin-right: 5px;    */
    width: 426px;    
    border: 1px solid #CCC;
    min-height: 53px;

}

#ranking_code_wrapper li {
    
    cursor: pointer;
    margin: 5px;
    padding: 5px;
    font-size: 1.2em;
    width: 120px;
    float: left;
    text-align: center

}

#rank_1, #rank_2, #rank_3 {
    
    cursor: pointer;
    margin: 0;
    padding: 0;
    font-size: 1.2em;
    width: 130px;
    float: left;
    text-align: center;
    margin-right: 10px;
    list-style: none;
    height: 53px;
    /*border: 1px solid #000*/
}

#rank_1 li, #rank_2 li, #rank_3 li {
    
    cursor: pointer;
    margin: 5px 0 0 0;
    padding: 5px;
    font-size: 1.2em;
    width: 120px;
    
    text-align: center
}

#rank_3 {
    margin-right: 0
}
#rank_1 {
    margin-left: 7px
}

.ui-slider { clear: both }
</style>

<script type="text/javascript">
    jQuery(function() {
        jQuery( "#ranking_code_wrapper, #rank_1, #rank_2, #rank_3" ).sortable({
            connectWith: ".connectedSortable",
            placeholder: "ui-state-highlight"
        }).disableSelection();
        //, #rank_1, #rank_2, #rank_3
       
        jQuery("#rank_1, #rank_2, #rank_3").sortable({
            update : function() {
                
                if(jQuery('li', this).length > 1){
                    
                    var ctr = 1;
                    var empty_cell_ctr = 0;
                    var empty_cells = [];
                    jQuery('li', this).each(function(){
                        
                        if(ctr == 2) { /* If dragging to an unempty cell. */
                            
                            /* Check empty cells. */
                            jQuery("div[id^='rank_']").each(function(){
                                
                                if(jQuery(this).html().trim() == '') {
                                    empty_cell_ctr++;
                                    empty_cells[empty_cell_ctr] = jQuery(this);
                                }                                
                            });
                            
                            if(empty_cell_ctr == 1) { /* If only one cell is empty, then put the replaced code here. */
                                
                                empty_cells[empty_cell_ctr].append(jQuery(this));
                                
                            } else jQuery('#ranking_code_wrapper').append(jQuery(this)); /* Else, bring it back to the code wrapper. */
                        }
                        
                        ctr++;
                    });
                }
            }
        });
    });
</script>

<div>
    <div style="font: 24px Arial; font-weight: bold"><?php echo $rta->samples_name?></div>
    <?php
    if($q_data->instruction != '') {
        ?><div>Instruction: <?php echo $q_data->instruction?></div><?php
    }
    ?>
    <div><b style="font-size: 20px"><?php echo $screen->label?></b></div>
    
    <div style="border: 2px solid #000; padding: 5px; min-height: 500px">
    
    <?php
    
    if(! empty($screen_items)) {
        
        foreach($screen_items as $item) {
            
            if( $item->type == 'liking' ||
                $item->type == 'compatibility' ||
                $item->type == 'jar' ) {
                
                if($item->type == 'liking' || $item->type == 'compatibility') {
                    
                    $labels['appearance'] = 'Appearance';
                    $labels['color_outer'] = 'Color (Outer)';
                    $labels['color_inner'] = 'Color (Inner)';
                    $labels['crispiness'] = 'Crispiness';
                    $labels['juiciness'] = 'Juiciness';
                    $labels['meat_texture'] = 'Meat Texture';
                    $labels['overall_flavor_blend'] = 'Overall Flavor Blend';
                    $labels['overall_saltiness'] = 'Overall Saltiness';
                    $labels['overall'] = 'Overall';
                }
                else
                if($item->type == 'jar') {
                    
                    $labels['color_outer'] = 'Color (Outer)';
                    $labels['color_inner'] = 'Color (Inner)';
                    $labels['crispiness'] = 'Crispiness';
                    $labels['juiciness'] = 'Juiciness';
                    $labels['meat_texture'] = 'Meat Texture';
                    $labels['overall_flavor_blend'] = 'Overall Flavor Blend';
                    $labels['overall_saltiness'] = 'Overall Saltiness';
                    $labels['presence_of_off-flavor'] = 'Presence of Off-Flavor';                    
                }
                
                //$instruction    = $item->type . '_instruction';
                $scale          = $item->type . '_scale';
                $type           = $item->type . '_type';
                $orientation    = $item->type . '_orientation';
                $order          = $item->type . '_order';
                $data           = $item->type . '_data';
                
                if(! empty($item->data)) {
                    
                    foreach($item->data as $data_value) {

                        /* START: Retrieve and initialize data. */
                        parse_str($data_value->$data, $sub_data);
                        $sub_data_final = array();

                        foreach($sub_data as $sub_data_key => $sub_data_value) {

                            $sub_data_final[str_replace('scale_', '', $sub_data_key)] = $sub_data_value;
                        }
                        /* END: Retrieve and initialize data. */

                        if($order == 'asc') $sub_data_final = array_reverse($sub_data_final, true);

                        ?>
                        <div><b><?php echo $labels[$data_value->$type]?></b></div>
                        <div style="padding-bottom: 10px">                    
                            <?php
                            if($data_value->$orientation == 'h') {
                                $orientation_style = ' style="float: left" ';
                            } else $orientation_style = '';

                            foreach($sub_data_final as $sub_data_final_key => $sub_data_final_value) {
                                
                                //echo '<div' . $orientation_style . '><input type="radio" />', $sub_data_final_key, '&nbsp;&nbsp;', $sub_data_final_value, '</div>';
                                echo '<div' . $orientation_style . '><input type="radio" />&nbsp;', $sub_data_final_value, '</div>';
                            }
                            ?>
                            <div style="clear: both"></div>                    
                        </div>
                        <?php
                        
                    } /* foreach($item->data as $data_value) */
                } /* if(! empty($item->data)) */
            }
            else
            if($item->type == 'comment') {
                
                ?>
                <div><?php echo $item->data[0]->comment_label?></div>
                <div><textarea><?php echo $item->data[0]->comment_data?></textarea></div>
                <?php
            }
            else
            if($item->type == 'instruction') {
                
                ?>
                <div><pre class="fntWrap" style="font: 12px Arial; width: 700px"><?php echo $item->data[0]->instruction_data?></pre></div>
                <?php
            }
            else
            if($item->type == 'pause_break') {
                
                $data = $item->data[0]->pause_break_data;
                $time = (int) $item->data[0]->pause_break_time;
                $option = $item->data[0]->pause_break_time_option;
                
                ?>
                <div style="background: url(<?php echo xy_url('sensory/loadPauseBreakPhoto/' . $item->id . '/' . time())?>) top left repeat; min-height: 700px">
                <div><pre class="fntWrap" style="font: 12px Arial; width: 700px"><?php echo $data?></pre></div>
                <?php
                if($option == 'shown' && $time > 0) {
                    
                    ?>
                    <div style="background: #FF0000; color: #FFF; width: 300px" id="timer"><span style="font: 100px Arial"><?php echo $time?></span> seconds left</div>
                    <script type="text/javascript">
                        var time = <?php echo $time?>;
                        timer();
                        function timer() {
                            
                            if(time > 0) {
                                time--;
                                jQuery('#timer span').html(time);
                            }
                            
                            setTimeout("timer()", 1000);
                        }
                    </script>
                    <?php
                }
                ?></div><?php
            }
            else
            if($item->type == 'ranking_for_preference') {
                
                $code_arr = explode(',', $item->data[0]->ranking_for_preference_data);
                ?>
                <div style="margin: 10px 0 10px 0">
                    
                    <div style="margin-bottom: 20px"><pre class="fntWrap" style="width: 800px"><?php echo $item->data[0]->ranking_for_preference_instruction?></pre></div>
                    <div style="margin-bottom: 10px"><b>Codes:</b></div>
                    <ul id="ranking_code_wrapper" class="connectedSortable" style=" border: 1px dashed #CCC;">
                        <?php
                        foreach($code_arr as $code) {
                            
                            ?>
                            <li class="ui-state-default"><b style="font-size: 24px"><?php echo $code?></b></li>
                            <?php
                        }
                        ?>
                    </ul>
                    <div style="clear: both">
                        
                        <div style="padding-top: 10px"><b>Ranking:</b></div>

                        <ul class="ranking_rank_wrapper">
                            <li>1st<br /><span style="font: 12px Arial">Most acceptable</span></li>
                            <li>2nd</li>
                            <li>3rd<br /><span style="font: 12px Arial">Least acceptable</span></li>
                        </ul>
                        <div style="clear: both">
                            
                            <div style="border: 1px solid #CCC; min-height: 53px; width: 426px">
                                <div id="rank_1" class="connectedSortable"></div>
                                <div id="rank_2" class="connectedSortable"></div>
                                <div id="rank_3" class="connectedSortable"></div>
                            </div>
                            
                        </div>
                    </div>                    
                </div>
                <?php
                
            }
            else
            if($item->type == 'ranking_for_intensity') {
                
                $code_arr = explode(',', $item->data[0]->ranking_for_intensity_data);
                ?>
                <div style="margin: 10px 0 10px 0">
                    
                    <div style="margin-bottom: 20px"><pre class="fntWrap" style="width: 800px"><?php echo $item->data[0]->ranking_for_intensity_instruction?></pre></div>
                    <div style="margin-bottom: 10px"><b>Codes:</b></div>
                    <ul id="ranking_code_wrapper" class="connectedSortable" style=" border: 1px dashed #CCC;">
                        <?php
                        foreach($code_arr as $code) {
                            
                            ?>
                            <li class="ui-state-default"><b style="font-size: 24px"><?php echo $code?></b></li>
                            <?php
                        }
                        ?>
                    </ul>
                    <div style="clear: both">
                        
                        <div style="padding-top: 10px"><b>Ranking:</b></div>

                        <ul class="ranking_rank_wrapper">
                            <li>1st<br /><span style="font: 12px Arial">Most acceptable</span></li>
                            <li>2nd</li>
                            <li>3rd<br /><span style="font: 12px Arial">Least acceptable</span></li>
                        </ul>
                        <div style="clear: both">
                            
                            <div style="border: 1px solid #CCC; min-height: 53px; width: 426px">
                                <div id="rank_1" class="connectedSortable"></div>
                                <div id="rank_2" class="connectedSortable"></div>
                                <div id="rank_3" class="connectedSortable"></div>
                            </div>
                        </div>
                    </div>                    
                </div>
                <?php
                
            }
            else
            if($item->type == 'paired_preference') {
                
                $code_arr = xy_code_get($q_data->codes, 'primary');
                $instruction = $item->data[0]->paired_preference_instruction;
                
                ?>
                <div style="margin: 10px 0 10px 0">
                    <div style="margin-bottom: 10px"><pre class="fntWrap" style="width: 800px"><?php echo $instruction?></pre></div>
                    <table cellpadding="0" cellspacing="0">
                    <?php
                    foreach($code_arr as $code) {

                        ?>
                        <td width="100">
                            <input id="code_<?php echo $code?>" name="code" type="radio" />
                            <label title="<?php echo $code?>" for="code_<?php echo $code?>"><b style="font-size: 24px; cursor: pointer"><?php echo $code?></b></label>
                        </td>
                        <?php
                    }
                    ?>
                    </table>
                </div>
                <?php
            }
            else
            if($item->type == 'multiple_choice' || $item->type == 'cata') {
                
                $question = $item->type . '_question';
                $data = $item->type . '_data';
                
                $question = $item->data[0]->$question;
                $choices = explode(',', $item->data[0]->$data);
                
                ?>
                <div style="margin: 10px 0 10px 0">
                <div><?php echo $question?></div>
                <div style="padding-left: 10px">
                    <?php
                    foreach($choices as $choice) { ?><div><input type="<?php echo (($item->type == 'cata') ? 'checkbox' : 'radio')?>" name="<?php echo $item->type, '_', $item->data[0]->id?>" /><?php echo $choice?></div><?php }
                    ?>
                </div>
                </div>
                <?php
            }
            else
            if($item->type == 'descriptive') {
                
                $html = '';
                $slider_value = array();
                 
                $label = $item->data[0]->descriptive_label;
                $length = $item->data[0]->descriptive_length;
                if(substr_count($length, '-')) {
                    
                    list($min, $max) = explode('-', $length);
                } else {
                    
                    $min = 0;
                    $max = $length;
                }
                
                $interval_type = $item->data[0]->descriptive_interval_type;
                $interval_data = $item->data[0]->descriptive_interval_data;
                
                if($interval_type == 'digit') {
                    
                    $divisor = (double) $interval_data;
                    if($divisor > 0) {
                        
                        $interval_data = array();
                        $quotient =  $length / $divisor;
                        
                        for($x=1; $x<=$divisor; $x++) {
                            
                            $sum += $quotient;
                            $interval_data[] = array('value'=> $sum, 'label' => $sum);
                        }
                        
                    }                    
                }
                else
                if($interval_type == 'custom') {
                    
                    $rows = explode('[=AXL=]', $interval_data);
                    $interval_data = array();
                    foreach($rows as $row) {
                        
                        parse_str($row, $tmp);
                        $interval_data[] = $tmp;
                    }
                    
                    $html_max = '<option value="' . $max . '">' . $max . '</option>';
                }
                
                $label_display_number = count($interval_data) + 2;
                ?>
                
                <div><?php echo $label?></div>
                <div style="clear: both; margin: 10px 0 50px 30px; width: 1000px">
                    <select id="descriptive_slider_<?php echo $item->id?>">
                        <option value="<?php echo $min?>"><?php echo $min?></option>
                        <?php
                        foreach($interval_data as $key => $data) {
                            
                            if($data['option'] == 'shown') $value = str_replace(' ', '&nbsp;', $data['label']);
                            else $value = $data['value'];
                            
                            if($data['value'] != $data['label']) $label = $data['value'] . ' - ' . $data['label'];
                            else $label = $data['value'];
                            
                            ?><option value="<?php echo $value?>"><?php echo $label?></option><?php
                        }
                        echo $html_max;
                        ?>
                        
                    </select>
		</div>
                
                <script type="text/javascript">
                    jQuery(function() { jQuery('select#descriptive_slider_<?php echo $item->id?>').selectToUISlider({ labels : <?php echo $label_display_number?> }).hide(); });
                </script>
                <?php
            }
            else
            if($item->type == 'duo_trio') {
                
                $code_arr = xy_code_get($q_data->codes, 'primary');
                
                $instruction = $item->data[0]->duo_trio_instruction;
                $ref_code = $item->data[0]->duo_trio_ref_code;
                $ref_position = $item->data[0]->duo_trio_ref_position;
                
                $positions['left'] = 0;
                $positions['center'] = 0;
                $positions['right'] = 0;
                
                unset($positions[$ref_position]);
                
                /* Removes reference number. */
                foreach($code_arr as $index => $code) { if($code == $ref_code) unset($code_arr[$index]); }
                
                $tmp = $code_arr;
                $code_arr = array();
                foreach($tmp as $code) { $code_arr[] =  $code; }
                
                $x = 0;
                foreach($positions as $position => $value) {
                    
                    $positions[$position] = $code_arr[$x];
                    $x++;
                }
                
                $positions[$ref_position] = '<b>' . $ref_code . '</b>';
                
                foreach($positions as $position => $value) {
                    
                    if($position == 'left') $position = 1;
                    elseif($position == 'center') $position = 2;
                    elseif($position == 'right') $position = 3;
                    
                    $positions[$position] = $value;
                }
                ?>
                <div style="margin: 10px 0 10px 0">
                    <div style="margin-bottom: 10px"><pre class="fntWrap" style="width: 800px"><?php echo $instruction?></pre></div>
                    <div style="padding-left: 10px">
                        <table cellpadding="0" cellspacing="0">
                            <tr>
                            <?php
                            for($x=1; $x<=3; $x++) {

                                ?>
                                <td style="width: 100px; font-size: 24px">
                                    <input type="radio" id="duo_trio_<?php echo $item->id, '_', $x?>" name="duo_trio_<?php echo $item->id?>" /> <label for="duo_trio_<?php echo $item->id, '_', $x?>"><?php echo $positions[$x]?></label>
                                </td>
                                <?php
                            }
                            ?>
                            </tr>
                        </table>
                    </div>
                </div>
                <?php
            }            
            else
            if($item->type == 'triangle') {
                
                $code_arr = xy_code_get($q_data->codes, 'primary');
                $instruction = $item->data[0]->triangle_instruction;
                
                ?>
                <div style="margin: 10px 0 10px 0">
                    <div style="margin-bottom: 10px"><pre class="fntWrap" style="width: 800px"><?php echo $instruction?></pre></div>
                    <table cellpadding="0" cellspacing="0">
                        <tr>
                        <?php
                        foreach($code_arr as $code) {

                            ?>
                            <td width="100">
                                <input id="code_<?php echo $code?>" name="code" type="radio" />
                                <label title="<?php echo $code?>" for="code_<?php echo $code?>"><b style="font-size: 24px; cursor: pointer"><?php echo $code?></b></label>
                            </td>
                            <?php
                        }
                        ?>
                        </tr>
                    </table>
                </div>
                <?php
            }
            else
            if($item->type == 'paired_comparison') {
                
                $instruction = $item->type . '_instruction';
                $instruction = $item->data[0]->$instruction;

                $type = $item->type . '_type';
                $type = $item->data[0]->$type;
                
                $tail = $item->tail . '_instruction';
                $tail = $item->data[0]->$tail;
                
                ?>
                <div style="margin: 10px 0 10px 0">
                    <div style="margin-bottom: 10px"><pre class="fntWrap" style="width: 800px"><?php echo $instruction?></pre></div>
                    <table cellpadding="0" cellspacing="0">
                        <tr>
                        <?php
                        
                        $element_name = $item->type . '_' . $item->id;
                        if($type == 'sd') {

                            ?>
                            <td width="200"><input type="radio" name="<?php echo $element_name?>" id="<?php echo $element_name?>_1" /> <label for="<?php echo $element_name?>_1"><b style="font-size: 24px">Same</b></label></td>
                            <td><input type="radio" name="<?php echo $element_name?>" id="<?php echo $element_name?>_2" /> <label for="<?php echo $element_name?>_2"><b style="font-size: 24px">Different</b></label></td>
                            <?php
                        }
                        else
                        if($type == 'directional') {
                            
                            $code_arr = xy_code_get($q_data->codes, 'primary');
                            
                            foreach($code_arr as $code) {

                                ?>
                                <td width="100">
                                    <input id="<?php echo $element_name?>_code_<?php echo $code?>" name="code" type="radio" />
                                    <label title="<?php echo $code?>" for="<?php echo $element_name?>_code_<?php echo $code?>"><b style="font-size: 24px; cursor: pointer"><?php echo $code?></b></label>
                                </td>
                                <?php
                            }                            
                        }
                    ?>
                    </table>
                </div>
                <?php
            }
            else
            if($item->type == '2afc') {
                
                $code_arr = xy_code_get($q_data->codes, 'primary');
                $instruction = $item->data[0]->_2afc_instruction;
                
                ?>
                <div style="margin: 10px 0 10px 0">
                    <div style="margin-bottom: 10px"><pre class="fntWrap" style="width: 800px"><?php echo $instruction?></pre></div>
                    <table cellpadding="0" cellspacing="0">
                        <tr>
                        <?php
                        foreach($code_arr as $code) {

                            ?>
                            <td width="100">
                                <input id="code_<?php echo $code?>" name="code" type="radio" />
                                <label title="<?php echo $code?>" for="code_<?php echo $code?>"><b style="font-size: 24px; cursor: pointer"><?php echo $code?></b></label>
                            </td>
                            <?php
                        }
                        ?>
                        </tr>
                    </table>
                </div>
                <?php
            }
            else
            if($item->type == '3afc') {
                
                $code_arr = xy_code_get($q_data->codes, 'primary');
                $instruction = $item->data[0]->_3afc_instruction;
                
                ?>
                <div style="margin: 10px 0 10px 0">
                    <div style="margin-bottom: 10px"><pre class="fntWrap" style="width: 800px"><?php echo $instruction?></pre></div>
                    <table cellpadding="0" cellspacing="0">
                        <tr>
                        <?php
                        foreach($code_arr as $code) {

                            ?>
                            <td width="100">
                                <input id="code_<?php echo $code?>" name="code" type="radio" />
                                <label title="<?php echo $code?>" for="code_<?php echo $code?>"><b style="font-size: 24px; cursor: pointer"><?php echo $code?></b></label>
                            </td>
                            <?php
                        }
                        ?>
                        </tr>
                    </table>
                </div>
                <?php
            }
            else
            if($item->type == 'sqs_main') {
                
                $partition = $item->type . '_partition';
                $partition = $item->data[0]->$partition;
                
                $range = $item->type . '_range';
                $range = $item->data[0]->$range;
                parse_str($range, $range);
                
                $cell = array();
                foreach($range as $key => $value) {
                    
                    list($tmp, $cell_number) = explode('_', $key);
                    $cell[$cell_number] = $value;
                }
                
                $heading = $item->type . '_heading';
                $heading = $item->data[0]->$heading;
                if($heading) $heading = explode(',', $heading);
                else $heading = array($heading);
                
                /*$h2 = $item->type . '_h2';
                $h2 = $item->data[0]->$h2;
                if($h2) $h2 = explode(',', $h2);
                else $h2 = array($h2);                */
                ?>
                <div style="margin: 10px 0 10px 0">
                    <table cellpadding="0" cellspacing="0">
                        <tr><?php foreach($heading as $label) { ?><td width="300" align="center"><b style="font-size: 16px"><?php echo $label?></b></td><?php } ?></tr>
                        <tr>
                            <?php                            
                            for($x=1; $x<=$partition; $x++) {
                                
                                $cell_number = explode(',', $cell[$x]);
                                
                                if($x < $partition) $border = ' style="border-right: 1px dashed #CCC"';
                                else $border = '';
                                
                                ?>
                                <td height="100" align="center"<?php echo $border?>>
                                    <table cellpadding="0" cellspacing="0">
                                        <tr><?php foreach($cell_number as $value) { ?><td width="300" align="center"><div id="sqs_main_selection_<?php echo $value?>" title="<?php echo $value?>"><div style="margin-top: 15px; cursor: default"><?php echo $value?></div></div></td><?php } ?></tr>
                                    </table>
                                </td>
                                <?php
                            }
                            ?>
                        </tr>
                        
                    </table>
                </div>
                <?php
            }
            else
            if($item->type == 'sqs_attribute') {
                
                $partition = $item->type . '_partition';
                $partition = $item->data[0]->$partition;
                
                ?><table cellpadding="0" cellspacing="0" width="100%"><?php
                
                if(! $printed) { /* So to print only once. */

                    $printed = true;
                    
                    $heading = $item->type . '_heading';
                    $heading = $item->data[0]->$heading;
                    if($heading) $heading = explode(',', $heading);
                    else $heading = array($heading);

                    $middle = ceil($partition / 2);
                    
                    ?><tr><?php
                    for($x=0; $x<$partition; $x++) {

                        $h = $heading[$x];
                        ?><td width="100" align="center"><b><?php echo $h?></b></td><?php
                        if(($x + 1) == $middle) {

                            ?><td width="100">&nbsp;</td><?php
                        }
                    }
                    ?></tr><?php
                }
                
                for($y=0, $t=count($item->data); $y<$t; $y++) {
                    
                    $sqs = $item->data[$y];
                    
                    ?><tr><?php
                    
                    for($x=1; $x<=$partition; $x++) {
                    
                        ?><td width="100" height="35">&nbsp;</td><?php
                        if($x == $middle) {

                            ?><td width="100" height="35" align="center"><b><?php echo $sqs->sqs_attribute_label?></b></td><?php
                        }
                    }
                    
                    ?></tr><?php
                    
                    $data = $sqs->sqs_attribute_data;
                    if(substr_count($data, ',')) $data = explode(',', $data);
                    else $data = array($data);

                    for($y2=0, $t2=count($data); $y2<$t2; $y2++) {
                        
                        ?><tr><?php
                        for($x=1; $x<=$partition; $x++) {

                            ?><td width="100" align="center" height="20"><input name="<?php echo strtolower($sqs->sqs_attribute_label), '_', ($y2 + 1)?>" type="radio" /></td><?php
                            if($x == $middle) {

                                ?><td width="100" align="center" height="20"><?php echo $data[$y2]?></td><?php
                            }
                        }
                        ?></tr><?php
                    }
                }

                ?></table><?php
            }
            else echo $item->type . '<br />';
        }
    }
    ?>
        <div style="clear: both">
            <div style="text-align: right">
            <?php

            if($back_url != '') {

                ?><input type="button" onclick="window.location.href='<?php echo $back_url?>'" value="Back" /><?php
            }

            if($next_url != '') {

                ?><input type="button" onclick="window.location.href='<?php echo $next_url?>'" value="Next" /><?php

            } else {

                ?><input type="button" onclick="window.close()" value="Finish" /><?php

            }
            ?>
            </div>
        </div>
    </div>
</div>