<?php defined('BASEPATH') or exit('No direct script access allowed');

/* 
 * Title : PO (Project Owner) Model Class
 * Author: Armande Bayanes (tuso@programmerspride.com)
 * Description: Handles Logging-in and Registration, etc ...
 * 
 * USER Level:
 * 1 -> Admin
 * 2 -> Project Owner
 * 3 -> Employee
 **/

class Po_model extends CI_Model {

    function __construct() {
        
        parent::__construct();
    }
    
    public function doLogIn($data) {
        
        if(empty($data))
            return;
        
        /* Cleans up all input and perform a mysql escape. */
        array_walk($data, 'xy_input_clean_up_byref');
        extract($data);
        
        $sql = "
            SELECT  *
            FROM    users
            WHERE   (employee_no='" . $username . "' OR username='" . $username . "')
            AND     password='" . $password . "'
            AND     level IN (2,4,6,7) AND `locked`='0'";
        
        $query = $this->db->query($sql);
        if($query->num_rows()) {
            
            $this->load->model('global_model');
            $this->global_model->doLogin($query);
            
            $data['go'] = 'home';
            
        } else {
            
            $data['title'] = 'OOOPS !!! WRONG';
            $data['msg'] = 'Sorry, but this account is <b>not valid</b>. Please make sure you <u>entered</u>  your username and password <b>correctly</b>.';
        }
        
        echo json_encode($data);
    }
}

/* End of file po_model.php */
/* Location: ./application/models/po_model.php */