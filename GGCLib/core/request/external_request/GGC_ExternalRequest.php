<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GGC_ExternalRequest
 *
 * @author Gianni
 */
abstract class GGC_ExternalRequest extends GGC_Request {
    /*
     * Instanza di riferimento per tutta l'applicazione
     */
    protected static $instance = NULL;
    
    static function getInstance() {
        return self::$instance;
    }

}

?>
