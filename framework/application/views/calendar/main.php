<?php defined('BASEPATH') or exit('No direct script access allowed')?>

<div id="section_wrapper">
    
    <?php include_once APPPATH . 'views/menu.inc.php'?>
    
    <div style=" clear: both">
    <?php
    if($target == 'month') {
        echo $calendar;
    }
    else
    if($target == 'week') {

        $week = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');    
        ?>
        <div style="margin: 5px 0 5px 0">
            <table style="background: #FFF">
                <tr><td>Choose a Week to view:</td>
                    <td><select id="week_number"><?php foreach($grouped_by_week_number as $week_number => $values) { ?><option value="<?php echo $week_number?>"># <?php echo $week_number?></option><?php } ?></select></td>
                    <td><span id="week_number_days"></span></td>
                </tr>            
            </table>
        </div>
        <table style="background: #FFF" width="100%" id="table_data" cellpadding="0" cellspacing="0">
            <tr><td width="150">&nbsp;</td>
                <td><div class="bold" style="color: #990000">Monday</div></td>
                <td><div class="bold" style="color: #990000">Tuesday</div></td>
                <td><div class="bold" style="color: #990000">Wednesday</div></td>
                <td><div class="bold" style="color: #990000">Thursday</div></td>
                <td><div class="bold" style="color: #990000">Friday</div></td>
                <td><div class="bold" style="color: #990000">Saturday</div></td>
                <td><div class="bold" style="color: #990000">Sunday</div></td>
            </tr>
            <tr><td rowspan="2"><div class="bold pad5">Analytical</td></tr>
            <tr><?php foreach($week as $group) { ?><td class="pad5 fntBlack" valign="top" id="analytical_<?php echo $group?>">&nbsp;</td><?php } ?></tr>
            <tr><td rowspan="2"><div class="bold pad5">Affective</div></td></tr>
            <tr><?php foreach($week as $group) { ?><td class="pad5 fntBlack" valign="top" id="affective_<?php echo $group?>">&nbsp;</td><?php } ?></tr>
            <tr><td rowspan="2"><div class="bold pad5">MICRO</div></td></tr>
            <tr><?php foreach($week as $group) { ?><td class="pad5 fntBlack" valign="top" id="micro_<?php echo $group?>">&nbsp;</td><?php } ?></tr>
            <tr><td rowspan="2"><div class="bold pad5">Physico Chem</div></td></tr>
            <tr><?php foreach($week as $group) { ?><td class="pad5 fntBlack" valign="top" id="physico_chem_<?php echo $group?>">&nbsp;</td><?php } ?></tr>
        </table>
        <?php
    }
    else
    if($target == 'day') {

        $analytical_count = 0;
        $affective_count = 0;
        $micro_count = 0;

        for($x=0, $y=count($data); $x<$y; $x++) {

            $row = $data[$x];

            $tmp = explode(' ', $row->requestor);
            $requestor = strtoupper($tmp[0][0] . $tmp[1][0] . $tmp[2][0]);

            $html = '
            <tr><td class="pad5">' . $row->time . '</td>
                <td class="pad5"><a title="' . $row->samples_name . '" href="' . xy_doc_root() . 'admin/rta_view/' . $row->id . '"><b>' . $row->samples_name . '</b></a></td>
                <td class="pad5">' . $row->samples_desc . '</td>
                <td class="pad5" align="center">' . $requestor . '</td>
                <td class="pad5">' . $row->location . '</td>
            </tr>';

            if($row->type_of_test == 'analytical') {

                $analytical_count++;
                $analytical_html .= $html;
            }
            else
            if($row->type_of_test == 'affective') {

                $affective_count++;
                $affective_html .= $html;
            }
            else
            if($row->type_of_test == 'micro') {

                $micro_count++;
                $micro_html .= $html;
            }
        }
        ?>
        <table style="background: #FFF" width="100%" id="table_data" cellpadding="0" cellspacing="0">
            <tr><td></td>
                <td><div class="bold" style="color: #990000">Time/Batch</div></td>
                <td><div class="bold" style="color: #990000">Name</div></td>
                <td><div class="bold" style="color: #990000">Description</div></td>
                <td><div class="bold" style="color: #990000">Requested by</div></td>
                <td><div class="bold" style="color: #990000">Location</div></td>
            </tr>
            <tr><td<?php echo (($analytical_count > 0) ? (' rowspan="' . ($analytical_count + 1) . '"') : ' rowspan="2"')?>><div class="bold pad5">Analytical<?php echo (($analytical_count > 0) ? (' <span class="normal">(</span>' . $analytical_count . '<span class="normal">)</span>') : '')?></div></td></tr>
            <?php
            if($analytical_html != '') echo $analytical_html;
            else echo '<tr><td colspan="5">&nbsp;</td></tr>';
            ?>
            <tr><td<?php echo (($affective_count > 0) ? (' rowspan="' . ($affective_count + 1) . '"') : ' rowspan="2"')?>><div class="bold pad5">Affective<?php echo (($affective_count > 0) ? (' <span class="normal">(</span>' . $affective_count . '<span class="normal">)</span>') : '')?></div></td></tr>
            <?php
            if($affective_html != '') echo $affective_html;
            else echo '<tr><td colspan="5">&nbsp;</td></tr>';
            ?>
            <tr><td<?php echo (($micro_count > 0) ? (' rowspan="' . ($micro_count + 1) . '"') : ' rowspan="2"')?>><div class="bold pad5">MICRO<?php echo (($micro_count > 0) ? (' <span class="normal">(</span>' . $micro_count . '<span class="normal">)</span>') : '')?></div></td></tr>
            <?php
            if($micro_html != '') echo $micro_html;
            else echo '<tr><td colspan="5">&nbsp;</td></tr>';
            ?>
        </table>
        <?php
    }
    ?>
    </div>
</div>