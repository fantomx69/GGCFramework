<?php
/**
 * Description of GGC_IniFileUserProvider
 *
 * @author Gianni
 */
class GGC_IniFileACLProvider extends GGC_FileACLProvider {
    function __construct() {
        parent::__construct();
        
        $fileName = GGC_ApplicationManager::getServerDocumentRootPath() .
                    GGC_ApplicationManager::getApplicationRootPath() .
                    'config/auth/ini/ACL.ini';
                    
            $this->config =
                    new GGC_IniStructuredDataSerializationProvider($fileName);
    }
    
    function init($mixed = NULL) {
        ;
    }
}

?>
