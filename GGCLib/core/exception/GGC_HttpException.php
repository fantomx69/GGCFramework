<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GGC_HttpException
 *
 * @author Gianni Carafone
 */
class GGC_HttpException extends GGC_Exception {

    public function __construct($message = '', $code = 0, $previous = NULL,
            $aryExtra = NULL) {
        
        parent::__construct($message, $code, $previous, NULL);

        $this->id = uniqid('GGC_', true);
        
        if (!is_null($aryExtra) && is_array($aryExtra))
            $this->_aryExtra = $aryExtra;
    }
    
    function getEntity() {
        return $this->_aryExtra['Entity'];
    }
    
    function getAction() {
        return $this->_aryExtra['Action'];
    }
        
    function getTipoOper() {
        return $this->_aryExtra['TipoOper'];
    }
    
    function getParameters() {
        return $this->_aryExtra['Parameters'];
    }
    
    function getRequest() {
        return $this->_aryExtra['Request'];
    }
    
    function getResponse() {
        return $this->_aryExtra['Response'];
    }
    
    function getReponseAction() {
        return $this->_aryExtra['ResponseAction'];
    }
    
    function getVerbosity() {
        return $this->_aryExtra['Verbosity'];
    }
    
    // Aggiungere anche log, ecc...    
}

?>
