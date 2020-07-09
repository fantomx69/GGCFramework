<?php
/**
 * Description of GGC_FileUserProvider
 *
 * @author Gianni
 */
abstract class GGC_FileUserProvider extends GGC_UserProvider {
    
    function existsUser($fieldValue = NULL, $fieldName = self::UFN_USERNAME) {
        
    }
    
    function getUser($fieldValue = NULL, $fieldName = self::UFN_USERNAME) {
        $result = NULL;
    
        $data = $this->config->get();
        
        foreach ($data as $groupName => $aryGroup) {
            if (substr($groupName, 0, 6) == 'User->') {
                if (($fieldName == static::UFN_USERNAME && $fieldValue == $aryGroup['UserName']) ||
                        ($fieldName == static::UFN_PASSWORD && $fieldValue == $aryGroup['Password']) ||
                        ($fieldName == static::UFN_TOKEN && $fieldValue == $aryGroup['Token'])) {
               
                    $result = new GGC_User($aryGroup['UserName'], $aryGroup['Token']);
        
                    if (!is_null($result)) {
                        $result->setPassword($aryGroup['Password']);
                        $result->setGuest($aryGroup['Guest']);
                        $result->setEnabled($aryGroup['Enabled']);
                        $result->setWaiting($aryGroup['Waiting']);
                        $result->setSuspended($aryGroup['Suspended']);
                        $result->setDeleted($aryGroup['Deleted']);
                    }
                    
                    break;
                }
            }    
        }
        
        return $result;
    }
    
    function getUsers() {
        
    }
    
    function addUser(GGC_User $user) {
        
    }

    function removeUser($fieldValue = NULL, $fieldName = self::UFN_USERNAME) {
        
    }

    function saveUser(GGC_User $user) {

    }
    
}

?>
