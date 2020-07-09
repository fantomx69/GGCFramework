<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GGC_SanitizeProvider
 *
 * @author Gianni Carafone
 */
abstract class GGC_SanitizeProvider extends GGC_Provider {
    /*
     * Costanti Sanitize Driver
     */
    const SD_GGC_HTTP = 1;
    const SD_GGC_CLI = 2;
    const SD_HTMLPURIFIER = 3;
    
    /*
     * Pattern/filtri predefiniti sanitizzazione
     */
    const S_FILTER_STRING = 11;
    const S_FILTER_NUM_FLOAT = 12;
    const S_FILTER_NUM_INT = 13;
    const S_FILTER_EMAIL = 14;
    const S_FILTER_URL = 15;
    const S_FILTER_REGEXP = 16;
    const S_FILTER_CALLBACK = 17;
    
    /*
     * Flags pattern/filtri sanitizzazione
     */
    const S_FLAG_STRING_STRIP_LOW = 21;
    const S_FLAG_STRING_STRIP_HIGH = 22;
    const S_FLAG_STRING_ENCODE_LOW = 23;
    const S_FLAG_STRING_ENCODE_HIGH = 24;
    const S_FLAG_STRING_ENCODE_QUOTES = 25;
    const S_FLAG_STRING_ENCODE_AMP = 26;
    const S_FLAG_STRING_SPECIAL_CHARS = 27;
    const S_FLAG_STRING_FULL_SPECIAL_CHARS = 28;
    const S_FLAG_STRING_ENTITIES = 29;
    const S_FLAG_STRING_MIDDLE_ENTITIES = 30;
    const S_FLAG_STRING_FULL_ENTITIES = 31;
    const S_FLAG_STRING_LTRIM = 32;
    const S_FLAG_STRING_RTRIM = 33;
    const S_FLAG_STRING_TRIM = 34;
    const S_FLAG_STRING_CLEAN_WHITE_LIST = 35;
    const S_FLAG_STRING_CLEAN_BLACK_LIST = 36;
    
    const S_FLAG_NUM_FLOAT_ALLOW_FRACTION = 37;
    const S_FLAG_NUM_FLOAT_ALLOW_THOUSAND = 38;
    const S_FLAG_NUM_FLOAT_ALLOW_SCIENTIFIC = 39;
        
    /*
     * Pattern/filtri predefiniti validazione
     */
    const V_FILTER_BOOLEAN = 61;
    const V_FILTER_NUM_FLOAT = 62;
    const V_FILTER_NUM_INT = 63;
    const V_FILTER_EMAIL = 64;
    const V_FILTER_URL = 65;
    const V_FILTER_IP = 66;
    const V_FILTER_REGEXP = 67;
    const V_FILTER_CALLBACK = 68;
    
    /*
     * Flags pattern/filtri validazione.
     * 
     */
    const V_FLAG_BOOLEAN_NULL_ON_FAILURE = 71;
    
    const V_FLAG_NUM_FLOAT_ALLOW_THOUSAND = 72;
    const V_FLAG_NUM_INT_ALLOW_OCTAL = 73;
    const V_FLAG_NUM_INT_ALLOW_HEX = 74;
//    const V_FLAG_NUM_RANGE = 75;
    
    const V_FLAG_URL_PATH_REQUIRED = 76;
    const V_FLAG_URL_QUERY_REQUIRED = 77;
    
    const V_FLAG_IP_IPV4 = 78;
    const V_FLAG_IP_IPV6 = 79;
    const V_FLAG_IP_NO_PRIV_RANGE = 80;
    const V_FLAG_IP_NO_RES_RANGE = 81;
    
    /**
     * Modello da rispettare.
     */
    
    /*
     * Pattern stringhe/Caratteri sanitizzazione.
     */
    protected $_sanitizeWhiteList = array();
    protected $_sanitizeBlackList = array();
    
    /*
     * Pattern stringhe/Caratteri validazione.
     */
    protected $_validateWhiteList = array();
    protected $_validateBlackList = array();
    
    /*
     * Mappa flag framework con flag php, quando possibile.
     */
    private $_aryPhpFilterMap = array();

    function __construct() {
        parent::__construct();
        
        /*
         * Valorizzazione mappa.
         */
        $this->_aryPhpFlagMap = array(
            'sanitize_filters' => array(
                self::S_FILTER_STRING => NULL,
                self::S_FILTER_NUM_FLOAT => FILTER_SANITIZE_NUMBER_FLOAT,
                self::S_FILTER_NUM_INT => FILTER_SANITIZE_NUMBER_INT,
                self::S_FILTER_EMAIL => FILTER_SANITIZE_EMAIL,
                self::S_FILTER_URL => FILTER_SANITIZE_URL,
                self::S_FILTER_REGEXP => NULL,
                self::S_FILTER_CALLBACK => FILTER_CALLBACK
            ),
            'sanitize_flags' => array(
                self::S_FLAG_STRING_STRIP_LOW => FILTER_FLAG_STRIP_LOW,
                self::S_FLAG_STRING_STRIP_HIGH => FILTER_FLAG_STRIP_HIGH,
                self::S_FLAG_STRING_ENCODE_LOW => FILTER_FLAG_ENCODE_LOW,
                self::S_FLAG_STRING_ENCODE_HIGH => FILTER_FLAG_ENCODE_HIGH,
                self::S_FLAG_STRING_ENCODE_AMP => FILTER_FLAG_ENCODE_AMP,
                self::S_FLAG_STRING_ENCODE_QUOTES => NULL,
                self::S_FLAG_STRING_SPECIAL_CHARS => NULL,
                self::S_FLAG_STRING_FULL_SPECIAL_CHARS => NULL,
                self::S_FLAG_STRING_ENTITIES => NULL,
                self::S_FLAG_STRING_MIDDLE_ENTITIES => NULL,
                self::S_FLAG_STRING_FULL_ENTITIES => NULL,
                self::S_FLAG_STRING_LTRIM => NULL,
                self::S_FLAG_STRING_RTRIM => NULL,
                self::S_FLAG_STRING_TRIM => NULL,
                self::S_FLAG_STRING_CLEAN_WHITE_LIST => NULL,
                self::S_FLAG_STRING_CLEAN_BLACK_LIST => NULL,
                
                self::S_FLAG_NUM_FLOAT_ALLOW_FRACTION => FILTER_FLAG_ALLOW_FRACTION,
                self::S_FLAG_NUM_FLOAT_ALLOW_THOUSAND => FILTER_FLAG_ALLOW_THOUSAND,
                self::S_FLAG_NUM_FLOAT_ALLOW_SCIENTIFIC => FILTER_FLAG_ALLOW_SCIENTIFIC
            ),
            'validate_filters' => array(
                self::V_FILTER_BOOLEAN => FILTER_VALIDATE_BOOLEAN,
                self::V_FILTER_NUM_FLOAT => FILTER_VALIDATE_FLOAT,
                self::V_FILTER_NUM_INT => FILTER_VALIDATE_INT,
                self::V_FILTER_EMAIL => FILTER_VALIDATE_EMAIL,
                self::V_FILTER_URL => FILTER_VALIDATE_URL,
                self::V_FILTER_IP => FILTER_VALIDATE_IP,
                self::V_FILTER_REGEXP => FILTER_VALIDATE_REGEXP,
                self::V_FILTER_CALLBACK => FILTER_CALLBACK 
            ),
            'validate_flags' => array(
                self::V_FLAG_BOOLEAN_NULL_ON_FAILURE => FILTER_NULL_ON_FAILURE,
                self::V_FLAG_NUM_FLOAT_ALLOW_THOUSAND => FILTER_FLAG_ALLOW_THOUSAND,
                self::V_FLAG_NUM_INT_ALLOW_OCTAL => FILTER_FLAG_ALLOW_OCTAL,
                self::V_FLAG_NUM_INT_ALLOW_HEX => FILTER_FLAG_ALLOW_HEX,
                /*self::V_FLAG_NUM_RANGE => NULL,*/
                self::V_FLAG_URL_PATH_REQUIRED => FILTER_FLAG_PATH_REQUIRED,
                self::V_FLAG_URL_QUERY_REQUIRED => FILTER_FLAG_QUERY_REQUIRED,
                self::V_FLAG_IP_IPV4 => FILTER_FLAG_IPV4,
                self::V_FLAG_IP_IPV6 => FILTER_FLAG_IPV6,
                self::V_FLAG_IP_NO_PRIV_RANGE => FILTER_FLAG_NO_PRIV_RANGE,
                self::V_FLAG_IP_NO_RES_RANGE => FILTER_FLAG_NO_RES_RANGE
            )
        );
    }
    
    /*
     * --------------------------------------------------------------------
     * Interfaccia da rispettare per utilizzo sanitizzazione e validazione.
     * --------------------------------------------------------------------
     */
    /*
     * Sanitizzazione.
     */
    abstract function sanitizeString($mixedVar, $aryOptions = NULL);
    abstract function sanitizeNumFloat($mixedVar, $aryOptions = NULL);
    abstract function sanitizeNumInt($mixedVar, $aryOptions = NULL);
    abstract function sanitizeEMail($mixedVar, $aryOptions = NULL);
    abstract function sanitizeUrl($mixedVar, $aryOptions = NULL);
    abstract function sanitizeRegExp($mixedVar, $aryOptions = NULL);
    abstract function sanitizeCallBack($mixedVar, $aryOptions = NULL);

    /*
     * Validazione.
     */
    abstract function validateBoolean($mixedVar, $aryOptions = NULL);
    abstract function validateNumFloat($mixedVar, $aryOptions = NULL);
    abstract function validateNumInt($mixedVar, $aryOptions = NULL);
    abstract function validateEMail($mixedVar, $aryOptions = NULL);
    abstract function validateUrl($mixedVar, $aryOptions = NULL);
    abstract function validateIP($mixedVar, $aryOptions = NULL);
    abstract function validateRegExp($mixedVar, $aryOptions = NULL);
    abstract function validateCallBack($mixedVar, $aryOptions = NULL);
    
    /*
     * Metodo comune
     */
    abstract function examinesArray($aryValues, $aryOptions);
    
    /*
     * Metodi specifici per l'input
     */
    abstract function validateInput($inputOption, $inputVarName, $filterType, $aryOptions = NULL);
    abstract function sanitizeInput($inputOption, $inputVarName, $filterType, $aryOptions = NULL);
    
    /*
     * Metodo comune
     */
    abstract function examinesInputArray($inputOption, $aryOptions);


    /*
     * -------------------
     * Funzioni di lavoro.
     * -------------------
     */
    function getSanitizeWhiteList() {
        return $this->_sanitizeWhiteList;
    }
    
    function setSanitizeWhiteList($aryValue) {
        if (is_array($aryValue))
            $this->_sanitizeWhiteList = $aryValue;
    }
    
    function getSanitizeBlackList() {
        return $this->_sanitizeBlackList;
    }
    
    function setSanitizeBlackList($aryValue) {
        if (is_array($aryValue))
            $this->_sanitizeBlackList = $aryValue;
    }
    
    protected function getPhpFilterMap($operationType = NULL, $flag = NULL) {
        $result = NULL;
        
        if (!empty($operationType)) {
            if (array_key_exists($operationType, $this->_aryPhpFilterMap))
                $result = $this->_aryPhpFilterMap[$operationType];
            
        } elseif (!empty($operationType) && !empty($flag)) {
            if (array_key_exists($operationType, $this->_aryPhpFilterMap) &&
                    array_key_exists($flag, $this->_aryPhpFilterMap[$operationType]))
                        $result = $this->_aryPhpFilterMap[$operationType][$flag];
        } else {
            $result = $this->_aryPhpFilterMap;
        }
        
        return $result;
    }
    
    /*
     * Permette di aggiungere oltre quelli esistenti altri valori, gestibili da
     * classi derivate da 'GGC_GGCSanitizeProvider' la quale implementa le funzionalità
     * di base.
     */
    protected function setPhpFilterMap($operationType, $flag, $value = NULL) {
        if (!empty($operationType) && $flag > 1000)
            $this->_aryPhpFilterMap[$operationType][$flag] = $value;
        else
            GGC_AnomalyManagement::centralizedAnomalyManagement (
                    array('Message' =>
                        'Classe : [ ' . __CLASS__ . ' ]' . PHP_EOL .
                        'Metodo : [ ' . __METHOD__ .' ]' . PHP_EOL .
                        'Condizioni inopportune per aggiungere flags di sanitizzazione
                            personalizzati.'));
    }
    
    /*
     * Permette di rimuovere solo i valori aggiunti oltre quelli esistenti, gestibili da
     * classi derivate da 'GGC_GGCSanitizeProvider' la quale implementa òe funzionalità
     * di base.
     */
    protected function unsetPhpFilterMap($operationType, $flag) {
        if (!empty($operationType) && $flag > 1000)
            unset($this->_aryPhpFilterMap[$operationType][$flag]);
        else
            GGC_AnomalyManagement::centralizedAnomalyManagement (
                    array('Message' =>
                        'Classe : [ ' . __CLASS__ . ' ]' . PHP_EOL .
                        'Metodo : [ ' . __METHOD__ .' ]' . PHP_EOL .
                        'Condizioni inopportune per rimuovere flags di sanitizzazione
                            personalizzati.'));
    }
    
}

?>
