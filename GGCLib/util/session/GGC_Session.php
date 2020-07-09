<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Wrapper oop per le sessioni standard di php
 *
 * @author Gianni Carafone
 */
abstract class GGC_Session {
    /*
     * Costanti per la definizione del tipo di gestione
     */
    const SM_EXTERNAL_HANDLER = 1;
    const SM_INTERNAL_HANDLER = 2;
    const SM_INTERNAL_HANDLER_ONLY = 3;
    const SM_OWNER = 4;
    
    /*
     * Variabili inerenti la sessione :
     * $_sessionData : buffer dati sessione gestione proprietaia.
     * $_encryptStatus : Determinare se la crittografia è attiva o meno.
     * $_managementType : Determina il tipo di gestione o handler.
     * 
     */
    protected $_sessionData = array();
    protected $_encryptStatus = false;
    protected $_managementType = GGC_Session::SM_EXTERNAL_HANDLER;
    protected $_moduleName = NULL;
    protected $_savePath = NULL;
    protected $_name = NULL;
    protected $_id = NULL;
    protected $_status = 0;
    
    /**
     * Metodi implementativi.
     */
    
    /*
     * Finchè si riterrà opportuno mantenere le variabili in modo protetto, la
     * loro inizializzazione può avvenire nelle classi derivate, però così facendo
     * dovrei ogni volta fare le assegnazioni, invece così devo passare i parametri.
     * Maaaa, l'uno vale l'altro, però passo i parametri percè se in futuro
     * le variabili veranno rese private, continuerà a funzionare.
     */
    function __construct(
            $savePath = NULL,
            $id = NULL,
            $name = NULL,
            $managementType = GGC_Session::SM_EXTERNAL_HANDLER,
            $encryptStatus = false) {
        
        $this->_savePath = $savePath;
        $this->_id = $id;
        $this->_name = $name;
        $this->_managementType = $managementType;
        $this->_encryptStatus = $encryptStatus;
    }
    
    function init() {
        if ($this->_managementType == GGC_Session::SM_INTERNAL_HANDLER ||
                $this->_managementType == GGC_Session::SM_INTERNAL_HANDLER_ONLY ||
                $this->_managementType == GGC_Session::SM_OWNER) {
           
            /*
             * Anche se non è obbligatorio, ma è più pulito.
             */
            if ($this->_managementType == GGC_Session::SM_INTERNAL_HANDLER_ONLY ||
                    $this->_managementType == GGC_Session::SM_OWNER)
                //session_module_name('user');
                $this->setModuleName ('user');
            
            $this->setHandler ();
        }
        
        $this->setSavePath($this->_savePath);
        $this->setID($this->_id);
        $this->setName($this->_name);
        $this->setStatus($this->_status);
    }
    
    function start() {
//        $this->init();
        
        session_start();
    }
    
    function end() {
        unset($_SESSION);
        session_destroy();
    }
    
    function getStatus() {
        $this->_status = session_status();
        return $this->_status;
    }
    
    protected function setStatus() {
        $this->_status = session_status();
    }

    function getName() {
        $this->_name = session_name();
        return $this->_name;
    }
    
    function setName($value = NULL) {
        $result = NULL;
        
        if (!empty($value))
            $result = session_name($value);
        
        $this->_name = session_name();
        
        if (is_null($result))
            $result = $this->_name;
        
        return $result;
    }
    
    function getModuleName() {
        $this->_moduleName = session_module_name();
        return $this->_moduleName;
    }
    
    function setModuleName($value = NULL) {
        $result = NULL;
        
        if (!empty($value))
            $result = session_module_name($value);
        
        $this->_moduleName = session_module_name();
        
        if (is_null($result))
            $result = $this->_moduleName;
        
        return $result;
    }
    
    function getSavePath() {
        $this->_savePath = session_save_path();
        return $this->_savePath;
    }
    
    function setSavePath($value = NULL) {
        $result = NULL;
        
        if (!empty($value))
            $result = session_save_path($value);
        
        $this->_savePath = session_save_path();
        
        if (is_null($result))
            $result = $this->_savePath;
        
        return $result;
    }
    
    function getID() {
        $this->_id = session_id();
        return $this->_id;
    }
    
    function setID($value = NULL) {
        $result = NULL;
        
        if (!empty($value))
            $result = session_id($value);
        
        $this->_id = session_id();
        
        if (is_null($result))
            $result = $this->_id;
        
        return $result;
    }
    
    function getValue($key) {
        if (array_key_exists($key, $_SESSION))
            return $_SESSION[$key];
    }
    
    function setValue($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    function unsetKey($key) {
        if (array_key_exists($key, $_SESSION))
            unset($_SESSION[$key]);
    }
    
    function existsKey($key) {
        return array_key_exists($key, $_SESSION);
    }
    
    function existsValue($value) {
        return in_array($value, $_SESSION);
    }
    
    function getKey($value) {
        $aryResult = array_keys($_SESSION, $value);
        
        if (count($aryResult) > 0)
            return $aryResult[0];
    }
    
    function regenerateID($delete_old_session = false) {
        session_regenerate_id($delete_old_session);
    }
    
    function save() {
        //...
    }
    
    function getEncryptStatus() {
        return $this->_encryptStatus;
    }
    
//    function setEncryptStatus($value) {
//        $this->_encryptStatus = (bool)$value;
//    }
    
    function getManagementType() {
        return $this->_managementType;
    }
    
//    function setManagementType($value) {
//        $this->managementType = $value;
//    }
    
    /*
     * Impostazione handler personalizzato delle classi derivate.
     */
    function setHandler() {
        session_set_save_handler(
             array($this, "open"),
             array($this, "close"),
             array($this, "read"),
             array($this, "write"),
             array($this, "destroy"),
             array($this, "gc")
         );
    }
}

?>
