<?php
/**
 * Description of GGC_User
 *
 * @author Gianni
 */
class GGC_User extends GGC_Object {
        /*
     * Riferimento all'estensione di informazioni riguardante l'utente.
     */
    private $_userProfile = NULL;
    
    private $_userName = NULL;
    private $_password = NULL;
    private $_token = NULL;
    
    private $_guest = false;
    private $_enabled = false;
    private $_waiting = false;
    private $_suspended = false;
    private $_deleted = false;
    
    private $_registrationDate = NULL;
    private $_enablingDate = NULL;
    private $_suspendedDate = NULL;
    private $_reactivationDate = NULL;
    private $_lastLoginDate = NULL;
    private $_lastLoginTime = NULL;
    private $_lastLogoutDate = NULL;
    private $_lastLogoutTime = NULL;

    function __construct($userName, $token) {
        parent::__construct();
        
        $this->_userName = $userName;
        $this->_token = $token;
    }
    
    function getUserProfile() {
        return $this->_userProfile;
    }

    function setUserProfile($objValue) {
        $this->_userProfile = $objValue;
    }

    function getUserName() {
        return $this->_userName;
    }
    
    function getToken() {
        return $this->_token;
    }
    
    function getPassword() {
        return $this->_password;
    }

    function setPassword($value) {
        $this->_password = $value;
    }

    function setGuest($value) {
        $this->_guest = (bool)$value;
    }
    
    function isGuest() {
        return $this->_guest;
    }
    
    function isEnabled() {
        return $this->_enabled;
    }
    
    function setEnabled($value) {
        $this->_enabled = (bool)$value;
    }
    
    function isWaiting() {
        return $this->_waiting;
    }
    
    function setWaiting($value) {
        $this->_waiting = (bool)$value;
    }
    
    function isSuspended() {
        return $this->_suspended;
    }
    
    function setSuspended($value) {
        $this->_suspended = (bool)$value;
    }
    
    function isDeleted() {
        return $this->_deleted;
    }
    
    function setDeleted($value) {
        $this->_deleted = (bool)$value;
    }

}

?>
