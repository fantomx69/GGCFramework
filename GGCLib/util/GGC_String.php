<?php
/**
 * Description of GGC_String
 *
 * @author Gianni Carafone
 */
class GGC_String {
    static function left($str,$len){
        return substr($str, 0, $len);
    }

    static function right($str,$len){
        $len=$len*-1;
        return substr($str, $len);
    }
}

?>
