<?php
//namespace GGC_lib\core;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GGC_Exception
 *
 * @author Gianni Carafone
 */

/*
 * TODO :
 * Definire le funzioni get/set per le proprietà rappresentate dagli elementi
 * dell'array $_extra.
 */
abstract class GGC_Exception extends Exception implements GGC_IObject {
    /*
     * Adesione contratto interfaccia "GGC_IObject"
     */
    private $id = NULL;
    private $name = NULL;
   
    function getObjID() {
        return $this->id;
    }
    
    function getObjName() {
        return $this->name;
    }
    
    function setObjName($value) {
        $this->name = $value;
    }
    
    function __toString() {
        return get_class();
    }
    
    /*
     * Implementazione classe
     */
    
    /*
     * Gestione extra.
     * array( 
     *  'Code' => ...
     *  'Message' => ...
     *  
     *  'Entity' => ...
     *  'Action' => ...
     *  'TipoOper' => ...
     *  'Parameters' => array()
     * --- oppure ---
     *  'Request' => objRequest
     *  'Response' => objResponse
     * 
     *  'ResponseAction' => ...  (Ha la precedenza su quello globale e di sezione entità)
     *  'Verbosity' => ...  (Ha la precedenza su quello globale e di sezione entità)
     *  'Log' => ...  (Ha la precedenza su quello globale e di sezione entità)
     * ) 
     * 
     * Nelle classi derivate, aggiungere altri elementi caratteristici di quel tipo derivato.
     */
    protected $_aryExtra = array();

    public function __construct($message = '', $code = 0, $previous = NULL,
            $aryExtra = NULL) {
        
        parent::__construct($message = '', $code = 0, $previous = NULL);

        $this->id = uniqid('GGC_', true);
        
        if (!is_null($aryExtra) && is_array($aryExtra))
            $this->_aryExtra = $aryExtra;
    }
    
    function getGGC_Code() {
        return $this->_aryExtra['Code'];
    }
    
    function getGGC_Message() {
        return $this->_aryExtra['Message'];
    }
    
    
    
}

?>
