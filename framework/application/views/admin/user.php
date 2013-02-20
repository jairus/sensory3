<?php defined('BASEPATH') or exit('No direct script access allowed')?>

<div id="section_wrapper">
    
    <ul id="xyNAV">        
        <li><a class="here" href="<?php echo xy_url('admin/user')?>">User List</a>
            <ul>
                <li style="height: 25px">&nbsp;</li>
            </ul>
        </li>
        <!--li><a href="<?php echo xy_url('admin/user_add')?>">Add new User</a></li-->
        <li style="border: 0; background: none; padding-left: 10px">
            <a id="user_add_trigger" style="border: 0; padding-left: 16px; background: url('<?php echo xy_url('media/images/16x16/item-add.png')?>') top left no-repeat" href="javascript:;">Add new</a>
        </li>
    </ul>
    
    <div style="clear: both">
        
        <div class="popup" id="popupjs_wrapper" style="display: none; width: 510px; text-align: left; font-size: 14px"> 
            <div class="popup_title"></div> 
            <div class="popup_content"> 
                <p style="margin-top: 5px"></p>
                <div class="popup_buttons" style="font-size: 14px">
                    <button id="popupjs_btn_ok" class="default">Save</button><button id="popupjs_btn_cancel" style="margin-left: 2px" class="close_popup">Cancel</button>
                </div>
            </div>
        </div>
        
        <table id="xy_list"></table>
        <div id="xy_list_pager"></div>
    </div>   
</div>