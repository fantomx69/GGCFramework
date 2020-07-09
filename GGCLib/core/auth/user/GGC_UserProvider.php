<?php
/**
 * Description of GGC_UserProvider
 *
 * @author Gianni
 */
abstract class GGC_UserProvider extends GGC_AuthProvider {
    /**
     * User/Univocal Field Name
     */
    const UFN_USERNAME = 'UserName';
    const UFN_PASSWORD = 'Password';
    const UFN_TOKEN = 'Token';
    
    abstract function addUser(GGC_User $user);
    abstract function removeUser($fieldValue = NULL, $fieldName = self::UFN_USERNAME);
    abstract function saveUser(GGC_User $user);
    abstract function getUsers();
    abstract function getUser($fieldValue = NULL, $fieldName = self::UFN_USERNAME);
    abstract function existsUser($fieldValue = NULL, $fieldName = self::UFN_USERNAME);
}

?>
