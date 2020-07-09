<?php
/**
 * Description of GGC_ControllerManager
 * 
 * Classe Manager/Factory per gestire tutte le funzionalità che i controller
 * espongono.
 *
 * @author Gianni Carafone
 */
final class GGC_ControllerManager {
    private static $_aryInstances = array();

    static function create($typeName, $context, $entityName = NULL,
            $entity = NULL, $instanceName = 'default') {
        if (empty($typeName)) {
            die('Specificare il tipo di controller da creare.');
        }
        
        if (array_key_exists($instanceName, self::$_aryInstances)) {
            die('Nome instanza già presente.');
        }
        
        self::$_aryInstances[$instanceName] = new $typeName($context, 
                $entityName, $entity);

        if (self::$_aryInstances[$instanceName] === NULL) {
            die('Impossibile creare il controller : ' . $typeName);
        }
    }
    
//    static function init (/*$entity,*/ $instanceName = 'default') {
//        if (self::$_aryInstances[$instanceName] === NULL)
//            die('Istanza Controller inesistente.');
//        
//        return self::$_aryInstances[$instanceName]->init();
//    }
    
    static function run (/*$entity,*/ $instanceName = 'default') {
        if (self::$_aryInstances[$instanceName] === NULL)
            die('Istanza Controller inesistente.');
        
        return self::$_aryInstances[$instanceName]->run(/*$entity*/);
    }
    
    static function get (/*$entity,*/ $instanceName = 'default') {
        if (self::$_aryInstances[$instanceName] === NULL)
            die('Istanza Controller inesistente.');
        
        return self::$_aryInstances[$instanceName]->get(/*$entity*/);
    }
    
    static function getInstance($instanceName = 'default') {
        $result = NULL;
        
        if (array_key_exists($instanceName, self::$_aryInstances)) {
            $result = self::$_aryInstances[$instanceName];
        }
        
        return $result;
    }
}

?>
