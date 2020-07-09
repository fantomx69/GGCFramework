<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GGC_OutputCacheManager
 *
 * @author Gianni
 */
class GGC_OutputHttpResponseCacheManager {
    private static $_aryInstances = array();
    
//    static function create($driver, $uriType, $uri, $cacheFile,
//            $updateInterval = 5, $instanceName = 'default') {
    static function create(
            GGC_Context $context,
            $cacheSaveProvider,
            $cacheOriginProvider,
            $sourceUri,
            $entityName,
            $rootPath = NULL,
            $updateInterval = 5,
            $updateByParams = NULL,
            $updateByHeaders = NULL,
            $updateByControls = NULL,
            $updateByContentEncodings = NULL,
            $aryUpdateByCustom = NULL,
            $instanceName = 'default') {
        
        if (array_key_exists($instanceName, self::$_aryInstances)) {
            // Per ora faccio solo return, ma in base alla gestione errori
            // ci si deve comportare.
            return;
        }
                
        if ($cacheSaveProvider == GGC_ResponseCacheProvider::CSP_FILE) {
            if ($cacheOriginProvider == GGC_ResponseCacheProvider::COP_PHP_FILE) {
                self::$_aryInstances[$instanceName] = 
                        new GGC_PhpFileOutputHttpResponseCacheProvider(
                                $context,
                                $sourceUri,
                                $entityName,
                                $rootPath,
                                $updateInterval,
                                $updateByParams,
                                $updateByHeaders,
                                $updateByControls,
                                $updateByContentEncodings,
                                $aryUpdateByCustom);
                
            } elseif ($cacheOriginProvider == GGC_ResponseCacheProvider::COP_SMARTY_TEMPLATE_FILE) {
                self::$_aryInstances[$instanceName] = 
                        new GGC_SmartyTplFileOutputHttpResponseCacheProvider(
                                $context,
                                $sourceUri,
                                $entityName,
                                $rootPath,
                                $updateInterval,
                                $updateByParams,
                                $updateByHeaders,
                                $updateByControls,
                                $updateByContentEncodings,
                                $aryUpdateByCustom);
            }
            
        } elseif ($cacheSaveProvider == GGC_ResponseCacheProvider::CSP_DB) {
            //...
        }
    }
    
    static function init($instanceName = 'default') {
        self::$_aryInstances[$instanceName]->init();
    }

    static function getInstance($instanceName = 'default') {
        $result = NULL;
        
        if (array_key_exists($instanceName, self::$_aryInstances)) {
            $result = self::$_aryInstances[$instanceName];
        }
        
        return $result;
    }
    
    static function get($instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->get();
    }
    
    static function update($instanceName = 'default') {
        self::$_aryInstances[$instanceName]->update();
    }
    
    static function clear($instanceName = 'default') {
        self::$_aryInstances[$instanceName]->clear();
    }
    
    static function getExpiresDateTime($instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->getExpiresDateTime();
    }

}

?>
