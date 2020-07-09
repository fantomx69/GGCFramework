<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GGC_InternalRequest
 *
 * @author Gianni
 */
abstract class GGC_InternalRequest extends GGC_Request {
    /**
     * Riferimento all'oggetto request appartenente al contesto da cui l'oggetto
     * corrente viene creato.
     */
    protected $objRequestFrom;
    
    function __construct($objRequestFrom) {
        parent::__construct();
        
        $this->objRequestFrom = $objRequestFrom;
    }
    
    function getObjRequestFrom() {
        return $this->objRequestFrom;
    }
    
    protected function integrityCheck($varName = NULL) {
        /*
         * Controllo integrità parent.
         */
        $result = parent::integrityCheck();
        
        /*
         * Controllo integrità. Se si è già in uno stto di errore, non serve
         * effettuare anche il controllo per questa classe.
         */
        if (empty($result)) {
            if ((empty($varName) || $varName == 'objRequestFrom') &&
                    !isset($this->objRequestFrom)) {
                $result = '[objRequestFrom] non presente.';
            }
        }
        
        return $result;
    }

}

?>
