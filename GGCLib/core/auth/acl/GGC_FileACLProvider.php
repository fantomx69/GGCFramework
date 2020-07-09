<?php
/**
 * Description of GGC_FileACLProvider
 *
 * @author Gianni
 */
abstract class GGC_FileACLProvider extends GGC_ACLProvider {
    
    function getACL($entityName, $userName = NULL) {
        $result = NULL;
        
        /*
         * Si controlla l'esistenza del nome entità.
         */
        if (empty($entityName)) {
            return $result;
        }
        
        /*
         * Si carica la parte ACL Global
         */
        $aryGlobalACL = $this->config->getGroup('ACL');
        
        /**
         * Recupero i nomi dei ruoli (o oggetti GGC_Role) dell'utente,
         * se passato.
         */
        $aryUserRoleNames = NULL;
        if (!is_null($userName)) {
           $aryUserRoleNames = GGC_RoleManager::getUserRoleNames($userName);
        }
        
        /*
         * Contenitore per le ACL Role Global.
         */
        $aryGlobalACLRoles = array();
        
        /*
         * Si carica la parte ACL Global Entity
         */
        $aryGlobalACLEntity = $this->config->getGroup('ACL->Entity');
        
        /*
         * Si carica l'intero file di configurazione.
         */
        $data = $this->config->get();
        
        /*
         * Contenitore per le ACL entità in questione.
         */
        $aryACL = NULL;
        
        /*
         * Si scorre il file di configurazione per identificare le sezioni
         * della entità in questione.
         */
        foreach ($data as $groupName => $aryGroup) {
            /**
             * Si carica la parte ACL Role Global, in base al nome utente,
             * se fornito, per pi essere utilizzato alla fine del ciclo.
             */
            if (strpos($groupName, 'ACL->Role->#') !== false) {
                $currentRoleName = \substr($groupName, 12, \strlen($groupName) - 12);
                
                if (is_null($aryUserRoleNames) ||
                        in_array($currentRoleName, $aryUserRoleNames)) {
                    
                    $aryGlobalACLRoles[$currentRoleName] = $aryGroup;
                }
            }
            
            /*
             * Si controlla se esiste l'ACL per l'entità specificata.
             */
            if (strpos($groupName, 'ACL->Entity->#' . $entityName) !== false) {
                /*
                 * Rimozione carattere di identificazione parte variabile.
                 */                
                $groupName = $stringa = str_replace('#', '', $groupName);
                
                /*
                 * Scomposizione in sottogruppi.
                 */
                $aryKeys = explode('->', $groupName);
                
                /**
                 * Identifiicazione profondità array in relazione alla stringa
                 * dei gruppi, e valorizzazione.
                 */
                $ref = &$aryACL;
                
                foreach ($aryKeys as $value) {
                    $ref =& $ref[$value];
                }
                
                foreach ($aryGroup as $key => $value) {
                    $ref[$key] = $value;
                }
                
                /**
                 * Si crea l'oggetto ACL da restituire e lo si valorizza in
                 * modo opportuno.
                 */
//                $result = new GGC_ACL($entityName);
//                $result->setACL($aryACL);
                
//                break;
            } 
        }
        
        /**
         * Si crea l'oggetto ACL da restituire e lo si valorizza in
         * modo opportuno.
         */
        $result = new GGC_ACL($entityName);
        $result->setACL($aryACL);
        
        /**
         * Si valorizza in ogni caso la parte statica/globale dell'ACL.
         */
        foreach ($aryGlobalACL as $key => $value) {
            GGC_ACL::setGlobalACL($key, $value);
        }

        foreach ($aryGlobalACLRoles as $roleName => $aryValue) {
            foreach ($aryValue as $key => $value) {
                GGC_ACL::setGlobalACLRole($roleName, $key, $value);
            }
        }

        foreach ($aryGlobalACLEntity as $key => $value) {
            GGC_ACL::setGlobalACLEntity($key, $value);
        }        
        
        return $result;
    }
    
    function addACL(GGC_ACL $acl,
            $scope = NULL, $fieldName = NULL,
            $entityName = NULL, $roleName = NULL,
            $actionName = NULL, $parameterName = NULL,
            $controlName = NULL, $controlPropertyName = NULL,
            $componentName = NULL, $componentPropertyName = NULL) {
        
    }
    
    function removeACL(GGC_ACL $acl,
            $scope = NULL, $fieldName = NULL,
            $entityName = NULL, $roleName = NULL,
            $actionName = NULL, $parameterName = NULL,
            $controlName = NULL, $controlPropertyName = NULL,
            $componentName = NULL, $componentPropertyName = NULL) {
        
    }
    
    function saveACL(GGC_ACL $acl,
            $scope = NULL, $fieldName = NULL,
            $entityName = NULL, $roleName = NULL,
            $actionName = NULL, $parameterName = NULL,
            $controlName = NULL, $controlPropertyName = NULL,
            $componentName = NULL, $componentPropertyName = NULL) {
        
    }
    
    function refreshACL(GGC_ACL &$acl) {
        $acl = $this->getACL($acl->getEntityName());
    }    
    
    
//    static function saveDefaultACL() {
//        
//    }
//    
//    static function saveDefaultRole($roleName = NULL) {
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
//    function getACL($entityName, $userName = NULL) {
//        
//    }
//    
//    function saveACL(GGC_ACL $acl) {
//        
//    }
//    
//    function refreshACL(GGC_ACL $acl) {
//        
//    }


}

?>
