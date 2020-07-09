<?php
/**
 * Classe che permette di manipolare valori bitwise in modo semplice.
 * Può essere usata autonomamente o eridata (nel qual caso forse sarebbe
 * meglio decorare le funzioni come protette; da valutare).
 * Esempi di utilizzo : http://php.net/manual/en/language.operators.bitwise.php
 *
 * @author : wbcarts at juno dot com 16-May-2012 08:52 on php.net
 *           Gianni Carafone ulteriori aggiunte e modifiche.
 * 
 */
class GGC_BitwiseFlag {
    private $flags;

    function isFlagSet($flag) {
     return (($this->flags & $flag) == $flag);
    }

    function setFlag($flag, $value) {
        if($value) {
          $this->flags |= $flag;
          
        } else {
          $this->flags &= ~$flag;
        }
    }
    
    function getFlags() {
        return $this->flags;
    }
}

?>
