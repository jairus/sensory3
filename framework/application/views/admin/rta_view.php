<?php defined('BASEPATH') or exit('No direct script access allowed')?>

<div id="section_wrapper">
    
    <?php
    if(empty($rta)) {
        
        echo 'RTA is empty.';
        return;
    }
    ?>
    <table cellpadding="0" cellspacing="0" id="step1">
        <tr><td class="padBtmTp2" nowrap="nowrap" width="200">Date Filed:</td>
            <td class="padBtmTp2 padLf5"><?php echo $rta->date_filed?></td>
        </tr>
        <tr><td class="padBtmTp2" nowrap="nowrap">Preferred date:</td>
            <td class="padBtmTp2 padLf5"><?php echo $rta->date_preferred?></td>
        </tr>
        <tr><td class="padBtmTp2" nowrap="nowrap">Requested by:</td>
            <td class="padBtmTp2 padLf5"><?php echo $requested_by?></td>
        </tr>
        <tr><td class="padBtmTp2" nowrap="nowrap">Approved by:</td>
            <td class="padBtmTp2 padLf5">
            
                <div class="fltLf" id="approved_by_wrapper"><?php echo $approved_by?></div>
                <div class="fltLf hidden padLf2" id="approved_by_other_wrapper"><input type="text" id="approved_by_other" maxlength="50" /> <img class="tooltip" src="<?php echo $this->config->item('XY')->DOCROOT?>media/images/tip.png" onmouseover="show_tip(this)" alt='Enter full name, separated by spaces. i.e. "<b>Niña Bay Buan</b>".<br /><br />Do not include second names and extensions such as: IV or Jr.<br /><br />Firstname, Middlename, and Lastname only.' /></div>
                
            </td>
        </tr>
        <tr><td class="padBtmTp2" nowrap="nowrap"><b>SBU</b>/Division:</td>
            <td class="padBtmTp2 padLf5">
                <?php echo $sbu?>
            </td>
        </tr>
        <tr><td class="padBtmTp2" nowrap="nowrap"><b>Type</b> of <u>test</u>:</td>
            <td class="padBtmTp2 padLf5">
                <?php echo $rta->type_of_test?>
            </td>
        </tr>
        <tr><td class="padBtmTp2" align="right" nowrap="nowrap"><u>Specifics</u> <b>#1</b>:</td>
            <td class="padBtmTp2 padLf5" height="30">
                <?php echo $specifics_1?>
            </td>
        </tr>
        <tr><td class="padBtmTp2" align="right" nowrap="nowrap"><u>Specifics</u> <b>#2</b>:</td>
            <td class="padBtmTp2 padLf5" height="30">
                <?php echo ($specifics_2 == '') ? 'Not Applicable.' : $specifics_2?>
            </td>
        </tr>
        <tr><td class="padBtmTp2" align="right" valign="top" nowrap="nowrap"># of Testing dates:</td>
            <td class="padBtmTp2 padLf5" height="30" valign="top">
                <?php
                echo (($rta->nof_testing_dates > 0) ? $rta->nof_testing_dates : 1);
                
                if($rta->nof_testing_dates > 1) {
                    
                    ?>
                    <div style="padding: 5px 0 2px 0">Frequency: <?php echo $rta->frequency?>.</div>
                    <?php
                }
                ?>
                <div>Schedule: <?php echo $schedule?>.</div>
            </td>
        </tr>
        <tr><td class="padBtmTp2" align="right" valign="top" nowrap="nowrap">Purpose: </td>
            <td class="padBtmTp2 padLf5">
                <?php echo $test_purpose?>
            </td>
        </tr>
        
        <tr><td class="padBtmTp2" align="right" nowrap="nowrap">Decision criteria:</td>
            <td class="padBtmTp2 padLf5"><?php echo $rta->decision_criteria?></td>
        </tr>
        <tr><td class="padBtmTp2" align="right" nowrap="nowrap">Next step (if Applicable):</td>
            <td class="padBtmTp2 padLf5"><?php echo $rta->next_step?></td>
        </tr>
        <tr><td class="padBtmTp2" align="right" nowrap="nowrap">Attributes to be tested:</td>
            <td class="padBtmTp2 padLf5"><?php echo $rta->attributes?></td>
        </tr>
        <tr><td class="padBtmTp2" align="right" nowrap="nowrap">Special requirement(s):</td>
            <td class="padBtmTp2 padLf5"><?php echo $rta->special_requirements?></td>
        </tr>

        <tr><td class="padBtmTp2" valign="top" nowrap="nowrap" width="100">Brief description of the Project:</td>
            <td class="padBtmTp2 padLf5 padTp4" valign="top">
                <div class="padBtmTp2"><?php echo $rta->project_desc?></div>
            </td>
        </tr>
        <tr><td class="padBtmTp2" valign="top" nowrap="nowrap">Sample product name:</td>
            <td class="padBtmTp2 padLf5">

                <div class="bold"><?php echo $rta->samples_name?></div>
            </td>
        </tr>
        <tr><td class="padBtmTp2" valign="top" nowrap="nowrap">Sample description:</td>
            <td class="padBtmTp2 padLf5">

                <div><?php echo $rta->samples_desc?></div>
            </td>
        </tr>
        <tr><td class="padBtmTp2" valign="top" nowrap="nowrap"><b>#</b> of Samples to be tested:</td>
            <td class="padBtmTp2 padLf5 padTp4">
                <div><?php echo $rta->no_of_samples?></div>
            </td>
        </tr>
        <tr><td class="padBtmTp2" colspan="2">Product Attribute(s) Description:</td></tr>
        <tr><td class="padBtmTp2" colspan="2">
                
                <table style="background: #FFF" id="table_data" cellpadding="0" cellspacing="0" border="0">
                    <tr><th><div><b>Attribute(s)</b></div></th>
                        <th><div><b>Code</b></div></th>
                        <th><div><b>PD</b></div></th>
                        <th><div><b>CU</b></div></th>
                        <th><div><b>Supplier</b></div></th>
                        <th nowrap="nowrap"><div><b>Batch Wt</b></div></th>
                        <th><div><b>Qty</b></div></th>
                        <th><div><b>Others</b></div></th>
                    </tr>
                    <?php
                    
                    if(! empty($rta->product_data)) {
                        foreach($rta->product_data as $row) {
                            
                            $row = (object) $row;
                            ?>
                            <tr><td><div><?php echo $row->variables?></div></td>
                                <td width="70"><div><?php echo $row->code?></div></td>
                                <td width="80"><div><?php echo $row->pd?></div></td>
                                <td width="80"><div><?php echo $row->cu?></div></td>
                                <td><div><?php echo $row->supplier?></div></td>
                                <td width="70"><div><?php echo $row->batch_weight?></div></td>
                                <td width="70" align="right"><div><?php echo $row->quantity?></div></td>
                                <td width="70"><div><?php echo $row->others?></div></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>                    
                </table>
                
            </td>
        </tr>
        
    </table>    
</div>