<?php defined('BASEPATH') or exit('No direct script access allowed');

/* 
 * Title : Employee Controller Class
 * Author: Armande Bayanes (tuso@programmerspride.com)
 * Description: Handles Logging-in and Registration, etc ...
 **/

class Employee extends XY_Controller {
    
    function __construct() {
        
        parent::__construct();
        
        $this->load->model('employee_model', '', TRUE);        
        $this->load->helper('url');
        
        /* Special request. */
        if($this->uri->segment(2) == 'async_search_name') {
            
            $this->employee_model->doSearchName(trim($_POST['search']));
            exit();
        }
        
        $try = array('login', 'async_login', 'register', 'async_register');
        
        /* Check if NOT logged-in. */
        $this->load->helper('url');
        if(empty($this->session)) {
            
            if(! in_array($this->uri->segment(2), $try)) {
                redirect(base_url('employee/login'));
            }
        }
        
        /* If already logged-in. */
        if(! empty($this->session)) {

            if(in_array($this->uri->segment(2), $try)) {
                redirect(base_url('home'));
            }            
        }
    }
    
    public function register() {
        
        $this->config->config['XY']->JS_VARS['BD_DAY'] = 0;
        $this->config->config['XY']->JS_VARS['TODAY'] = "'" . $this->config->config['XY']->TODAY . "'";
        
        /* Plugin for Birthdate. */
        $this->document->loadPlugin('birthdate');
        
        /* START: SBU. */
        $html = '';
        $query = $this->db->query("SELECT * FROM sbu ORDER BY ordering");        
        if($query->num_rows() > 0) {
            
            $this->config->config['XY']->JS_VARS['SBU'] = 'new Array()';
            $x = 0;
            foreach($query->result() as $row) {

                /* Create JS variables. */
                $this->config->config['XY']->JS_VARS['[var=false]SBU[' . $x . ']'] = "'" . $row->name . "'";
                $x++;
                
                /* Create select options. */
                $html .= '<option value="' . $row->id . '">' . $row->name . '</option>';                
            }
        } $data['sbu'] = $html;
        /* END: SBU. */
        
        /* START: SBU Locations. */
        $html = '';
        $query = $this->db->query("SELECT * FROM sbu_locations ORDER BY ordering");        
        if($query->num_rows() > 0) {
            
            $this->config->config['XY']->JS_VARS['SBU_LOC'] = 'new Array()';
            $x = 0;
            foreach($query->result() as $row) {
                
                /* Create JS variables. */
                $this->config->config['XY']->JS_VARS['[var=false]SBU_LOC[' . $x . ']'] = "'" . $row->name . "'";
                $x++;
                
                /* Create select options. */
                $html .= '<option value="' . $row->id . '">' . $row->name . '</option>';                
            }
        } $data['sbu_locations'] = $html;
        /* END: SBU Locations. */
        
        /* START: Departments. */
        $html = '';
        $query = $this->db->query("SELECT * FROM departments ORDER BY name");
        if($query->num_rows() > 0) {
            
            //$this->config->config['XY']->JS_VARS['SBU_LOC'] = 'new Array()';
            $x = 0;
            foreach($query->result() as $row) {
                
                /* Create JS variables. */
                //$this->config->config['XY']->JS_VARS['[var=false]SBU_LOC[' . $x . ']'] = "'" . $row->name . "'";
                $x++;
                
                /* Create select options. */
                $html .= '<option value="' . $row->id . '">' . $row->name . '</option>';
            }
        } $data['departments'] = $html;
        /* END: Departments. */
        
        $data['blankGIF'] = xy_url('media/images/blank.gif');
        
        $this->load->vars($data);
        $data['content'] = $this->getViewFile(__FUNCTION__);
        
        $this->load->view('main', $data);
    }
    
    /* Triggered via Asynchronous request. */
    public function async_register() {
        
        /* Secure this page from unauthorized remote access. */
        $this->securePage();
        
        if(empty($_POST)) exit('No data.');
        
        $tmp = trim($_POST['data']);
        if(substr_count($tmp, '&') == 0) exit('No data.');
        
        $tmp = explode('&', $tmp);
        $input = array();
        foreach($tmp as $row) {
            
            list($key, $value) = explode('=', $row);
            $input[$key] = mysql_real_escape_string(trim($value));
            
        }

        extract($input);
        
        if($sbu == 'other' && $sbu_other != '') {
            
            /* Passed "sbu other" might already exists but failed to be validated by JavaScript. */
            $sql = "
                SELECT  *
                FROM    sbu
                WHERE   name='" . $sbu_other . "'";
            $query = $this->db->query($sql);

            $tmp = $query->row();
            if(empty($tmp)) {

                /* Insert SBU so it will be available on the next instance of the "registration process". */
                $sql = "
                    INSERT
                    INTO    sbu
                    SET     name='" . $sbu_other . "',
                            created='" . $this->config->config['XY']->DATE . "'";
                $this->db->query($sql);
                $sbu = $this->db->insert_id();

            } else $sbu = $tmp->id;            
        }

        if($sbu_loc == 'other' && $sbu_loc_other != '') {

            /* Passed "sbu loc other" might already exists but failed to be validated by JavaScript. */
            $sql = "
                SELECT  *
                FROM    sbu_locations
                WHERE   name='" . $sbu_loc_other . "'";
            $query = $this->db->query($sql);

            $tmp = $query->row();
            if(empty($tmp)) {

                /* Insert SBU Location so it will be available on the next instance of the "registration process". */
                $sql = "
                    INSERT
                    INTO    sbu_locations
                    SET     name='" . $sbu_loc_other . "',
                            created='" . $this->config->config['XY']->DATE . "'";
                $this->db->query($sql);
                $sbu_loc = $this->db->insert_id();

            } else $sbu_loc = $tmp->id;            
        }
            
        $birthdate = $bd_year . '-' . $bd_month . '-' . $bd_day;
        
        $password = $birthdate;
        
        unset($data);
        
        if($mname != '') {
            
            $sql_append = " AND middlename='" . $mname . "'";
            $mname_msg = ' ' . $mname . ' ';
        }
        
        if(! isset($similar_user_confirmation_done)) {
            
            /* See if similar user exists. */
            $sql = "
                SELECT  firstname,
                        middlename,
                        lastname
                FROM    users
                WHERE   firstname='" . $fname . "'
                " . $sql_append . "
                AND     lastname='" . $lname . "'";
            $query = $this->db->query($sql);
            $tmp = $query->row();
            if(! empty($tmp)) {

                $data['confirm'] = 1; /* Just a Flag to tell JavaScript to pop a confirmation message. */

                if($tmp->middlename != '') {
                    $mname_errmsg = ' ' . $tmp->middlename . ' ';
                }

                $data['msg'] = 'A similar User was found!<br /><br />Are you sure that <b>' . $fname . $mname_msg . $lname . '</b><br />is NOT <b>' . $tmp->firstname . $mname_errmsg . $tmp->lastname . '</b> ?';
                exit(json_encode($data));
            }
        }
        
        $sql = "
            INSERT
            INTO    users
            SET     employee_no='" .    (isset($employee_no) ? $employee_no : '') . "',
                    firstname='" .      $fname . "',
                    middlename='" .     $mname . "',
                    lastname='" .       $lname . "',
                    gender='" .         $icon . "',
                    birthdate='" .      $birthdate . "',
                    password='" .       $password . "',
                    sbu=" .             $sbu . ",
                    sbu_location=" .    $sbu_loc . ",
                    department='" .     $dept . "',
                    office_email='" .   $office_email . "',
                    mobile_no='" .      $mobile_no . "',
                    local_no='" .       $local_no . "',
                    ip_now='" .        $this->client->getIP() . "',
                    created='" .       $this->config->config['XY']->DATE . "'";
        $this->db->query($sql);
        $user_id = $this->db->insert_id();
        
        if($user_id) {
            
            $sql = "UPDATE sbu SET user_id=" . $user_id . " WHERE id=" . $sbu;
            $this->db->query($sql);
            
            $sql = "UPDATE sbu_locations SET user_id=" . $user_id . " WHERE id=" . $sbu_loc;
            $this->db->query($sql);
            
            $data['title'] = 'SUCCESS !!! JOINED';
            $data['msg'] = 'User was successfully <b>registered</b>.';
            $data['go'] = $this->config->config['XY']->DOCROOT . 'home';
            $data['icon'] = 'ok';
            
        } else {
            
            $data['title'] = 'OOOPS !!! ERROR';
            $data['msg'] = 'An unknown <b>ERROR occured</b>. Please try again later';            
        }
        
        echo json_encode($data);
    }
    
    public function login() {
        
        /*$date_for_js = date('F d, Y H:i:s', strtotime($this->config->config['XY']->DATE));
        $this->config->config['XY']->JS_VARS['DATE'] = "'" . $date_for_js . "'";
        
        $js_func  = 'var today = new Date();';
        $js_func .= 'var server = new Date(DATE);';
        $js_func .= 'alert(server); alert(today);';
        
        //$this->config->config['XY']->JS_FUNC[] = $js_func;
        */
        $data['content'] = $this->getViewFile(__FUNCTION__);        
        $this->load->view('main', $data);
    }
    
    public function async_login() {
        
        /* Secure this page from unauthorized remote access. */
        $this->securePage();
        
        if(empty($_POST)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        $this->employee_model->doLogIn($input);
    }    
}

/* End of file employee.php */
/* Location: framework/application/controllers/employee.php */