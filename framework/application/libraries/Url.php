<?php defined('BASEPATH') or exit('No direct script access allowed');

/*
 * Title        : URL Library Class
 * Author       : Armande Bayanes (tuso@programmerspride.com)
 * Description  : Carefully parse mixed URLs such as: http://domain.com/class_controller/method/?var1=value1&var2=value2
 * Note         : Autoloaded (refer to: ./application/config/autoload.php)
 **/

class URL {
    
    private $ci;    
    private $xy;
    private $native = false;
    
    public function __construct() {
        
        $this->ci =& get_instance(); /* Access CI's native resources. */
        $this->xy = $this->ci->config->config['XY'];
        
        $uri = str_replace($this->xy->DOCROOT, '/', $_SERVER['REQUEST_URI']);
        $script = str_replace($this->xy->DOCROOT, '/', $_SERVER['SCRIPT_NAME']);

        $uri = explode('/', $uri);
        $script = explode('/', $script);

        /*
         * Remove the SCRIPT NAME from the URI so it will only
         *      contain the passed parameters.
         */

        $tmp = array_diff($uri, $script);

        # Check URL format.
        $index = 1;
        if((! empty($tmp[$index])) &&
            substr_count($tmp[$index], '?')) { # If the native URL format is still preferred.

            $this->ci->config->config['XY']->URI = $this->getURI($tmp[$index]);
            $this->native = true;
        } else {

            # Reset indeces.
            foreach($tmp as $value) {

                # Find and retrieve other native commands that are added (somewhere) in the URL.
                if( substr_count($value, '?')) {

                    $this->loadURI($value);
                    
                } else $this->ci->config->config['XY']->URI[] = $value;
            }
        }
    }
    
    private function getURI($uri) {
        
        /* Removes everything else until '?'. */
        $tmp = substr($uri, strpos($uri, '?') + 1);
        
        if(substr_count($tmp, '=') == 0) return array();        
        parse_str($tmp, $uri);

        return $uri;        
    }
    
    private function loadURI($uri) {
        
        $this->ci->config->config['XY']->URI = array_merge($this->ci->config->config['XY']->URI, $this->getURI($uri));
    }
}

/* End of file Url.php */
/* Location: ./application/libraries/Url.php */