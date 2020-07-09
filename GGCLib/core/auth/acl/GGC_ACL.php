<?php
/**
 * Description of GGC_ACL
 *
 * @author Gianni
 */
class GGC_ACL extends GGC_Object {
    /*
     * Direzione algoritmo di controllo.
     */
//    static $algorithmDirection = static::ACD_TOPDOWN;
    
    /**
     * Contenitori per i dati ACL globali predefiniti.
     */
    private static $_aryGlobalACL = array();
    private static $_aryGlobalACLRoles = array();
    private static $_aryGlobalACLEntity = array();
    
    /**
     * Contenitore gerarchico per i dati membro/instanza ACL .
     */
    private $_aryACL = NULL;
    
    /*
     * Nome entità.
     */
    private $_entityName = NULL;
    
    /**
     * Metodi di gestione Default ACL.
     */
    static function getGlobalACL($fieldName) {
        $result = NULL;
        
        if (array_key_exists($fieldName, static::$_aryGlobalACL)) {
            $result = static::$_aryGlobalACL[$fieldName];
        }
        
        return $result;
    }
    
    static function setGlobalACL($fieldName, $fieldValue) {
        /*
         * Controllo integrità nome campo e valore campo.
         */
        //...
        
        static::$_aryGlobalACL[$fieldName] = $fieldValue;
    }
    
    static function removeGlobalACL($fieldName) {
        
    }
    
    /**
     * Metodi di gestione Default Role.
     */
    static function getGlobalACLRole($roleName, $fieldName) {
        $result = NULL;
        
        if (array_key_exists($roleName, static::$_aryGlobalACLRoles) &&
                array_key_exists($fieldName, static::$_aryGlobalACLRoles[$roleName])) {
            $result = static::$_aryGlobalACLRoles[$roleName][$fieldName];
        }
        
        return $result;
    }
    
    static function setGlobalACLRole($roleName, $fieldName, $fieldValue) {
        /*
         * Controllo integrità nome ruolo, nome campo e valore campo.
         */
        //...
        
        static::$_aryGlobalACLRoles[$roleName][$fieldName] = $fieldValue;
    }
    
    static function removeGlobalACLRole($roleName, $fieldName = NULL) {
        
    }    
    
    /**
     * Metodi di gestione Default Entity.
     */
    static function getGlobalACLEntity($fieldName) {
        $result = NULL;
        
        if (array_key_exists($fieldName, static::$_aryGlobalACLEntity)) {
            $result = static::$_aryGlobalACLEntity[$fieldName];
        }
        
        return $result;
    }
    
    static function setGlobalACLEntity($fieldName, $fieldValue) {
        /*
         * Controllo integrità nome campo e valore campo.
         */
        //...
        
        static::$_aryGlobalACLEntity[$fieldName] = $fieldValue;
    }
    
    static function removeGlobalACLEntity($fieldName) {
        
    }
    
    function __construct($entityName) {
        parent::__construct();
        
        $this->_entityName = $entityName;
    }
            
    function getEntityName() {
        return $this->_entityName;
    }

    /**
     * Metodi inerenti le singole instanze e quindi le singole entità sulle
     * quali configurare le ACL.
     */
    
    /*
     * Valorizzazione regole inerenti l'entità in questione, valorizzando
     * direttamente l'array che contiene tutte le regole.
     */
    function setACL($aryACL) {
        $this->_aryACL = $aryACL;
    }
    
    function getACL() {
        return $this->_aryACL;
    }
    
    /**
     * Gestione Entity.
     */
    function getACLEntity($fieldName, $isDefault = true) {
        $result = NULL;
        
        if ($isDefault) {
            $aryTemp = $this->_aryACL['ACL']['Entity'];
        } else {
            $aryTemp = $this->_aryACL['ACL']['Entity'][$this->_entityName];
        }
        
        if (is_array($aryTemp) && array_key_exists($fieldName, $aryTemp)) {
           $result = $aryTemp[$fieldName];
        }
        
        return $result;
    }
    
    function setACLEntity($fieldName, $fieldValue, $isDefault = true) {
        
    }
    
    function removeACLEntity($fieldName, $isDefault = true) {
        
    }
    
    /**
     * Gestione Entity/Role. Se viene fornito il nome ruolo, si recupera il
     * dato in questione per quel ruolo, altrimenti si prende il valore di default
     * dei ruoli per l'entità in questione.
     */
    function getACLEntityRole($fieldName, $roleName = NULL) {
        $result = NULL;
        
        if (empty($roleName)) {
            $aryTemp = $this->_aryACL['ACL']['Entity'][$this->_entityName]['Roles'];
        } else {
            $aryTemp = $this->_aryACL['ACL']['Entity'][$this->_entityName]['Role'][$roleName];
        }
        
        if (is_array($aryTemp) && array_key_exists($fieldName, $aryTemp)) {
           $result = $aryTemp[$fieldName];
        }
        
        return $result;
    }
    
    function setACLEntityRole($fieldName, $fieldValue, $roleName = NULL) {
        
    }
    
    function removeACLEntityRole($fieldName, $roleName) {
        
    }
    
    /**
     * Gestione Entity/Role/Action.
     * 
     * NOTA* :
     * Anzichè effettuare tutti quesi controlli di esistenza chiavi e coerenza
     * array, per ora silenzio la geswtione errori e funziona bene.
     */
    function getACLEntityAction($fieldName, $roleName, $actionName = NULL) {
        $result = NULL;
        $aryTemp = NULL;
        
//        if (array_key_exists('ACL', $this->_aryACL) &&
//                array_key_exists('Entity', $this->_aryACL['ACL']) &&
//                array_key_exists($this->_entityName, $this->_aryACL['ACL']['Entity']) &&
//                array_key_exists('Role', $this->_aryACL['ACL']['Entity'][$this->_entityName]) &&
//                array_key_exists($roleName, $this->_aryACL['ACL']['Entity'][$this->_entityName]['Role'])) {
            
            if (empty($actionName)) {
//                if (array_key_exists('Actions', $this->_aryACL['ACL']['Entity']
//                        [$this->_entityName]['Role'][$roleName])) {
                    
                    $aryTemp = @$this->_aryACL['ACL']['Entity'][$this->_entityName]
                            ['Role'][$roleName]['Actions'];
//                }
                
            } else {
//                if (array_key_exists('Action', $this->_aryACL['ACL']['Entity']
//                        [$this->_entityName]['Role'][$roleName]) &&
//                        array_key_exists($actionName, $this->_aryACL['ACL']['Entity']
//                        [$this->_entityName]['Role'][$roleName]['Action'])) {

                    $aryTemp = @$this->_aryACL['ACL']['Entity'][$this->_entityName]
                            ['Role'][$roleName]['Action'][$actionName];
//                }
            }
//        }
        
        if (is_array($aryTemp) && array_key_exists($fieldName, $aryTemp)) {
           $result = $aryTemp[$fieldName];
        }
        
        return $result;
    }
    
    function setACLEntityAction($fieldName, $fieldValue, $roleName,
            $actionName = NULL) {
        
    }
    
    function removeACLEntityAction($fieldName, $roleName, $actionName = NULL) {
        
    }
    
    /**
     * Gestione Entity/Role/Action/Parameter.
     */
    function getACLEntityActionParameter($fieldName, $roleName, $actionName,
            $parameterName = NULL) {
        $result = NULL;
        
        if (empty($parameterName)) {
            $aryTemp = @$this->_aryACL['ACL']['Entity'][$this->_entityName]
                    ['Role'][$roleName]['Action'][$actionName]['ActionParameters'];
        } else {
            $aryTemp = @$this->_aryACL['ACL']['Entity'][$this->_entityName]
                    ['Role'][$roleName]['Action'][$actionName]['ActionParameter']
                    [$parameterName];
        }
        
        if (is_array($aryTemp) && array_key_exists($fieldName, $aryTemp)) {
           $result = $aryTemp[$fieldName];
        }
        
        return $result;
    }
    
    function setACLEntityActionParameter($fieldName, $fieldValue, $roleName,
            $actionName, $parameterName = NULL) {
        
    }
    
    function removeACLEntityActionParameter($fieldName, $roleName,
            $actionName, $parameterName = NULL) {
        
    }    
    
    /**
     * Gestione Entity/Role/Control.
     */
    function getACLEntityControl($fieldName, $roleName, $controlName = NULL) {
        $result = NULL;
        
        if (empty($controlName)) {
            $aryTemp = @$this->_aryACL['ACL']['Entity'][$this->_entityName]
                    ['Role'][$roleName]['Controls'];
        } else {
            $aryTemp = @$this->_aryACL['ACL']['Entity'][$this->_entityName]
                    ['Role'][$roleName]['Control'][$controlName];
        }
        
        if (is_array($aryTemp) && array_key_exists($fieldName, $aryTemp)) {
           $result = $aryTemp[$fieldName];
        }
        
        return $result;
    }
    
    function setACLEntityControl($fieldName, $fieldValue, $roleName,
            $controlName = NULL) {
        
    }
    
    function removeACLEntityControl($fieldName, $roleName, $controlName = NULL) {
        
    }
    
    /**
     * Gestione Entity/Role/Control/Property.
     */
    function getACLEntityControlProperty($fieldName, $roleName, $controlName,
            $propertyName = NULL) {
        $result = NULL;
        
        if (empty($propertyName)) {
            $aryTemp = @$this->_aryACL['ACL']['Entity'][$this->_entityName]
                    ['Role'][$roleName]['Control'][$controlName]['Properties'];
        } else {
            $aryTemp = @$this->_aryACL['ACL']['Entity'][$this->_entityName]
                    ['Role'][$roleName]['Control'][$controlName]['Property']
                    [$propertyName];
        }
        
        if (is_array($aryTemp) && array_key_exists($fieldName, $aryTemp)) {
           $result = $aryTemp[$fieldName];
        }
        
        return $result;
    }
    
    function setACLEntityControlProperty($fieldName, $fieldValue, $roleName,
            $controlName, $propertyName = NULL) {
        
    }
    
    function removeACLEntityControlProperty($fieldName, $roleName, $controlName,
            $propertyName = NULL) {
        
    }
    
    /**
     * Gestione Entity/Role/Component.
     */
    function getACLEntityComponent($fieldName, $roleName, $componentName = NULL) {
        $result = NULL;
        
        if (empty($componentName)) {
            $aryTemp = @$this->_aryACL['ACL']['Entity'][$this->_entityName]
                    ['Role'][$roleName]['Components'];
        } else {
            $aryTemp = @$this->_aryACL['ACL']['Entity'][$this->_entityName]
                    ['Role'][$roleName]['Component'][$componentName];
        }
        
        if (is_array($aryTemp) && array_key_exists($fieldName, $aryTemp)) {
           $result = $aryTemp[$fieldName];
        }
        
        return $result;
    }
    
    function setACLEntityComponent($fieldName, $fieldValue, $roleName,
            $componentName = NULL) {
        
    }
    
    function removeACLEntityComponent($fieldName, $roleName, $componentName = NULL) {
        
    }
    
    /**
     * Gestione Entity/Role/Component/Property.
     */
    function getACLEntityComponentProperty($fieldName, $roleName, $componentName,
            $propertyName = NULL) {
        $result = NULL;
        
        if (empty($propertyName)) {
            $aryTemp = @$this->_aryACL['ACL']['Entity'][$this->_entityName]
                    ['Role'][$roleName]['Component'][$componentName]['Properties'];
        } else {
            $aryTemp = @$this->_aryACL['ACL']['Entity'][$this->_entityName]
                    ['Role'][$roleName]['Component'][$componentName]['Property']
                    [$propertyName];
        }
        
        if (is_array($aryTemp) && array_key_exists($fieldName, $aryTemp)) {
           $result = $aryTemp[$fieldName];
        }
        
        return $result;
    }
    
    function setACLEntityComponentProperty($fieldName, $fieldValue, $roleName,
            $componentName, $propertyName = NULL) {
        
    }
    
    function removeACLEntityComponentProperty($fieldName, $roleName, $componentName,
            $propertyName = NULL) {
        
    }
    
}

?>
