<?php
if( isset( $param ) ){
        $message = "Successfully Cancelled RTA";
        echo '<p id="message">' . $message . '</p>';	
}
if( isset($action) ){
    if( isset( $query ) ){
?>
            <table width="100%" border="1" cellpadding="0" cellspacing="0" id = "steal_cat" style ="margin-top: 10px;">
                <tr>
                    <th scope="col" colspan="">Index / ID</th>
                    <th scope="col" colspan="">Product Identifier</th>
                    <th scope="col" colspan="">Pack Name</th>
                    <th scope="col" colspan="">Quantity</th> 
                    <th scope="col" colspan="">Status</th>
                    <th scope="col" colspan="2">Action</th>

                </tr>
                <?php if( $query->num_rows() > 0):	#print_r($query);?>
                        <?php foreach( $query->result_array() as $rows ): ?>
                        <tr>	
                            <td>
                                <?php echo $rows['index']?>
                            </td>
                        	<td>
                                <?php echo $rows['id']?>
                            </td>
                            <td>
                                <?php echo $rows['pack']?>
                            </td>
                            <td>
                                <?php echo $rows['quantity']?>
                            </td>
                            <td>
                                <?php 
                                    if( $rows['flag'] == 1 ){
                                        echo "Active";
                                    }else{
                                        echo "Cancelled";
                                    }
                                ?>
                            </td>
                            <td align="center" class="action">
                                <a href="<?php echo $this->config->config['XY']->DOCROOT?>admin/edit/<?php echo $rows['id'];?>"> 
                                    <img title="Edit" alt="Edit" src="<?php echo $editPNG?>" />
                                </a>
                            </td>
                            <?php
                                if( $rows['flag'] == 1 ){?>
                                    <td align="center" class="action">
                                        <img title="Cancel" class="cancelRTA" alt="Cancel" src="<?php echo $cancelPNG?>" id="<?php echo $rows['index']?>" />
                                    </td> 
                            <?php  }else{?>
                                        <td align="center" >
                                            <img title="Cancel" alt="Cancel" src="<?php echo $cancelPNG?>" id="<?php echo $rows['index']?>" />
                                        </td> 
                            <?php    
                                   }
                            ?>
                            
                        </tr>	   
                                       
                        <?php endforeach;?>
                        
                    <?php endif;
                        
                    ?>   
                
            </table>
<?php        
        }   
}

?>
<a href="<?php echo $this->config->config['XY']->DOCROOT?>admin/user/"><b>List of Users</b></a>