<?php defined('BASEPATH') or exit('No direct script access allowed');

/*
 * Title        : DOCUMENT Library Class
 * Author       : Armande Bayanes (tuso@programmerspride.com)
 * Description  : Which sets the document's "title", "description", "keywords", etc ...
 * Note         : Autoloaded (refer to: ./application/config/autoload.php)
 **/

class Document {
    
    private $ci;    
    private $xy;
    
    public function __construct() {
        
        $this->ci =& get_instance(); /* Access CI's native resources. */
        $this->xy = $this->ci->config->config['XY'];        
    }
    
    public function noJScript($argPageURL) {

        return '<noscript><meta http-equiv="Refresh" content="0;URL=\'' . $argPageURL . '\'" /></noscript>';
    }
    
    public function loadHead() {
        
        $config = $this->xy;
        
        $JS_VARS = $config->JS_VARS;
        
        $header = $this->noJScript('js.html') . "\n";
        $header .= '<title>' . $config->DOCUMENT->Title . '</title>' . "\n" .
        (($config->DOCUMENT->Keywords != '') ? ('<meta name="keywords" content="' . $config->DOCUMENT->Keywords . '" />' . "\n") : '') .
        (($config->DOCUMENT->Description != '') ? ('<meta name="description" content="' . $config->DOCUMENT->Description . '" />' . "\n") : '');
        
        if($config->ICON != "") {

            $header .= '<link rel="icon" href="' . $config->DOCROOT . 'media/' . $config->ICON . '.ico" type="image/x-icon" />' . "\n" .
            '<link rel="shortcut icon" href="' . $config->DOCROOT . 'media/' . $config->ICON . '.ico" type="image/x-icon" />' . "\n";
        }
        
        $config = $this->xy;
        
        $JS_VARS = $config->JS_VARS;
        
        $header = $this->noJScript('js.html') . "\n";
        $header .= '<title>' . $config->DOCUMENT->Title . '</title>' . "\n" .
        (($config->DOCUMENT->Keywords != '') ? ('<meta name="keywords" content="' . $config->DOCUMENT->Keywords . '" />' . "\n") : '') .
        (($config->DOCUMENT->Description != '') ? ('<meta name="description" content="' . $config->DOCUMENT->Description . '" />' . "\n") : '');
        
        if($config->ICON != "") {

            $header .= '<link rel="icon" href="' . $config->DOCROOT . 'media/' . $config->ICON . '.ico" type="image/x-icon" />' . "\n" .
            '<link rel="shortcut icon" href="' . $config->DOCROOT . 'media/' . $config->ICON . '.ico" type="image/x-icon" />' . "\n";
        }
        
        $css = '';
        if(($t = count($config->CSS)) > 0) {

            for($x=0; $x<$t; $x++) {
                
                $css .= "\n@import url(\"" . $config->DOCROOT . $config->CSS[$x] . "\");";
            }
        }
        
        if($css != '') {

            $css = '<style type="text/css">' . $css . "\n" . '</style>' . "\n";
            $header .= $css;
        }
        
        if(! empty($JS_VARS)) {

            $js_vars1 = '';
            $js_vars2 = '';
            
            foreach($JS_VARS as $key => $value) {
                
                if(! is_numeric($value)) {
                    
                    if(substr_count($value, "'") > 2) {
                        
                        $value_tmp = substr($value, 1, strlen($value) - 2);
                        $value = '"' . $value_tmp . '"';
                    }
                }
                
                if(substr_count($key, '[position=1]') > 0) {
                    
                    $key = str_replace('[position=1]', '', $key);
                    
                    /* Special case to remove "var" on variable declarations. */
                    if(substr_count($key, '[var=false]') > 0) $js_vars1 .= "\n" . str_replace('[var=false]', '', $key) . ' = ' . $value . ';';
                    else $js_vars1 .= "\n" . 'var ' . $key . ' = ' . $value . ';';
                }
                else {
                    
                    /* Special case to remove "var" on variable declarations. */
                    if(substr_count($key, '[var=false]') > 0) $js_vars2 .= "\n" . str_replace('[var=false]', '', $key) . ' = ' . $value . ';';
                    else $js_vars2 .= "\n" . 'var ' . $key . ' = ' . $value . ';';
                
                }                
            }

            if($js_vars1 != '') $header .= '<script type="text/javascript">' . $js_vars1 . "\n" . '</script>'. "\n";
        }
        
        if(($t = count($config->JS)) > 0) {

            for($x=0; $x<$t; $x++) {
                
                $path = $config->JS[$x];

                /* No need to check when file exists since there are cases
                 * that we need to load JS files with parameters such as:
                 * 
                 * i.e. ../scripts/scriptaculous.js?load=effects,builder,dragdrop
                 **/
                
                $header .=  '<script type="text/javascript" src="' . $config->DOCROOT . $config->JS[$x] . '"></script>' . "\n";                
            }
        }
        
        if($js_vars2 != '') $header .= '<script type="text/javascript">' . $js_vars2 . "\n" . '</script>'. "\n";
        
        if(($t = count($config->JS_FUNC)) > 0) {

            for($x=0; $x<$t; $x++) {
                
                $header .=  '<script type="text/javascript">' . $config->JS_FUNC[$x] . '</script>' . "\n";                
            }
        }
        
        echo $header;
    }
    
    public function loadPlugin($plugin_name, $plugin_path = false) {
        
        static $ctr = 0;
        $path = APPPATH . 'third_party/' . $plugin_name;

        if(is_dir($path)) {

            /* Create a globally accessible JS variable for the Plugin's path.
             * i.e. PLUGIN_PATH['birthdate'] = 'framework/application/third_party/birthdate/';
             **/
            
            if($plugin_path) {

                if($ctr == 0) $this->ci->config->config['XY']->JS_VARS["[position=1]PLUGIN_PATH"] =  'new Array()';
                $this->ci->config->config['XY']->JS_VARS["[var=false][position=1]PLUGIN_PATH['" . strtolower($plugin_name) . "']"] =  "'" . $path . "/'";
                
                $ctr++;
            }

            $this->loadPluginGlobals($path);
        }
    }
    
    /* Get and initialize globally loadable files such as JS and CSS files
     * inside the Plugin directory.
     **/
    private function loadPluginGlobals($directory) {
        
        if(is_dir($directory)) {

            foreach(scandir($directory) as $file) {

                if($file == "." || $file == "..") {
                    
                    /* Continue to the next loop. Don't include "." and ".." in the scan since it will just loops forever. */
                    continue;
                }

                $filename = $directory . "/" . $file;

                if(is_dir($filename)) {

                    $this->loadPluginGlobals($filename);

                } else {

                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    if(in_array($ext, array('css', 'js')) && basename($filename) != 'index.php') {

                        if($ext == 'css') $this->ci->config->config['XY']->CSS[] = $filename;
                        elseif($ext == 'js') $this->ci->config->config['XY']->JS[] = $filename;
                        //elseif($ext == 'php' && $argIncPHP) require_once $filename;
                    }
                }
            } /* foreach(scandir($directory) as $file) */
        } /* if(is_dir($directory)) */
    }
}

/* End of file Document.php */
/* Location: ./application/libraries/Document.php */