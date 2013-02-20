<?php
    if( isset( $query ) ){
        if( $query->num_rows() > 0 ){
            foreach( $query->result_array() as $rows ):?>
                <p>
                    <label for="index">Index / ID:</label>
                    <input type="text" id="index" name="index" size="20" class="form_field" value="<?php echo $rows['index']?>" disabled="disabled"/>
                   
                </p>
                <p>
                    <label for="id">Product Identifier:</label>
                    <input type="text" id="pid" name="id" size="60" class="form_field" value="<?php echo $rows['id']?>"/>
                </p>
                <p>
                    <label for="pack">Pack Name:</label>
                    <input type="text" id="pname" name="pack" size="20" class="form_field" value="<?php echo $rows['pack']?>"/>
                </p>
                <p>
                    <label for="quantity">Quantity:</label>
                    <input type="text" id="quant" name="quantity" size="2" class="form_field" value="<?php echo $rows['quantity']?>"/>
                </p>
 <?php      
            endforeach;
            echo "<input style=\"width: 90px\" type=\"button\" value=\"Update\" class=\"updateRTA\"  />";?>
            <a href="<?php echo $this->config->config['XY']->DOCROOT?>admin/showlist/"><b>RTA Records</b></a>
 <?php       }
    }
?>