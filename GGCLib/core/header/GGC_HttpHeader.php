<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GGC_HttpHeader
 *
 * @author Gianni
 */
abstract class GGC_HttpHeader extends GGC_Header {
    protected $name = NULL;
    protected $value = NULL;
    
    function __construct($name, $value) {
        parent::__construct();
        
        if (!empty($name)) {
            $this->name = $name;
        }
        
        if (!empty($value)) {
            $this->value = $value;
        }
    }
    
    function getName() {
        return $this->name;
    }
    
    function getValue() {
        return $this->value;
    }
    
    function get() {
        return array('name' => $this->name, 'value' => $this->value);
    }
}

?>
