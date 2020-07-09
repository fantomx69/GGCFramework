<?php
//namespace GGC_lib\core;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Serve per simulare il subclassing di "GGC_Object" per quelle classi che non
 * possono ereditare da "GGC_Object".
 *
 * @author Gianni Carafone
 */
interface GGC_IObject {
    public function __construct();
    
    function getObjID();
    
    function getObjName();
    
    function setObjName($value);
    
    function __toString();
}

?>
