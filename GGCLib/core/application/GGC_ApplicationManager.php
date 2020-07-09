<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GGC_ApplicationManager
 *
 * @author Gianni Carafone
 */
class GGC_ApplicationManager {
    private static $_instance = NULL;
    
    /*
     * Creazione instanza applicazione.
     * $typeName = Si passa il nome esatto della classe tipo applicazione.
     * $applicationType = Si passa solo il tipo di applicazione , as esempio :
     * 'http', 'cli', '...'.
     * $configDriver = Si passa il tipo driver per gestire il file di config.
     * 
     * NOTA / TODO :
     * In tutte le classi manager mettere un parametro che consenta la di passare
     * un determiknato tipo di oggetto, di classe da instanziare, controllando, però,
     * che sia del tipo provider base, ad esempio per un oggetto applicazione, questo
     * deve essere del tipo "GGC_ApplicationProvider". Fare così per tutto il framework.
     */
    static function create($typeName = NULL, $applicationType = NULL, 
            $serverDocumentRootPath = NULL, $frameworkRootPath = NULL, 
            $applicationRootPath = NULL, $configDriver = NULL,
            $applicationName = NULL, $applicationCacheFilePath = NULL) {
        
        if (self::$_instance === NULL) {
            $type = NULL;

            if ($typeName !== NULL) {
                $type = $typeName;
            } elseif ($applicationType !== NULL) {
                $type = 'GGC_' . $applicationType . 'ApplicationProvider';
            }

            if ($type !== NULL) {
                self::$_instance = new $type($serverDocumentRootPath, 
                        $frameworkRootPath, $applicationRootPath, $configDriver,
                        $applicationName, $applicationCacheFilePath);
            } 
        }
        
        if (self::$_instance === NULL) {
            die('Impossibile creare l\'istanza di applicazione!')  ;
        }
    }
    
    static function run() {
        self::$_instance->run();
    }
    
    static function modelNameFormat($value) {
        return GGC_ApplicationProvider::modelNameFormat($value);
    }
    
    static function dataModelNameFormat($value) {
        return GGC_ApplicationProvider::dataModelNameFormat($value);
    }
    
    static function viewNameFormat($value) {
        return GGC_ApplicationProvider::viewNameFormat($value);
    }
    
    static function controllerNameFormat($value) {
        return GGC_ApplicationProvider::controllerNameFormat($value);
    }
    
    static function entityNameFormat($value) {
        return GGC_ApplicationProvider::entityNameFormat($value);
    }    
    
    static function getApplicationType() {
        return self::$_instance->getApplicationType();
    }
    
    static function getConfigDriver() {
        return self::$_instance->getConfigDriver();
    }
    
//    static function setConfigDriver($value) {
//        self::$_instance->setConfigDriver($value);
//    }
    
    static function getServerDocumentRootPath() {
        return self::$_instance->getServerDocumentRootPath();
    }
    
//    static function setServerDocumentRootPath($value) {
//        self::$_instance->_serverDocumentRootPath = $value;
//    }
    
    static function getFrameworkRootPath() {
        return self::$_instance->getFrameworkRootPath();
    }
    
//    static function setFrameworkRootPath($value) {
//        self::$_instance->_frameworkRootPath = $value;
//    }
    
    static function getApplicationRootPath() {
        return self::$_instance->getApplicationRootPath();
    }
    
//    static function setApplicationRootPath($value) {
//        self::$_instance->_applicationRootPath = $value;
//    }
    
    static function getApplicationName() {
        return self::$_instance->getApplicationName();
    }

    static function setCacheValue($group, $key, $value) {
        return self::$_instance->setCacheValue($group, $key, $value);
    }
    
    static function getCacheValue($group, $key) {
        return self::$_instance->fetCacheValue($group, $key);
    }
    
    static function removeCacheValue($group, $key) {
        return self::$_instance->removeCacheValue($group, $key);
    }
    
}

?>
