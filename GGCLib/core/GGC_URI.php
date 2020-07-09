<?php
//Rimuovere 
//include 'GGC_String.php';

/**
 * Description of GGC_URI
 *
 * @author Gianni Carafone
 */
class GGC_URI extends GGC_Object {
    private $_aryURIElements = array(
        /**
         * $_schemeAfterSep = '//' se seguito da authority, ovvero, opzionalmente
         * 'username' e 'password' e comunque 'hostname';
         * $_schemeAfterSep = ':' se seguito subito da 'path';
         */
        'Scheme' => NULL, 'SchemeAfterSep' => '://',
        'UserName' => NULL, 'UserNameAfterSep' => ':',
        'Password' => NULL, 'PasswordAfterSep' => '@',
        /**
         * $_hostNameAfterSep = '/' se seguito subito da 'path';
         * $_hostNameAfterSep = ':' se seguito da 'port';
         */
        'HostName' => NULL, 'HostNameAfterSep' => '/',
        'Port' => 80, 'PortAfterSep' => '/',
        /*
         * $_path indica il percoso completo compeso di nome file script;
         * $_shortPath che indica il percorso escluso il file name e $_fileName.
         * $_pathAfterSep indica il separatore dopo il path e prima di una eventuale query
         * che può essere '?' per le query normali e '/' per le query user frendly;
         */
        'Path' => NULL, 'ShortPath' => NULL, 'FileName' => NULL, 'PathAfterSep' => '?',
        /**
         * $_query indica la stringa di query normale;
         * $_aryQueryVariables rappresenta un array di chievi/valori della query;
         * $_queryVariablesSep = '&' per le query normali e '/' per le quey user friendly;
         * $_queryAfeterSep = '#' se esiste il fragment dopo;
         */
        'Query' => NULL, 'QueryVariables' => array(), 'QueryVariablesSep' => '&', 'QueryAfterSep' => NULL,
        'Fragment' => NULL
        );
    
    private $_userFriendly = false;
//    private $_modRewrite = false;
    
    /**
     * Costanti per gestire gli elementi della uri.
     */
    const SCHEME = 'Scheme', SCHEME_AFTER_SEP = 'SchemeAfterSep';
    const USER_NAME = 'UserName', USER_NAME_AFTER_SEP = 'UserNameAfterSep';
    const PASSWORD = 'Password', PASSWORD_AFTER_SEP = 'PasswordAfterSep';
    const HOST_NAME = 'HostName', HOST_NAME_AFTER_SEP = 'HostNameAfterSep';
    const PORT = 'Port', PORT_AFTER_SEP = 'PortAfterSep';
    const PATH = 'Path', SHORT_PAT = 'ShortPath', FILE_NAME = 'FileName',
            PATH_AFTER_SEP = 'PathAfterSep';
    const QUERY = 'Query', QUERY_VARIABLES = 'QueryVariables',
            QUERY_VARIABLES_SEP = 'QueryVariablesSep',
            QUERY_AFTER_SEP = 'QueryAfterSep';
    const FRAGMENT = 'Fragment';
    const USER_FRIENDLY = 'UserFriendly';
//    const MOD_REWRITE = 'ModRewrite';
    
    function __construct($uri = NULL, array $aryOptions = array()) {
        parent::__construct();
        
        /*
         * Eventuale inizializzazione
         */
        foreach ($aryOptions as $key => $value) {
            if (array_key_exists($key, $this->_aryURIElements)) {
                $this->_aryURIElements[$key] = $value;
                
            } elseif ($key == static::USER_FRIENDLY) {
                $this->_userFriendly = (bool)$value;
                
//            } elseif ($key == static::MOD_REWRITE) {
//                $this->_modRewrite = (bool)$value;
            }
        }
        
        /*
         * Eventuale sezionamento uri passata. Se invece l'uri è stata costruita
         * a mano impostando gli elementi dell'array options, il sezionamento non
         * verrà eseguito.
         */
        $this->sectioning($uri);
        
        /**
         * Controllo integrità.
         */
        $errMsg = $this->integrityCheck();
        
        if (!empty($errMsg)) {
            GGC_AnomalyManagement::centralizedAnomalyManagement (NULL,
                    array('Message' => $errMsg));
        }
        
    }
    
    /**
     * Get/Set elementi uri.
     */
    function getURI() {
        $result = $this->getScheme() . $this->getSchemeAfterSep();
        
        if ($this->getUserName()) {
            $result .= $this->getUserName() . $this->getUserNameAfterSep();
        }
        
        if ($this->getPassword()) {
            $result .= $this->getPassword() . $this->getPasswordAfterSep();
        }
        
        $result .= $this->getHostName() . $this->getHostNameAfterSep();
        
        if ($this->getPath()) {
            $result .= $this->getPath();
        }
        
        if ($this->getQuery()) {
            $result .= $this->getPathAfterSep() . $this->getQuery();
        }
        
        if ($this->getFragment()) {
            $result .= $this->getQueryAfterSep() . $this->getFragment();
        }
        
        return $result;
    }
    
    /**
     * Get/Set Scheme.
     */
    function getScheme() {
        return $this->_aryURIElements[static::SCHEME];
    }
    function setScheme($value) {
        $this->_aryURIElements[static::SCHEME] = $value;
        //$this->integrityCheck(static::SCHEME, true);
    }
    function getSchemeAfterSep() {
        return $this->_aryURIElements[static::SCHEME_AFTER_SEP];
    }
    function setSchemeAfterSep($value) {
        $this->_aryURIElements[static::SCHEME_AFTER_SEP] = $value;
        //$this->integrityCheck(static::SCHEME, true);
    }
    
    /**
     * Get/Set User Name.
     */
    function getUserName() {
        return $this->_aryURIElements[static::USER_NAME];
    }
    function setUserName($value) {
        $this->_aryURIElements[static::USER_NAME] = $value;
    }
    function getUserNameAfterSep() {
        return $this->_aryURIElements[static::USER_NAME_AFTER_SEP];
    }
    function setUserNameAfterSep($value) {
        $this->_aryURIElements[static::USER_NAME_AFTER_SEP] = $value;
    }
    
    /**
     * Get/Set Password.
     */
    function getPassword() {
        return $this->_aryURIElements[static::PASSWORD];
    }
    function setPassword($value) {
        $this->_aryURIElements[static::PASSWORD] = $value;
    }
    function getPasswordAfterSep() {
        return $this->_aryURIElements[static::PASSWORD_AFTER_SEP];
    }
    function setPasswordAfterSep($value) {
        $this->_aryURIElements[static::PASSWORD_AFTER_SEP] = $value;
    }
    
    /**
     * Get/Set Host Name.
     */
    function getHostName() {
        return $this->_aryURIElements[static::HOST_NAME];
    }
    function setHostName($value) {
        $this->_aryURIElements[static::HOST_NAME] = $value;
    }
    function getHostNameAfterSep() {
        return $this->_aryURIElements[static::HOST_NAME_AFTER_SEP];
    }
    function setHostNameAfterSep($value) {
        $this->_aryURIElements[static::HOST_NAME_AFTER_SEP] = $value;
    }
    
    /**
     * Get/Set Port.
     */
    function getPort() {
        return $this->_aryURIElements[static::PORT];
    }
    function setPort($value) {
        $this->_aryURIElements[static::PORT] = $value;
    }
    function getPortAfterSep() {
        return $this->_aryURIElements[static::PORT_AFTER_SEP];
    }
    function setPortAfterSep($value) {
        $this->_aryURIElements[static::PORT_AFTER_SEP] = $value;
    }
    
    /**
     * Get/Set Path.
     */
    function getPath() {
        return $this->_aryURIElements[static::PATH];
    }
    function setPath($value) {
        $this->_aryURIElements[static::PATH] = $value;
    }
    function getPathAfterSep() {
        return $this->_aryURIElements[static::PATH_AFTER_SEP];
    }
    function setPathAfterSep($value) {
        $this->_aryURIElements[static::PATH_AFTER_SEP] = $value;
    }
    
    /**
     * Get/Set Query-Variables.
     */
    function getQuery() {
        return $this->_aryURIElements[static::QUERY];
    }
    function setQuery($value) {
        $this->_aryURIElements[static::QUERY] = $value;
        
        /**
         * Aggiornamento array variabili query.
         */
        $this->updateQueryVariables();
    }
    function getQueryAfterSep() {
        return $this->_aryURIElements[static::QUERY_AFTER_SEP];
    }
    function setQueryAfterSep($value) {
        $this->_aryURIElements[static::QUERY_AFTER_SEP] = $value;
    }
    function getQueryVariable($key) {
        $result = NULL;
        
        if (array_key_exists($key, $this->_aryURIElements[static::QUERY_VARIABLES])) {
            $result = $this->_aryURIElements[static::QUERY_VARIABLES][$key];
        }
        
        return $result;
    }
    function setQueryVariable($key, $Value) {
        $this->_aryURIElements[static::QUERY_VARIABLES][$key] = $Value;
        
        /**
         * Aggiornamento stringa query.
         */
        $this->updateQuery();
    }
    function unsetQueryVariable($key) {
        if (array_key_exists($key, $this->_aryURIElements[static::QUERY_VARIABLES])) {
            unset($this->_aryURIElements[static::QUERY_VARIABLES][$key]);
            
            /*
             * Aggiornamento stringa query.
             */
            $this->updateQuery();
        }
    }
    function getQueryVariables() {
        return $this->_aryURIElements[static::QUERY_VARIABLES];
    }
    function setQueryVarables(array $aryValue) {
        $this->_aryURIElements[static::QUERY_VARIABLES] = $aryValue;
        
        /*
         * Aggiornamento stringa query.
         */
        $this->updateQuery();
    }
    function getQueryVariablesSep() {
        return $this->_aryURIElements[static::QUERY_VARIABLES_SEP];
    }
    function setQueryVariablesSep($value) {
        $this->_aryURIElements[static::QUERY_VARIABLES_SEP] = $value;
    }
    private function updateQuery() {
        $this->_aryURIElements[static::QUERY] = NULL;
        
        foreach ($this->_aryURIElements[static::QUERY_VARIABLES] as $key => $value) {
            $this->_aryURIElements[static::QUERY] .= $key . '=' . $value . '&';
        }
        
        $this->_aryURIElements[static::QUERY] =
                GGC_String::left($this->_aryURIElements[static::QUERY],
                strlen($this->_aryURIElements[static::QUERY])-1);
    }
    private function updateQueryVariables() {
        unset($this->_aryURIElements[static::QUERY_VARIABLES]);
        
        $aryQuery = explode($this->_aryURIElements[static::QUERY_VARIABLES_SEP],
                $this->_aryURIElements[static::QUERY]);
        
        foreach ($aryQuery as $key => $value) {
            if (!$this->_userFriendly) {
                $aryQueryVariable = explode('=', $value);

                $this->_aryURIElements[static::QUERY_VARIABLES][$aryQueryVariable[0]] =
                        $aryQueryVariable[1];
                
            } else {
                if (!($key % 2)) {
                    $this->_aryURIElements[static::QUERY_VARIABLES][$value] = NULL;
                } else {
                    $this->_aryURIElements[static::QUERY_VARIABLES][$aryQuery[$key-1]] = $value;
                }
            }
        }
    }
    
    /**
     * Get/Set Fragment
     */
    function getFragment() {
        return $this->_aryURIElements[static::FRAGMENT];
    }
    function setFragment($value) {
        $this->_aryURIElements[static::FRAGMENT] = $value;
    }
        
    /**
     * Get/Set User Friendly
     */
    function getUserFriendly() {
        return $this->_userFriendly;
    }
    function setUserFriendly($value) {
        $this->_userFriendly = (bool)$value;
        
        /*
         * Adeguamento separatori in base al tipo di url.
         */
        if ($value) {
            $this->setPortAfterSep('/');
            $this->setQueryVariablesSep('/');
        } else {
            $this->setPortAfterSep('?');
            $this->setQueryVariablesSep('&');
        }
        
        /*
         * Aggiornamento stringa query.
         */
        $this->updateQuery();
    }
    
    /**
     * Get/Set Mod Rewrite
     */
//    function getModRewrite() {
//        return $this->_modRewrite;
//    }
//    function setModRewrite($value) {
//        $this->_modRewrite = (bool)$value;
//    }
    
    /*
     * Sezionamento eventuale uri passata al costruttore.
     */
    private function sectioning($uri) {
        if (empty($uri)) return;
        
        /**
         * Si scorre la stringa con notazione tipo array[].
         */
        $uriLenght = strlen($uri);
        $temp = NULL;
        $j = 0;
        for($i=0; $i<$uriLenght; $i++) {
            if ($this->getScheme() == false &&
                    strpos($this->getSchemeAfterSep(), $uri[$i]) !== false) {
                $this->setScheme($temp);
                
                /**
                 * Avanzamento fino al successivo carattere utile, ovvero al
                 * successivo carattere che non a un separatore.
                 */
                for($j=$i; $j<$uriLenght; $j++) {
                    if (strpos($this->getSchemeAfterSep(), $uri[$j]) === false)
                        break;
                }
                ($j<$uriLenght) ? $i=$j : $i=$uriLenght-1;
                
                $temp = '';
                
            } elseif ($this->getUserName() == false &&
                    strpos($this->getUserNameAfterSep(), $uri[$i]) !== false) {
                $this->setUserName($temp);
                
                for($j=$i; $j<$uriLenght; $j++) {
                    if (strpos($this->getUserNameAfterSep(), $uri[$j]) === false)
                        break;
                }
                ($j<$uriLenght) ? $i=$j : $i=$uriLenght-1;
                
                $temp = '';
                
            } elseif ($this->getPassword() == false &&
                    strpos($this->getPasswordAfterSep(), $uri[$i]) !== false) {
                $this->setPassword($temp);
                
                for($j=$i; $j<$uriLenght; $j++) {
                    if (strpos($this->getPasswordAfterSep(), $uri[$j]) === false)
                        break;
                }
                ($j<$uriLenght) ? $i=$j : $i=$uriLenght-1;
                
                $temp = '';
                
            } elseif ($this->getHostName() == false &&
                    strpos($this->getHostNameAfterSep(), $uri[$i]) !== false) {
                $this->setHostName($temp);
                
                for($j=$i; $j<$uriLenght; $j++) {
                    if (strpos($this->getHostNameAfterSep(), $uri[$j]) === false)
                        break;
                }
                ($j<$uriLenght) ? $i=$j : $i=$uriLenght-1;
                
                $temp = '';
                
            } elseif ($this->getPort() == false &&
                    strpos($this->getPortAfterSep(), $uri[$i]) !== false) {
                $this->setPort($temp);
                
                for($j=$i; $j<$uriLenght; $j++) {
                    if (strpos($this->getPortAfterSep(), $uri[$j]) === false)
                        break;
                }
                ($j<$uriLenght) ? $i=$j : $i=$uriLenght-1;
                
                $temp = '';
                
            } elseif (!$this->getPath()) {
                $endPath = false;
                
                if (strpos($this->getPathAfterSep(), $uri[$i]) !== false) {
                    if (!$this->_userFriendly || ($this->_userFriendly &&
                            GGC_String::right($temp, 4) == '.php'))
                            $endPath = true;
                    
                } elseif ($i == $uriLenght-1) {
                    $endPath = true;
                }
                
                if ($endPath) {
//                if (strpos($this->getPathAfterSep(), $uri[$i]) !== false ||
//                        $i == $uriLenght-1 ||
//                        ($this->_userFriendly &&
//                        GGC_String::right($temp, 5) == '.php' . $this->getPathAfterSep())) {
                    
                    if ($i == $uriLenght-1) {
                        $temp .= $uri[$i];
                    }
                    
                    $this->setPath($temp);

                    for($j=$i; $j<$uriLenght; $j++) {
                        if (strpos($this->getPathAfterSep(), $uri[$j]) === false)
                            break;
                    }
                    ($j<$uriLenght) ? $i=$j : $i=$uriLenght-1;

                    $temp = '';
                }
                
            } elseif (!$this->getQuery()) {
                if (strpos($this->getQueryAfterSep(), $uri[$i]) !== false ||
                        $i == $uriLenght-1) {
                    
                    if ($i == $uriLenght-1) {
                        $temp .= $uri[$i];
                    }
                    
                    $this->setQuery($temp);

                    for($j=$i; $j<$uriLenght; $j++) {
                        if (strpos($this->getQueryAfterSep(), $uri[$j]) === false)
                            break;
                    }
                    ($j<$uriLenght) ? $i=$j : $i=$uriLenght-1;

                    $temp = '';
                }
                
            } elseif (!$this->getFragment() && $i == $uriLenght-1) {
                $temp .= $uri[$i];

                $this->setFragment($temp);

                $temp = '';
            }
            
            if ($i < $uriLenght-1) {
                $temp .= $uri[$i];
            }
        }
    }
    
    private function integrityCheck($varName = NULL, $autoRaising = false) {
        $result = NULL;
        
        if ((empty($varName) || $varName == static::SCHEME) &&
                $this->getScheme()) {
            $result = '[Scheme] non presente.';
        }
        
        if ((empty($varName) || $varName == static::HOST_NAME) &&
                $this->getHostName()) {
            $result = '[Host Name] non presente.';
        }
                
        if (!empty($result) && $autoRaising) {
            GGC_AnomalyManagement::centralizedAnomalyManagement (NULL,
                    array('Message' => $result));
        }
        
        return $result;
    }
}

?>
