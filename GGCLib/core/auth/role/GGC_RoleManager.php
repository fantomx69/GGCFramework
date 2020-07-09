<?php
/**
 * Description of GGC_RoleManager
 *
 * @author Gianni
 */
class GGC_RoleManager {
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
            static::$_aryInstances[$instanceName] = new GGC_IniFileRoleProvider();
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
    
    static function addRole(GGC_Role $role, $instanceName = 'default') {
        if (array_key_exists($instanceName, self::$_aryInstances)) {
            static::$_aryInstances[$instanceName]->addRole($role);
        }
    }
    
    static function removeRole($roleName, $instanceName = 'default') {
        if (array_key_exists($instanceName, self::$_aryInstances)) {
            static::$_aryInstances[$instanceName]->removeRole($roleName);
        }
    }    

    static function saveRole(GGC_Role $role, $instanceName = 'default') {
        if (array_key_exists($instanceName, self::$_aryInstances)) {
            static::$_aryInstances[$instanceName]->saveRole($role);
        }
    }
    
    static function getRoles($instanceName = 'default') {
        if (array_key_exists($instanceName, self::$_aryInstances)) {
            static::$_aryInstances[$instanceName]->getRoles();
        }
    }
    
    static function getRole($roleName, $instanceName = 'default') {
        if (array_key_exists($instanceName, self::$_aryInstances)) {
            return static::$_aryInstances[$instanceName]->getRole($roleName);
        }
    }
    
    static function existsRole($roleName, $instanceName = 'default') {
        if (array_key_exists($instanceName, self::$_aryInstances)) {
            return static::$_aryInstances[$instanceName]->existsRole($roleName);
        }
    }
    
    static function isUserInRole($userName, $roleName, $instanceName = 'default') {
        return static::$_aryInstances[$instanceName]->isUserInRole($userName, $roleName);
    }
    
    static function getUserRoles($userName, $instanceName = 'default') {
        return static::$_aryInstances[$instanceName]->getUserRoles($userName);
    }
    
    static function getUserRoleNames($userName, $instanceName = 'default') {
        return static::$_aryInstances[$instanceName]->getUserRoleNames($userName);
    }
}

?>
