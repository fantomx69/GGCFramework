<?php
/**
 * Classe modello per i provider specializzati.
 * 
 * TODO :
 * Aggiungere anche il provider per DB, con la scelta del tipo db tramite
 * costanti dichiarate proprio nella relativa classe provider.
 * 
 * @author Gianni Carafone
 */
abstract class GGC_StructuredDataSerializationProvider extends GGC_Provider {
    /**
     * Costanti Serialization Driver
     */
    const SD_INI = 1;
    const SD_XML = 2;
    const SD_DB_SQL_SQLITE = 11;
    const SD_DB_SQL_MYSQL = 12;
    const SD_DB_SQL_PGSQL = 13;
    const SD_DB_NOSQL_REDIS = 21;
    const SD_CACHE_MEMCACHE = 31;
    const SD_CACHE_APC = 32;
    const SD_CACHE_FILE = 33;
    const SD_MEM_PHP = 41;
    const SD_MEM_GGC = 42;
    
    protected $instance = NULL;

    function init($mixed = NULL) {
        ;
    }
    
    function load($force = false) {
        $this->instance->load($force);
    }
    
    function save() {
        $this->instance->save();
    }
    
    function getValue($group, $key) {
        return $this->instance->getValue($group, $key);
    }
    
    function setValue($group, $key, $value) {
        $this->instance->setValue($group, $key, $value);
    }
    
    function removeValue($group, $key) {
        $this->instance->removeValue($group, $key);
    }
    
    function keyExists($group, $key) {
        return $this->instance->keyExists($group, $key);
    }
    
    function valueExists($group, $value) {
        return $this->instance->valueExists($group, $value);
    }
    
    function getGroup($key) {
        return $this->instance->getGroup($key);
    }
    
    function setGroup($key, $aryValue, $setDeepMode = false,
            $setEmptyMode = false) {
        $this->instance->setGroup($key, $aryValue, $setDeepMode, $setEmptyMode);
    }
    
    function removeGroup($key) {
        $this->instance->removeGroup($key);
    }
    
    function groupExists($key) {
        return $this->instance->groupExists($key);
    }
    
    function get($group = NULL, $key = NULL) {
        return $this->instance->get($group, $key);
    }
    
    function set($group, $key = NULL, $value = NULL, $setDeepMode = false,
            $setEmptyMode = false) {
        $this->instance->set($group, $key, $value, $setDeepMode, $setEmptyMode);
    }
    
    function clear() {
        $this->instance->clear();
    }
    
    function isEmpty() {
        return $this->instance->isEmpty();
    }
}

?>
