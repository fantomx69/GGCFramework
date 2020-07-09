<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GGC_SanitizeManager
 *
 * @author Gianni Carafone
 */
class GGC_SanitizeManager {
    private static $_aryInstances = array();
    
    static function create($driver, $instanceName = 'default') {
        if (array_key_exists($instanceName, self::$_aryInstances)) {
            // Per ora faccio solo return, ma in base alla gestione errori
            // ci si deve comportare.
            return;
        }
                
        if ($driver == GGC_SanitizeProvider::SD_GGC_HTTP) {
            self::$_aryInstances[$instanceName] = GGC_GGCHttpSanitizeProvider::create();
            
        } elseif ($driver == GGC_SanitizeProvider::SD_GGC_CLI) {
            self::$_aryInstances[$instanceName] = GGC_GGCCliSanitizePovider::create();
            
        } elseif ($driver == GGC_SanitizeProvider::SD_HTMLPURIFIER) {
            self::$_aryInstances[$instanceName] = GGC_HTMLPurifierSanitizeProvider::create();
        }    
    }
    
    static function getInstance($instanceName = 'default') {
        $result = NULL;
        
        if (array_key_exists($instanceName, self::$_aryInstances)) {
            $result = self::$_aryInstances[$instanceName];
        }
        
        return $result;
    }
    
    /*
     * Interfaccia metodi sanitizzazione
     */
    static function sanitizeString($mixedVar, $aryOptions = NULL, $instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->sanitizeString($mixedVar, $aryOptions);
    }
    
    static function sanitizeNumFloat($mixedVar, $aryOptions = NULL, $instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->sanitizeNumFloat($mixedVar, $aryOptions);
    }
    
    static function sanitizeNumInt($mixedVar, $aryOptions = NULL, $instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->sanitizeNumInt($mixedVar, $aryOptions);
    }
    
    static function sanitizeEMail($mixedVar, $aryOptions = NULL, $instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->sanitizeEMail($mixedVar, $aryOptions);
    }
    
    static function sanitizeUrl($mixedVar, $aryOptions = NULL, $instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->sanitizeUrl($mixedVar, $aryOptions);
    }
    
    static function sanitizeRegExp($mixedVar, $aryOptions = NULL, $instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->sanitizeRegExp($mixedVar, $aryOptions);
    }
    
    static function sanitizeCallBack($mixedVar, $aryOptions = NULL, $instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->sanitizeCallBack($mixedVar, $aryOptions);
    }
    
    /*
     * Interfaccia metodi validazione.
     */
    static function validateBoolean($mixedVar, $aryOptions = NULL, $instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->validateBoolean($mixedVar, $aryOptions);
    }
    
    static function validateNumFloat($mixedVar, $aryOptions = NULL, $instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->validateNumFloat($mixedVar, $aryOptions);
    }
    
    static function validateNumInt($mixedVar, $aryOptions = NULL, $instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->validateNumInt($mixedVar, $aryOptions);
    }
    
    static function validateEMail($mixedVar, $aryOptions = NULL, $instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->validateEMail($mixedVar, $aryOptions);
    }
    
    static function validateUrl($mixedVar, $aryOptions = NULL, $instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->validateUrl($mixedVar, $aryOptions);
    }
    
    static function validateIP($mixedVar, $aryOptions = NULL, $instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->validateIP($mixedVar, $aryOptions);
    }
    
    static function validateRegExp($mixedVar, $aryOptions = NULL, $instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->validateRegExp($mixedVar, $aryOptions);
    }
    
    static function validateCallBack($mixedVar, $aryOptions = NULL, $instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->validateCallBack ($mixedVar, $aryOptions);
    }
    
    /*
     * Metodo comune.
     */
    static function examinesArray($aryValues, $aryOptions, $instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->examinesArray($aryValues, $aryOptions);
    }
    
    /*
     * Metodi specifici per l'input
     */
    static function sanitizeInput($inputOption, $inputVarName, $filterType, $aryOptions = NULL, $instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->sanitizeInput($inputOption, $inputVarName, $filterType, $aryOptions);
    }
    
    static function validateInput($inputOption, $inputVarName, $filterType, $aryOptions = NULL, $instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->validateInput($inputOption, $inputVarName, $filterType, $aryOptions);
    }
    
    /*
     * Metodo comune.
     */
    static function examinesInputArray($inputOption, $aryOptions, $instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->examinesInputArray($inputOption, $aryOptions);
    }
    
    /*
     * Metodi gestione white e black list
     */
    static function getSanitizeWhiteList($instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->getSanitizeWhiteList();
    }
    
    static function setSanitizeWhiteList($aryValue, $instanceName = 'default') {
        self::$_aryInstances[$instanceName]->setSanitizeWhiteList($aryValue);
    }
    
    static function getSanitizeBlackList($instanceName = 'default') {
        return self::$_aryInstances[$instanceName]->getSanitizeBlackList();
    }
    
    static function setSanitizeBlackList($aryValue, $instanceName = 'default') {
        self::$_aryInstances[$instanceName]->setSanitizeBlackList($aryValue);
    }
    
}

?>
