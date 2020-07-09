<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Classe wrapper per la gestione avanzata dei cookies.
 *
 * @author Gianni Carafone
 */
class GGC_Cookie {
    /*
     * Stato url-encoding.
     */
    private $_urlEncodingStatus = true;

    /*
     * Stato deleting oggetto cookie.
     */
    private $_deleteStatus = false;
    
    /*
     * Stato sistema di crittografia.
     */
    private static $_encryptionStatus = true;
    
    /*
     * Chiave di, eventuale, crittazione.
     */
    private static $_encryptionKey = 'y@hk97JJFL09swQzxc535#0guGWEY8309lldfghJ9791';
    
    /*
     * Dati cookie.
     */
    private $_name = NULL;
    private $_value = NULL;
    private $_expire = 0;
    private $_path = '/';
    private $_domain = NULL;
    private $_secure = false;
    private $_httpOnly = false;

    /*
     * Buffer oggetti wrapper cookies
     */
    protected static $aryCookies = array();
    
    function __construct($name = NULL, $value = NULL, $expire = 0, $path = '/',
            $domain = NULL, $secure = false, $httpOnly = false) {
        
        $this->_name = $name;
        $this->_value = $value;
        $this->_expire = $expire;
        $this->_path = $path;
        
        if (is_null($domain))
            $domain = $this->_getRootDomain ();
        $this->_domain = $domain;
        
        $this->_secure = $secure;
        $this->_httpOnly = $httpOnly;
    }
    
    function getUrlEncodingStatus() {
        return $this->_urlEncodingStatus;
    }
    
    function setUrlEncodingStatus($value) {
        $this->_urlEncodingStatus = (bool)$value;
    }

    function getDeleteStatus() {
        return $this->_deleteStatus;
    }
    
    function setDeleteStatus($value) {
        $this->_deleteStatus = (bool)$value;
    }
    
    function delete() {
        $this->setExpire(time() - 3600);
        $this->send();
        unset(self::$aryCookies[$this->_name]);
        unset($_COOKIE[$this->_name]);
    }
    
    function getName() {
        return $this->_name;
    }
    
    function setName($value = NULL) {
        $this->_name = $value;
    }
    
    function getValue() {
        return $this->_value;
    }
    
    function setValue($value = NULL) {
        $this->_value = $value;
    }
    
    function getExpire() {
        return $this->_expire;
    }
    
    function setExpire($value = 0) {
        $this->_expire = $value;
    }
    
    function getPath() {
        return $this->_path;
    }
    
    function setPath($value = '/') {
        $this->_path = $value;
    }
    
    function getDomain() {
        return $this->_domain;
    }
    
    function setDomain($value = NULL) {
        $this->_domain = $value;
    }
    
    function getSecure() {
        return $this->_secure;
    }
    
    function setSecure($value = false) {
        $this->_secure = $value;
    }
    
    function getHttpOnly() {
        return $this->_httpOnly;
    }
    
    function setHttpOnly($value = false) {
        $this->_httpOnly = $value;
    }
    
    function send() {
        $value = $this->getValue();
        
        if (self::$_encryptionStatus)
            $value = $this->_encryption($value);
        
        /*
         * Si controlla se effettuare (come si dovrebbe sempre fare) l'encoding.
         */
        if ($this->_urlEncodingStatus) {
            setcookie($this->getName(), $value, $this->getExpire(),
                        $this->getPath(), $this->getDomain(), $this->getSecure(),
                        $this->getHttpOnly());
        
        } else {
            setrawcookie($this->getName(), $value, $this->getExpire(),
                        $this->getPath(), $this->getDomain(), $this->getSecure(),
                        $this->getHttpOnly());
        }
    }
    
    private function _encryption($value = NULL) {
        if (empty($value))
            return GGC_AnomalyManagement::centralizedAnomalyManagement (NULL,
                    array('Message' => 'Specificare il valore da crittografare.'));
        
        $iv_size = mcrypt_get_iv_size(MCRYPT_3DES, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);    
        $key_size = mcrypt_get_key_size(MCRYPT_3DES, MCRYPT_MODE_ECB);
        $key = substr(self::$_encryptionKey, 0, $key_size);
        
        return base64_encode(mcrypt_encrypt(MCRYPT_3DES, $key, $value, MCRYPT_MODE_ECB, $iv));
    }
    
    private function _decryption($value = NULL) {
        if (empty($value))
            return GGC_AnomalyManagement::centralizedAnomalyManagement (NULL,
                    array('Message' => 'Specificare il valore da de-crittografare.'));
        
        $iv_size = mcrypt_get_iv_size(MCRYPT_3DES, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);    
        $key_size = mcrypt_get_key_size(MCRYPT_3DES, MCRYPT_MODE_ECB);
        $key = substr(self::$_encryptionKey, 0, $key_size);
        
        return mcrypt_decrypt(MCRYPT_3DES, $key, base64_decode($value), MCRYPT_MODE_ECB, $iv);
    }
    
    private function _getRootDomain()
    {
        $host = $_SERVER['HTTP_HOST'];
        $parts = explode('.', $host);
        
        if(count($parts) > 1){
            $tld = array_pop($parts);
            $domain = array_pop($parts) . '.' . $tld;
            
        } else {
            $domain = array_pop($parts);
        }
        
        return $domain;
    }
    
    /*
     * ----------------
     * Funzioni static.
     * ----------------
     */
    
    static function getCookie($name) {
        $result = NULL;
        
        if (!empty($name)) {
            if (array_key_exists($name, self::$aryCookies)) {
                $result = self::$aryCookies[$name];

            } elseif (array_key_exists($name, $_COOKIE)) {
                self::$aryCookies[$name] = new GGC_Cookie($name, $_COOKIE[$name]);

                if (self::$_encryptionStatus)
                    self::$aryCookies[$name]->setValue(
                            self::$aryCookies[$name]->_decryption(
                                    self::$aryCookies[$name]->getValue()));

                $result = self::$aryCookies[$name];
            }
        }
        
        return $result;
    }
    
    static function getAllCookies() {
        $aryResult = array();
        
        foreach ($_COOKIE as $name => $value) {
            $aryResult[$name] = $this->getCookie($name);
        }
        
        return $aryResult;
    }
    
    static function sendCookie($name) {
        if (empty($name))
            return GGC_AnomalyManagement::centralizedAnomalyManagement (NULL,
                    array('Message' => 'Specificare il nome cookie da inviare.'));
        
        $cookie = self::getCookie($name);
        
        if (!is_null($cookie)) 
            $cookie->send();
    }
    
    static function sendAllCookies() {
        $aryCookies = self::getAllCookies();
        
        foreach ($aryCookies as $cookie) {
            $cookie->send();
        }
    }
    
    static function deleteCookie($name) {
        if (empty($name))
            return GGC_AnomalyManagement::centralizedAnomalyManagement (NULL,
                    array('Message' => 'Specificare il nome cookie da cancellare.'));
        
        $cookie = self::getCookie($name);
        
        if (!is_null($cookie)) 
            $cookie->delete();
    }
    
    static function getEncryptionKey() {
        return self::$_encryptionKey;
    }
    
    static function setEncryptionKey($value, $addEntropy = false) {
        self::$_encryptionKey = $value;
        
        if ($addEntropy) {
            //...
        }
    }
    
}

?>
