<?php
/*
 * Title        : 2-AFC Library Class
 * Author       : Armande Bayanes (tuso@programmerspride.com)
 * Description  : 
 * */

class _2Afc {
    
    private $patterns = array('AB', 'BA');
    
    function generate($panelists, $code_1, $code_2) {
        
        if($panelists == 0) return;
        
        $a = array($code_1[0], $code_2[0]);
        $b = array($code_1[1], $code_2[1]);
        
        $tmp = array();
        
        foreach($this->patterns as $pattern) {
            
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
    
    function swap($p, $size) {
        
        /* Slide down the array looking for where we're smaller than the next guy. */
        for ($i = $size - 1; $p[$i] >= $p[$i+1]; --$i) { }

        /* If this doesn't occur, we've finished our permutations
         * the array is reversed: (1, 2, 3, 4) => (4, 3, 2, 1).
         **/
        if ($i == -1) { return false; }

        /* Slide down the array looking for a bigger number than what we found before. */
        for ($j = $size; $p[$j] <= $p[$i]; --$j) { }

        /* Swap them. */
        $tmp = $p[$i]; $p[$i] = $p[$j]; $p[$j] = $tmp;

        /* Now reverse the elements in between by swapping the ends. */
        for (++$i, $j = $size; $i < $j; ++$i, --$j) {
             $tmp = $p[$i]; $p[$i] = $p[$j]; $p[$j] = $tmp;
        }

        return $p;
    }
}

/* End of file _2Afc.php */
/* Location: ./application/libraries/_2Afc.php */