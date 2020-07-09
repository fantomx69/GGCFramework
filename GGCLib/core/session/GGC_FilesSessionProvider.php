<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GGC_FileSystemSessionProvider
 * 
 * Questa classe definisce il contratto che l'implementazione del provider
 * vero e proprio deve avere affinchè possa essere usato
 *
 * @author Gianni Carafone
 */
class GGC_FilesSessionProvider extends GGC_SessionProvider {
    static function create(
            $savePath = NULL,
            $id = NULL,
            $name = NULL,
            $managementType = GGC_Session::SM_EXTERNAL_HANDLER,
            $encryptStatus = false) {

        return new GGC_FilesSessionProvider($savePath, $id, $name,
                $managementType, $encryptStatus);
    }
    
    function __construct(
            $savePath = NULL,
            $id = NULL,
            $name = NULL,
            $managementType = GGC_Session::SM_EXTERNAL_HANDLER,
            $encryptStatus = false) {
        parent::__construct();
        
        $this->_session = new GGC_FilesSession($savePath, $id, $name,
                $managementType, $encryptStatus);
    }
}

?>
