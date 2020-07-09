<?php
/**
 * Sistema di gestione cache autoloader, il quale basandosi sulla gestione
 * del sistema di serializzazione standard del framework, automaticamente
 * eredita la possibilità di gestire la cache autoload in tutti i modi
 * possibili che il sistema implementa.
 * 
 * @author Gianni Carafone
 */

class GGC_AutoloaderCacheManager {
    private static $_aryInstances = array();
    
    static function open($driver, $fileName, $forceCreation = false, $instanceName = 'default') {
        if (array_key_exists($instanceName, self::$_aryInstances)) {
            // Per ora faccio solo return, ma in base alla gestione errori
            // ci si deve comportare.
            return;
        }
                
        if ($driver == GGC_ConfigProvider::SD_INI) {
            self::$_aryInstances[$instanceName] = new GGC_IniAutoloaderCacheProvider($fileName, $forceCreation);
        }    
    }
    
    static function opened($instanceName = 'default') {
        return array_key_exists($instanceName, self::$_aryInstances);
    }
    
    static function getInstance($instanceName = 'default') {
        $result = NULL;
        
        if (array_key_exists($instanceName, self::$_aryInstances)) {
            $result = self::$_aryInstances[$instanceName];
        }
        
        return $result;
    }
    
    /**
     * Inizio implementazione interfaccia "GGC_COnfigProvider"
     */
        
    static function load($force = false, $instanceName = 'default') {
        self::$_aryInstances[$instanceName]->load($force);
    }
    
    static function save($instanceName = 'default') {
        self::$_aryInstances[$instanceName]->save();
    }
    
    static function getValue($group, $key, $instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->getValue($group, $key);
    }
    
    static function setValue($group, $key, $value, $instanceName = 'default') {
        self::$_aryInstances[$instanceName]->setValue($group, $key, $value);
    }
    
    static function removeValue($group, $key, $instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->removeValue($group, $key);
    }
    
    static function keyExists($group, $key, $instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->keyExists($group, $key);
    }
    
    static function valueExists($group, $value, $instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->valueExists($group, $value);
    }
    
    static function getGroup($key, $instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->getGroup($key);
    }
    
    static function setGroup($key, $aryValue,
            $setDeepMode = false, $setEmptyMode = false, $instanceName = 'default') {
        self::$_aryInstances[$instanceName]->setGroup($key, $aryValue,
                $setDeepMode, $setEmptyMode);
    }
    
    static function removeGroup($key, $instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->removeGroup($key);
    }
    
    static function groupExists($key, $instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->groupExists($key);
    }
    
    static function get($group = NULL, $key = NULL, $instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->get($group, $key);
    }
    
    static function set($group, $key = NULL, $value = NULL, $setDeepMode = false,
            $setEmptyMode = false, $instanceName = 'default') {
        self::$_aryInstances[$instanceName]->set($group, $key, $value, $setDeepMode,
                $setEmptyMode);
    }
    
    static function clear($instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->clear();
    }
    
    static function isEmpty($instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->isEmpty();
    }
}

?>
