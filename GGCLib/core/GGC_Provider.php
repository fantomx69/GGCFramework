<?php
//namespace GGC_lib\core;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GGC_Provider
 * 
 * Funge da classe base per tutte le classi astratte derivate, le quali fungeranno
 * da contratto per le ulteriori classi implementative.
 *
 * @author Gianni Carafone
 */
abstract class GGC_Provider extends GGC_Object {
    abstract protected function init($mixed = NULL);
}

?>
