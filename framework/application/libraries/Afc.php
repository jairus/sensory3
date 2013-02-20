<?php
/*
 * Title        : AFC Library Class
 * Author       : Armande Bayanes (tuso@programmerspride.com)
 * Description  : 
 **/

class Afc {
    
    private $patterns = array('BAA', 'ABA', 'AAB');
    
    function generate($controls, $experiments, $panelists) {
        
        if($panelists == 0) return;
        
        $code_arr = $controls;
        
        $size = count($code_arr) - 1;
        $perm = range(0, $size);
        $j = 0;
        $permutations = $tmp = array();
        $y = 1;
        
        do { 
             foreach ($perm as $i) { $permutations[$j][] = $code_arr[$i]; }
        } while ($perm = $this->swap($perm, $size) and ++$j);
        
        foreach($this->patterns as $pattern) {
    
            for($x=1; $x<=4; $x++) {

                if($pattern == 'BAA') {

                    $tmp[] = $experiments[$y - 1] . ' ' . implode(' ', $permutations[$y - 1]);
                }
                else
                if($pattern == 'ABA') {

                    if($x < 3) {

                        $tmp[] = $permutations[$y - 1][0] . ' ' . $experiments[$y - 1] . ' ' . $permutations[$y - 1][1];

                    } else {

                        $tmp[] = $permutations[$y - 1][1] . ' ' . $experiments[$y - 1] . ' ' . $permutations[$y - 1][0];
                    }
                }
                else
                if($pattern == 'AAB') {

                    if($x < 3) {

                        $tmp[] = implode(' ', $permutations[$y - 1]) . ' ' . $experiments[$y - 1];
                    } else {

                        $perm = $permutations[$y - 1];
                        $tmp[] = $perm[1] . ' ' . $perm[0] . ' ' . $experiments[$y - 1];
                    }
                }

                if($y >= 2) $y = 1;
                else $y++;
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

/* End of file Afc.php */
/* Location: ./application/libraries/Afc.php */