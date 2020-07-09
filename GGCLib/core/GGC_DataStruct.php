<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GGC_DataStruct
 * 
 * Questa rappresenta la classe base per gestire i dati di qualunque formato.
 * Tutte le fonti dati verranno riportate qui dentro ad una forma bidimensionale
 * anche se derivano da strutture multilivello. Per quando riguarda, ad esempio :
 * formato INI = array(<nome sezione> => array(<chiave> => <valore>));
 * formato tabella DB = array(<num. rec> => array(<campo> => <valore>));
 * formato XML = array(<path nodo> => array(<chiave> => <valore>)), dove
 *    <path nodo> = <nodo1->nodo2->nodo3->nodoN>.
 * 
 * NOTA/TODO :
 * Decidere in futuro se rendere questa classe "abstract"
 *
 * @author Gianni Carafone
 */
class GGC_DataStruct {
    protected $_data = array();
    protected $_delimiter = '->';

    function load() {
        ;
    }
    
    function save() {
        ;
    }
    
    function init() {
        ;
    }

    function getValue($group, $key)
    {
        $result = NULL;
        
        if ($this->groupExists($group)) {
            if (array_key_exists($key, $this->_data[$group])) {
                $result = $this->_data[$group][$key];
            }
        }
        
        return $result;
    }
    
    function setValue($group, $key, $value)
    {
        $this->_data[$group][$key] = $value;

        if(isset($this->_data[$group][$key]))
            return true;
        else 
            return false;
    }
    
    function removeValue($group, $key) {
        $result = false;
        
        if ($this->groupExists($group) &&
                array_key_exists($key, $this->_data[$group])) {
            unset ($this->_data[$group][$key]);
            $result = true;
        }
        
        return $result;
    }
    
    function keyExists($group, $key) {
        return $this->groupExists($group) &&
                array_key_exists($key, $this->_data[$group]);
    }
    
    function valueExists($group, $value) {
        return $this->groupExists($group) &&
                in_array($value, $this->_data[$group]);
    }

    function getGroup($key)
    {
        $result = NULL;
        
        if (array_key_exists($key, $this->_data)) {
            $result = $this->_data[$key];
        }    

        return $result;
    }
    
    function setGroup($group, $array, $setDeepMode = false, $setEmptyMode = false)
    {
        if(!is_array($array))
            return false;
        
        if ($setDeepMode) {
            foreach ($array as $key => $value) {
                if ($setEmptyMode || (!$setEmptyMode && !empty($value)))
                    $this->setValue ($group, $key, $value);
            }
            
        } else
            return $this->_data[$group] = $array;
    }
    
    function removeGroup($key) {
        $result = false;
        
        if (array_key_exists($key, $this->_data)) {
            unset ($this->_data[$key]);
            $result = true;
        }
        
        return $result;
    }
    
    function groupExists($key) {
        return array_key_exists($key, $this->_data);
    }

    /*
     * Questa funzione può svolgere quattro compiti diversi, ossia :
     * 1) Ritorna un semplice valore.
     * 2) Ritorna una sezione intera.
     * 3) Ritorna l'intero data array.
     */
    function get($group = NULL, $key = NULL)
    {
        $result = NULL;
        
        if(empty($group) && empty($key)) {
            $result = $this->_data;
            
        } elseif (empty($key)) {
            $result = $this->getSection($group);
            
        } else {
            $result = $this->getValue($group, $key);
        }
        
        return $result;
    }

    /*
     * Questa funzione può svolgere quattro compiti diversi, ossia :
     * 1) Impostare un semplice valore;
     * 2) Impostare una sezione intera;
     * 3) Reimpostare l'intero data array.
     * 4) Reimpostare sezioni scelta dl data array.
     */
    function set($group, $key = NULL, $value = NULL, $setDeepMode = false,
            $setEmptyMode = false)
    {
        if(is_array($group) && GGC_Array::countDimension($group) == 2 &&
                empty($key) && empty($value)) {
            
            if ($setDeepMode) {
                foreach ($group as $subGroup => $subGroupValues) {
                    $this->setGroup($subGroup, $subGroupValues, $setDeepMode,
                            $setEmptyMode);
                }
                return true;
                
            } else
                return $this->_data = $group;
        }
        
        if(is_array($key) && is_null($value))
            return $this->setGroup($group, $key);

        return $this->setValue($group, $key, $value);
    }
    
    function clear() {
        unset($this->_data);
    }
    
    function isEmpty() {
        return empty($this->_data);
    }
    
    function getDelimiter() {
        return $this->_delimiter;
    }
    
    function setDelimiter($value) {
        $this->_delimiter = $value;
    }
    
}

?>
