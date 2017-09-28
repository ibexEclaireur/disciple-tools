<?php
/**
 * Functions that provide utilities to the entire dt system
 */

/**
 * Counts the depth of a multidimensional array
 * The function that returns 1 if the initial array does not have arrays as elements, 2 if at least one element is an array, and so on.
 *
 * @param array $array
 *
 * @return int
 */
function dt_array_depth(array $array) {
    $max_depth = 1;
    
    foreach ($array as $value) {
        if (is_array($value)) {
            $depth = dt_array_depth($value) + 1;
            
            if ($depth > $max_depth) {
                $max_depth = $depth;
            }
        }
    }
    
    return $max_depth;
}
