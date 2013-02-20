<?php defined('BASEPATH') or exit('No direct script access allowed');

/*
 * Title        : XY_CONTROLLER Core Class
 * Author       : Armande Bayanes (tuso@programmerspride.com)
 * Description  : Collection of methods needed to be accesed globally.
 * Note         : Autoloaded (refer to the bottom of ./application/config/config.php)
 **/

class XY_Controller extends CI_Controller {
    
    public $session;
    public $configXY;
    
    function __construct() {
        
        parent::__construct();
        
        $this->session = xy_current_user();
        $this->configXY = $this->config->item('XY');
        
        $this->load->helper('url');
        
        $this->configXY->JS[] = 'media/js/purl.js';
        
        if(! is_object($this->document)) { return; }
        
        $this->document->loadPlugin('jquery.ui.1.8');
        $this->document->loadPlugin('popupjs', true);
    }
    
    protected function getViewFile($function_name, $data = array()) {
        
        if(empty($function_name)) return;
        
        /* Set "main.php" for index and not "index.php". */
        if($function_name == 'index') $function_name = 'main';

        if(empty($this->configXY->JS_VARS['SECTION'])) $this->config->config['XY']->JS_VARS['SECTION'] = "'" . $function_name . "'";
        $blankGIF = $this->config->config['XY']->DOCROOT . 'media/images/blank.gif';
        $this->config->config['XY']->JS_VARS['blankGIF'] = "'" . $blankGIF . "'";
        
        /* Get the name of the Class in this File. */        
        $class = strtolower(get_class($this));
        
        $path = APPPATH . 'views/' . $class . '/';
        
        /* Load general CSS found for current View. */
        if(file_exists($path . $class . '.css')) {
            $this->config->config['XY']->CSS[] = $path . $class . '.css';
        }
        
        /* Load CSS found for the specific and current View. */
        if(file_exists($path . $function_name . '.css')) {
            $this->config->config['XY']->CSS[] = $path . $function_name . '.css';
        }
        
        /* Load general JS found for the current View. */
        if(file_exists($path . $class . '.js')) {
            $this->config->config['XY']->JS[] = $path . $class . '.js';
        }
        
        /* Load JS found for the specific and current View. */
        if(file_exists($path . $function_name . '.js')) {
            $this->config->config['XY']->JS[] = $path . $function_name . '.js';
        }
        
        if(! empty($data)) $this->load->vars($data);
        
        /* Load the content of File as String. */
        return $this->load->file($path . $function_name . '.php', true);
    }
    
    protected function securePage() {
        
        if(! isset($_SERVER['HTTP_REFERER']) || ! isset($_SERVER['HTTP_HOST'])) {
            exit('Secured ...');
        }
        
        /* Makes sure request made on the same host. */
        if(strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) <= -1) {
            exit('Secured ...');
        }
    }
    
    public function _404_() {
        
        echo 'You requested a page that cannot be accessed.';
    }
    
    protected function send_mail($subject, $message, $email) {
        
        if($subject == '' || $message == '' || $email == '')
            return;
        
        $target_url = 'http://sensory.nmgdev.com/mailer/async_sendmail';
        $variables = 'subject=' . $subject . '&message=' . $message . '&email=' . email;
        
        $ch = curl_init($target_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS , $variables);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
        curl_setopt($ch, CURLOPT_HEADER, 0); /* DO NOT RETURN HTTP HEADERS */
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  /* RETURN THE CONTENTS OF THE CALL */
        $response = curl_exec($ch);
        curl_close($ch);
        
        return $response;
        
        /*ob_start();
        header("Content-Type: text/html");
        $Temp_Output = ltrim(rtrim(trim(strip_tags(trim(preg_replace ( "/\s\s+/" , " " , html_entity_decode($Rec_Data)))),"\n\t\r\h\v\0 ")), "%20");
        $Temp_Output = ereg_replace (' +', ' ', trim($Temp_Output));
        $Temp_Output = ereg_replace("[\r\t\n]","",$Temp_Output);
        $Temp_Output = substr($Temp_Output,307,200);
        echo $Temp_Output;
        $Final_Out=ob_get_clean();
        echo $Final_Out;  
        curl_close($ch);*/
        
        //} else die('Hacking attempt Logged!');
    }
}
