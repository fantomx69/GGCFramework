<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GGC_Context
 *
 * @author Gianni
 */
class GGC_Context extends GGC_Object {
    private $_request = NULL;
    private $_response = NULL;
    private $_controller = NULL;
    
    public function __construct($request, $response) {
        parent::__construct();
        
        $this->_request = $request;
        $this->_response = $response;
        
        /**
         * 
         * Controllo integritrà.
         */
        $errMsg = $this->integrityCheck();
        
        if (!empty($errMsg)) {
            GGC_AnomalyManagement::centralizedAnomalyManagement (NULL,
                    array('Message' => $errMsg));
        }
    }
    
    function getRequest() {
        return $this->_request;
    }
    
    function getResponse() {
        return $this->_response;
    }
    
    function getController() {
        return $this->_controller;
    }
    
    function setController($value) {
        $this->_controller = $value;
    }

    private function integrityCheck($varName = NULL) {
        $result = NULL;
        
        if ((empty($varName) || $varName == '_request') &&
                !isset($this->_request)) {
            $result = '[request] non presente.';
        }
        
        if ((empty($varName) || $varName == '_response') &&
                !isset($this->_response)) {
            $result .= PHP_EOL . '[response] non presente.';
        }
        
        return $result;
    }
}

?>
