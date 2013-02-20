<?php defined('BASEPATH') or exit('No direct script access allowed');

class Home_model extends CI_Model {
    
    function __construct() {

        parent::__construct();        
    }
    
    public function getPendingRTAs($user_id = 0) {
        
        if($user_id > 0) $sql_append = " AND requested_by_id=" . $user_id;
        $sql = "SELECT * FROM rta_forms WHERE state=3" . $sql_append;
        $query = $this->db->query($sql);
        return $query->num_rows();
    }
    
    public function getTotalAlerts($user_id) {
        
        $sql = "SELECT * FROM alerts WHERE user_id=" . $user_id . " AND new=1";
        $query = $this->db->query($sql);
        return $query->num_rows();
    }
}

/* End of file home_model.php */
/* Location: framework/application/models/home_model.php */