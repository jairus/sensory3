<?php defined('BASEPATH') or exit('No direct script access allowed')?>
<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

array_walk($item, 'xy_screen_and_item_string_html');
extract($item);

$code_arr  = explode(',', $codes);

$answer = $_SESSION['EXAM'][$screen_code][$screen_count]['items'][$ctr - 1]->axl;
if(! $answer) $answer = new stdClass;

$type_ctr = $type . '_' . $ctr;

$y = count($code_arr);
for($x=0; $x<$y; $x++) {
    
    $xx = $x + 1;
    
    if($xx == 1) $rank_label = $rank_most;
    elseif($xx == $y) $rank_label = $rank_least;
    else $rank_label = '';
    
    if($rank_label != '') $rank_label = '<br /><span style="font-weight: normal">' . $rank_label . '</span>';
    
    $ranks .= '<li>' . xy_rank_label($xx) . $rank_label . '</li>';
    $rank_receiver .= '<div id="' . $type_ctr  . '_rank_' . $xx . '" class="rank_' . $xx . ' connectedSortable"></div>';
}
?>
<script type="text/javascript">
jQuery(function() {
    
    EXAM.answers[<?php echo $ctr - 1?>] = { 'type' : '<?php echo $type?>', 'axl' : <?php echo json_encode($answer)?> };
    
    jQuery( "#<?php echo $type_ctr?>_ranking_code_wrapper" ).sortable({
        connectWith: ".connectedSortable",
        placeholder: "ui-state-highlight"
    }).disableSelection();
    
    jQuery("div[class^=rank_]").sortable({
        connectWith: ".connectedSortable",
        placeholder: "ui-state-highlight",
        update : function() {

            if(jQuery('li', this).length > 1){

                var ctr = 1;
                var empty_cell_ctr = 0;
                var empty_cells = [];
                jQuery('li', this).each(function(){

                    if(ctr == 2) { /* If dragging to an unempty cell. */

                        /* Check empty cells. */
                        jQuery("div[id^='<?php echo $type_ctr?>_rank_']").each(function(){

                            if(jQuery(this).html().trim() == '') {
                                empty_cell_ctr++;
                                empty_cells[empty_cell_ctr] = jQuery(this);
                            }
                        });

                        if(empty_cell_ctr == 1) { /* If only one cell is empty, then put the replaced code here. */

                            empty_cells[empty_cell_ctr].append(jQuery(this));

                        } else jQuery('#<?php echo $type_ctr?>_ranking_code_wrapper').append(jQuery(this)); /* Else, bring it back to the code wrapper. */
                    }

                    ctr++;
                });
            }
            
            jQuery("div[id^='<?php echo $type_ctr?>_rank_']").each(function(){
                
                var e = jQuery(this); var id = e.attr('id').split('_'); 
                var index = parseInt(id[id.length - 1], 10);
                
                EXAM.answers[<?php echo $ctr - 1?>]['axl'][index] = jQuery('li b', e).html();                
            });
            
            EXAM.session_updater_peritem(<?php echo $ctr?>);
        }
    }).disableSelection();
    
    /* START: Initialize answer if present. */
    jQuery.each(EXAM.answers[<?php echo $ctr - 1?>]['axl'], function(key, value){
        
        jQuery('#<?php echo $type_ctr?>_ranking_code_wrapper li').each(function(){
            
            var e = jQuery(this);
            if(parseInt(jQuery('b', e).html(), 10) == value) { jQuery('#<?php echo $type_ctr?>_rank_'+ key).append(e); }            
        });
    });
    /* END: Initialize answer if present. */
});

jQuery.extend(
    
    EXAM.axl, {
        
        <?php echo $type_ctr?> : function() {
                
                if(! EXAM.answers[<?php echo $ctr - 1?>].axl) {
                    
                    Popup.dialog({
                        title : 'ERROR',
                        message : '<div>Please select an answer for "Ranking for Preference item# <?php echo $ctr?>".</div>',
                        buttons: ['Okay', 'Cancel'],
                        width: '420px'
                    });

                    return false;
                }
                
                return true;                
        }
    }
);
</script>
<div style="margin: 10px 0 10px 0">
                    
    <div style="margin-bottom: 20px"><?php echo $i?></div>
    <div style="margin-bottom: 10px"><b>Codes:</b></div>
    <ul id="<?php echo $type_ctr?>_ranking_code_wrapper" class="connectedSortable ranking_code_wrapper" style=" border: 1px dashed #CCC;">
        <?php
        foreach($code_arr as $code) {

            ?><li class="ui-state-default"><b style="font-size: 24px"><?php echo $code?></b></li><?php
        }
        ?>
    </ul>
    <div style="clear: both">

        <div style="padding-top: 10px"><b>Ranking:</b></div>
        <ul class="ranking_rank_wrapper"><?php echo $ranks?></ul>
        <div style="clear: both">
            <div style="border: 1px solid #CCC; min-height: 53px; padding: 0 5px 0 5px"><?php echo $rank_receiver?></div>
        </div>
        
    </div>
</div>