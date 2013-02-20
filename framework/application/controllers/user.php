<?php defined('BASEPATH') or exit('No direct script access allowed');

/* 
 * Title : User Controller Class
 * Author: Armande Bayanes (tuso@programmerspride.com)
 * Description: 
 **/

class User extends XY_Controller {
    
    function __construct() {
        
        parent::__construct();

        $this->load->model('user_model', '', true);
        
        /* If NOT logged-in. */
        if(empty($this->session)) {
            redirect(base_url('avail/login'));
        }
        
        /* Check if NOT Admin. */
        /*if(! empty($this->session)) {
            
            if($this->session->level != 1) {
                
                redirect(base_url('home/error'));
                return;        
            }
        }*/
    }
    
    public function alert() {
        
        $page = (int) $this->configXY->URI['page'];
        
        $sql = "UPDATE alerts SET new=0 WHERE user_id=" . $this->session->id . " AND new=1";
        $this->db->query($sql);
        
        $tmp = $this->user_model->getAlerts($this->session->id, $page);
        if(! empty($tmp)) {
            
            list($data['paging'], $data['alerts']) = $tmp;
        }
        
        $data['content'] = $this->getViewFile(__FUNCTION__, $data);
        $this->load->view('main', $data);
    }
}

/* End of file user.php */
/* Location: framework/application/controllers/user.php */