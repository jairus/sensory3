<?php defined('BASEPATH') or exit('No direct script access allowed');

class Home extends XY_Controller {
    
    function __construct() {
        
        parent::__construct();

        $this->load->model('home_model', '', true);        
        
        /* START: Process logging out. */
        if($this->uri->segment(2) == 'logout') {
            
            $this->load->model('global_model', '', true);
            $this->global_model->doLogOut();
            
            $sql = "UPDATE stations SET state=0 WHERE ip='" . $this->client->getIP() . "'";
            $this->db->query($sql);
            
            //redirect(base_url((($this->session->level == 1) ? 'admin' : (($this->session->level == 2) ? 'po' : 'employee')) . '/login'));
            redirect(base_url(base64_decode($this->configXY->URI['r']) . '/login'));
            
        } /* END: Process logging out. */

        /* If NOT logged-in. */
        if(empty($this->session)) {
            redirect(base_url('employee/login'));
        }
        
    }
    
    public function index() {
    
        $data['user_level'] = $this->session->level;
        
        if($this->session->level == 3) {
            
            $this->load->model('exam_model', '', true);
            $data['ip_hooked'] = $this->exam_model->getIfIPHooked();
        }
        
        if($this->session->level == 1 || $this->session->level == 7) {
            
            $tmp = $this->home_model->getPendingRTAs();
            if($tmp) {
                
                if($tmp > 0) {
                    
                    $msg = '<a href="' . xy_url('admin/rta/?pending') . '" style="font-family: Verdana"><u><b>' . $tmp . '</b></u> <u>pending</u></a>';
                } else $msg = '<b>' . $tmp . '</b> pending';
                
                $msg = 'There ' . (($tmp > 1) ? 'are' : 'is') . ' ' . $msg . ' RTA waiting for your approval.';
                
            }
            else { $msg = 'There\'s no pending RTA.'; }
            $data['rta_pending'] = $msg;
        }
        else
        if($this->session->level == 2 || $this->session->level == 7) {
            
            $tmp = $this->home_model->getPendingRTAs($this->session->id);
            if($tmp) {
                
                if($tmp > 0) {
                    
                    $msg = '<a href="' . xy_url('po/rta_by_owner/?pending') . '" style="font-family: Verdana"><u><b>' . $tmp . '</b></u> <u>pending</u></a> RTA still waiting for approval.';
                } else $msg = 'no pending RTA.';
                
                $msg = 'You have ' . $msg;
                
            }
            else { $msg = 'There\'s no pending RTA.'; }
            $data['rta_pending'] = $msg;
            
            $tmp = $this->home_model->getTotalAlerts($this->session->id);
            if($tmp) {
                
                if($tmp > 0) {
                    
                    $msg = '<a href="' . $this->config->config['XY']->DOCROOT . 'user/alert" style="font-family: Verdana"><u><b>' . $tmp . '</b></u> notification(s)</a>.';
                } else $msg = 'no Alert(s).';
                
                $msg = 'You have ' . $msg;
                
            }
            else { $msg = 'There\'s no current notification(s).'; }
            $data['alerts'] = $msg;            
        }
        //$data['calendar'] = $this->calendar->generate();
        
        //$this->load->vars($data);
        $data['content'] = $this->getViewFile(__FUNCTION__, $data); /* index */
        $this->load->view('main', $data);

        //$this->ci =& get_instance(); /* Access CI's native resources. */

    }
    
    public function async_admin_forward_ip() {
        
        /*$sensorium[1] = $sensorium[2] = array();
        
        for($x=1; $x<=15; $x++) { $sensorium[1][$x] = $sensorium[2][$x] = $x; }
        
        $sql = "
            SELECT  id
            FROM    stations
            WHERE   ip='" . $this->client->getIP() . "'";
        $query = $this->db->query($sql);
        if($query->num_rows()) {*/
        
        //if($this->session->id == 1) $this->client->setIP('192.168.0.2');
        
        $sql = "UPDATE stations SET state=1 WHERE ip='" . $this->client->getIP() . "' AND state=0";
        
        /*} else {
            
            $sql = "
                INSERT
                INTO    stations
                SET     state=1,
                        ip='" . $this->client->getIP() . "'";            
        }*/
        
        $this->db->query($sql);
        
        echo $sql;
    }
    
    public function error() {
        
        $this->load->view('error');
    }
}

/* End of file home.php */
/* Location: framework/application/controllers/home.php */