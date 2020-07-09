<?php
/**
 * @author Gianni Carafone
 */
abstract class GGC_Request extends GGC_Object {
    /**
     * RT = Request Type
     */
    const RT_EXTERNAL = 1;
    const RT_EXTERNAL_HTTP = 2;
    const RT_EXTERNAL_CLI = 3;
    const RT_INTERNAL = 4;
    const RT_INTERNAL_FORWARD = 5;
    const RT_INTERNAL_GET = 6;
//    const RT_INTERNAL_EXECUTE = 7;
    
    /**
     * RSL = Request Stack Level
     */
    const RSL_FIRST = 0;
    const RSL_LAST = -1;
    
    /**
     * RFT = Request Field Type
     */
//    const RFT_URI = 11;
//    const RFT_ENTITY = 12;
//    const RFT_ACTION = 13;
//    const RFT_TYPE_OPER = 14;
//    const RFT_PARAMETERS = 15;
    const RFT_REQUEST_TYPE = 'RequestType'; //sync/async
    const RFT_RESPONSE_DATA_TYPE = 'ResponseDataType'; //vedi config.
    const RFT_RESPONSE_DATA_EXCHANGE_PROTOCOL = 'ResponseDataExchangeProtocol'; //vedi config.
    const RFT_ENTITY = 'Entity';
    const RFT_ACTION = 'Action';
    
    /**
     * Tipo variabile da sanatizzare
     */
    const SR_SERVER = 21;
    const SR_ENV = 22;
    
    /**
     * Per non interrogare ogni volta il sistema di configurazione.
     *
     * @var string
     */
    protected static $workParamPrefix = NULL;
    protected static $workParamSuffix = NULL;

    /**
     * Dati di richiesta sanatizzati e validati.
     */
    protected $server = NULL;
    protected $env = NULL;
    
    /*
     * Tipo richiesta Sync/Async.
     */
//    protected $requestType = NULL;

    /*
     * Cache/buffer volatile request, per qualunque utilizzo e necessità.
     * Dura il tempo delle richiesta e risposta.
     */
    private $_cache = NULL;
    
    /**
     * Campi richiesta
     */
    protected $uri;
//    protected $entity;
//    protected $action;
//    protected $typeOper;
    protected $aryParameters = array();
    protected $aryWorkParameters = array();
    
    /*
     * Array stack instanze request.
     */
    private static $_aryInstances = array();
    
    protected static function requestStackUpdate(GGC_Request $instance,
            $instanceName) {
        /**
         * Controllo integrità
         */
        $errMsg = NULL;
        
        if (!isset($instance)) {
            $errMsg = '[instance] non specificato.';
        }
        
        if (empty($instanceName)) {
            $errMsg .= PHP_EOL . '[instanceName] non specificato.';
        }
        
        if (self::instanceNameExists($instanceName)) {
            $errMsg .= PHP_EOL . '[InstanceName : ' . $instanceName . '] già presente.';
        }
        
        if (self::entityNameExists($instance->getEntity())) {
            $errMsg .= PHP_EOL . '[Entità : ' . $instance->getEntity() . '] già presente nello stack richieste.';
        }
        
        if (!empty($errMsg)) {
            return GGC_AnomalyManagement::centralizedAnomalyManagement (NULL,
                    array('Message' => $errMsg));
        }
        
        /*
         * Valorizzazione stack instanze.
         */
        self::$_aryInstances[][$instanceName] = $instance;
    }
    
    static function getInstanceByName($instanceName) {
        $result = NULL;
        
        if (!empty($instanceName)) {
            if (self::instanceNameExists($instanceName)) {
                $result =
                    self::$_aryInstances[self::getIndexFromInstanceName(
                            $instanceName)][$instanceName];
            }
        }
        
        return $result;
    }
    
    static function getInstanceByLevel($rsLevel = self::RSL_LAST) {
        $result = NULL;

        if (!empty($rsLevel)) {
            $aryTemp = self::$_aryInstances[self::requestStackKeyReclaim($rsLevel)];
            $result = $aryTemp[key($aryTemp)];
        }
        
        return $result;
    }

    static function getInstanceByEntity($entityName) {
        $result = NULL;
        
        if (!empty($entityName)) {
            foreach (self::$_aryInstances as $aryInstances) {
                foreach ($aryInstances as $instance) {
                    if ($entityName == $instance->getEntity()) {
                        $result = $instance;
                        break;
                    }
                }
            }
        }
        
        return $result;
    }

    static function getInstances($type = NULL) {
        $result = NULL;
        
        if (empty($type)) {
            $result = self::$_aryInstances;
            
        } else {
            foreach (self::$_aryInstances as $key => $value) {
                if ($value instanceof $type) {
                    $result[$key] = $value;
                }
            }
        }
            
        return $result;    
    }
    
    static function getEntityByInstanceName($instanceName) {
        $result = NULL;
        
        if (!empty($instanceName)) {
            $instance = self::getInstance($instanceName);
            
            if (!empty($instance)) {
                $result = $instance->getEntity();
            }
        }
        
        return $result;
    }
    
    static function getActionByInstanceName($instanceName) {
        $result = NULL;
        
        if (!empty($instanceName)) {
            $instance = self::getInstance($instanceName);
            
            if (!empty($instance)) {
                $result = $instance->getAction();
            }
        }
        
        return $result;
    }
    
    static function getTypeOperByInstanceName($instanceName) {
        $result = NULL;
        
        if (!empty($instanceName)) {
            $instance = self::getInstance($instanceName);
            
            if (!empty($instance)) {
                //$result = $instance->getTypeOper();
                $result = $instance->getWorkParameter('TypeOper');
            }
        }
        
        return $result;
    }
    
    static function getParameterByInstanceName($instanceName, $parameterName) {
        $result = NULL;
        
        if (!empty($instanceName) && !empty($parameterName)) {
            $instance = self::getInstance($instanceName);
            
            if (!empty($instance)) {
                $result = $instance->getParameter($parameterName);
            }
        }
        
        return $result;
    }
    
    static function getParametersByInstanceName($instanceName) {
        $result = NULL;
        
        if (!empty($instanceName)) {
            $instance = self::getInstance($instanceName);
            
            if (!empty($instance)) {
                $result = $instance->getParameters();
            }
        }
        
        return $result;
    }
    
    static function getEntityByInstanceLevel($rsLevel = self::RSL_LAST) {
        $result = NULL;
        
        if (!empty($rsLevel)) {
            $instance = self::getInstance(NULL, self::requestStackKeyReclaim($rsLevel));
            
            if (!empty($instance)) {
                $result = $instance->getEntity();
            }
        }
        
        return $result;
    }
    
    static function getActionByInstanceLevel($rsLevel = self::RSL_LAST) {
        $result = NULL;
        
        if (!empty($rsLevel)) {
            $instance = self::getInstance(NULL, self::requestStackKeyReclaim($rsLevel));
            
            if (!empty($instance)) {
                $result = $instance->getAction();
            }
        }
        
        return $result;
    }
    
    static function getTypeOperByInstanceLevel($rsLevel = self::RSL_LAST) {
        $result = NULL;
        
        if (!empty($rsLevel)) {
            $instance = self::getInstance(NULL, self::requestStackKeyReclaim($rsLevel));
            
            if (!empty($instance)) {
                $result = $instance->getWorkParameter('TypeOper');
            }
        }
        
        return $result;
    }
    
    static function getParameterByInstanceLevel($parameterName, $rsLevel = self::RSL_LAST) {
        $result = NULL;
        
        if (!empty($rsLevel) && !empty($parameterName)) {
            $instance = self::getInstance(NULL, self::requestStackKeyReclaim($rsLevel));
            
            if (!empty($instance)) {
                $result = $instance->getParameter($parameterName);
            }
        }
        
        return $result;
    }
    
    static function getParametersByInstanceLevel($rsLevel = self::RSL_LAST) {
        $result = NULL;
        
        if (!empty($rsLevel)) {
            $instance = self::getInstance(NULL, self::requestStackKeyReclaim($rsLevel));
            
            if (!empty($instance)) {
                $result = $instance->getParameters();
            }
        }
        
        return $result;
    }
    
    static function getActionByEntity($entityName = NULL, $objEntity = NULL) {
        $result = NULL;
        
        if (empty($entityName) && isset($objEntity)) {
            $entityName = $objEntity->get_name();
        }
        
        if (!empty($entityName)) {
            $instance = self::getInstanceByEntity($entityName);
            
            if (isset($instance)) {
                $result = $instance->getAction();
            }
        }
        
        return $result;
    }
    
    static function getTypeOperByEntity($entityName = NULL, $objEntity = NULL) {
        $result = NULL;
        
        if (empty($entityName) && isset($objEntity)) {
            $entityName = $objEntity->get_name();
        }
        
        if (!empty($entityName)) {
            $instance = self::getInstanceByEntity($entityName);
            
            if (isset($instance)) {
                $result = $instance->getWorkParameter('TypeOper');
            }
        }
        
        return $result;
    }
    
    static function getParameterByEntity($parameterName,
            $entityName = NULL, $objEntity = NULL) {
        $result = NULL;
        
        if (!empty($parameterName)) {
            if (empty($entityName) && isset($objEntity)) {
                $entityName = $objEntity->get_name();
            }

            if (!empty($entityName)) {
                $instance = self::getInstanceByEntity($entityName);

                if (isset($instance)) {
                    $result = $instance->getParameter($parameterName);
                }
            }
        }
        
        return $result;
    }
    
    static function getParametersByEntity($entityName = NULL, $objEntity = NULL) {
        $result = NULL;
        
        if (empty($entityName) && isset($objEntity)) {
            $entityName = $objEntity->get_name();
        }
        
        if (!empty($entityName)) {
            $instance = self::getInstanceByEntity($entityName);
            
            if (isset($instance)) {
                $result = $instance->getParameters();
            }
        }
        
        return $result;
    }
    
    static function workParamFormat($value) {
        return self::$workParamPrefix . $value . self::$workParamSuffix;
    }

    private static function requestStackKeyReclaim($key) {
        $result = $key;
        
        if ($result == self::RSL_FIRST) {
            $result = 0;
            
        } elseif ($result == self::RSL_LAST) {
            $result = count(self::$_aryInstances) - 1;
            
            if ($result < 0) {
                $result = 0;
            }
        }
        
        return $result;
    }
    
    private static function instanceNameExists($instanceName) {
        $result = false;
        
        foreach (self::$_aryInstances as $aryInstances) {
            if (array_key_exists($instanceName, $aryInstances)) {
                $result = true;
                break;
            }
        }
        
        return $result;
    }
    
    private static function entityNameExists($entityName) {
        $result = false;
        
        foreach (self::$_aryInstances as $aryInstances) {
            foreach ($aryInstances as $instance) {
                if ($entityName == $instance->getEntity()) {
                    $result = true;
                    break;
                }
            }
        }
        
        return $result;
    }

    private static function getIndexFromInstanceName($instanceName) {
        $result = NULL;
        
        foreach (self::$_aryInstances as $key => $aryInstances) {
            if (array_key_exists($instanceName, $aryInstances)) {
                $result = $key;
                break;
            }
        }
        
        return $result;
    }

//    protected function requestSectioning($uri = NULL) {
//        if (!empty($uri)) {
//            /*
//             * Si scompone l'uri e si ricavano le parti della query-string.
//             */
//            $parts = parse_url($uri);
//            parse_str($parts['query'], $query);
//            
//            /*
//             * Si analizza la query-string e si importano i valori.
//             */
//            if (count($query) > 0) {
//                $this->uri = $uri;
//
//                /*
//                 * Conterrà le coppie chiave=>Valore dei parametri.
//                 */
//                $parameters = array();
//
//                foreach ($query as $key => $value) {
//                    if ($key == 'entity') {
//                        $this->entity = $value;
//                    }
//                    if ($key == 'action') {
//                        $this->action = $value;
//                    }
//                    if ($key == 'type_oper') {
//                        $this->typeOper = $value;
//                    }
//
//                    /*
//                     * TODO :
//                     * recupero parametri dentro un array e aggiungere tutto
//                     * l'array come elemento.
//                     */
//                    $paramPrefix = trim(GGC_ConfigManager::getValue('General', 'WorkParamRequestPrefix'));
//                    
//                    if (!empty($paramPrefix) &&
//                            substr($key, 0, strlen($paramPrefix)) == $paramPrefix) {
//                        
//                        $parameters[$key] = $value;
//                        
//                    } else {
//                        $paramSuffix = trim(GGC_ConfigManager::getValue('General', 'WorkParamRequestSuffix'));
//                        
//                        if (!empty($paramSuffix) &&
//                            substr($key, -1, strlen($paramPrefix)) == $paramSuffix) {
//                        
//                            $parameters[$key] = $value;
//                        }    
//                    }
//                }
//                
//                /*
//                 * TODO :
//                 * Aggiungo l'array $parameters.
//                 */
//                $this->aryParameters = $parameters;
//            }
//        }
//    }
//    protected function requestSectioning($uri = NULL) {
//        if (!empty($uri)) {
//            /**
//             * Si scompone l'uri e si ricavano le parti della query-string.
//             */
//            $parts = parse_url($uri);
//            parse_str($parts['query'], $query);
//            
//            /*
//             * Assegnazione uri/url.
//             */
//            $this->uri = $uri;
//            
//            /**
//             * Recupero eventuale prefisso e suffisso.
//             */
//            $paramPrefix = trim(GGC_ConfigManager::getValue('General', 'WorkParamRequestPrefix'));
//            $paramSuffix = trim(GGC_ConfigManager::getValue('General', 'WorkParamRequestSuffix'));
//            
//            /*
//             * Si analizza la query-string e si importano i valori.
//             */
//            if (!empty($query)) {
//                /**
//                 * Recupero eventuale prefisso e suffisso.
//                 */
////                $paramPrefix = trim(GGC_ConfigManager::getValue('General', 'WorkParamRequestPrefix'));
////                $paramSuffix = trim(GGC_ConfigManager::getValue('General', 'WorkParamRequestSuffix'));
//
//                foreach ($query as $key => $value) {
//                    /*
//                     * Assegnazione parametri primari.
//                     */
//                    if ($key == $paramPrefix . 'entity' . $paramSuffix) {
//                        $this->entity = $value;
//                    }
//                    if ($key == $paramPrefix . 'action' . $paramSuffix) {
//                        $this->action = $value;
//                    }
//                    if ($key == $paramPrefix . 'type_oper' . $paramSuffix) {
//                        $this->typeOper = $value;
//                    }
//                    
//                    /*
//                     * Assegnazione altri parametri.
//                     */
//                    if ((!empty($paramPrefix) && substr($key, 0, strlen($paramPrefix)) == $paramPrefix) ||
//                            (!empty($paramSuffix) && substr($key, -1, strlen($paramPrefix)) == $paramSuffix)) {
//                        $this->aryWorkParameters[$key] = $value;
//                        
//                    } else {
//                        $this->aryParameters[$key] = $value;
//                    }
//                }
//            }
//            
//            /*
//             * Se il client non ha passato nessuna entità, si prova a
//             * fornire quella di default del sistema.
//             */
//            if (empty($this->entity)) {
//                $this->entity = GGC_ResourceBinding::getDefaultEntity();
//                $this->aryWorkParameters[$paramPrefix . 'entity' . $paramSuffix] = $this->entity;
//            }
//        }
//    }
    protected function requestSectioning($uri = NULL) {
        if (!empty($uri)) {
            /**
             * Si scompone l'uri e si ricavano le parti della query-string.
             */
            $parts = parse_url($uri);
            parse_str($parts['query'], $query);
            
            /*
             * Assegnazione uri/url.
             */
            $this->uri = $uri;
            
            /*
             * Si analizza la query-string e si importano i valori.
             */
            if (!empty($query)) {
                foreach ($query as $key => $value) {
                    /*
                     * Assegnazione parametri di lavoro e non.
                     */
                    if ((!empty(self::$workParamPrefix) && substr($key, 0, strlen(self::$workParamPrefix)) == self::$workParamPrefix) ||
                            (!empty(self::$workParamSuffix) && substr($key, -1, strlen(self::$workParamSuffix)) == self::$workParamSuffix)) {
                        
                        if (!array_key_exists($key, $this->aryWorkParameters)) {
                            $this->aryWorkParameters[$key] = $value;
                        } else {
                            //errore
                        }
                        
                    } else {
                        if (!array_key_exists($key, $this->aryParameters)) {
                            $this->aryParameters[$key] = $value;
                        } else {
                            //errore
                        }
                    }
                }
            }
            
            /*
             * Se il client non ha passato nessuna entità, si prova a
             * fornire quella di default del sistema.
             */
            $this->setDefaultEntity();
            
            /*
             * Se il client non ha passato nessuna action, si prova a
             * fornire quella di default del sistema.
             */
            $this->setDefaultAction();
            
        }
    }
    
    protected function setDefaultEntity() {
        if (!array_key_exists(self::workParamFormat(self::RFT_ENTITY), $this->aryWorkParameters) ||
            empty($this->aryWorkParameters[self::workParamFormat(self::RFT_ENTITY)])) {

            $this->aryWorkParameters[self::workParamFormat(self::RFT_ENTITY)] =
                    GGC_ResourceBinding::getDefaultEntity();
        }
    }
    
    protected function setDefaultAction() {
        if (!array_key_exists(self::workParamFormat(self::RFT_ACTION), $this->aryWorkParameters) ||
            empty($this->aryWorkParameters[self::workParamFormat(self::RFT_ACTION)])) {

            $this->aryWorkParameters[self::workParamFormat(self::RFT_ACTION)] =
                GGC_ResourceBinding::getDefaultActionByRealEntity(
                        GGC_ResourceBinding::getRealEntityByVirtualEntity(
                                $this->getEntity()));
        }        
    }

    function getRequestType() {
        return $this->aryWorkParameters[self::workParamFormat(self::RFT_REQUEST_TYPE)];        
    }
    
    function getResponseDataType() {
        return $this->aryWorkParameters[self::workParamFormat(self::RFT_RESPONSE_DATA_TYPE)];
    }
    
    function getResponseDataExchangeProtocol() {
        return $this->aryWorkParameters[self::workParamFormat(self::RFT_RESPONSE_DATA_EXCHANGE_PROTOCOL)];
    }
    
    function getURI() {
        return $this->uri;
    }

    function getEntity() {
        return $this->aryWorkParameters[self::workParamFormat(self::RFT_ENTITY)];
    }
    
    function getAction() {
        $result = NULL;
        
        if (array_key_exists(self::workParamFormat(self::RFT_ACTION), $this->aryWorkParameters)) {
            $result = $this->aryWorkParameters[self::workParamFormat(self::RFT_ACTION)];
        }
        return $result;
    }
    
    function getParameter($name) {
        $result = NULL;
        
        if (!empty($name) && array_key_exists($name, $this->aryParameters)) {
            $result = $this->aryParameters[$name];
        }
        
        return $result;
    }
    
    function getParameters($aryNames = NULL) {
        $result = NULL;
            
        if (!empty($aryNames)) {
            foreach ($aryNames as $key) {
                if (array_key_exists($key, $this->aryParameters)) {
                    $result[$key] = $this->aryParameters[$key];
                }
            }
            
        } else {
            $result = $this->aryParameters;
        }
        
        return $result;
    }
    
    function getWorkParameter($name, $format = true) {
        $result = NULL;
        
        if ($format) {
            $name = self::workParamFormat($name);
        }
        
        if (!empty($name) && array_key_exists($name, $this->aryWorkParameters)) {
            $result = $this->aryWorkParameters[$name];
        } 
        
        return $result;
    }
    
    function getWorkParameters($aryNames = NULL, $format = true) {
        $result = NULL;
        
        if (!empty($aryNames)) {
            foreach ($aryNames as $key) {
                if ($format) {
                    $key = self::workParamFormat($key);
                }
                
                if (array_key_exists($key, $this->aryWorkParameters)) {
                    $result[$key] = $this->aryWorkParameters[$key];
                }
            }
            
        } else {
            $result = $this->aryWorkParameters;
        }
        
        return $result;
    }
    
    function getAllParameters($aryNames = NULL) {
        /*
         * Forma concisa con type cast.
         */
//        return array_merge($this->getWorkParameters($aryNames),
//                (array)$this->getParameters($aryNames));
        
        /**
         * Forma più prolissa, ma forse, più pulita e veloce, FORSE.
         */
        $wParams = $this->getWorkParameters($aryNames);
        $params = $this->getParameters($aryNames);
        
        if (empty($params)) {
            return $wParams;
        } else {
            return array_merge($wParams, $params);
        }
        
    }

    function setCacheValue($group, $key, $value) {
        $this->initCache();
        return $this->_cache->setValue($group, $key, $value);
    }
    
    function getCacheValue($group, $key) {
        $this->initCache();
        return $this->_cache->getValue($group, $key);
    }
    
    function removeCacheValue($group, $key) {
        $this->initCache();
        return $this->_cache->removeValue($group, $key);
    }
    
    function clearCache() {
        $this->initCache();
        $this->_cache->clear();
    }
    
    private function initCache() {
        if (is_null($this->_cache)) {
            $this->_cache = new GGC_DataStruct();
        }
    }

    function getServer() {
        return $this->server;
    }
    
    function setServer($aryVar) {
        $this->server = $aryVar;
    }
    
    function getEnv() {
        return $this->env;
    }
    
    function setEnv($aryVar) {
        $this->env = $aryVar;
    }
    
    protected function integrityCheck($varName = NULL) {
        $result = NULL;
        
        /*
         * Controllo presenza parametro entità (virtuale o reale, se consentito,
         * che sia.
         */
        if ((empty($varName) || $varName == 'entity') &&
                empty($this->aryWorkParameters[self::workParamFormat(self::RFT_ENTITY)])) {
            $result = '[Entity] non presente.';
        }
        
        /*
         * Il controllo del parametro action, verrà fatto, anche, nella
         * classe base HttpController, perchè a qual punto, avrò ottenuto la
         * mappatura tra entità virtuale/passata e  quella reale, e quindi potrò
         * ricavare la action.
         * Lo anche qui, perchè altrimenti dovrei permettere di assegnare dall'esterno
         * la action, una volta calcoalta nel controller.
         */
        if ((empty($varName) || $varName == 'action') &&
                empty($this->aryWorkParameters[self::workParamFormat(self::RFT_ACTION)])) {
            $result = '[Action] non presente.';
        }
        
        return $result;
    }    

}

?>
