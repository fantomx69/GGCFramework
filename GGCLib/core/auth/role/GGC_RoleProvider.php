<?php
/**
 * Description of GGC_RoleProvider
 *
 * @author Gianni
 */
abstract class GGC_RoleProvider extends GGC_AuthProvider {
    abstract function addRole(GGC_Role $role);
    abstract function removeRole($roleName);
    abstract function saveRole(GGC_Role $role);
    abstract function getRoles();
    abstract function getRole($roleName);
    abstract function existsRole($roleName);
    abstract function isUserInRole($userName, $roleName);
    abstract function getUserRoles($userName);
    abstract function getUserRoleNames($userName);

}

?>
