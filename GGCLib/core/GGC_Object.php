<?php
//namespace GGC_lib\core;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GGC_Object
 * 
 * Classe base per tutto il framework.
 *
 * @author Gianni Carafone
 */
class GGC_Object {
    private $_id = NULL;
    private $_name = NULL;
    private $_description;
    private $_creationDateTime = NULL;
    
    function __construct() {
        /*
         * Generazione ID univoco
         */
//        $cStrong = false;
//        
//        for ($i = 20; $i <= 25; $i++) {
//            $bytes = openssl_random_pseudo_bytes($i, $cStrong);
//            $hex   = bin2hex($bytes);
//            
//            if ($cStrong) {
//                $this->id = $hex;
//                break;
//            } else
//                die('Generazione ID oggetto non possibile!');
//        }
        $this->_id = uniqid('GGC_', true);
        
        $this->_creationDateTime = date("Y-m-d H:i:s:") . substr(microtime(), 2, 8);
    }
    
    function getObjID() {
        return $this->_id;
    }
    
    function getCreationDateTime() {
        return $this->_creationDateTime;
    }
    
    function getCreationDate($format = NULL) {
        $result = substr($this->_creationDateTime, 0, 10);
        
        if (!empty($format)) {
            $result = NULL;
            
            foreach ($format as $value) {
                if ($value == 'Y') {
                    $result .= substr($this->_creationDateTime, 0, 4);
                } elseif ($value == 'y') {
                    $result .= substr($this->_creationDateTime, 2, 2);
                } elseif ($value == 'm' || $value == 'M') {
                    $result .= substr($this->_creationDateTime, 6, 2);
                } elseif ($value == 'd' || $value == 'D') {
                    $result .= substr($this->_creationDateTime, 9, 2);
                } elseif ($value == '-') {
                    $result .= $value;
                }
            }
        }
        
        return $result;
    }
    
    function getCreationTime($format = NULL) {
        $result = substr($this->_creationDateTime, 12, 17);
        
        if (!empty($format)) {
            $result = NULL;
            
            foreach ($format as $value) {
                if ($value == 'H') {
                    $result .= substr($this->_creationDateTime, 12, 2);
                } elseif ($value == 'i') {
                    $result .= substr($this->_creationDateTime, 15, 2);
                } elseif ($value == 's') {
                    $result .= substr($this->_creationDateTime, 18, 2);
                } elseif ($value == 'u') {
                    $result .= substr($this->_creationDateTime, 21, 8);
                } elseif ($value == ':') {
                    $result .= $value;
                }
            }
        }
        
        return $result;
    }
    
    function getObjName() {
        return $this->_name;
    }
    
    function setObjName($value) {
        $this->_name = $value;
    }
    
    function getObjDescription() {
        return $this->_description;
    }
    
    function setObjDescription($value) {
        $this->_description = $value;
    }
    
    function getClassName() {
        return get_class($this);
    }
    
    function __toString() {
        return get_class();
    }
    
    function getClassDirPath() {
        $reflector = new ReflectionClass(get_class($this));
        return dirname($reflector->getFileName());
    }
    
    function getClassFilePath() {
        $reflector = new ReflectionClass(get_class($this));
        return $reflector->getFileName();
    }
    
}

?>
