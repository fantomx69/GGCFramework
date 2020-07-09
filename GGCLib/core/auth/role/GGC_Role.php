<?php
/**
 * Description of GGC_Role
 *
 * @author Gianni
 */
class GGC_Role extends GGC_Object {
    /*
     * Nome ruolo.
     */
    private $_name = NULL;

    /*
     * Lista nomi utente.
     */
    private $_aryUserNames = NULL;
    
    function __construct($name, $aryUsers = NULL) {
        parent::__construct();
        
        $this->_name = $name;
        $this->_aryUsers = $aryUsers;
    }
    
    function getRoleName() {
        return $this->_name;
    }
    
    function setRoleName($value) {
        $this->_name = $value;
    }
    
    function getUserNames() {
        return $this->_aryUserNames;
    }
    
    function setUserNames($aryValues) {
        $this->_aryUserNames = $aryValues;
    }
    
    function addUserName($userName) {
        $this->_aryUserNames[] = $userName;
    }
    
    function isUserInRole($userName) {
        return in_array($userName, $this->_aryUserNames);
    }
}

?>
