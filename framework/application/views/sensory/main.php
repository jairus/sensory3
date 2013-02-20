<?php defined('BASEPATH') or exit('No direct script access allowed')?>

<ul id="xyNAV">
    <li><a href="<?php echo xy_url('admin/rta')?>">RTA</a></li>
    
    <?php
    if(in_array($this->session->level, $access_level)) {
        
        ?>
        
        <li><a href="<?php echo xy_url('po/rta_create')?>">File RTA</a></li>
        <li><a href="<?php echo xy_url('po/rta_by_owner')?>">MY RTA</a>
        <?php
    }
    ?>
        
    </li>
    <?php
    if(in_array($this->session->level, $access_level)) {
        
        ?>
        <li><a href="<?php echo xy_url('calendar')?>">Calendar</a></li>
        <li><a class="here" href="<?php echo xy_url('sensory')?>">Sensory</a>
            
            <ul>
                <li style="padding-top: 5px">
                    <?php
                    if(empty($view)) {
                        
                        ?>
                        <a href="<?php echo xy_url('admin/rta/?target=approved')?>">Click here to select an RTA.</a>
                        <?php
                    }
                    ?>
                    <!--Pick date:
                    <?php echo $month ?>
                    
                    <select id="dd_day"><option value="">Select:</option></select>
                    
                    <?php echo $year ?>
                    -->
                </li>
                
            </ul>
        </li>
        <?php
    }
    ?>
</ul>