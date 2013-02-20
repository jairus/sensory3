<?php
/*
 * Title        : CLIENT Library Class
 * Author       : Armande Bayanes (tuso@programmerspride.com)
 * Description  : Get the User's IP and Browser.
 **/

class Client {

    private $ip;
    private $ua;

    function  __construct() {

        if(isset($_SERVER)) {

            if(isset($_SERVER['HTTP_CLIENT_IP'])){
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            }
            elseif(isset($_SERVER['HTTP_FORWARDED_FOR'])){
                $ip = $_SERVER['HTTP_FORWARDED_FOR'];
            }
            elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

            } else $ip = $_SERVER['REMOTE_ADDR'];

        } else {

            if(getenv( 'HTTP_CLIENT_IP')) {
                $ip = getenv( 'HTTP_CLIENT_IP' );
            }
            elseif (getenv('HTTP_FORWARDED_FOR')) {
                $ip = getenv('HTTP_FORWARDED_FOR');
            }
            elseif (getenv('HTTP_X_FORWARDED_FOR')) {
                $ip = getenv('HTTP_X_FORWARDED_FOR');

            } else $ip = getenv('REMOTE_ADDR');

        }

        if(stristr($_SERVER['HTTP_USER_AGENT'], 'Opera Mini')) {

            if(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])) $browser = addslashes(strip_tags($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']));
            else $browser = addslashes(strip_tags($_SERVER['HTTP_USER_AGENT']));

        } else $browser = addslashes(strip_tags($_SERVER['HTTP_USER_AGENT']));

        $this->ip = $ip;
        $this->ua = $browser;
    }
    
    public function setIP($ip) {
        
        if(empty($ip))
            return;
        
        $this->ip = $ip;
    }
    
    public function getIP() {

        return $this->ip;
    }

    public function getUA() {

        return $this->ua;
    }
}

/* End of file Client.php */
/* Location: ./application/libraries/Client.php */