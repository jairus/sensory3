<?php defined('BASEPATH') or exit('No direct script access allowed');

/* 
 * Title : Non-employee Controller Class
 * Author: Armande Bayanes (tuso@programmerspride.com)
 * Description: Handles Logging-in, etc ...
 **/

class Ne extends XY_Controller {
    
    function __construct() {
        
        parent::__construct();
        
        $this->load->model('ne_model', '', TRUE);
        
        $try = array('login', 'async_login');
        
        /* Check if NOT logged-in. */
        if(empty($this->session)) {
            
            if(! in_array($this->uri->segment(2), $try)) {
                redirect(base_url('ne/login'));
            }
        }
        
        /* If already logged-in. */
        if(! empty($this->session)) {

            if(in_array($this->uri->segment(2), $try)) {
                redirect(base_url('home'));
            }            
        }
    }
    
    public function login() {
        
        $data['blankGIF'] = xy_url('media/images/blank.gif');
        
        $data['content'] = $this->getViewFile(__FUNCTION__, $data);
        $this->load->view('main', $data);
    }
    
    public function async_login() {
        
        /* Secure this page from unauthorized remote access. */
        $this->securePage();
        
        if(empty($_POST)) exit('No data.');
        $input = $_POST;
        unset($input['t']);
        
        $this->ne_model->doLogIn($input);
    }
}

/* End of file ne.php */
/* Location: framework/application/controllers/ne.php */