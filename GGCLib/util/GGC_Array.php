<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GGC_Array
 *
 * @author Gianni Carafone
 */
class GGC_Array {
//    static function countDimension($ary, $count = 0) {
//        if(is_array($ary)) {
//            return self::countDimension(current($ary), ++$count);
//        } else {
//            return $count;
//        }
//    }
//    
    static function countDimension($array) {
        $max_depth = 1;

        foreach ($array as $value) {
            if (is_array($value)) {
                $depth = self::countDimension($value) + 1;

                if ($depth > $max_depth) {
                    $max_depth = $depth;
                }
            }
        }

        return $max_depth;
    }
    
    static function isMultiDimesional($ary) {
        return (count($ary, COUNT_RECURSIVE) > count($ary));
    }
    
    static function md_array_key_exists ($key, $array) {
        foreach ($array as $item => $val)
        {
            if ($item === $key)
            {
                return true;
            }

            if (is_array ($val))
            {
                if (true == self::md_array_key_exists ($item, $val))
                    return true;
            }
        }

        return false;
    }
    
    static function md_array_get_value ($key, $array) {
        foreach ($array as $item => $val)
        {
            if ($item === $key)
            {
                return $val;
            }

            if (is_array ($val))
            {
                if (true == self::md_array_get_value ($key, $val))
                    return $val;
            }
        }

        return NULL;
    }
}

?>
