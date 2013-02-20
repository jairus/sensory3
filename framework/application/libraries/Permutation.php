<?php
/*
 * Title        : PERMUTATION Library Class
 * Author       : Armande Bayanes (tuso@programmerspride.com)
 * Description  : 
 **/

class Permutation {
    
    /*
     * Jollibee Sensory
     * The number of products is equal to the total number of codes. So no need to pass it.
     **/
    
    function generate($code_arr, $panelists) {
        
        $size = count($code_arr) - 1;
        if($size == 0) {
            
            $permutations[0][] = $code_arr[0];
            return $permutations;            
        }
        
        $perm = range(0, $size);
        $j = 0;

        do { 
             foreach ($perm as $i) { $permutations[$j][] = $code_arr[$i]; }
        } while ($perm = $this->swap($perm, $size) and ++$j);

        /*$permutation_total = count($permutations);
        $permutation_ctr = 0;

        $seat = array();
        for($x=1; $x<=$panelists; $x++) {

            if($permutation_ctr < $permutation_total) $permutation_ctr++;
            else $permutation_ctr = 1;
            
            $seat[$x] = implode(' ', $permutations[$permutation_ctr - 1]);
        }
        
        return $seat;        */
        
        return $permutations;
    }
    
    /*
     * function generate($code_arr, $panelists) {
        
        if($panelists == 0) return;
        
        $size = count($code_arr) - 1;
        $perm = range(0, $size);
        $j = 0;

        do { 
             foreach ($perm as $i) { $permutations[$j][] = $code_arr[$i]; }
        } while ($perm = $this->swap($perm, $size) and ++$j);

        $permutation_total = count($permutations);
        $permutation_ctr = 0;

        $seat = array();
        for($x=1; $x<=$panelists; $x++) {

            if($permutation_ctr < $permutation_total) $permutation_ctr++;
            else $permutation_ctr = 1;
            
            $seat[$x] = implode(' ', $permutations[$permutation_ctr - 1]);
        }
        
        return $seat;        
    }
     */
    
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

/* End of file Permutation.php */
/* Location: ./application/libraries/Permutation.php */