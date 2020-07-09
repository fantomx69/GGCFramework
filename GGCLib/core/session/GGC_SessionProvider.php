<?php
/**
 * Description of GGC_SessionProvider
 * 
 * Questa classe rappresenta il contratto che le implementazioni derivate concrete
 * devono rispettare per funzionare. Qui verranno definiti i metodi e le proprietà
 * che la classe manager/factory "GGC_SessionManager" utilizzerà sempre e in modo standard.
 *
 * @author Gianni Carafone
 */
abstract class GGC_SessionProvider extends GGC_Provider {
    /*
     * Inbstanza Sessione corrente di lavoro.
     */
    protected $_session = NULL;
    
    /*
     * Costanti session driver.
     */
    const SD_FILES = 1;
    const SD_MEM_WS = 2;
    const SD_MEM_OS = 3;
    const SD_DB_SQL_SQLITE = 11;
    const SD_DB_SQL_MYSQL = 12;
    const SD_DB_SQL_PGSQL = 13;
    const SD_DB_NOSQL_REDIS = 21;
    const SD_CACHE_MEMCACHE = 31;
    const SD_CACHE_MEMCACHED = 32;
    const SD_CACHE_APC = 33;
    const SD_CACHE_MSESSION = 34;
    
    function init($mixed = NULL) {
        $this->_session->init();
        
        /*
         * Sincronizzazione valori iniziali con quelli del file di conf.
         */
        $sessionSavePath = $this->_session->getSavePath();

        if ($sessionSavePath != GGC_ConfigManager::getValue ('General->Session', 'SavePath', 'Init') &&
                GGC_ConfigManager::getValue ('General->Session', 'SavePath', 'Init') != '') {
            $this->_session->setSavePath(GGC_ConfigManager::getValue ('General->Session', 'SavePath', 'Init'));
        
            $sessionSavePath = $this->_session->getSavePath();
        }
        
        if (!empty($sessionSavePath) && $sessionSavePath != GGC_ConfigManager::getValue ('General->Session', 'SavePath')) {
            GGC_ConfigManager::setValue ('General->Session', 'SavePath', $sessionSavePath);
            
            /*
             * Per ora disabilito la modifica del file di conf, finchè non
             * viene gestita l'autenticazione e autotizzazione degli utenti.
             */
//            GGC_ConfigManager::save();
        }
    }
    
    function start() {
//        $this->init();
        
        if (!is_null($this->_session)) {
            $this->_session->start();
            
        } else {
            GGC_AnomalyManagement::centralizedAnomalyManagement(
                    array('Message' =>
                        'Classe : [ ' . __CLASS__ . ' ]' . PHP_EOL .
                        'Metodo : [ ' . __METHOD__ .' ]' . PHP_EOL .
                        'Riferimento, instanza sessione, nullo. Sessione non avviabile.'));
        }
    }
    
    function end() {
        $this->_session->end();
    }
    
    function getStatus() {
        return $this->_session->getStatus();
    }
    
    function getName() {
        return $this->_session->getName();
    }
    
    function setName($value) {
        return $this->_session->setName($value);
    }
    
    function getID() {
        return $this->_session->getID();
    }
    
    function setID($value = NULL) {
        return $this->_session->setID($value);
    }
    
    function getValue($key) {
        return $this->_session->getValue($key);
    }
    
    function setValue($key, $value) {
        $this->_session->setValue($key, $value);
    }
    
    function unsetKey($key) {
        $this->_session->unsetKey($key);
    }    
    
    function existsKey($key) {
        return $this->_session->existsKey($key);
    }
    
    function existsValue($value) {
        return $this->_session->existsValue($value);
    }
    
    function getKey($value) {
        return $this->_session->getKey($value);
    }
    
    function regenerateID($delete_old_session = false) {
        $this->_session->regenerateID($delete_old_session);
    }
    
    function save() {
        $this->_session->save();
    }
}

?>
