<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GGC_SessionManager
 * 
 * Questa classe serve per avere un'interfaccia standard ai diversi sistemi
 * di gestione/memorizzazione delle sessioni. Per la gestione verranno utilizzati
 * sempre gli stessi metodi, con gli stessi nomi, ma ovviamente, con funzionalità
 * implementative diverse. Questo lo si può ottenere o facendo specificare il tipo
 * di driver con un parametro, ad esempio "driver" e poi nei relativi metodi
 * fare i controlli a seconda del driver utilizzato, oppure sfruttando il concetto
 * o pattern "Provider Model" e "Factory/Dependency Injection".
 * Questa classe rappreseta la classe manager per istanziare i provider concreti
 * e richiamarne i metodi e settare le proprietà.
 * Questa dovrebbe essere una classe statica.
 *
 * @author Gianni Carafone
 */
class GGC_SessionManager /*extends GGC_Object*/ {
    private static $_arySession = array();
    
    private static function init() {
        //...
    }
    
    /*
     * Crea la sessione vera e propria del tip opportuno.
     * 
     * $name = nome instanza e NON nome sessione.
     */
    static function create(
            $driver = GGC_SessionProvider::SD_FILES,
            $savePath = NULL,
            $id = NULL,
            $name = NULL,
            $managementType = GGC_Session::SM_EXTERNAL_HANDLER,
            $encryptStatus = false,
            $instanceName = 'default') {
        if (array_key_exists($instanceName, self::$_arySession)) {
            // Per ora faccio solo return, ma in base alla gestione errori
            // ci si deve comportare.
            return;
        }
       
        if ($driver == GGC_SessionProvider::SD_FILES) {
            self::$_arySession[$instanceName] = GGC_FilesSessionProvider::create(
                $savePath, $id, $name, $managementType, $encryptStatus);
        } elseif ($driver == GGC_SessionProvider::SD_MEM_WS) {
            self::$_arySession[$instanceName] = GGC_MemWSSessionProvider::create(
                $savePath, $id, $name, $managementType, $encryptStatus);
        }    
    }
    
    static function start(
            $driver = GGC_SessionProvider::SD_FILES,
            $savePath = NULL,
            $id = NULL,
            $name = NULL,
            $managementType = GGC_Session::SM_EXTERNAL_HANDLER,
            $encryptStatus = false,
            $instanceName = 'default') {
        
        if (!array_key_exists($instanceName, self::$_arySession)) {
            self::create($driver, $savePath, $id, $name, $managementType,
                $encryptStatus, $instanceName);
        }
        
        self::init();
        
        self::$_arySession[$instanceName]->init();
        self::$_arySession[$instanceName]->start();
    }
    
    static function end($instanceName = 'default') {
        self::$_arySession[$instanceName]->end();
    }
    
    static function getStatus($instanceName = 'default') {
        return self::$_arySession[$instanceName]->getStatus();
    }
    
    static function getName($instanceName = 'default') {
        return self::$_arySession[$instanceName]->getName();
    }
    
    static function setName($value, $instanceName = 'default') {
        return self::$_arySession[$instanceName]->setName($value);
    }
    
    static function getID($instanceName = 'default') {
        return self::$_arySession[$instanceName]->getID();
    }
    
    static function setID($value = NULL, $instanceName = 'default') {
        return self::$_arySession[$instanceName]->setID($value);
    }
    
    static function getValue($key, $instanceName = 'default') {
        return self::$_arySession[$instanceName]->getValue($key);
    }
    
    static function setValue($key, $value, $instanceName = 'default') {
        self::$_arySession[$instanceName]->setValue($key, $value);
    }
    
    static function unsetKey($key, $instanceName = 'default') {
        return self::$_arySession[$instanceName]->unsetKey($key);
    }    
    
    static function existsKey($key, $instanceName = 'default') {
        return self::$_arySession[$instanceName]->existsKey($key);
    }
    
    static function existsValue($value, $instanceName = 'default') {
        return self::$_arySession[$instanceName]->existsValue($value);
    }
    
    static function getKey($value, $instanceName = 'default') {
        return self::$_arySession[$instanceName]->getKey($value);
    }
    
    static function regenerateID($delete_old_session = false, $instanceName = 'default') {
        self::$_arySession[$instanceName]->regenerateID($delete_old_session);
    }
    
    static function save($instanceName = 'default') {
        self::$_arySession[$instanceName]->save();
    }

    static function getInstance($instanceName = 'default') {
        $result = NULL;
        
        if (array_key_exists($instanceName, self::$_arySession)) {
            $result = self::$_arySession[$instanceName];
        }
        
        return $result;
    }
}

?>