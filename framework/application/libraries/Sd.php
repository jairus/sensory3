<?php
/*
 * Title        : Same/Different Library Class
 * Author       : Armande Bayanes (tuso@programmerspride.com)
 * Description  : 
 * */

class Sd {
    
    private $patterns = array('AA', 'BB', 'AB', 'BA');
    
    function generate($panelists, $code_1, $code_2) {
        
        if($panelists == 0) return;
        
        $a = array($code_1[0], $code_2[0]);
        $b = array($code_1[1], $code_2[1]);
        
        $tmp = array();
        
        foreach($this->patterns as $pattern) {
            
            if($pattern == 'AA') $tmp[] = $a[0] . ' ' . $a[1];
            elseif($pattern == 'BB') $tmp[] = $b[0] . ' ' . $b[1];
            else
            if($pattern == 'AB') {
                
                $tmp[] = $a[0] . ' ' . $b[0];
                $tmp[] = $a[0] . ' ' . $b[1];
                $tmp[] = $a[1] . ' ' . $b[0];
                $tmp[] = $a[1] . ' ' . $b[1];
            }
            else
            if($pattern == 'BA') {
                
                $tmp[] = $b[0] . ' ' . $a[0];
                $tmp[] = $b[0] . ' ' . $a[1];
                $tmp[] = $b[1] . ' ' . $a[0];
                $tmp[] = $b[1] . ' ' . $a[1];
            }            
        }
        
        return $this->seat_distribution($tmp, $panelists);
    }
    
    function seat_distribution($combinations, $panelists) {
        
        $combination_total = count($combinations);
        $combination_ctr = 0;
        
        $seat = array();
        for($x=1; $x<=$panelists; $x++) {

            if($combination_ctr < $combination_total) $combination_ctr++;
            else $combination_ctr = 1;
            
            $seat[$x] = $combinations[$combination_ctr - 1];
        }
        
        return $seat;
    }
}

/* End of file Problema.php */
/* Location: ./application/libraries/Problema.php */