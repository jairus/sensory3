<div id ="editForm">
<?php
    if( isset( $query ) ){
        if( $query->num_rows() > 0 ){
            
            foreach( $query->result_array() as $rows ):
                $requested_by = substr($rows['firstname'],0,1) . substr($rows['middlename'],0,1) . substr($rows['lastname'],0,1);
                $requested_by .= " <span class=\"tip\">( ".$rows['firstname'] . " " . substr($rows['middlename'],0,1) . ". " . $rows['lastname'] . " )</span>";
                
                ?>                    
                <input type="text" id="id" hidden="hidden" value="<?php echo $rows['id']?>"/>
                <input type="text" id="uri" hidden="hidden" value="<?php echo $rows['requested_by_id']?>"/>
                <div class="row">
                        <div class="col1">Date Filed:</div>
                        <div class="col2">
                            <label id="date_filed" name="date_filed" class="form_field"> <?php echo date('m/d/Y', strtotime($rows['date_filed']))?> </label>
                        </div>
                </div>
                <div class="row">
                        <div class="col1">Preferred Date:</div>
                        <div class="col2">
                            <input type="text" id="date_pref" name="date_pref" size="20" class="form_field" value="<?php echo date('m/d/Y', strtotime($rows['preferred_date']))?>" />
                            <div class="tip">( Move by changing the current date. )</div>
                        </div>
                </div>
                
                <div class="row">
                        <div class="col1">Status:</div>
                        <div class="col2">
                            <b><?php
                            if($rows['state'] == 1) echo 'Approved';
                            elseif($rows['state'] == 2) echo 'Moved';
                            elseif($rows['state'] == 3) echo 'Pending';
                            else echo 'Cancelled';
                            ?></b><br />
                            Change state:
                            <select id="state">
                                <option value="0">Select</option>
                                <option value="1">Cancel</option>
                                <option value="2">Approve</option>
                            </select>                            
                        </div>
                        <div style="clear:both"></div>
                        <br/>
                        <?php 
                        /*if( $state != 'Approved' ){?>
                            <input style="width:90px" type="button" value="Approved" class="approveRTA" name="<?php echo $rows['requested_by_id']?>" id="<?php echo $rows['id']?>" />
                        <?php 
                        }*/
                        ?>
                        
                </div>   
                
                <div class="row">
                        <div class="col1">Requested By:</div>
                        <div class="col2">
                            <label id="requested_by" name="requested_by" class="form_field"> <?php echo $requested_by?> </label>
                        </div>
                </div>
                <div class="row">
                        <div class="col1">SBU:</div>
                        <div class="col2">
                            <label id="sbu" name="sbu" class="form_field"> <?php echo ucfirst($rows['sbu_name'])?> </label>
                        </div>
                </div>
                <div class="row">
                        <div class="col1">Type of test:</div>
                        <div class="col2">
                            <label id="type_of_test" name="type_of_test" class="form_field"> <?php echo ucfirst($rows['type_of_test'])?> </label>
                        </div>
                </div>
                <?php
                if( isset( $purpose ) ){?>
                <div class="row">
                        <div class="col1">Test Purpose:</div>
                        <div class="col2">
                <?php    
                    foreach( $purpose->result_array() as $purp ):?>
                            <label>
                                <?php echo $purp['content'] , "<br/><br/>";?>
                            </label>
                            
                <?php        
                    endforeach;
                }       
                ?>
                       </div>
                </div>
                
                <?php
                if( isset( $spec1 ) ){?>
                <div class="row">
                        <div class="col1">Specifics 1:</div>
                        <div class="col2">
                <?php    
                    foreach( $spec1->result_array() as $specone ):?>
                        <label id="spec_1" name="spec_1" class="form_field"> <?php echo $specone['content']?> </label>
                <?php        
                    endforeach;
                }       
                ?>
                       </div>
                </div>
                
                <?php
                if( isset( $spec2 ) ){?>
                <div class="row">
                        <div class="col1">Specifics 2:</div>
                        <div class="col2">
                <?php    
                    foreach( $spec2->result_array() as $spectwo ):?>
                        <label id="spec_2" name="spec_2" class="form_field"> <?php echo $spectwo['content']?> </label>
                <?php        
                    endforeach;
                }       
                ?>
                       </div>
                </div>
                
                <div class="row">
                        <div class="col1">Background / Brief description of the Project:</div>
                        <div class="col2">
                            <?php echo $rows['project_desc']?>
                        </div>
                </div>
                
                <div class="row">
                        <div class="col1">Sample(s) Description:</div>
                        <div class="col2">
                            <?php echo $rows['samples_desc']?>
                        </div>
                </div>
                
                <div class="row">
                        <div class="col1">Decision Criteria:</div>
                        <div class="col2">
                            <?php echo $rows['decision_criteria']?>
                        </div>
                </div>
                
                <div class="row">
                        <div class="col1">Next Step:</div>
                        <div class="col2">
                            <?php echo $rows['next_step']?>
                        </div>
                </div>
                
                <div class="row">
                        <div class="col1">Attributes to be tested:</div>
                        <div class="col2">
                            <?php echo $rows['attributes']?>
                        </div>
                </div>
                
                <div class="row">
                        <div class="col1">Special Instruction:</div>
                        <div class="col2">
                            <?php echo $rows['instruction']?>
                        </div>
                </div>
                
                
 <?php      
            endforeach;
            
 ?>

                
                
            <!--a href="<?php echo $this->config->config['XY']->DOCROOT?>admin/" class="linkbtn"><b>Home</b></a-->
 <?php       }
    }
?>
                <div class="row">
                <div style="width: 170px" class="btnWrapper"><input type="button" onclick="RTA_FORM.update(this,'<?php echo $rows['id']?>')" value="Update this Form" style="width: 170px" class="btn"></div>
                </div>
</div>