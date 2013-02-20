<?php defined('BASEPATH') or exit('No direct script access allowed')?>

<?php include_once APPPATH . 'views/sensory/create_test/steps.inc.php'?>

<table cellspacing="0" cellpadding="0">
    <div>Include (Welcome Text) Instruction?</div>
    <div>

            <input id="instruct_1" type="radio" name="instruct" value="yes"<?php echo (($q->instruction == '') ? '' : ' checked="checked"')?> /> <label for="instruct_1">Yes</label>
            <input id="instruct_2" type="radio" name="instruct" value="no"<?php echo (($q->instruction == '') ? ' checked="checked"' : '')?> /> <label for="instruct_2">No</label>
            </div>
            <div id="instruction_wrapper"<?php echo (($q->instruction == '') ? ' style="display: none"' : '')?>>
                <textarea id="instruction"><?php echo $q->instruction?></textarea>
            </div>
    
    <div style="padding-top: 15px">Type of Test : <b><?php echo ucfirst($rta->type_of_test)?></b></div>
    
    <?php
    if($spec1) {
        ?><div style="padding-left: 40px">Specifics #1: <?php echo $spec1?></div><?php
    }

    if($spec2) {
        ?><div style="padding-left: 40px">Specifics #2: <b><?php echo $spec2?></b></div><?php
    }
    ?>
    
    <div style="margin-top: 20px">No. of Samples: <?php echo $rta->no_of_samples?></div>
    
</table>


<div style="margin-top: 20px; float: right">
    <input type="button" value="Next > Step 2" onclick="STEP_1.submit()" />
    <a href="<?php echo xy_url('sensory/create_test/' . $rta->id . '/?step=2')?>">Skip</a>
</div>