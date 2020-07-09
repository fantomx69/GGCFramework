<?php
/**
 * Description of GGC_IniFileUserProvider
 *
 * @author Gianni
 */
class GGC_IniFileRoleProvider extends GGC_FileRoleProvider {
    function __construct() {
        parent::__construct();
        
        $fileName = GGC_ApplicationManager::getServerDocumentRootPath() .
                    GGC_ApplicationManager::getApplicationRootPath() .
                    'config/auth/ini/Role.ini';
                    
        $this->config =
                new GGC_IniStructuredDataSerializationProvider($fileName);
    }
    
    function init($mixed = NULL) {
        ;
    }
}

?>
