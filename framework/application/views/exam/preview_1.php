<?php defined('BASEPATH') or exit('No direct script access allowed')?>

<div>
    <div style="font: 32px Arial; font-weight: bold"><?php echo $rta->samples_name?></div>
    <table>
        <tr><td width="90" align="right">Type:</td>
            <td style="padding-left: 5px"><b><?php echo ucfirst($rta->type_of_test)?></b></td>
        </tr>
        <tr><td width="90" align="right">Specifics #1:</td>
            <td style="padding-left: 5px"><b><?php echo $spec1?></b></td>
        </tr>
        <tr><td width="90" align="right">Specifics #2:</td>
            <td style="padding-left: 5px"><b><?php echo $spec2?></b></td>
        </tr>
        <tr><td width="90" align="right"># of Samples:</td>
            <td style="padding-left: 5px"><b><?php echo $rta->no_of_samples?></b></td>
        </tr>
    </table>
    <div style="border: 2px solid #000; padding: 5px; min-height: 500px">
    <div style="text-align: right; color: #999">Screen #<?php echo $screen_no?></div>
    <ul style="min-height: 500px">
    <?php
    if(! empty($test_items)) {

        $ctr = 0;
        foreach($test_items as $row) {

            $content = unserialize(base64_decode($row->content));
            
            $ctr++;
            if($row->item_name == 'liking' || $row->item_name == 'compatibility') {

                
                //parse_str($content['scale_data'], $scale_data);
                //$scale_data = array_reverse($scale_data);
                
                $tmp = $row->item_name . '_orientation';
                $orientation = $row->$tmp;
                
                $tmp = $row->item_name . '_order';
                $order = $row->$tmp;
                
                $tmp = $row->item_name . '_data';
                $data = $row->$tmp;
                parse_str($data, $data);
                
                $tmp = $row->item_name . '_type';
                $type = $row->$tmp;
                
                if($order == 'desc') { $data = array_reverse($data); }
                
                ?>
                <li style="margin: 5px 0 5px 0; padding-top: 20px; clear: both">Item <b>#<?php echo $ctr?></b></li>
                <li style="list-style: none">
                    <ul>
                    <?php
                    foreach($data as $k => $v) {
                        
                        ?>
                        <li style="list-style: none<?php echo (($orientation == 'h') ? '; float: left' : '')?>">
                            <input name="item<?php echo $ctr?>" type="radio" id="item_<?php echo $ctr . $k?>" /> <label for="item_<?php echo $ctr . $k?>"><?php echo str_replace('_', '', $k), ' ', $v?></label>
                        </li>
                        <?php
                        
                    }
                    ?>
                    </ul>
                </li>
                <?php
            }
            else
            if($row->item_name == 'instruction') {

                echo '<li style="margin: 5px 0 5px 0; padding-top: 20px; list-style: none">', str_replace('\n', '<br />', $content['instruction']), '</li>';
            }
            else
            if($row->item_name == 'comment') {

                ?>
                <li style="margin: 5px 0 5px 0; padding-top: 20px; list-style: none">
                Your comment below: <br />
                <textarea></textarea></li>
                <?php
            }
            else
            if($row->item_name == 'jar') {

                if( $row->jar_type == 'color_inner' ||
                    $row->jar_type == 'color_outer') {
                    
                    $tmp = explode('_', $row->jar_type);
                    ?>
                    <li style="margin: 5px 0 5px 0; padding-top: 20px; clear: both">Item <b>#<?php echo $ctr?></b></li>
                    <li style="list-style: none">
                        <ul>
                            <li style="list-style: none"><b><?php echo ucfirst($tmp[0]), ' (' . ucfirst($tmp[1]) . ')'?></b></li>
                            <li style="list-style: none"><input type="radio" />Much too pale</li>
                            <li style="list-style: none"><input type="radio" />Somewhat too pale</li>
                            <li style="list-style: none"><input type="radio" />Just right</li>
                            <li style="list-style: none"><input type="radio" />Somewhat too dark</li>
                            <li style="list-style: none"><input type="radio" />Much too dark</li>
                        </ul>
                    </li>
                    <?php
                }
                else
                if($row->jar_type == 'crispiness') {
                    ?>
                    <li style="margin: 5px 0 5px 0; padding-top: 20px; clear: both">Item <b>#<?php echo $ctr?></b></li>
                    <li style="list-style: none">
                        <ul>
                            <li style="list-style: none"><b>Crispiness</b></li>
                            <li style="list-style: none"><input type="radio" />Much too soggy</li>
                            <li style="list-style: none"><input type="radio" />Somewhat too soggy</li>
                            <li style="list-style: none"><input type="radio" />Just right</li>
                            <li style="list-style: none"><input type="radio" />Somewhat too crispy</li>
                            <li style="list-style: none"><input type="radio" />Much too crispy</li>
                        </ul>
                    </li>                    
                    <?php
                }                
                else
                if($row->jar_type == 'juiciness') {
                    ?>
                    <li style="margin: 5px 0 5px 0; padding-top: 20px; clear: both">Item <b>#<?php echo $ctr?></b></li>
                    <li style="list-style: none">
                        <ul>
                            <li style="list-style: none"><b>Juiciness</b></li>
                            <li style="list-style: none"><input type="radio" />Much too dry</li>
                            <li style="list-style: none"><input type="radio" />Somewhat too dry</li>
                            <li style="list-style: none"><input type="radio" />Just right</li>
                            <li style="list-style: none"><input type="radio" />Somewhat too juicy</li>
                            <li style="list-style: none"><input type="radio" />Much too juicy</li>
                        </ul>
                    </li>                    
                    <?php
                }
                else
                if($row->jar_type == 'meat_texture') {
                    ?>
                    <li style="margin: 5px 0 5px 0; padding-top: 20px; clear: both">Item <b>#<?php echo $ctr?></b></li>
                    <li style="list-style: none">
                        <ul>
                            <li style="list-style: none"><b>Meat Texture</b></li>
                            <li style="list-style: none"><input type="radio" />Much too tender</li>
                            <li style="list-style: none"><input type="radio" />Somewhat too tender</li>
                            <li style="list-style: none"><input type="radio" />Just right</li>
                            <li style="list-style: none"><input type="radio" />Somewhat too tough</li>
                            <li style="list-style: none"><input type="radio" />Much too tough</li>
                        </ul>
                    </li>                    
                    <?php
                }
                if($row->jar_type == 'overall_flavor_blend') {
                    ?>
                    <li style="margin: 5px 0 5px 0; padding-top: 20px; clear: both">Item <b>#<?php echo $ctr?></b></li>
                    <li style="list-style: none">
                        <ul>
                            <li style="list-style: none"><b>Overall flavor blend</b></li>
                            <li style="list-style: none"><input type="radio" />Much too weak</li>
                            <li style="list-style: none"><input type="radio" />Somewhat too weak</li>
                            <li style="list-style: none"><input type="radio" />Just right</li>
                            <li style="list-style: none"><input type="radio" />Somewhat too strong</li>
                            <li style="list-style: none"><input type="radio" />Much too strong</li>
                        </ul>
                    </li>                    
                    <?php
                }
                if($row->jar_type == 'overall_saltiness') {
                    ?>
                    <li style="margin: 5px 0 5px 0; padding-top: 20px; clear: both">Item <b>#<?php echo $ctr?></b></li>
                    <li style="list-style: none">
                        <ul>
                            <li style="list-style: none"><b>Overall saltiness</b></li>
                            <li style="list-style: none"><input type="radio" />Much too lacking in saltiness</li>
                            <li style="list-style: none"><input type="radio" />Somewhat not salty</li>
                            <li style="list-style: none"><input type="radio" />Just right</li>
                            <li style="list-style: none"><input type="radio" />Somewhat too salty</li>
                            <li style="list-style: none"><input type="radio" />Much too salty</li>
                        </ul>
                    </li>
                    <?php
                }
                if($row->jar_type == 'presence_of_off-flavor') {
                    ?>
                    <li style="margin: 5px 0 5px 0; padding-top: 20px; clear: both">Item <b>#<?php echo $ctr?></b></li>
                    <li style="list-style: none">
                        <ul>
                            <li style="list-style: none"><b>Presence of Off-flavor</b></li>
                            <li style="list-style: none"><input type="radio" />None</li>
                            <li style="list-style: none"><input type="radio" />Just recognizable</li>
                            <li style="list-style: none"><input type="radio" />Slightly strong</li>
                            <li style="list-style: none"><input type="radio" />Moderately</li>
                            <li style="list-style: none"><input type="radio" />Very strong</li>
                        </ul>
                    </li>                    
                    <?php
                }
                
            }
            else
            if($row->item_name == 'pause/break') {

                ?>
                <li style="margin: 5px 0 5px 0; list-style: none">
                    <?php echo $row->pause_break_data?>
                </li>
                <?php
            }
            else echo '<li style="margin: 5px 0 5px 0; list-style: none">', $row->item_name, '</li>';
        }        
    }
    ?>
    </ul>
    <?php
    if($next_url != '') {
        ?><div style="text-align: right"><input type="button" onclick="window.location.href='<?php echo $next_url?>'" value="Next" /></div><?php
    } else {
        ?><div style="text-align: right"><input type="button" onclick="xyDIALOG.message('FINISH !!!','End of Preview.','','','info')" value="Finish" /></div><?php
    }
    ?>
    
    </div>
</div>