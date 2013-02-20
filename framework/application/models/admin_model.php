<?php defined('BASEPATH') or exit('No direct script access allowed');

/* 
 * Title : Admin Model Class
 * Author: Armande Bayanes (tuso@programmerspride.com)
 * Description: Handles Logging-in and Registration, etc ...
 * 
 * USER Level:
 * 1 -> Admin
 * 2 -> Project Owner
 * 3 -> Employee
 * 
 * RTA Status:
 * 0 -> canceled
 * 1 -> approved / approved but moved
 * 2 -> TBR (To Be Re-scheduled)
 * 3 -> pending
 * 
 **/

class Admin_model extends CI_Model {
	
    public function __construct() {
        
        parent::__construct ();
    }
    
    public function getUsers($fields = '*', $condition = '') {
        
        $result = array();

        $sql = "SELECT " . $fields . " FROM users WHERE `locked`='0' " . $condition; // ORDER BY level, lastname
        $query = $this->db->query($sql);
        
        if($query->num_rows()) {
            
            $result['data'] = $query->result();
            $result['total'] = $query->num_rows();
        }
        
        return $result;
    }
        
    /* Get RTA depending on what State is passed. */
    /*public function getRTA($state, $search = '', $sort = '', $subsort = '', $page = 1) {
        
        if($state === '') $state = 3;
        
        $sql_append = " WHERE rta.state='" . $state . "'";
        
        if($search != '') {
            
            $sql_search = " AND (CONCAT(SUBSTRING(u.firstname,1,1),SUBSTRING(u.middlename,1,1),SUBSTRING(u.lastname,1,1)) LIKE '%" . $search . "%' OR rta.samples_name LIKE '%" . $search . "%' OR rta.samples_desc LIKE '%" . $search . "%')";
        }
        
        if($sort != '' && $subsort != '') {
            
            if($sort == 'location') {
                
                $sql_sort = " AND rta.location=" . $subsort;
            }
            else
            if($sort == 'tot') {
                
                $sql_sort = " AND rta.type_of_test='" . $subsort . "'";
            }
            
        }
        
        //CONCAT_WS(' ',u.firstname,u.middlename,u.lastname) AS uname,
        $sql = "
            SELECT  rta.*,
                    (SELECT username FROM users WHERE id=rta.requested_by_id) AS uname,                    
                    sbu.name AS sbu_name,
                    loc.name AS location,
                    (SELECT username FROM users WHERE id=rta.processed_by_id) AS processed_by
            FROM    rta_forms rta
            LEFT
            JOIN    users u
            ON      rta.requested_by_id=u.id
            LEFT
            JOIN    sbu
            ON      rta.sbu=sbu.id
            LEFT
            JOIN    sbu_locations loc
            ON      rta.location=loc.id" . $sql_append . $sql_search . $sql_sort . " ORDER BY rta.date_filed DESC";

        $query = $this->db->query($sql);
        
        $response = array();
        
        if($query->num_rows()) {
            
            $rpp = 10;
            if(intval($page) <= 0) $page = 1;
            $start = ($page - 1) * $rpp;
            $maxpage = ceil($query->num_rows() / $rpp);

            $sql .= " LIMIT " . $start . "," . $rpp;
            $query = $this->db->query($sql);

            $uri = $_SERVER['REQUEST_URI'];
            $page_occurence = (int) strpos($uri, '&page');
            if($page_occurence == 0) {

                $page_occurence = (int) strpos($uri, '?page');
                if($page_occurence == 0) $page_occurence = strlen($uri);
            }

            $uri = substr($uri, 0, $page_occurence);
            if($uri[strlen($uri) - 1] != '/' && substr_count($uri, '=') == 0) $uri .= '/';

            for($x=1; $x<= $maxpage; $x++) {

                if($x == $page) $pages .= ', <b style="font-size: 18px">' . $x . '</b>';
                else $pages .= ', <a title="page ' . $x . '" href="' . $uri . ((substr_count($uri, '?') == 0) ? '?' : '&') . 'page=' . $x . '">' . $x . '</a>';
            }
            
           $response = array('Page: ' . substr($pages, 2), $query->result());
        }
        
        return $response;
    }*/
    
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
            AND     level IN(1,7) AND `locked`='0'";
        
        $query = $this->db->query($sql);
        if($query->num_rows()) {
            
            $this->load->model('global_model');
            $this->global_model->doLogin($query);
            
            $data['go'] = 'home';
            
        } else {
            
            $data['title'] = 'OOOPS !!! WRONG';
            $data['msg'] = 'Sorry, but this account is <b>not valid</b>. Please make sure you <u>entered</u> your username and password <b>correctly</b>.';
        }
        
        echo json_encode($data);
    }
    
    public function getUserName($id) {
        
        if($id == 0)
            return;
        
        $sql = "
            SELECT  id,
                    CONCAT_WS(' ',firstname,middlename,lastname) AS name,
                    username AS uname
            FROM    users
            WHERE   id=" . $id;
        $tmp = $this->db->query($sql)->row();
        
        if(empty($tmp)) return 'None.';
        
        /*$tmp2 = explode(' ', $tmp->name);
        $initials = strtoupper($tmp2[0][0] . $tmp2[1][0] . $tmp2[2][0]);*/
        
        return ($tmp->uname != '') ? $tmp->uname : $tmp->name;        
    }
    
    public function getSpecifics($spec_1, $spec_2) {
        
        $sql_append = "=" . $spec_1;
        $multi = false;
        $specifics_1 = '';
        $specifics_2 = '';
        
        if(substr_count($spec_1, ',')) {
            $sql_append = " IN(" . $spec_1 . ")";
            $multi = true;
        }
        
        $sql = "
            SELECT  *
            FROM    specifics
            WHERE   number=1
            AND     id" . $sql_append . "
            ORDER
            BY      content";
        $query = $this->db->query($sql);
        
        if($multi) {

            foreach($query->result() as $row) {
                $specifics_1 .= $row->content . '<br />';
            }
            
        } else {
            $tmp = $query->row();
            $specifics_1 = $tmp->content;
        }
        
        if($spec_2 > 0) {
            
            $sql = "
                SELECT  *
                FROM    specifics
                WHERE   number=2
                AND     id=" . $spec_2 . "
                ORDER
                BY      content";
            $tmp = $query = $this->db->query($sql)->row();
            $specifics_2 = $tmp->content;
        }
        
        return array($specifics_1, $specifics_2);
    }
    
    public function doUserList($data) {
        
        array_walk($data, 'xy_input_clean_up_byref');
        extract($data);
        
        if(! $page) $page = 1;

        $limit = $rows;
        if(! $limit) $limit = 15;
        
        /* START: Search. */
        if(! empty($filters)) {
            
            $filters = json_decode(stripslashes($filters));
            
            /* Multiple parameter passed for search. */
            foreach($filters->rules as $rule) {
                
                $field = $rule->field;
                $data = $rule->data;
                
                if($field && $data) {
                
                    if($field == 'name') {
                        
                        $sql_search .= " AND (`firstname` LIKE '%" . $data . "%' OR `lastname` LIKE '%" . $data . "%') ";
                    }
                    else
                    if($field == 'level') {
                        
                        $sql_search .= " AND level=" . $data . " ";
                    }
                    else
                    $sql_search .= " AND `" . $field . "` LIKE '%" . $data . "%' ";
                }
            }
            
        }
        /* END: Search. */
        
        $response = new stdClass();
        
        $sql = "
            SELECT  u.id,
                    u.level,
                    u.employee_no,
                    u.username,
                    u.firstname,
                    u.middlename,
                    u.lastname,
                    u.password,
                    u.birthdate,
                    (SELECT name FROM departments WHERE id=u.department) AS department_name
            FROM    users AS u
            WHERE   `locked`='0' " . $sql_search;
        
        $query = $this->db->query($sql);
        $count = $query->num_rows();

        if(! $count) { /* Tell the GRID that there ano records to view. */
            
            $response->total = 0;
            exit(json_encode($response));
        }
        
        if($count > 0) { $total_pages = ceil($count / $limit); }
        else { $total_pages = 0; }

        if($page > $total_pages) $page = $total_pages;
        $start = $limit * $page - $limit;
        
        $sql .= " ORDER BY " . $sidx . " " . $sord . " LIMIT " . $start . " , " . $limit;
        $query = $this->db->query($sql);
        
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        
        $x = 0;
        $state = xy_rta_state(true);
        
        foreach($query->result() as $row) {
            
            $response->rows[$x]['id'] = $row->id;
            
            $response->rows[$x]['cell'] = array(
                ((($page - 1) * $limit) + ($x + 1)),
                $row->id,
                $row->employee_no,
                $this->level[$row->level],
                ('<b>' . $row->lastname . '</b>, ' . $row->firstname . ' ' . $row->middlename),
                $row->username,
                $row->birthdate,
                $row->password,
                $row->department_name
            );
            
            $x++;
        }
        
        echo json_encode($response);
    }
    
    public function doUserLoad($id) {
        
        $sql = "SELECT * FROM users WHERE id=" . $id;
        $query = $this->db->query($sql);
        
        $response = array();
        if($query->num_rows()) { $response = $query->row(); }
        
        return $response;
    }
    
    public function doUserAE($data) {
        
        if(empty($data))
            return;
        
        array_walk($data, 'xy_input_clean_up_byref');
        extract($data);
        
        $employee_no = (double) $employee_no;
        if($employee_no > 0) {
            $sql_append = " employee_no=" . $employee_no . ", ";
        }
        
        $birthdate = date('Y-m-d', strtotime($birthdate));
        
        $sql_set = $sql_append . "
                    level=" . (int) $level . ",
                    superior_id=" . (double) $superior . ",
                    firstname='" . $fname . "',
                    middlename='" . $mname . "',
                    lastname='" . $lname . "',
                    email='" . $email . "',
                    username='" . $username . "',
                    password='" . $password . "',
                    birthdate='" . $birthdate . "'";

        if($id > 0) {
            
            $sql = "
            UPDATE  users
            SET     " . $sql_set . "
            WHERE   id=" . $id;
            
        } else {
            
            $sql = "
                INSERT
                INTO    users
                SET     " . $sql_set;            
        }
        
        $this->db->query($sql);
        
        $data['superior_id'] = $superior;
        $data['employee_no'] = $employee_no;
        $data['birthdate'] = $birthdate;
        $data['firstname'] = $fname;
        $data['middlename'] = $mname;
        $data['lastname'] = $lname;
        $data['email'] = $email;
        $data['username'] = $username;
        $data['password'] = $password;
        
        if($level == 1) {
            
            if(! $id) $id = $this->db->insert_id();
            
            $data['level'] = $level;
            $data['id'] = $id;
            $this->doCURL_AdminAE($data, (($id > 0) ? 'edit' : 'add'));
            unset($data['id']);
        }
        
        $data['level'] = $this->level[$level];
        
        return $data;
    }
    
    public function doUserDelete($data) {
        
        array_walk($data, 'xy_input_clean_up_byref');
        extract($data);
        
        $id = (double) $id;
        if($id == 0) return;
        
        $sql = "UPDATE users SET `locked`='1' WHERE id=" . $id;
        $query = $this->db->query($sql);
        
        $data['id'] = $id;
        $this->doCURL_AdminAE($data, 'delete');
    }
    
    /*
     * Execute external PHP file to add / update
     * an Admin user.
     * 
     * http://jollibee.programmerspride.com/exec_admin_ae.php
     **/
    public function doCURL_AdminAE($data, $action_type) {
        
        if(empty($data) || $action_type == '')
            return;
        
        $data = base64_encode(serialize($data));
        extract($_POST);
        
        $fields = array(
            'action_type' => $action_type,
            'data' => $data
        );
        
        xy_email($fields, 'exec_admin_ae.php');        
    }
}

/* End of file admin_model.php */
/* Location: ./application/models/admin_model.php */