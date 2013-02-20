<?php defined('BASEPATH') or exit('No direct script access allowed')?>
<div id="section_wrapper">
    
    <?php include_once APPPATH . 'views/menu.inc.php'?>
    
    <div id="section_wrapper_inner">
        
        <table cellpadding="0" cellspacing="0" width="100%">
            
            <tr><td class="padBtmTp2" nowrap="nowrap" width="200">Date Filed:</td>
                <td class="padBtmTp2 padLf5"><?php echo $rta->date_filed?></td>
            </tr>
            
            <tr><td class="padBtmTp2" nowrap="nowrap"><span class="mandatory">*</span> Preferred date:</td>
                <td class="padBtmTp2 padLf5"><input type="text" id="preferred_date" value="<?php echo $rta->date_preferred?>" /> <img title="i.e. Enter PD of the sample if for SL study." alt="i.e. Enter PD of the sample if for SL study." src="<?php echo xy_doc_root()?>media/images/tip.png" /></td>
            </tr>
            <tr><td class="padBtmTp2" nowrap="nowrap"><span class="mandatory">*</span> Requested by:</td>
                <td class="padBtmTp2 padLf5"><?php echo $requested_by?></td>
            </tr>
            <tr><td class="padBtmTp2" nowrap="nowrap">Approved by:</td>
                <td class="padBtmTp2 padLf5">

                    <div class="fltLf" id="approved_by_wrapper"><?php echo $approved_by?></div>
                    <div class="fltLf hidden padLf2" id="approved_by_other_wrapper"><input type="text" id="approved_by_other" maxlength="50" /> <img title='Enter full name, separated by spaces. i.e. "<b>Niña Bay Buan</b>".<br /><br />Do not include second names and extensions such as: IV or Jr.<br /><br />Firstname, Middlename, and Lastname only.' src="<?php echo xy_url('media/images/tip.png')?>" alt='Enter full name, separated by spaces. i.e. "<b>Niña Bay Buan</b>".<br /><br />Do not include second names and extensions such as: IV or Jr.<br /><br />Firstname, Middlename, and Lastname only.' /></div>

                </td>
            </tr>
            <tr><td class="padBtmTp2" nowrap="nowrap"><span class="mandatory">*</span> <b>SBU</b>/Division:</td>
                <td class="padBtmTp2 padLf5">
                <div class="fltLf">
                    <select id="sbu" onchange="GBL.toggle_other_field(this,'sbu_other_wrapper','sbu_other')">
                        <option value="">Select:</option>
                        <?php echo $sbu?>
                        <option value="other">Others</option>
                    </select>
                </div>
                <div class="fltLf hidden padLf2" id="sbu_other_wrapper"><input type="text" id="sbu_other" maxlength="50" /></div>
                </td>
            </tr>
            <tr><td class="padBtmTp2" nowrap="nowrap"><span class="mandatory">*</span> <b>Type</b> of <u>test</u>:</td>
                <td class="padBtmTp2 padLf5">
                    <select id="type_of_test" onchange="RTA_FORM.toggle_type_of_test(this)">
                        <option value="">Select:</option>
                        <option value="affective"<?php echo (($rta->type_of_test == 'affective') ? ' selected="selected"' : '')?>>Affective</option>
                        <option value="analytical"<?php echo (($rta->type_of_test == 'analytical') ? ' selected="selected"' : '')?>>Analytical</option>
                        <option value="micro"<?php echo (($rta->type_of_test == 'micro') ? ' selected="selected"' : '')?>>MICRO</option>
                        <option value="physico_chem"<?php echo (($rta->type_of_test == 'physico_chem') ? ' selected="selected"' : '')?>>Physico Chem</option>
                    </select>
                </td>
            </tr>
            <tr><td class="padBtmTp2" align="right" nowrap="nowrap"><span class="mandatory">*</span> <u>Specifics</u> <b>#1</b>:</td>
                <td class="padBtmTp2 padLf5" height="30">
                    <div class="fltLf hidden" id="spec1_affective_wrapper"><?php echo $spec1_affective?></div>
                    <div class="fltLf hidden" id="spec1_analytical_wrapper"><?php echo $spec1_analytical?></div>
                    <div class="fltLf hidden" id="spec1_micro_wrapper"><?php echo $spec1_micro?></div>
                    <div class="fltLf hidden" id="spec1_physico_chem_wrapper"><?php echo $spec1_physico_chem?></div>
                    <div class="fltLf hidden padLf2" id="spec1_other_wrapper"><span id="spec1_other_label"><b>Others</b>: </span><input id="spec1_other" type="text" /> <img title="You can enter multiple items from this Field. Just separate them with comma (<b>,</b>).<br/><br/>i.e. <b>Item 1, Item 2</b>." id="spec1_other_tip" alt="" src="<?php echo xy_url('media/images/tip.png')?>" /></div>
                </td>
            </tr>
            <tr><td class="padBtmTp2" align="right" nowrap="nowrap"><span class="mandatory">*</span> <u>Specifics</u> <b>#2</b>:</td>
                <td class="padBtmTp2 padLf5" height="30">
                    <div class="fltLf hidden" id="spec2_affective_wrapper"><?php echo $spec2_affective?></div>
                    <div class="fltLf hidden" id="spec2_analytical_wrapper"><?php echo $spec2_analytical?></div>
                    <div class="fltLf hidden" id="spec2_micro_wrapper"><?php echo $spec2_micro?></div>
                    <div class="fltLf hidden" id="spec2_physico_chem_wrapper"><?php echo $spec2_physico_chem?></div>
                    <div class="fltLf hidden padLf2" id="spec2_other_wrapper"><span id="spec2_other_label"><b>Others</b>: </span><input id="spec2_other" type="text" /> <img title="You can enter multiple items from this Field. Just separate them with comma (<b>,</b>).<br/><br/>i.e. <b>Item 1, Item 2</b>." id="spec2_other_tip" alt="" src="<?php echo xy_url('media/images/tip.png')?>" /></div>
                </td>
            </tr>
            <tr><td class="padBtmTp2" align="right" valign="top" nowrap="nowrap"><span class="mandatory">*</span> # of Testing dates:</td>
                <td class="padBtmTp2 padLf5" height="30" valign="top">
                    <input id="nof_testing_dates" type="text" style="width: 50px; text-align: right" value="<?php echo (($rta->nof_testing_dates > 0) ? $rta->nof_testing_dates : 1)?>" onkeypress="return GBL.numOnly(event)" />
                    <div id="frequency_wrapper" class="hidden">
                        <div style="padding: 5px 0 2px 0">Frequency:</div>
                        <div>
                        <select id="frequency">
                            <option value="">Select:</option>
                            <option value="d"<?php echo (($rta->frequency == 'd') ? ' selected="selected"' : '')?>>Daily</option>
                            <option value="w"<?php echo (($rta->frequency == 'w') ? ' selected="selected"' : '')?>>Weekly</option>
                            <option value="2m"<?php echo (($rta->frequency == '2m') ? ' selected="selected"' : '')?>>Twice a Month</option>
                            <option value="m"<?php echo (($rta->frequency == 'm') ? ' selected="selected"' : '')?>>Monthly</option>
                            <option value="30d"<?php echo (($rta->frequency == '30d') ? ' selected="selected"' : '')?>>30 Days</option>
                            <option value="other"<?php echo (($rta->frequency == 'other') ? ' selected="selected"' : '')?>>Others</option>
                        </select>
                        </div>
                    </div>
                    <div id="other_schedule_wrapper">
                        <div class="padBtmTp2">Separate the Dates with "comma" (,). i.e. <b>08/23/2011, 08/29/2011</b>.</div>
                        <div><textarea id="other_schedule"><?php echo $rta->schedule_other?></textarea></div>
                    </div>
                    <div id="calculated_schedule"></div>
                </td>
            </tr>
            <tr><td class="padBtmTp2" align="right" valign="top" nowrap="nowrap"><span class="mandatory">*</span> Purpose:</td>
                <td class="padBtmTp2 padLf4">

                    <div class="fltLf">
                        <div>
                            <?php echo $test_purpose?>
                            <div class="padLf20">
                                <input type="checkbox" id="tpurpose_all_trigger" onclick="RTA_FORM.toggle_tpurpose_all(this)" /><label for="tpurpose_all_trigger">Check / Un-check <b>All</b></label>
                                <div class="padBtmTp2">&nbsp;<b>Others</b> <input type="text" id="tpurpose_other" maxlength="50" /></div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>

            <tr><td class="padBtmTp2" align="right" nowrap="nowrap"><span class="mandatory">*</span> Decision criteria:</td>
                <td class="padBtmTp2 padLf5"><textarea id="decision_criteria"><?php echo $rta->decision_criteria?></textarea> <img title="i.e. NSD Triangle; At least 85%." alt="i.e. NSD Triangle; At least 85%." src="<?php echo xy_doc_root()?>media/images/tip.png" /></td>
            </tr>
            <tr><td class="padBtmTp2" align="right" nowrap="nowrap">Next step (if Applicable):</td>
                <td class="padBtmTp2 padLf5"><input id="next_step" type="text" value="<?php echo $rta->next_step?>" style="width: 350px" /> <img title="i.e. If SD, proceed to preference test." alt="i.e. If SD, proceed to preference test." src="<?php echo xy_doc_root()?>media/images/tip.png" /></td>
            </tr>
            <tr id="attr_to_test_tr">
                <td class="padBtmTp2" align="right" nowrap="nowrap"><span class="mandatory">*</span> Attributes to be tested:</td>
                <td class="padBtmTp2 padLf5"><input id="attr_to_test" type="text" value="<?php echo $rta->attributes?>" style="width: 350px" /> <img title="i.e. OA, Flavor, Texture." alt="i.e. OA, Flavor, Texture." src="<?php echo xy_doc_root()?>media/images/tip.png" /></td>
            </tr>
            <tr><td class="padBtmTp2" align="right" nowrap="nowrap">Special requirement(s):</td>
                <td class="padBtmTp2 padLf5"><input id="special_requirements" type="text" value="<?php echo $rta->special_requirements?>" style="width: 350px" /> <img title="i.e. Spicy Eaters; Coffee Drinkers, Kids; As is, as applied." alt="i.e. Spicy Eaters; Coffee Drinkers, Kids; As is, as applied." src="<?php echo xy_doc_root()?>media/images/tip.png" /></td>
            </tr>
            
        </table>

        <table cellpadding="0" cellspacing="0" width="100%">
            <tr><td class="padBtmTp2" valign="top" nowrap="nowrap"><span class="mandatory">*</span> Brief description of the Project: <img title="i.e. To develop new 39ers; To deplete raw material;" alt="i.e. To develop new 39ers; To deplete raw material;" src="<?php echo xy_doc_root()?>media/images/tip.png" /></td>
                <td class="padBtmTp2 padLf5 padTp4" valign="top">
                    <div class="padBtmTp2"><textarea id="project_desc"><?php echo $rta->project_desc?></textarea></div>
                </td>
            </tr>
            <tr><td class="padBtmTp2" valign="top" nowrap="nowrap"><span class="mandatory">*</span> Sample product name: <img title="i.e. New Rice Meal; Chickenjoy; Hampat" alt="i.e. New Rice Meal; Chickenjoy; Hampat" src="<?php echo xy_doc_root()?>media/images/tip.png" /></td>
                <td class="padBtmTp2 padLf5">

                    <div><input type="text" id="samples_name" value="<?php echo $rta->samples_name?>" /></div>
                </td>
            </tr>
            <tr><td class="padBtmTp2" valign="top" nowrap="nowrap"><span class="mandatory">*</span> Sample description: <img title="i.e. Chicken Pork Adobo w/ Rice; Juiciness Improvement; Texture" alt="i.e. Chicken Pork Adobo w/ Rice; Juiciness Improvement; Texture" src="<?php echo xy_doc_root()?>media/images/tip.png" /></td>
                <td class="padBtmTp2 padLf5">

                    <div><textarea id="samples_desc"><?php echo $rta->samples_desc?></textarea></div>
                </td>
            </tr>
            <tr><td class="padBtmTp2" valign="top" nowrap="nowrap"><span class="mandatory">*</span> <b>#</b> of Samples to be tested:</td>
                <td class="padBtmTp2 padLf5 padTp4">

                    <div><input type="text" id="no_of_samples" onkeypress="return GBL.numOnly(event)" onkeyup="RTA_FORM.pad_row(this)" maxlength="5" value="<?php echo $rta->no_of_samples?>" /></div>
                </td>
            </tr>
            <tr><td class="padBtmTp2" colspan="2"><span class="mandatory">*</span> Detailed Description:</td></tr>
            <tr><td class="padBtmTp2" colspan="2">

                    <table id="table_data" cellpadding="0" cellspacing="0" border="0" width="100%">
                        <tr><th><div><b>Sample(s)</b> <img title="Indicate description of Control and Experiments." alt="Indicate description of Control and Experiments." src="<?php echo $this->config->item('XY')->DOCROOT?>media/images/tip.png" /></div></th>
                            <th><div><b>Code</b></div></th>
                            <th><div><b>PD</b> <img title="Put a tentative PD schedule." alt="Put a tentative PD schedule." src="<?php echo $this->config->item('XY')->DOCROOT?>media/images/tip.png" /></div></th>
                            <th><div><b>CU</b> <img title="Put a tentative CU schedule." alt="Put a tentative CU schedule." src="<?php echo $this->config->item('XY')->DOCROOT?>media/images/tip.png" /></div></th>
                            <th><div><b>Supplier</b></div></th>
                            <th nowrap="nowrap" align="center"><div><b>Batch Weight<br />Produced</b></div></th>
                            <th align="center"><div><b>Quantity<br />Delivered</b></div></th>
                            <th><div><b>Others</b></div></th>
                        </tr>
                    </table>

                </td>
            </tr>
            
            
            <tr><td></td>
                <td class="padBtmTp2 padLf5" nowrap="nowrap"><input style="width: auto; padding: 3px; font: 14px 'Lucida Grande'" type="button" value="Update this Form" onclick="RTA_FORM.submit()" /></div></td>
            </tr>        
        </table>
    </div>
</div>