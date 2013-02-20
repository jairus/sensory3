<?php defined('BASEPATH') or exit('No direct script access allowed')?>
<?php
if(isset($screen)) extract($screen);
xy_screen_and_item_string_decode($screen_title_or_label_value);

$data['code_1'] = $code_1;
$data['code_2'] = $code_2;
$data['controls'] = $controls;
$data['experiments'] = $experiments;

function xy_qitem_assign($item, $screen_total, $screen, $data, $rta_id, $screen_code, $screen_count) {
    
    static $ctr = 0;
    $ctr++;
    
    extract($data);
    
    $ci =& get_instance(); 
    
    /* START: Get batch. */
    $sql = "SELECT batch_content FROM q WHERE rta_id=" . $rta_id;
    $query = $ci->db->query($sql);
    if(! $query->num_rows()) exit('Error: No batch assigned for this Questionnaire.'); /* This should not even occuring. All must be properly set by the Administrator. */

    $batch_content = $query->row()->batch_content;
    if(substr_count($batch_content, ',')) $batch_content = explode(',', $batch_content);
    else $batch_content = array($batch_content);

    $batch_current = $ci->configXY->URI['batch'];
    if($batch_current) $batch_current = str_replace ('-', ' ', $batch_current);

    $batch_current_pos = array_search($batch_current, $batch_content) + 1;
    /* END: Get batch. */
    
    $type_arr = array('triangle', '2afc', '3afc', 'duo_trio', 'ranking_for_intensity', 'paired_preference');
    
    //echo $item['type'];
    
    if(in_array($item['type'], $type_arr)) {
        
        $sql = "SELECT s1d,s2d FROM q_code_distributions WHERE rta_id=" . $rta_id . " AND batch=" . $batch_current_pos;
        $query = $ci->db->query($sql);
        if(! $query->num_rows()) exit('Error: No code distributions assigned for this Questionnaire.'); /* This should not even occuring. All must be properly set by the Administrator. */
        
        $station_number = $ci->exam_model->getIfIPHooked();
        
        if($station_number >= 1 && $station_number <= 16) $distribution = explode(',', $query->row()->s1d);
        elseif($station_number >= 17 && $station_number <= 32) $distribution = explode(',', $query->row()->s2d);
        
        $code_choice = '';
        
        foreach($distribution as $d) {
            
            list($station, $code) = explode(':', $d);
            if($station == $station_number) {
                
                $code_choice = $code;
                break;
            }
        }
        
        if(! $code_choice) exit('Error: No code assigned for this station yet.');
        
        $code_choice = str_replace(array('li_' . $station_number, '_'), array('', ' '), $code_choice);
        $code_choice = explode(' ', trim($code_choice));        
    }
    
    $func = 'xy_qitem_' . $item['type'];
    if(function_exists($func)) { echo '<div style="margin: ' . (($ctr > 1) ? 20 : 0) . 'px 0 20px 0">', call_user_func($func, $item, $ctr), '</div>'; }
    else {
        
        $file = APPPATH . 'views/exam/actual/' . $item['type'] . '.php';
        if(file_exists($file)) include $file;                
    }
}

function xy_qitem_instructionx($item, $ctr) {
    
    array_walk($item, 'xy_screen_and_item_string_html');
    extract($item);
    
    echo $ctr;
    if($i) {
        
        //$i = str_replace('[nl]', '<br />', $i);
        $html = '<div>' . $i . '</div>';        
    }
    
    return $html;
}

function xy_qitem_multiple_choicex($item, $index) {
    
    array_walk($item, 'xy_screen_and_item_string_html');
    extract($item);
    
    //$choices = explode('[=ROW=]', str_replace('[quote]', "'", $choices));
    $choices = explode('[=ROW=]', $choices);
    
    $ctr = 0;
    foreach($choices as $choice) {
        
        $ctr++;
        $html_choice .= '<div><input name="' . $type . '_' . $index . '_choice" id="' . $type . '_' . $index . '_choice_' . $ctr . '" type="radio" /> <label for="' . $type . '_' . $index . '_choice_' . $ctr . '">' . $choice . '</label></div>';
    }
    
    if($question) $question = '<div><b>' . $question . '</b></div>';    
    $html .= $question . $html_choice;
    
    return $html;
}

function xy_qitem_catax($item, $index) {
    
    array_walk($item, 'xy_screen_and_item_string_html');
    extract($item);
    
    //$choices = explode('[=ROW=]', str_replace('[quote]', "'", $choices));
    $choices = explode('[=ROW=]', $choices);
    
    $ctr = 0;
    foreach($choices as $choice) {
        
        $ctr++;
        $html_choice .= '<div><input name="' . $type . '_' . $index . '_choice" id="' . $type . '_' . $index . '_choice_' . $ctr . '" type="checkbox" /> <label for="' . $type . '_' . $index . '_choice_' . $ctr . '">' . $choice . '</label></div>';
    }
    
    if($question) $question = '<div><b>' . $question . '</b></div>';    
    $html .= $question . $html_choice;
    
    return $html;
}

if($viewable['cmd']) {
    
    ?><div class="screen_title"><?php echo (($screen_title_or_label_visibility == 'shown') ? $screen_title_or_label_value : '&nbsp;')?></div><?php
}
?>
<div class="popup" id="popupjs_wrapper" style="display: none; text-align: left; font-size: 14px">
    <div class="popup_title"></div> 
    <div class="popup_content"> 
        <p style="margin-top: 5px"></p>

        <div class="popup_buttons" style="font-size: 14px">
            <button id="popupjs_btn_ok" class="default">Okay</button><button id="popupjs_btn_cancel" style="margin-left: 2px" class="close_popup">Cancel</button>
        </div>
    </div>
</div>
<div class="wrapper_inner">
    <?php
    if($viewable['cmd']) {
        
        ?><div style="min-height: 400px; margin-top: 10px; width: inherit"><?php
        
        if(($total = count($items)) > 0) {

            for($x=0; $x<$total; $x++) {

                $item = $items[$x];
                xy_qitem_assign($item, $screen_total, $screen, $data, $rta_id, $screen_code, $screen_count);
            }
        }
        ?>
        </div>
        <div style="margin-bottom: 10px">
            <div style="float: left"><?php echo $button_prev?></div>
            <div style="float: right"><?php echo $button_next?></div>
            <div style="clear: both"></div>
        </div>
        <?php
        
    } else {
        
        if($viewable['type'] == 'backing') echo '<b>Sorry, but you\'re not allowed to go back.</b>';
        elseif($viewable['type'] == 'advancing') echo '<b style="color: #CC0000">Sorry, but you\'re not allowed to be in this page yet.</b>';
        
    }
    ?>
</div>
