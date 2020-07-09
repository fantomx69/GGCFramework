<?php
/**
 * Description of GGC_ACLProvider
 *
 * @author Gianni
 */
abstract class GGC_ACLProvider extends GGC_AuthProvider {
    /**
     * Access Control Value
     */
    const ACV_ALLOW = 'allow';
    const ACV_DENY = 'deny';
    
    /**
     * Scope gerarchia ACL. Access Control Scope
     */
    const ACS_GLOBAL = 1;
    const ACS_GLOBAL_ROLE = 2;
    const ACS_GLOBAL_ENTITY = 3;
    const ACS_ENTITY = 4;
    const ACS_ENTITY_ROLE = 5;
    const ACS_ENTITY_ACTION = 6;
    const ACS_ENTITY_ACTION_PARAMETER = 7;
    const ACS_ENTITY_CONTROL = 8;
    const ACS_ENTITY_CONTROL_PROPERTY = 9;
    const ACS_ENTITY_COMPONENT = 10;
    const ACS_ENTITY_COMPONENT_PROPERTY = 9;
    
    /**
     * Access Control Algorithm Direction
     */
//    const ACD_TOPDOWN = 1;
//    const ACD_BOTTOMUP = 2;
    
    /**
     * Metodi di gestione dell'intera ACL, ovvero dati globali statici e quelli
     * instanza, inerenti la specifica entità trattata in quel momento.
     */
//    abstract function getACL($entityName, $userName = NULL);
//    abstract function saveACL(GGC_ACL $acl);
//    abstract function refreshACL(GGC_ACL &$acl);
    abstract function getACL($entityName, $userName = NULL);
    
    abstract function addACL(GGC_ACL $acl,
            $scope = NULL, $fieldName = NULL,
            $entityName = NULL, $roleName = NULL,
            $actionName = NULL, $parameterName = NULL,
            $controlName = NULL, $controlPropertyName = NULL,
            $componentName = NULL, $componentPropertyName = NULL);
    
    abstract function removeACL(GGC_ACL $acl,
            $scope = NULL, $fieldName = NULL,
            $entityName = NULL, $roleName = NULL,
            $actionName = NULL, $parameterName = NULL,
            $controlName = NULL, $controlPropertyName = NULL,
            $componentName = NULL, $componentPropertyName = NULL);
    
    abstract function saveACL(GGC_ACL $acl,
            $scope = NULL, $fieldName = NULL,
            $entityName = NULL, $roleName = NULL,
            $actionName = NULL, $parameterName = NULL,
            $controlName = NULL, $controlPropertyName = NULL,
            $componentName = NULL, $componentPropertyName = NULL);
    
    abstract function refreshACL(GGC_ACL &$acl);
    
    
    /**
     * Metodi di gestione dei soli dati instanza ovvero dell'ACL dell'entità
     * trattata.
     */
//    abstract function addACLEntity(GGC_ACL $acl);
//    abstract function removeACLEntity($entityName);
//    abstract function saveACLEntity(GGC_ACL $acl);
//    abstract function refreshACLEntity(GGC_ACL &$acl);
//
//    abstract function removeACLEntityRole($entityName, $roleName = NULL);
//    abstract function saveACLEntityRole(GGC_ACL $acl, $roleName = NULL);
//    abstract function refreshACLEntityRole(GGC_ACL &$acl, $roleName = NULL);
//    
//    abstract function removeACLEntityAction($entityName, $roleName,
//            $actionName = NULL);
//    abstract function saveACLEntityAction(GGC_ACL $acl, $roleName,
//            $actionName = NULL);
//    abstract function refreshACLEntityAction(GGC_ACL &$acl, $roleName,
//            $actionName = NULL);
//    
//    abstract function removeACLEntityActionParameter($entityName, $roleName,
//            $actionName, $parameterName = NULL);
//    abstract function saveACLEntityActionParameter(GGC_ACL $acl, $roleName,
//            $actionName, $parameterName = NULL);
//    abstract function refreshACLEntityActionParameter(GGC_ACL &$acl, $roleName,
//            $actionName, $parameterName = NULL);
    
    //...e via dicendo per controlli e componenti.
    //...
    
    
}

?>
