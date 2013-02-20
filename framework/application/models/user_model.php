<?php defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model {

    function __construct() {
        
        parent::__construct();
    }
    
    public function getAlerts($user_id, $page = 1) {
        
        $sql = "
        SELECT  a.*,
                rta.samples_name AS sample,
                u.username AS approved_by
        FROM    alerts a
        LEFT
        JOIN    rta_forms rta
        ON      a.rta_form_id=rta.id
        LEFT
        JOIN    users u
        ON      a.admin_id=u.id
        WHERE   a.user_id=" . $user_id . "
        ORDER
        BY      a.created
        DESC";
        
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
            if($uri[strlen($uri) - 1] != '/') $uri .= '/';
            
            for($x=1; $x<= $maxpage; $x++) {

                if($x == $page) $pages .= ', <b style="font-size: 18px">' . $x . '</b>';
                else $pages .= ', <a title="page ' . $x . '" href="' . $uri . ((substr_count($uri, '?') == 0) ? '?' : '&') . 'page=' . $x . '">' . $x . '</a>';
            }
            
            $response = array('Page: ' . substr($pages, 2), $query->result());
        }
        
        return $response;
    }
}

/* End of file user_model.php */
/* Location: ./application/models/user_model.php */