<?php
//namespace GGC_lib\core;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GGC_Error
 *
 * @author Gianni Carafone
 */
class GGC_Error extends ErrorException implements GGC_IObject {
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
     *  'ActionResponse' => ...  (Ha la precedenza su quello globale e di sezione entità)
     *  'Verbosity' => ...  (Ha la precedenza su quello globale e di sezione entità)
     *  'Log' => ...  (Ha la precedenza su quello globale e di sezione entità)
     * ) 
     * 
     * Nelle classi derivate, aggiungere altri elementi caratteristici di quel tipo derivato.
     */
    protected $_aryExtra = array();
    
    public function __construct($message = '', $code = 0, $severity = 1,
            $filename = __FILE__, $lineno = __LINE__, $previous = NULL) {
        
        parent::__construct($message = '', $code = 0, $severity = 1,
            $filename = __FILE__, $lineno = __LINE__, $previous = NULL);

        $this->id = uniqid('GGC_', true);
    }
}

?>
