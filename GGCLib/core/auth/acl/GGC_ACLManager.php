<?php
/**
 * Description of GGC_ACLManager
 *
 * @author Gianni
 */
class GGC_ACLManager {
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
            static::$_aryInstances[$instanceName] = new GGC_IniFileACLProvider();
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

    static function getACL($entityName, $userName = NULL, $instanceName = 'default') {
        return static::$_aryInstances[$instanceName]->getACL($entityName, $userName);
    }
    
    static function addACL(GGC_ACL $acl,
            $scope = NULL, $fieldName = NULL,
            $entityName = NULL, $roleName = NULL,
            $actionName = NULL, $parameterName = NULL,
            $controlName = NULL, $controlPropertyName = NULL,
            $componentName = NULL, $componentPropertyName = NULL,
            $instanceName = 'default') {
        
        static::$_aryInstances[$instanceName]->addACL($acl,
            $scope, $fieldName,
            $entityName, $roleName,
            $actionName, $parameterName,
            $controlName, $controlPropertyName,
            $componentName, $componentPropertyName);
    }
    
    static function removeACL(GGC_ACL $acl,
            $scope = NULL, $fieldName = NULL,
            $entityName = NULL, $roleName = NULL,
            $actionName = NULL, $parameterName = NULL,
            $controlName = NULL, $controlPropertyName = NULL,
            $componentName = NULL, $componentPropertyName = NULL,
            $instanceName = 'default') {
        
        static::$_aryInstances[$instanceName]->removeACL($acl,
            $scope, $fieldName,
            $entityName, $roleName,
            $actionName, $parameterName,
            $controlName, $controlPropertyName,
            $componentName, $componentPropertyName);
    }
    
    static function saveACL(GGC_ACL $acl,
            $scope = NULL, $fieldName = NULL,
            $entityName = NULL, $roleName = NULL,
            $actionName = NULL, $parameterName = NULL,
            $controlName = NULL, $controlPropertyName = NULL,
            $componentName = NULL, $componentPropertyName = NULL,
            $instanceName = 'default') {
        
        static::$_aryInstances[$instanceName]->saveACL($acl,
            $scope, $fieldName,
            $entityName, $roleName,
            $actionName, $parameterName,
            $controlName, $controlPropertyName,
            $componentName, $componentPropertyName);
    }
    
    static function refreshACL(GGC_ACL &$acl, $instanceName = 'default') {
        static::$_aryInstances[$instanceName]->refreshACL($acl);
    }
    
    
    
    
//    /**
//     * Default ACL.
//     */
//    static function getDefaultACL($fieldName) {
//        //Richiama metodo statico GGC_ACL
//    }
//    
//    static function setDefaultACL($fieldName, $fieldValue) {
//        //...
//    }
//    
//    static function removeDefaultACL($fieldName) {
//        
//    }
//    
//    static function saveDefaultACL() {
//        
//    }
//    
//    /**
//     * Default Role.
//     */
//    static function getDefaultRole($roleName, $fieldName) {
//        
//    }
//    
//    static function setDefaultRole($roleName, $fieldName, $fieldValue) {
//        
//    }
//    
//    static function removeDefaultRole($roleName, $fieldName = NULL) {
//        
//    }
//    
//    static function saveDefaultRole($roleName = NULL) {
//        
//    }
//    
//    /**
//     * Default Entity.
//     */
//    static function getDefaultEntity($fieldName) {
//        
//    }
//    
//    static function setDefaultEntity($fieldName, $fieldValue) {
//        
//    }
//    
//    static function removeDefaultEntity($fieldName) {
//        
//    }
//    
//    static function saveDefaultEntity() {
//        
//    }
//    
//    /**
//     * Metodi gestione instanza.
//     */
//    static function getACL($entityName, $userName = NULL) {
//        
//    }
//    
//    static function saveACL(GGC_ACL $acl) {
//        
//    }
//    
//    static function refreshACL(GGC_ACL $acl) {
//        
//    }

}

?>
