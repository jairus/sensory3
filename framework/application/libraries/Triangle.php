<?php
/*
 * Title        : TRIANGLE Library Class
 * Author       : Armande Bayanes (tuso@programmerspride.com)
 * Description  : 
 **/

class Triangle {
    
    public function generate($code_arr, $panelist) {
        
        $patterns = array('BAA', 'ABA', 'AAB', 'ABB', 'BAB', 'BBA');
        
        list($code_1, $code_2) = $code_arr;
        
        $tmp = array();
        foreach($patterns as $pattern) { $tmp =  array_merge($tmp, $this->pattern($pattern, $code_1, $code_2)); }
        $patterns = $tmp;
        
        $pattern_ctr = 0;
        $pattern_total = count($patterns);
        $seat = array();

        for($x=1; $x<=$panelist; $x++) {

            if($pattern_ctr < $pattern_total) $pattern_ctr++;
            else $pattern_ctr = 1;

            $seat[$x] = $patterns[$pattern_ctr - 1];
        }
        
        return $seat;
        /*
        $seat = array();
        for($x=0; $x<24; $x++) { $seat[] = $patterns[$x]; }
        
        return $seat;*/
    }
    
    public function pattern($pattern, $code_1, $code_2) {
        
        if($pattern == 'BAA') {

            $c[1] = $code_1[1] . ' ' . $code_1[0] . ' ' . $code_2[0];
            $c[2] = $code_2[1] . ' ' . $code_2[0] . ' ' . $code_1[0];

            $c[3] = $code_1[1] . ' ' . $code_2[0] . ' ' . $code_1[0];
            $c[4] = $code_2[1] . ' ' . $code_1[0] . ' ' . $code_2[0];
        }
        else
        if($pattern == 'ABA') {

            $c[1] = $code_1[0] . ' ' . $code_1[1] . ' ' . $code_2[0];
            $c[2] = $code_2[0] . ' ' . $code_2[1] . ' ' . $code_1[0];

            $c[3] = $code_2[0] . ' ' . $code_1[1] . ' ' . $code_1[0];
            $c[4] = $code_1[0] . ' ' . $code_2[1] . ' ' . $code_2[0];
        }
        else
        if($pattern == 'AAB') {

            $c[1] = $code_1[0] . ' ' . $code_2[0] . ' ' . $code_1[1];
            $c[2] = $code_2[0] . ' ' . $code_1[0] . ' ' . $code_1[1];

            $c[3] = $code_2[0] . ' ' . $code_1[0] . ' ' . $code_2[1];
            $c[4] = $code_1[0] . ' ' . $code_2[0] . ' ' . $code_2[1];
        }
        else
        if($pattern == 'ABB') {

            $c[1] = $code_1[0] . ' ' . $code_1[1] . ' ' . $code_2[1];
            $c[2] = $code_2[0] . ' ' . $code_2[1] . ' ' . $code_1[1];

            $c[3] = $code_1[0] . ' ' . $code_2[1] . ' ' . $code_1[1];
            $c[4] = $code_2[0] . ' ' . $code_1[1] . ' ' . $code_2[1];
        }
        else
        if($pattern == 'BAB') {

            $c[1] = $code_1[1] . ' ' . $code_1[0] . ' ' . $code_2[1];
            $c[2] = $code_2[1] . ' ' . $code_2[0] . ' ' . $code_1[1];

            $c[3] = $code_2[1] . ' ' . $code_1[0] . ' ' . $code_1[1];
            $c[4] = $code_1[1] . ' ' . $code_2[0] . ' ' . $code_2[1];
        }
        else
        if($pattern == 'BBA') {

            $c[1] = $code_1[1] . ' ' . $code_2[1] . ' ' . $code_1[0];
            $c[2] = $code_2[1] . ' ' . $code_1[1] . ' ' . $code_2[0];

            $c[3] = $code_2[1] . ' ' . $code_1[1] . ' ' . $code_1[0];
            $c[4] = $code_1[1] . ' ' . $code_2[1] . ' ' . $code_2[0];
        }

        return $c;
    }
}

/* End of file Triangle.php */
/* Location: ./application/libraries/Triangle.php */