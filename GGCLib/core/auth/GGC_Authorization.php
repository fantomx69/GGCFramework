<?php
/**
 * Description of GGC_Authorization
 *
 * @author Gianni
 */
class GGC_Authorization {
    private static $_isACLCache = false;
    private static $_aryACLCache = array();

    static function init($roleProvider = GGC_AuthProvider::SP_FILE_INI,
            $aclProvider = GGC_AuthProvider::SP_FILE_INI) {
        GGC_RoleManager::create($roleProvider);
        GGC_ACLManager::create($aclProvider);
    }

    static function isACLCache() {
        return static::$_isACLCache;
    }

    static function setACLCache($value) {
        static::$_isACLCache = (bool)$value;
    }
    
    static function clearACLChache() {
        unset(static::$_aryACLCache);
    }
    
    static function refreshACLCache() {
        foreach (static::$_aryACLCache as &$acl) {
            GGC_ACLManager::refreshACL($acl);
        }
    }

    static function isAuthorized($entityName,
            $actionName = NULL, $actionParameterName = NULL, $actionParameterValue = NULL,
            $controlName = NULL, $controlPropertyName = NULL, $controlPropertyValue = NULL,
            $componentName = NULL, $componentPropertyName = NULL, $componentPropertyValue = NULL,
            $userName = NULL, GGC_USer $user = NULL) {
        
        $result = false;
        
        /**
         * Controllo integrità
         */
        if (empty($entityName)) {
            $errMsg = '[Nome Entità] non presente.';
        }
        
        if (empty($userName)) {
            if (!is_null($user)) {
                $userName = $user->getUserName();
            }
        }
        
        if (empty($userName)) {
            $userName = GGC_Authentication::getUserName();
        }
        
        if (empty($userName)) {
            $errMsg .= PHP_EOL . '[Utente] non presente.';
        }
        
        if (empty($actionName) && empty($controlName) && empty($componentName)) {
            $errMsg .= PHP_EOL . 'Specificare almeno uno tra : ' .
                '[Nome Azione], [Nome Controllo], [Nome Componente]';
        }
        
        if (!empty($errMsg)) {
            return GGC_AnomalyManagement::centralizedAnomalyManagement (NULL,
                    array('Message' => $errMsg));
        }
        
        /**
         * Recupero oggetto ACL.
         */
        $acl = NULL;
        
        if (static::$_isACLCache &&
                array_key_exists($entityName, static::$_aryACLCache)) {
            $acl = static::$_aryACLCache[$entityName];
        }
        
        if (is_null($acl)) {
            $acl = GGC_ACLManager::getACL($entityName, $userName);
        }
        
//        if (is_null($acl)) {
//            return GGC_AnomalyManagement::centralizedAnomalyManagement (NULL,
//                    array('Message' => 'Impossbile recupero oggetto ACL'));
//        }
        
        /*
         * Eventuale salvataggio in cache.
         */
        if (static::$_isACLCache &&
                !array_key_exists($entityName, static::$_aryACLCache)) {
            static::$_aryACLCache[$entityName] = $acl;
        }
        
        /**
         * Controllo autorizzazione.
         */
        /**
         * Controllo ACL globale.
         */
        $entityNameAuth = GGC_ACL::getGlobalACL('Entity');
        $actionNameAuth = GGC_ACL::getGlobalACL('Action');
        $actionParameterNameAuth = GGC_ACL::getGlobalACL('ActionParameter');
        $actionParameterValueAuth = GGC_ACL::getGlobalACL('ActionParameterValue');
        $controlNameAuth = GGC_ACL::getGlobalACL('Control');
        $controlPropertyNameAuth = GGC_ACL::getGlobalACL('ControlProperty');
        $controlPropertyValueAuth = GGC_ACL::getGlobalACL('ControlPropertyValue');
        $componentNameAuth = GGC_ACL::getGlobalACL('Component');
        $componentPropertyNameAuth = GGC_ACL::getGlobalACL('ComponentProperty');
        $componentPropertyValueAuth = GGC_ACL::getGlobalACL('ComponentProperty');
        
        /*
         * Recupero ruoli utente in questione.
         */
        $aryUserRoles = GGC_RoleManager::getUserRoleNames($userName);

        /**
         * Controllo ACL Role globale.
         */
        if(!is_null($aryUserRoles)) {
            $i = 0;
            foreach ($aryUserRoles as $roleName) {
                if ($i == 0 || $entityNameAuth != 'allow') {
                    $entityNameAuth = ($auth = GGC_ACL::getGlobalACLRole($roleName, 'Entity')) != NULL ?
                            $auth : $entityNameAuth;
                }

                if ($i == 0 || $actionNameAuth != 'allow') {
                    $actionNameAuth = ($auth = GGC_ACL::getGlobalACLRole($roleName, 'Action')) != NULL ?
                            $auth : $actionNameAuth;
                }
                if ($i == 0 || $actionParameterNameAuth != 'allow') {
                    $actionParameterNameAuth = ($auth = GGC_ACL::getGlobalACLRole($roleName, 'ActionParameter')) != NULL ?
                            $auth : $actionParameterNameAuth;
                }
                if ($i == 0 || $actionParameterValueAuth != 'allow') {
                    $actionParameterValueAuth = ($auth = GGC_ACL::getGlobalACLRole($roleName, 'ActionParameterValue')) != NULL ?
                            $auth : $actionParameterValueAuth;
                }

                if ($i == 0 || $controlNameAuth != 'allow') {
                    $controlNameAuth = ($auth = GGC_ACL::getGlobalACLRole($roleName, 'Control')) != NULL ?
                            $auth : $controlNameAuth;
                }
                if ($i == 0 || $controlPropertyNameAuth != 'allow') {
                    $controlPropertyNameAuth = ($auth = GGC_ACL::getGlobalACLRole($roleName, 'ControlProperty')) != NULL ?
                            $auth : $controlPropertyNameAuth;
                }
                if ($i == 0 || $controlPropertyValueAuth != 'allow') {
                    $controlPropertyValueAuth = ($auth = GGC_ACL::getGlobalACLRole($roleName, 'ControlPropertyValue')) != NULL ?
                            $auth : $controlPropertyValueAuth;
                }

                if ($i == 0 || $componentNameAuth != 'allow') {
                    $componentNameAuth = ($auth = GGC_ACL::getGlobalACLRole($roleName, 'Component')) != NULL ?
                            $auth : $componentNameAuth;
                }
                if ($i == 0 || $componentPropertyNameAuth != 'allow') {
                    $componentPropertyNameAuth = ($auth = GGC_ACL::getGlobalACLRole($roleName, 'ComponentProperty')) != NULL ?
                            $auth : $componentPropertyNameAuth;
                }
                if ($i == 0 || $componentPropertyValueAuth != 'allow') {
                    $componentPropertyValueAuth = ($auth = GGC_ACL::getGlobalACLRole($roleName, 'ComponentPropertyValue')) != NULL ?
                            $auth : $componentPropertyValueAuth;
                }

                $i++;
            }
        }
        
        /**
         * Controllo Entity ACL globale.
         */
        $actionNameAuth = ($auth = GGC_ACL::getGlobalACLEntity('Action')) != NULL ?
                $auth : $actionNameAuth;
        $actionParameterNameAuth = ($auth = GGC_ACL::getGlobalACLEntity('ActionParameter')) != NULL ?
                $auth : $actionParameterNameAuth;
        $actionParameterValueAuth = ($auth = GGC_ACL::getGlobalACLEntity('ActionParameterValue')) != NULL ?
                $auth : $actionParameterValueAuth;
        
        $controlNameAuth = ($auth = GGC_ACL::getGlobalACLEntity('Control')) != NULL ?
                $auth : $controlNameAuth;
        $controlPropertyNameAuth = ($auth = GGC_ACL::getGlobalACLEntity('ControlProperty')) != NULL ?
                $auth : $controlPropertyNameAuth;
        $controlPropertyValueAuth = ($auth = GGC_ACL::getGlobalACLEntity('ControlPropertyValue')) != NULL ?
                $auth : $controlPropertyValueAuth;
        
        $componentNameAuth = ($auth = GGC_ACL::getGlobalACLEntity('Component')) != NULL ?
                $auth : $componentNameAuth;
        $componentPropertyNameAuth = ($auth = GGC_ACL::getGlobalACLEntity('ComponentProperty')) != NULL ?
                $auth : $componentPropertyNameAuth;
        $componentPropertyValueAuth = ($auth = GGC_ACL::getGlobalACLEntity('ComponentPropertyValue')) != NULL ?
                $auth : $componentPropertyValueAuth;
        
        /**
         * Controllo inerente l'entità in questione, sempre che esista l'ACL
         * per l'entità in questione.
         */
        if (!is_null($acl)) {
            /**
             * Entity Default.
             */
            $actionNameAuth = ($auth = $acl->getACLEntity('Action')) != NULL ?
                    $auth : $actionNameAuth;
            $actionParameterNameAuth = ($auth = $acl->getACLEntity('ActionParameter')) != NULL ?
                    $auth : $actionParameterNameAuth;
            $actionParameterValueAuth = ($auth = $acl->getACLEntity('ActionParameterValue')) != NULL ?
                    $auth : $actionParameterValueAuth;

            $controlNameAuth = ($auth = $acl->getACLEntity('Control')) != NULL ?
                    $auth : $controlNameAuth;
            $controlPropertyNameAuth = ($auth = $acl->getACLEntity('ControlProperty')) != NULL ?
                    $auth : $controlPropertyNameAuth;
            $controlPropertyValueAuth = ($auth = $acl->getACLEntity('ControlPropertyValue')) != NULL ?
                    $auth : $controlPropertyValueAuth;

            $componentNameAuth = ($auth = $acl->getACLEntity('Component')) != NULL ?
                    $auth : $componentNameAuth;
            $componentPropertyNameAuth = ($auth = $acl->getACLEntity('ComponentProperty')) != NULL ?
                    $auth : $componentPropertyNameAuth;
            $componentPropertyValueAuth = ($auth = $acl->getACLEntity('ComponentPropertyValue')) != NULL ?
                    $auth : $componentPropertyValueAuth;

            /**
             * Entity effettiva.
             */
            $actionNameAuth = ($auth = $acl->getACLEntity('Action', false)) != NULL ?
                    $auth : $actionNameAuth;
            $actionParameterNameAuth = ($auth = $acl->getACLEntity('ActionParameter', false)) != NULL ?
                    $auth : $actionParameterNameAuth;
            $actionParameterValueAuth = ($auth = $acl->getACLEntity('ActionParameterValue', false)) != NULL ?
                    $auth : $actionParameterValueAuth;

            $controlNameAuth = ($auth = $acl->getACLEntity('Control', false)) != NULL ?
                    $auth : $controlNameAuth;
            $controlPropertyNameAuth = ($auth = $acl->getACLEntity('ControlProperty', false)) != NULL ?
                    $auth : $controlPropertyNameAuth;
            $controlPropertyValueAuth = ($auth = $acl->getACLEntity('ControlPropertyValue', false)) != NULL ?
                    $auth : $controlPropertyValueAuth;

            $componentNameAuth = ($auth = $acl->getACLEntity('Component', false)) != NULL ?
                    $auth : $componentNameAuth;
            $componentPropertyNameAuth = ($auth = $acl->getACLEntity('ComponentProperty', false)) != NULL ?
                    $auth : $componentPropertyNameAuth;
            $componentPropertyValueAuth = ($auth = $acl->getACLEntity('ComponentPropertyValue', false)) != NULL ?
                    $auth : $componentPropertyValueAuth;
            /**
             * Entity/Roles default
             */
            $actionNameAuth = ($auth = $acl->getACLEntityRole('Action')) != NULL ?
                    $auth : $actionNameAuth;
            $actionParameterNameAuth = ($auth = $acl->getACLEntityRole('ActionParameter')) != NULL ?
                    $auth : $actionParameterNameAuth;
            $actionParameterValueAuth = ($auth = $acl->getACLEntityRole('ActionParameterValue')) != NULL ?
                    $auth : $actionParameterValueAuth;

            $controlNameAuth = ($auth = $acl->getACLEntityRole('Control')) != NULL ?
                    $auth : $controlNameAuth;
            $controlPropertyNameAuth = ($auth = $acl->getACLEntityRole('ControlProperty')) != NULL ?
                    $auth : $controlPropertyNameAuth;
            $controlPropertyValueAuth = ($auth = $acl->getACLEntityRole('ControlPropertyValue')) != NULL ?
                    $auth : $controlPropertyValueAuth;

            $componentNameAuth = ($auth = $acl->getACLEntityRole('Component')) != NULL ?
                    $auth : $componentNameAuth;
            $componentPropertyNameAuth = ($auth = $acl->getACLEntityRole('ComponentProperty')) != NULL ?
                    $auth : $componentPropertyNameAuth;
            $componentPropertyValueAuth = ($auth = $acl->getACLEntityRole('ComponentPropertyValue')) != NULL ?
                    $auth : $componentPropertyValueAuth;

            /**
             * Entity/Role effettivo.
             */
            if(!is_null($aryUserRoles)) {
                $i = 0;
                foreach ($aryUserRoles as $roleName) {
                    /*
                     * Determinazione permesso ActionName.
                     */
                    if ($i == 0 || $actionNameAuth != 'allow') {
                        $actionNameAuth = ($auth = $acl->getACLEntityRole('Action', $roleName)) != NULL ?
                                $auth : $actionNameAuth;

                        $actionNameAuth = ($auth = $acl->getACLEntityAction($actionName, $roleName)) != NULL ?
                            $auth : $actionNameAuth;
                    }
                    /*
                     * Determinazione permesso ActionParameterName.
                     */
                    if ($i == 0 || $actionParameterNameAuth != 'allow') {
                        $actionParameterNameAuth = ($auth = $acl->getACLEntityRole('ActionParameter', $roleName)) != NULL ?
                                $auth : $actionParameterNameAuth;

                        $actionParameterNameAuth = ($auth = $acl->getACLEntityAction('ActionParameter', $roleName)) != NULL ?
                            $auth : $actionParameterNameAuth;

                        $actionParameterNameAuth = ($auth = $acl->getACLEntityAction('ActionParameter', $roleName, $actionName)) != NULL ?
                            $auth : $actionParameterNameAuth;

                        $actionParameterNameAuth = ($auth = $acl->getACLEntityActionParameter($actionParameterName, $roleName, $actionName)) != NULL ?
                            $auth : $actionParameterNameAuth;
                    }
                    /*
                     * Determinazione permesso ActionParameterValue.
                     */
                    if ($i == 0 || $actionParameterValueAuth != 'allow') {
                        $actionParameterValueAuth = ($auth = $acl->getACLEntityRole('ActionParameterValue', $roleName)) != NULL ?
                                $auth : $actionParameterValueAuth;

                        $actionParameterValueAuth = ($auth = $acl->getACLEntityAction('ActionParameterValue', $roleName)) != NULL ?
                            $auth : $actionParameterValueAuth;

                        $actionParameterValueAuth = ($auth = $acl->getACLEntityAction('ActionParameterValue', $roleName, $actionName)) != NULL ?
                            $auth : $actionParameterValueAuth;

                        $actionParameterValueAuth = ($auth = $acl->getACLEntityActionParameter('ActionParameterValue', $roleName, $actionName)) != NULL ?
                            $auth : $actionParameterValueAuth;

                        $actionParameterValueAuth = ($auth = $acl->getACLEntityActionParameter($actionParameterValue, $roleName, $actionName, $actionParameterName)) != NULL ?
                            $auth : $actionParameterValueAuth;
                    }

                    if ($i == 0 || $controlNameAuth != 'allow') {
                        $controlNameAuth = ($auth = $acl->getACLEntityRole('Control', $roleName)) != NULL ?
                                $auth : $controlNameAuth;
                    }
                    if ($i == 0 || $controlPropertyNameAuth != 'allow') {
                        $controlPropertyNameAuth = ($auth = $acl->getACLEntityRole('ControlProperty', $roleName)) != NULL ?
                                $auth : $controlPropertyNameAuth;
                    }
                    if ($i == 0 || $controlPropertyValueAuth != 'allow') {
                        $controlPropertyValueAuth = ($auth = $acl->getACLEntityRole('ControlPropertyValue', $roleName)) != NULL ?
                                $auth : $controlPropertyValueAuth;
                    }

                    if ($i == 0 || $componentNameAuth != 'allow') {
                        $componentNameAuth = ($auth = $acl->getACLEntityRole('Component', $roleName)) != NULL ?
                                $auth : $componentNameAuth;
                    }
                    if ($i == 0 || $componentPropertyNameAuth != 'allow') {
                        $componentPropertyNameAuth = ($auth = $acl->getACLEntityRole('ComponentProperty', $roleName)) != NULL ?
                                $auth : $componentPropertyNameAuth;
                    }
                    if ($i == 0 || $componentPropertyValueAuth != 'allow') {
                        $componentPropertyValueAuth = ($auth = $acl->getACLEntityRole('ComponentPropertyValue', $roleName)) != NULL ?
                                $auth : $componentPropertyValueAuth;
                    }

                    $i++;
                }
            
            }
        
        }
        
        $result = ($entityNameAuth == 'allow' || is_null($entityNameAuth)) &&
                
                ($actionNameAuth == 'allow' || is_null($actionNameAuth)) &&
                (is_null($actionParameterName) || $actionParameterNameAuth == 'allow' || is_null($actionParameterNameAuth)) &&
                (is_null($actionParameterValue) || $actionParameterValueAuth == 'allow' || is_null($actionParameterValueAuth)) &&
                
                (is_null($controlName) || $controlNameAuth == 'allow' || is_null($controlNameAuth)) &&
                (is_null($controlPropertyName) || $controlPropertyNameAuth == 'allow' || is_null($controlPropertyNameAuth)) &&
                (is_null($controlPropertyValue) || $controlPropertyValueAuth == 'allow' || is_null($controlPropertyValueAuth)) &&
                
                (is_null($componentName) || $componentNameAuth == 'allow' || is_null($componentNameAuth)) &&
                (is_null($componentPropertyName) || $componentPropertyNameAuth == 'allow' || is_null($componentPropertyNameAuth)) &&
                (is_null($componentPropertyValue) || $componentPropertyValueAuth == 'allow' || is_null($componentPropertyValueAuth));
        
        return $result; 
    }
    
}

?>
