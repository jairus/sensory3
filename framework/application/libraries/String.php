<?php
/*
 * Title        : STRING Library Class
 * Author       : Armande Bayanes (tuso@programmerspride.com)
 * Description  : String formatting and Input validation functions.
 **/

Class String {
    
    public function getAlphanumericOnly($string, $space = true) {

        $string = trim($string);
        $string = preg_replace('/[^a-zA-Z0-9\- ]/', '', $string);
        
        if(! $space) {
            
            /* Replace space with "_". */
            $string = str_replace(' ', '_', $string);
        }
        
        return $string;
    }
}

/* End of file String.php */
/* Location: ./application/libraries/String.php */