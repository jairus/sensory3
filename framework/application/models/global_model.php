<?php defined('BASEPATH') or exit('No direct script access allowed');

/* 
 * Title : Global Model Class
 * Author: Armande Bayanes (tuso@programmerspride.com)
 * Description: Handles Logging-in and Registration, etc ...
 **/

class Global_model extends CI_Model {
	
    public function __construct() {
        
        parent::__construct ();
    }
    
    public function doLogIn($query) {
        
        if(! $query)
            return;
        
        $row = $query->row();
        $config = $this->config->item('XY');

        $session = base64_encode($row->id . "|" . $config->TODAY . "|" . $this->client->getIP() . "|" . $this->client->getUA());
        setcookie("UserLOGSession" . $config->SESSIONID, $session, strtotime($config->DATE) + 31536000, "/");

        $sql = "
        UPDATE  `users`
        SET     `session`='" . $session . "',
                `ip_now`='" . $this->client->getIP() . "'
        WHERE   `id`='" . $row->id . "'";
        $this->db->query($sql);

        $_SESSION['UserLOGID' . $config->SESSIONID] = $row->id;

        $sql = "
        SELECT  `id`
        FROM    `user_logs`
        WHERE   (`user_id`=0 OR `user_id`='" . $row->id . "')
        AND     `user_ip`='" . $this->client->getIP() . "'
        AND     `browser`='" . $this->client->getUA() . "'
        AND     DATE_FORMAT(`created`, '%Y-%m-%d')='" . $config->TODAY . "'";
        $query = $this->db->query($sql);
        $r = $query->row(); # If no blank / 0 user id then re-use similar id from the previous log.

        if(! empty($r)) {

            $sql = "
            UPDATE  `user_logs`
            SET     `user_id`='" . $row->id . "',
                    `logged`=1,
                    `created`='" . $config->DATE . "'
            WHERE   `id`='" . $r->id . "'";
            $this->db->query($sql);
        }
    }
    
    public function doLogOut() {
        
        $config = $this->config->config['XY'];
        
        //if(isset($_SESSION['UserLOGID' . $config->SESSIONID])) {

            $session = base64_decode($_COOKIE['UserLOGSession' . $config->SESSIONID]);
            $session = explode("|", $session); # get the log-in date of current active session

            $sql = "
                UPDATE  `user_logs`
                SET     `logged`=0
                WHERE   `user_id`=" . intval($_SESSION['UserLOGID' . $config->SESSIONID]) . "
                AND     `user_ip`='" . $this->client->getIP() . "'
                AND     `browser`='" . $this->client->getUA() . "'
                AND     DATE_FORMAT(`created`, '%Y-%m-%d')='" . $session[1] . "'
                ORDER
                BY      `id`
                DESC";
            $this->db->query($sql);

            setcookie("UserLOGSession" . $config->SESSIONID, "", strtotime($config->DATE) - 31536000, "/");
            unset($_SESSION['UserLOGID' . $config->SESSIONID]);
            unset($_SESSION['UserLoggedDetails' . $config->SESSIONID]);
        //}
    }
    
    public function getStations() {
        
        $sql = "SELECT * FROM stations";
        $query = $this->db->query($sql);
        if($query->num_rows()) {
            
        }
    }
}        