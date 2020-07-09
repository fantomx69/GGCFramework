<?php
/**
 * Description of GGC_FileRoleProvider
 *
 * @author Gianni
 */
abstract class GGC_FileRoleProvider extends GGC_RoleProvider {
    
    function existsRole($roleName) {
        $result = false;
    
        $data = $this->config->get();
        
        foreach ($data as $groupName => $aryGroup) {
            if (substr($groupName, 0, 5) == 'Role->') {
                $currentRoleName = substr($groupName, 7, strlen($groupName) - 7);
                
                if ($roleName == $currentRoleName) {
                    $result = true;
                    break;
                }
            }    
        }
        
        return $result;
    }
    
    function getRole($roleName) {
        $result = NULL;
    
        $data = $this->config->get();
        
        foreach ($data as $groupName => $aryGroup) {
            if (substr($groupName, 0, 5) == 'Role->') {
                $currentRoleName = substr($groupName, 7, strlen($groupName) - 7);
                
                if ($roleName == $currentRoleName) {
                    $result = new GGC_Role($currentRoleName);
        
                    if (!is_null($result)) {
                        foreach ($aryGroup as $value) {
                            $result->addUserName($value);
                        }
                    }
                    
                    break;
                }
            }    
        }
        
        return $result;
    }
    
    function getRoles() {
        $result = NULL;
    
        $data = $this->config->get();
        
        foreach ($data as $groupName => $aryGroup) {
            if (substr($groupName, 0, 5) == 'Role->') {
                $result[] = substr($groupName, 7, strlen($groupName) - 7);
            }    
        }
        
        return $result;
    }
    
    function addRole(GGC_Role $role) {
        
    }

    function removeRole($roleName) {
        
    }

    function saveRole(GGC_Role $role) {

    }
    
    function isUserInRole($userName, $roleName) {
        $result = false;
    
        $data = $this->config->get();
        
        foreach ($data as $groupName => $aryGroup) {
            if (substr($groupName, 0, 5) == 'Role->') {
                $currentRoleName = substr($groupName, 7, strlen($groupName) - 7);
                
                if ($roleName == $currentRoleName) {
                    $result = in_array($userName, $aryGroup);
                    
                    break;
                }
            }    
        }
        
        return $result;
        
    }
    
    function getUserRoles($userName) {
        $result = NULL;
    
        $data = $this->config->get();
        
        foreach ($data as $groupName => $aryGroup) {
            if (substr($groupName, 0, 5) == 'Role->') {
                if (in_array($userName, $aryGroup)) {
                    $currentRoleName = substr($groupName, 7, strlen($groupName) - 7);

                    $result[] = new GGC_Role($currentRoleName, $aryGroup);
                }
            }    
        }
        
        return $result;
        
    }
    
    function getUserRoleNames($userName) {
        $result = NULL;

        $data = $this->config->get();

        foreach ($data as $groupName => $aryGroup) {
            if (substr($groupName, 0, 6) == 'Role->') {
                if (in_array($userName, $aryGroup)) {
                    $currentRoleName = substr($groupName, 7, strlen($groupName) - 7);
                    
                    $result[] = $currentRoleName;
                }
            }    
        }

        return $result;

    }    
    
//    function addUser($userName, $roleName) {
//        
//    }
//    
//    function removeUser($userName, $roleName) {
//        
//    }

}

?>
