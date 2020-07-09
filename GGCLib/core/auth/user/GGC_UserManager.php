<?php
/**
 * Description of GGC_UserManager
 *
 * @author Gianni
 */
class GGC_UserManager {
    /*
     * Riferimenti alle instanze create o recuperate.
     */
    private static $_aryInstances = array();
    
    static function create($provider, $instanceName = 'default') {
        if (array_key_exists($instanceName, static::$_aryInstances)) {
            // Per ora faccio solo return, ma in base alla gestione errori
            // ci si deve comportare.
            return;
        }
        
        if ($provider == GGC_AuthProvider::SP_FILE_INI) {
            static::$_aryInstances[$instanceName] = new GGC_IniFileUserProvider();
        } elseif ($provider == GGC_AuthProvider::SP_FILE_XML) {
            
        } else {
            //errore...
        }
    }
    
    static function getInstance($instanceName = 'default') {
        $result = NULL;
        
        if (array_key_exists($instanceName, self::$_aryInstances)) {
            $result = static::$_aryInstances[$instanceName];
        }
        
        return $result;
    }
    
    static function addUser(GGC_User $user, $instanceName = 'default') {
        if (array_key_exists($instanceName, self::$_aryInstances)) {
            static::$_aryInstances[$instanceName]->addUser($user);
        }
    }
    
    static function removeUser($fieldValue = NULL, $fieldName = self::UFN_USERNAME,
            $instanceName = 'default') {
        if (array_key_exists($instanceName, self::$_aryInstances)) {
            static::$_aryInstances[$instanceName]->removeUser($fieldValue, $fieldName);
        }
    }    

    static function saveUser(GGC_User $user, $instanceName = 'default') {
        if (array_key_exists($instanceName, self::$_aryInstances)) {
            static::$_aryInstances[$instanceName]->saveUser($user);
        }
    }
    
    static function getUsers($instanceName = 'default') {
        if (array_key_exists($instanceName, self::$_aryInstances)) {
            static::$_aryInstances[$instanceName]->getUsers();
        }
    }
    
    static function getUser($fieldValue = NULL, $fieldName = self::UFN_USERNAME,
            $instanceName = 'default') {
        if (array_key_exists($instanceName, self::$_aryInstances)) {
            return static::$_aryInstances[$instanceName]->getUser($fieldValue, $fieldName);
        }
    }
    
    static function existsUser($fieldValue = NULL, $fieldName = self::UFN_USERNAME,
            $instanceName = 'default') {
        if (array_key_exists($instanceName, self::$_aryInstances)) {
            return static::$_aryInstances[$instanceName]->existsUser($fieldValue, $fieldName);
        }
    }

}

?>
