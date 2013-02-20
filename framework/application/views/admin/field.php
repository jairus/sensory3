<?php defined('BASEPATH') or exit('No direct script access allowed')?>

<div id="section_wrapper">
    <ul id="xyNAV">

        <li><a<?php echo (($section == 'sbu') ? ' class="here"' : '') ?> href="<?php echo xy_doc_root()?>admin/field/sbu">SBU/Division</a>
            <?php
            if($section == 'sbu') {
                ?><ul><li style="height: 25px">&nbsp;</li></ul><?php
            }
            ?>        
        </li>
        <li><a<?php echo (($section == 'purpose-of-test') ? ' class="here"' : '') ?> href="<?php echo xy_doc_root()?>admin/field/purpose-of-test">Purpose of Test</a>
            <?php
            if($section == 'purpose-of-test') {
                ?><ul><li style="height: 25px">&nbsp;</li></ul><?php
            }
            ?>
        </li>
        <li><a<?php echo (($section == 'spec1') ? ' class="here"' : '') ?> href="<?php echo xy_doc_root()?>admin/field/spec1">Specifics #1</a>
            <?php
            if($section == 'spec1') {
                ?>
                <ul>
                    <li style="padding-top: 5px; height: 20px"><a<?php echo (($target == 'affective') ? ' class="here"' : '') ?> href="<?php echo xy_url('admin/field/spec1/?target=affective')?>">Affective</a></li>
                    <li style="padding-top: 5px; height: 20px"><a<?php echo (($target == 'analytical') ? ' class="here"' : '') ?> href="<?php echo xy_url('admin/field/spec1/?target=analytical')?>">Analytical</a></li>
                    <li style="padding-top: 5px; height: 20px"><a<?php echo (($target == 'micro') ? ' class="here"' : '') ?> href="<?php echo xy_url('admin/field/spec1/?target=micro')?>">MICRO</a></li>
                    <li style="padding-top: 5px; height: 20px"><a<?php echo (($target == 'physico_chem') ? ' class="here"' : '') ?> href="<?php echo xy_url('admin/field/spec1/?target=physico_chem')?>">Physico Chem</a></li>
                </ul>
                <?php
            }
            ?>        
        </li>
        <li><a<?php echo (($section == 'spec2') ? ' class="here"' : '') ?> href="<?php echo xy_doc_root()?>admin/field/spec2">Specifics #2</a>
            <?php
            if($section == 'spec2') {
                ?>
                <ul>
                    <li style="padding-top: 5px; height: 20px"><a<?php echo (($target == 'affective') ? ' class="here"' : '') ?> href="<?php echo xy_url('admin/field/spec2/?target=affective')?>">Affective</a></li>
                    <li style="padding-top: 5px; height: 20px"><a<?php echo (($target == 'analytical') ? ' class="here"' : '') ?> href="<?php echo xy_url('admin/field/spec2/?target=analytical')?>">Analytical</a></li>
                    <li style="padding-top: 5px; height: 20px"><a<?php echo (($target == 'micro') ? ' class="here"' : '') ?> href="<?php echo xy_url('admin/field/spec2/?target=micro')?>">MICRO</a></li>
                    <li style="padding-top: 5px; height: 20px"><a<?php echo (($target == 'physico_chem') ? ' class="here"' : '') ?> href="<?php echo xy_url('admin/field/spec2/?target=physico_chem')?>">Physico Chem</a></li>
                </ul>
                <?php
            }
            ?>
        </li>
        <li><a<?php echo (($section == 'location') ? ' class="here"' : '') ?> href="<?php echo xy_url('admin/field/location')?>">Locations</a>
            <?php
            if($section == 'location') {
                ?>
                <ul><li style="height: 25px">&nbsp;</li></ul>
                <?php
            }
            ?>
        </li>
        <li><a<?php echo (($section == 'department') ? ' class="here"' : '') ?> href="<?php echo xy_url('admin/field/department')?>">Departments</a>
            <?php
            if($section == 'department') {
                ?>
                <ul><li style="height: 25px">&nbsp;</li></ul>
                <?php
            }
            ?>
        </li>
    </ul>

    <div style="padding-top: 12px; clear: both">
        <table width="100%" cellpadding="0" cellspacing="0" id="table_data" style="background: #FFF">
            <?php
            if(! ($section == 'spec2' && ($this->configXY->URI['target'] == 'analytical' || $this->configXY->URI['target'] == 'micro'))) {
                
                ?>
                <th nowrap="nowrap" valign="top" width="50" align="center"><div>#</div></th>
                <th nowrap="nowrap" valign="top" width="200"><div>Item name<br /><a href="javascript:FIELD.add_field_create()"><b>Add Field</b></a></div></th>
                <th nowrap="nowrap" valign="top" width="50"><div>Edit</div></th>
                <th nowrap="nowrap" valign="top" width="50" align="center"><div>Delete<br /><input type="checkbox" id="del_chk_all" onclick="FIELD.del_chk_all()" /></div></th>
                <th>&nbsp;</th>
                <?php
            } else {
                
                ?>
                <td><div>Not Applicable</div></td>
                <?php
            }
            if($tdata > 0) {

                for($x=0; $x<$tdata; $x++) {

                    $row = $data[$x];

                    ?>
                    <tr><td align="center"><?php echo ($x + 1)?></td>
                        <td style="padding: 2px"><input id="field_<?php echo $row->id?>" style="width: 500px" maxlength="100" type="text" value="<?php echo $row->item?>" onkeydown="FIELD.mark_to_edit(this,<?php echo $row->id?>)" onkeyup="FIELD.mark_to_edit(this,<?php echo $row->id?>)" /></td>
                        <td align="center"><input id="checkbox_edit_<?php echo $row->id?>" type="checkbox" value="<?php echo $row->item?>" disabled="disabled" /></td>
                        <td align="center"><input name="del_chk" id="checkbox_delete_<?php echo $row->id?>" type="checkbox" value="<?php echo $row->item?>" onclick="FIELD.mark_to_delete(this,<?php echo $row->id?>)" /></td>
                        <td style="padding-left: 5px">&nbsp;</td>
                    </tr>
                    <?php
                }
            }
            ?>
        </table>
        <div class="padBtmTp2"><input style="width: 130px" type="button" value="Update Changes" onclick="FIELD.submit()" /></div>
    </div>
</div>