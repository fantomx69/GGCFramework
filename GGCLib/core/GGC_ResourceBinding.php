<?php
/**
 * Si occupa della gestione dei parametri di lavoro.
 * Li recupera, ne controlla la validità.
 *
 * @author Gianni
 */
class GGC_ResourceBinding {
    static function virtualEntityExists($entityName) {
        return array_key_exists($entityName, GGC_ConfigManager::getGroup('ValidVirtualEntities'));
    }
    
    static function realEntityExists($entityName) {
        return array_key_exists($entityName, GGC_ConfigManager::getGroup('ValidRealEntities'));
    }
    
    static function getDefaultEntity() {
        $result = NULL;
        
        if (array_key_exists('DefaultEntity', GGC_ConfigManager::getGroup('General'))) {
            $result = GGC_ConfigManager::getValue('General', 'DefaultEntity');
        }
        
        return $result;
    }
    
    static function getVirtualEntityByRealEntity($entityName) {
        $result = NULL;
        
        $aryEntityBinding = GGC_ConfigManager::getGroup('VirtualEntityToRealEntityBinding');
        
        foreach ($aryEntityBinding as $key => $value) {
            if ($entityName == $value) {
                $result = $key;
                break;
            }
        }
        
        return $result;
    }
    
    static function getRealEntityByVirtualEntity($entityName) {
        $result = NULL;
        
        if (array_key_exists($entityName, GGC_ConfigManager::getGroup('VirtualEntityToRealEntityBinding'))) {
            $result = GGC_ConfigManager::getValue('VirtualEntityToRealEntityBinding', $entityName);
        }
        
        return $result;
    }
    
    static function getVirtualEntities() {
        $result = NULL;
        
        if (array_key_exists('ValidVirtualEntities', GGC_ConfigManager::get())) {
            $result = GGC_ConfigManager::getGroup('ValidVirtualEntities');
        }
        
        return $result;
    }
    
    static function getRealEntities() {
        $result = NULL;
        
        if (array_key_exists('ValidRealEntities', GGC_ConfigManager::get())) {
            $result = GGC_ConfigManager::getGroup('ValidRealEntities');
        }
        
        return $result;
    }
    
    static function getControllerByRealEntity($entityName) {
        $result = NULL;
        
        if (array_key_exists($entityName, GGC_ConfigManager::getGroup('RealEntityToControllerBinding'))) {
            $result = GGC_ConfigManager::getValue('RealEntityToControllerBinding', $entityName);
        }
        
        return $result;
    }
    
    static function getDefaultActionByRealEntity($entityName) {
        $result = NULL;
        
        if (array_key_exists('DefaultAction', GGC_ConfigManager::getGroup($entityName))) {
            $result = GGC_ConfigManager::getValue($entityName, 'DefaultAction');
        }
        
        return $result;
    }
    
    static function virtualActionExists($entityName, $actionName) {
        return array_key_exists($actionName,
                GGC_ConfigManager::getGroup($entityName . '->ValidVirtualActions'));
    }
    
    static function realActionExists($entityName, $actionName) {
        return array_key_exists($actionName,
                GGC_ConfigManager::getGroup($entityName . '->ValidRealActions'));
    }
    
    static function getVirtualActionByRealAction($entityName, $actionName) {
        $result = NULL;
        
        $aryActionBinding = 
            GGC_ConfigManager::getGroup($entityName . '->VirtualActionToRealActionBinding');
        
        foreach ($aryActionBinding as $key => $value) {
            if ($actionName == $value) {
                $result = $key;
                break;
            }
        }
        
        return $result;
    }
    
    static function getRealActionByVirtualAction($entityName, $actionName) {
        $result = NULL;
        
        if (array_key_exists($actionName, GGC_ConfigManager::getGroup($entityName . '->VirtualActionToRealActionBinding'))) {
            $result = GGC_ConfigManager::getValue($entityName . '->VirtualActionToRealActionBinding', $actionName);
        }
        
        return $result;
    }
    
    static function getPageFromRealAction($entityName, $actionName) {
        $result = NULL;
        
        if (array_key_exists($actionName, GGC_ConfigManager::getGroup($entityName . '->RealActionToPageBinding'))) {
            $result = GGC_ConfigManager::getValue($entityName . '->RealActionToPageBinding', $actionName);
        }
        
        return $result;
    }
    
    static function getFunctionFromRealAction($entityName, $actionName) {
        $result = NULL;
        
        if (array_key_exists($actionName, GGC_ConfigManager::getGroup($entityName . '->RealActionToFunctionBinding'))) {
            $result = GGC_ConfigManager::getValue($entityName . '->RealActionToFunctionBinding', $actionName);
        }
        
        return $result;
    }
    
//    static function getEffectiveEntity($context) {
//        $result = NULL;
//        
//        if (isset($context)) {
//            /*
//             * Si prende l'entità esistente nelle richiesta.
//             */
//            $entityParam = $context->getRequest()->getEntity();
//            
//            /*
//             * Se l'entità della richiesta è inesistente, si prende quella di
//             * default del sistema.
//             */
//            if (empty($entityParam) && array_key_exists('DefaultEntity',
//                    GGC_ConfigManager::getGroup('General'))) {
//                
//                    $entityParam = GGC_ConfigManager::getValue('General',
//                            'DefaultEntity');
//            }
//        
//            /*
//             * Si controlla la presenza d valori nella lista filtro entità virtuali
//             * se non è consentito l'accesso diretto alla lista valori entità reali.
//             */
//            if (!empty($entityParam) && count(GGC_ConfigManager::getGroup('ValidVirtualEntities')) > 0 && 
//                    !in_array($entityParam, GGC_ConfigManager::getGroup('ValidVirtualEntities'))) {
//
//                $entityParam = NULL;
//            }
//            
//            /*
//             * Una volta accertato il parametro entity e se questo appartiene alla
//             * eventuale lista di filtro "ValidVirtualEntities" se l'accesso diretto
//             * ai valori reali non è possibile, ci accingiamo a proseguire nei controlli.
//             */
//            if (!empty($entityParam)) {
//                /*
//                 * Se il valore è presente nella lista di quelli virtuali, si controlla
//                 * la tabella di associazione tra valori virtuali e quelli reali.
//                 */
//                if (array_key_exists($entityParam, GGC_ConfigManager::getGroup('VirtualEntityToRealEntityBinding')) &&
//                        (GGC_ConfigManager::getValue('VirtualEntityToRealEntityBinding', $entityParam)) &&
//                        array_key_exists(GGC_ConfigManager::getValue('VirtualEntityToRealEntityBinding', $entityParam),
//                                GGC_ConfigManager::getGroup('RealEntityToControllerBinding')) &&
//                        (GGC_ConfigManager::getValue('RealEntityToControllerBinding',
//                                GGC_ConfigManager::getValue('VirtualEntityToRealEntityBinding', $entityParam)))) {
//                    
//                    if (count(GGC_ConfigManager::getGroup('ValidRealEntities')) > 0) {
//                        if (in_array($entityParam, GGC_ConfigManager::getGroup('ValidRealEntities'))) {
//                            $result = GGC_ConfigManager::getValue('VirtualEntityToRealEntityBinding', $entityParam);
//                        }
//                               
//                    } else {
//                        $result = GGC_ConfigManager::getValue('VirtualEntityToRealEntityBinding', $entityParam);
//                    }
//                }
//            
//                if (empty($result)) {
//                    /*
//                     * Se è permesso l'accesso diretto ai valori reali, si controllano
//                     * questi ultimi, ma non prima di aver controllato l'eventuale lista
//                     * di filtro dei valori reali.
//                     */
//                    if (GGC_ConfigManager::getValue('General', 'RealEntityNameDirectAccess') == 1) {
//                        if (count(GGC_ConfigManager::getGroup('ValidRealEntities')) > 0) {
//                            if (in_array($entityParam, GGC_ConfigManager::getGroup('ValidRealEntities')) &&
//                                    array_key_exists($entityParam, GGC_ConfigManager::getGroup('RealEntityToControllerBinding')) &&
//                                    (GGC_ConfigManager::getValue('RealEntityToControllerBinding', $entityParam))) {
//
//                                $result = $entityParam;
//                            }
//
//                        } else {
//                            if (array_key_exists($entityParam, GGC_ConfigManager::getGroup('RealEntityToControllerBinding')) &&
//                                    (GGC_ConfigManager::getValue('RealEntityToControllerBinding', $entityParam))) {
//
//                                $result = $entityParam;
//                            }
//                        }
//                    }
//                }
//            }
//        }
//        
//        return $result;
//    }
    
//    static function getEffectiveAction($context, $entity = NULL) {
//        $result = NULL;
//        
//        if (isset($context)) {
//            /*
//             * Determinazione entity effettiva.
//             */
//            if (empty($entity)) {
//                $entity = self::getEffectiveEntity($context);
//            }
//            
//            if (!empty($entity)) {
//                /*
//                 * Se è presente come parametro, viene considerato;
//                 */
//                $actionParam = $context->getRequest()->getAction();
//
//                /*
//                 * Se action è null, viene ricavato dal file di config.
//                 */
//                if (empty($actionParam)) {
//                    if (array_key_exists('DefaultAction', GGC_ConfigManager::getGroup($entity)) &&
//                            (GGC_ConfigManager::getValue($entity, 'DefaultAction'))) {
//
//                        $actionParam = GGC_ConfigManager::getValue($entity, 'DefaultAction');
//                    }
//                }
//
//                /*
//                 * Si controlla la presenza d valori nella lista filtro actions virtuali
//                 * se non è consentito l'accesso diretto alla lista valori actions reali.
//                 */
//                if ($actionParam != NULL &&
//                        count(GGC_ConfigManager::getGroup($entity . '->ValidVirtualActions')) > 0 && 
//                        !in_array($actionParam, GGC_ConfigManager::getGroup($entity . '->ValidVirtualActions'))) {
//
//                    $actionParam = NULL;
//                }
//                
//                /*
//                 * Una volta accertato il parametro action e se questo appartiene alla
//                 * eventuale lista di filtro "ValidVirtualEntities" se l'accesso diretto
//                 * ai valori reali non è possibile, ci accingiamo a proseguire nei controlli.
//                 */
//                if (!empty($actionParam)) {
//                    /*
//                     * Se il valore è presente nella lista di quelli virtuali, si controlla
//                     * la tabella di associazione tra valori virtuali e quelli reali.
//                     */
//                    if (array_key_exists($actionParam, GGC_ConfigManager::getGroup($entity . '->VirtualActionToRealActionBinding')) &&
//                            (GGC_ConfigManager::getValue($entity . '->VirtualActionToRealActionBinding', $actionParam) != '') &&
//                            ((array_key_exists(GGC_ConfigManager::getValue($entity . '->VirtualActionToRealActionBinding', $actionParam),
//                                    GGC_ConfigManager::getGroup($entity . '->RealActionToPageBinding')) &&
//                                    (GGC_ConfigManager::getValue($entity . '->RealActionToPageBinding', GGC_ConfigManager::getValue($entity . '->VirtualActionToRealActionBinding', $actionParam)) != '')) ||
//                            (array_key_exists(GGC_ConfigManager::getValue($entity . '->VirtualActionToRealActionBinding', $actionParam),
//                                    GGC_ConfigManager::getGroup($entity . '->RealActionToFunctionBinding')) &&
//                                    (GGC_ConfigManager::getValue($entity . '->RealActionToFunctionBinding', GGC_ConfigManager::getValue($entity . '->VirtualActionToRealActionBinding', $actionParam)) != '')))) {
//                        
//                        if (count(GGC_ConfigManager::getGroup($entity . '->ValidRealActions')) > 0) {
//                            if (in_array(GGC_ConfigManager::getValue($entity . '->VirtualActionToRealActionBinding', $actionParam),
//                                    GGC_ConfigManager::getGroup($entity . '->ValidRealActions'))) {
//                                
//                                $result = GGC_ConfigManager::getValue($entity . '->VirtualActionToRealActionBinding', $actionParam);
//                            }
//                                   
//                        } else {
//                            $result = GGC_ConfigManager::getValue($entity . '->VirtualActionToRealActionBinding', $actionParam);
//                        }
//                    }  
//
//                    if (empty($result)) {
//                        /*
//                         * Se è permesso l'accesso diretto ai valori reali, si controllano
//                         * questi ultimi, ma non prima di aver controllato l'eventuale lista
//                         * di filtro dei valori reali.
//                         */
//                        if (GGC_ConfigManager::getValue('General', 'RealActionNameDirectAccess') == 1 ||
//                                GGC_ConfigManager::getValue($entity, 'RealActionNameDirectAccess') == 1) {
//                            
//                            if (count(GGC_ConfigManager::getGroup($entity . '->ValidRealActions')) > 0) {
//                                if (in_array($actionParam, GGC_ConfigManager::getGroup($entity . '->ValidRealActions')) &&
//                                        ((array_key_exists($actionParam, GGC_ConfigManager::getGroup($entity . '->RealActionToPageBinding')) &&
//                                        (GGC_ConfigManager::getValue($entity . '->RealActionToPageBinding', $actionParam) != '')) ||
//                                        (array_key_exists($actionParam, GGC_ConfigManager::getGroup($entity . '->RealActionToFunctionBinding')) &&
//                                        (GGC_ConfigManager::getValue($entity . '->RealActionToFunctionBinding', $actionParam) != '')))) {
//                                    
//                                    $result = $actionParam;
//                                }
//
//                            } else {
//                                if ((array_key_exists($actionParam, GGC_ConfigManager::getGroup($entity . '->RealActionToPageBinding')) &&
//                                        (GGC_ConfigManager::getValue($entity . '->RealActionToPageBinding', $actionParam) != '')) ||
//                                        (array_key_exists($actionParam, GGC_ConfigManager::getGroup($entity . '->RealActionToFunctionBinding')) &&
//                                                (GGC_ConfigManager::getValue($entity . '->RealActionToFunctionBinding', $actionParam) != ''))) {
//                                  
//                                    $result = $actionParam;
//                                }
//                            }
//                        }
//                    }
//                }
//            } //if (!empty($entity))
//        } //if (isset($context))
//        
//        return $result;
//    }
}

?>
