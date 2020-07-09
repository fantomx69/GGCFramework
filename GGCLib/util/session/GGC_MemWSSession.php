<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * In questa classe e in tutte le classi derivte da GGC_Session, si andranno a
 * definire le funzioni di gestione personalizzate, se ci sono, ed eventualmente
 * attivare l'handler personalizzato su queste funzioni, oppure utilizzarle in
 * modo diretto se la gestione è proprietaria.
 *
 * @author Gianni Carafone
 */
class GGC_MemWSSession extends GGC_Session {
    
    function __construct(
            $savePath = NULL,
            $id = NULL,
            $name = NULL,
            $managementType = GGC_Session::SM_EXTERNAL_HANDLER,
            $encryptStatus = false) {
        
        parent::__construct($savePath, $id, $name, $managementType,
            $encryptStatus);
    }
    
    /*
     * Metodi standard e di override
     */
    function init() {
        /*
         * Si effettua un primo controllo de tipo di gestione/handler della sessione
         */
        if ($this->_managementType !== GGC_Session::SM_OWNER) {
            parent::init();
            
            /*
             * Si controlla se il tipo di gestione/handler della sessione è realizzato
             * da un modulo o libreira esterna, se così fosse, bisgna adeguare
             * il modulename dell'ambiente; lo stesso module_name del php.ini.
             */
            if ($this->_managementType == GGC_Session::SM_EXTERNAL_HANDLER)
                $this->setModuleName ('mm');
        } else {
            
        }
    }
    
    function start() {
        if ($this->_managementType !== GGC_Session::SM_OWNER) {
            parent::start();
        } else {
            
        }
    }
    
    function end() {
        if ($this->_managementType !== GGC_Session::SM_OWNER) {
            parent::end();
        } else {
            
        }
        
    }
    
    function getSavePath() {
        if ($this->_managementType !== GGC_Session::SM_OWNER) {
            return parent::getSavePath();
        } else {
            
        }
    }
    
    function setSavePath($value = NULL) {
        if ($this->_managementType !== GGC_Session::SM_OWNER) {
//            if (!empty($value) && !is_dir($value))
//                mkdir ($value);
            
            return parent::setSavePath($value);
        } else {
            
        }
    }
    
    function getStatus() {
        if ($this->_managementType !== GGC_Session::SM_OWNER) {
            return parent::getStatus();
        } else {
            
        }
    }
    
    protected function setStatus() {
        if ($this->_managementType !== GGC_Session::SM_OWNER) {
            parent::setStatus();
        } else {
            
        }
    }

    function getName() {
        if ($this->_managementType !== GGC_Session::SM_OWNER) {
            return parent::getName();
        } else {
            
        }
    }
    
    function setName($value = NULL) {
        if ($this->_managementType !== GGC_Session::SM_OWNER) {
            return parent::setName($value);
        } else {
            
        }
    }
    
    function getID() {
        if ($this->_managementType !== GGC_Session::SM_OWNER) {
            return parent::getID();
        } else {
            
        }
    }
    
    function setID($value = NULL) {
        if ($this->_managementType !== GGC_Session::SM_OWNER) {
            return parent::setID($value);
        } else {
            
        }
    }
    
    function getValue($key) {
        if ($this->_managementType !== GGC_Session::SM_OWNER) {
            return parent::getValue($key);
        } else {
            
        }
    }
    
    function setValue($key, $value) {
        if ($this->_managementType !== GGC_Session::SM_OWNER) {
            parent::setValue($key, $value);
        } else {
            
        }
    }
    
    function existsKey($key) {
        if ($this->_managementType !== GGC_Session::SM_OWNER) {
            return parent::existsKey($key);
        } else {
            
        }
    }
    
    function existsValue($value) {
        if ($this->_managementType !== GGC_Session::SM_OWNER) {
            return parent::existsValue($value);
        } else {
            
        }
    }
    
    function getKey($value) {
        if ($this->_managementType !== GGC_Session::SM_OWNER) {
            return parent::getKey($value);
        } else {
            
        }
    }
    
    function regenerateID($delete_old_session = false) {
        if ($this->_managementType !== GGC_Session::SM_OWNER) {
            parent::regenerateID($delete_old_session);
        } else {
            
        }
    }
    
    /*
     * Metodi handler personalizzato
     */
    public function open($savePath, $sessionName) {
        return true;
    }

    public function close() {
        return true;
    }

    public function read($id) {
        return '';
    }

    public function write($id, $data) {
        return true;
    }

    public function destroy($id) {
        return true;
    }
    
    /*
     * NOTA/TODO :
     * In futuro valutare di sostituire "glob" con "opendir".
     */
    function gc($maxlifetime) 
    {
        return true;
    }

}

?>
