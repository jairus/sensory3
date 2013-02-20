<?php
/*
 * Title        : LOGGER Library Class
 * Author       : Armande Bayanes (tuso@programmerspride.com)
 * Description  : Where User's Session and Cookie are set. Tracking and logging of Users take place.
 **/

class Logger {
    
    private $ci;
    private $xy;
    
    function  __construct() {
        
        $this->ci =& get_instance();
        $this->xy = $this->ci->config->config['XY'];

        # Check if there is an active cookie.
        if(isset($_COOKIE['UserLOGSession' . $this->xy->SESSIONID]) && empty($_SESSION['UserLOGID' . $this->xy->SESSIONID])) {

            # Get id of who is currently in the active session.
            $sql = "
            SELECT  `id`
            FROM    `users`
            WHERE   `session`='" . $_COOKIE['UserLOGSession' . $this->xy->SESSIONID] . "'";
            $query = $this->ci->db->query($sql);
            $user = $query->row();

            if(! empty($user)) $_SESSION['UserLOGID' . $this->xy->SESSIONID] = $user->id;
            else header('Location: ' . $this->xy->DOCROOT . 'home/logout');
        }

        # Logged user information
        if(! empty($_SESSION['UserLOGID' . $this->xy->SESSIONID])) {

            $userid = intval($_SESSION['UserLOGID' . $this->xy->SESSIONID]);

            if(empty($_SESSION['UserLoggedDetails' . $this->xy->SESSIONID])) {

                # initialize user data
                $query = $this->ci->db->query("SELECT * FROM `users` WHERE `id`=" . $userid);
                $r = $query->row();

                if(! empty($r)) {

                    $_SESSION['UserLoggedDetails' . $this->xy->SESSIONID] = $r;
                }
            }

            if(! empty($_SESSION['UserLoggedDetails' . $this->xy->SESSIONID])) {

                $sql = "
                SELECT  `id`,
                        `created`
                FROM    `user_logs`
                WHERE   `user_id`='" . $userid . "'
                AND     `user_ip`='" . $this->ci->client->getIP() . "'
                AND     `browser`='" . $this->ci->client->getUA() . "'
                AND     DATE_FORMAT(`created`, '%Y-%m-%d')='" . $this->xy->TODAY . "'";

                $query = $this->ci->db->query($sql);
                $r = $query->row();

                if(! empty($r)) {

                    $log_time = date('Y-m-d H:i:s', strtotime($r->created));
                    $curr_time = date('Y-m-d H:i:s', strtotime($this->xy->DATE) - (60 * 5)); # Less 5 minutes.

                    if($log_time <= $curr_time) { # Make 5 minutes to pass first before updating.
                        
                        $sql = "
                        UPDATE  `user_logs`
                        SET     `created`='" . $this->xy->DATE . "'
                        WHERE   `id`='" . $r->id . "'";
                    }

                # Special case on BROWSER UPDATES but still has an ACTIVE COOKIE
                } else {

                    $sql = "
                    INSERT
                    INTO    `user_logs`
                    SET     `user_id`='" . $userid . "',
                            `logged`=1,
                            `user_ip`='" . $this->ci->client->getIP() . "',
                            `browser`='" . $this->ci->client->getUA() . "',
                            `created`='" . $this->xy->DATE . "'";
                }

                $query = $this->ci->db->query($sql);
            }
        }

        # SESSION LOGGER: If session has expired or cookie deleted or no active cookie / session.
        if(empty($_COOKIE['UserLOGSession' . $this->xy->SESSIONID]) && empty($_SESSION['UserLOGID' . $this->xy->SESSIONID])) {

            # $TODAY is defined @ globals.php
            # check if an empty log of the client was already saved on the current date
            $sql = "
            SELECT  *
            FROM    `user_logs`
            WHERE   (`user_id`=0 OR `logged`=0)
            AND     `user_ip`='" . $this->ci->client->getIP() . "'
            AND     `browser`='" . $this->ci->client->getUA() . "'
            AND     DATE_FORMAT(`created`, '%Y-%m-%d')='" . $this->xy->TODAY . "'";

            $query = $this->ci->db->query($sql);
            $r = $query->row();

            if(empty($r)) {

                # Log this current Client / User browsing the Site
                $sql = "
                INSERT
                INTO	`user_logs`
                SET     `user_ip`='" . $this->ci->client->getIP() . "',
                        `browser`='" . $this->ci->client->getUA() . "',
                        `created`='" . $this->xy->DATE . "'";
                $query = $this->ci->db->query($sql);

            }
            else {

                /* Updates the time of Client / User's each browse of the Site
                   NOTE : important to see if Client / User is still active
                */

                $log_time = date('Y-m-d H:i:s', strtotime($r->created));
                $curr_time = date('Y-m-d H:i:s', strtotime($this->xy->DATE) - (60 * 5)); # Less 5 minutes.

                if($log_time <= $curr_time) { # Make 5 minutes to pass first before updating.

                    $sql = "
                    UPDATE  `user_logs`
                    SET     `created`='" . $this->xy->DATE . "'
                    WHERE   (`user_id`=0 OR `logged`=0)
                    AND     `user_ip`='" . $this->ci->client->getIP() . "'
                    AND     `browser`='" . $this->ci->client->getUA() . "'
                    AND     DATE_FORMAT(`created`, '%Y-%m-%d')='" . $this->xy->TODAY . "'";
                    $query = $this->ci->db->query($sql);
                }
            }

        } else {

            # If logs in to 2 browsers, then just take the latest and logs out the old
            if($_SESSION['UserLoggedDetails' . $this->xy->SESSIONID]->session != $_COOKIE['UserLOGSession' . $this->xy->SESSIONID]) {
                header('Location: ' . $this->xy->DOCROOT . 'home/logout');
            }
        }
    }
}

/* End of file Logger.php */
/* Location: ./application/libraries/Logger.php */