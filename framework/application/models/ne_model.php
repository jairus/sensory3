<?php defined('BASEPATH') or exit('No direct script access allowed');

/* 
 * Title : Non-employee Model Class
 * Author: Armande Bayanes (tuso@programmerspride.com)
 * Description: Handles Logging-in and Registration, etc ...
 * 
 * USER Level:
 * 1 -> Admin
 * 2 -> Project Owner
 * 3 -> Employee
 * 5 -> Non-employee
 **/

class Ne_model extends CI_Model {

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
            INSERT
            INTO    users
            SET     `level`=5,
                    firstname='" . $fname . "',
                    lastname='" . $lname . "',
                    gender='" . $gender . "',
                    age='" . $age . "',
                    created='" . $this->configXY->DATE . "'";
        $this->db->query($sql);
        
        $sql = "
            SELECT  *
            FROM    users
            WHERE   id=" . $this->db->insert_id();
        
        $query = $this->db->query($sql);
        if($query->num_rows()) {
            
            $this->load->model('global_model', '', TRUE);
            $this->global_model->doLogIn($query);
            
        }/* else {
            
            $data['title'] = 'OOOPS !!! WRONG';
            $data['msg'] = 'Sorry, but this account is <b>not valid</b>.';
        }*/
        
        echo json_encode($data);
    }
}

/* End of file ne_model.php */
/* Location: ./application/models/ne_model.php */