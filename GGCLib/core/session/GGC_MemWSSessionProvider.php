<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GGC_CookieSessionProvider
 *
 * @author Gianni Carafone
 */
class GGC_MemWSSessionProvider extends GGC_SessionProvider {
    static function create(
            $savePath = NULL,
            $id = NULL,
            $name = NULL,
            $managementType = GGC_Session::SM_EXTERNAL_HANDLER,
            $encryptStatus = false) {

        return new GGC_MemWSSessionProvider($savePath, $id, $name,
                $managementType, $encryptStatus);
    }
    
    function __construct(
            $savePath = NULL,
            $id = NULL,
            $name = NULL,
            $managementType = GGC_Session::SM_EXTERNAL_HANDLER,
            $encryptStatus = false) {
        parent::__construct();
        
        $this->_session = new GGC_MemWSSession($savePath, $id, $name,
                $managementType, $encryptStatus);
    }
}

?>
