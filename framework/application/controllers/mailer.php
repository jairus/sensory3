<?php defined('BASEPATH') or exit('No direct script access allowed')?>

<?php
/* 
 * Title : Mailer Controller Class
 * Author: Armande Bayanes (tuso@programmerspride.com)
 * Description: Handles Logging-in and Registration, etc ...
 **/

class Mailer extends XY_Controller {
    
    function __construct() {
        
        parent::__construct();
        
    }
    
    public function async_sendmail() {
        
        if(empty($_POST) || empty($this->session)) exit();
        
        $input = $_POST;
        unset($input['t']);
        
        array_walk($input, 'xy_input_clean_up_byref');
        extract($input);
        
        if($subject == '' || $message == '' || $email == '') exit();
        
        $config = Array(
            //'protocol' => 'smtp',
            //'smtp_host' => 'smtp.com',
            //'smtp_port' => 25,
            //'smtp_user' => $sender_email,
            //'smtp_pass' => 'apsmt11',
            'mailtype' => 'html'
        );
        
        //$email = 'tuso@programmerspride.com';
        //$subject = 'RTA Filed';
        //$message = 'This is a success message ...';
        
        $this->load->library('email', $config);
        $this->email->from('armande@nmgresources.ph', 'Sensory Software');
            
        $this->email->to($email);
        $this->email->subject($subject);
        $this->email->message($message);
        $this->email->send();
        echo $this->email->print_debugger();
    }
}

/* End of file mailer.php */
/* Location: framework/application/controllers/mailer.php */