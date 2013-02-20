<?php defined('BASEPATH') or exit('No direct script access allowed');

/* 
 * Title : Employee Model Class
 * Author: Armande Bayanes (tuso@programmerspride.com)
 * Description: Handles Logging-in and Registration, etc ...
 * 
 * USER Level:
 * 1 -> Admin
 * 2 -> Project Owner
 * 3 -> Employee
 * 
 **/

class Employee_model extends CI_Model {

    function __construct() {
        
        parent::__construct();
    }
    
    /* START: for Registration and Log-in. */
    public function doSearchName($search) {
        
        if(! $search)
            return;
        
        $tmp = array();
        if(substr_count($search, ' ')) $tmp = explode(' ', $search);
        else $tmp[] = $search;
         
        $sql_append = "";
        foreach($tmp as $value) {

            $value = mysql_real_escape_string($value);
            $sql_append .= " OR firstname LIKE '%" . $value . "%'";
            $sql_append .= " OR middlename LIKE '%" . $value . "%'";
            $sql_append .= " OR lastname LIKE '%" . $value . "%'";
        }

        if($sql_append != '') $sql_append = substr($sql_append, 3);

        $sql = "SELECT * FROM users WHERE (" . $sql_append. ") AND `level` IN(3,6,7)";
        $query = $this->db->query($sql);
        
        $html = '';
        if($query->num_rows()) {

            foreach($query->result() as $row) {

                $name = $row->firstname . ' ' . $row->middlename . ' ' . $row->lastname;
                $html .= '<div title="' . $name . '" onclick="EMPLOYEE.select_name(' . $row->id . ',' . intval($row->employee_no) . ')" style="cursor: pointer"><input type="radio" name="search_result_name" value="' . $row->id . '"' . (($query->num_rows() == 1) ? ' checked="checked"' : '') . ' /> ' . $name . '</div>';
            }
        }

        if($html != '') echo '<div class="pad5">', $html, '</div>';
    }
    
    public function doLogIn($data) {
        
        if(empty($data))
            return;
        
        /* Cleans up all input and perform a mysql escape. */
        array_walk($data, 'xy_input_clean_up_byref');
        extract($data);
        
        if($eid > 0) {
            
            $sql = "
                SELECT  *
                FROM    users
                WHERE   employee_no=" . $eid . "
                AND     password='" . $password . "'
                AND     level IN (3,6,7)";
            
        } else {
            
            $sql = "
                SELECT  *
                FROM    users
                WHERE   id=" . $rid . "
                AND     password='" . $password . "'
                AND     level IN(3,6,7)";
        }
        
        $sql .= " AND `locked`='0'";
        
        $query = $this->db->query($sql);
        if($query->num_rows()) {
            
            
            $this->load->model('global_model', '', TRUE);
            $this->global_model->doLogIn($query);
            
        } else {
            
            $data['title'] = 'OOOPS !!! WRONG';
            $data['msg'] = 'Sorry, but this account is <b>not valid</b>. Please make sure you <u>entered</u> your password <b>correctly</b>.';
        }
        
        echo json_encode($data);
    }
    /* END: for Registration and Log-in. */    
}

/* End of file employee_model.php */
/* Location: ./application/models/employee_model.php */