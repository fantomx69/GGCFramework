<?php
/**
 * Classe per la gestione assistita della risposta. Non si è obbligati ad
 * usare tale classe e le sue funzionalità. Qui non si deve creare la classe
 * 'manager' perchè non staimo applicando il pattern provider model. Se si crea la classe
 * 'manager' se ne deve creare una per pere ogni tipo di riposta, perchè possono
 * non avere metedo in comune, quindi la classe manager sarebbe una ripetizione
 * di codesta classe, allora si è optati per le funzioni statiche di creazione e
 * resituzione instanza, per avere comunque un singleton ed una gestione centralizzata.
 * 
 * TODO :
 * Implementare un sistema di sanitizzazione automatica all'atto di creazione
 * dell'oggetto request, passando al costruttore o settendo proprietà, per la
 * scelta di quale variabile da sanatizzare e ei parametri di sanitizzazione,
 * magari prevendendo una sanitizzazione di default.
 * 
 * TODO :
 * Tutti i campi qui utilizzati per personalizzare la creazione degli oggetti,
 * dovranno essere messi anche nel file di configurazione, per poi far scegliere
 * a quale criterio dare la precedneza per creare l'oggetto.
 * 
 * TODO :
 * In futuro, se necessario, oltre che ai campi di classe per la sanitizzazione,
 * mettere anche quelli instanza, dove verranno salvati, tramite il costruttore,
 * i dati passati nella creazione dei singoli oggetti, con le relative funzioni
 * "get...()" di restituzione.
 *
 * @author Gianni Carafone
 */
class GGC_HttpRequest extends GGC_ExternalRequest {
    /**
     * Tipo variabile da sanatizzare (SR = Sanitize Request)
     */
    const SR_GET = 3;
    const SR_POST = 4;
    const SR_COOKIE = 5;
    const SR_REQUEST = 6;
    const SR_SESSION = 7;
    
    /**
     * Tipo lista (black o white list) da considerare nella saitizzazione.
     * (SL = Sanitze List)
     */
    const SL_BLACK = 1;
    const SL_WHITE = 2;
    
    /*
     * Array contentente i tipi di varibili di input da sanatizzare come
     * comportamento standard/default.
     */
    protected $arySanitizeVarType = array(self::SR_GET, self::SR_POST, self::SR_COOKIE);
    
    /*
     * Array contentete criteri standard/default di sanitizzazione.
     */
    protected $arySanitizeOptions = array('filter' => GGC_SanitizeProvider::S_FILTER_STRING,
            'flags' => array(
                GGC_SanitizeProvider::S_FLAG_STRING_ENCODE_QUOTES,
                GGC_SanitizeProvider::S_FLAG_STRING_STRIP_HIGH,
                GGC_SanitizeProvider::S_FLAG_STRING_ENCODE_LOW,
                GGC_SanitizeProvider::S_FLAG_STRING_CLEAN_BLACK_LIST,
                GGC_SanitizeProvider::S_FLAG_STRING_CLEAN_WHITE_LIST),
            'options' => array());
    
    /*
     * Array contenente criteri standard/default black o white list.
     */
    protected $arySanitizeList = array('@','#','&','<','>');
    
    /*
     * Tipo lista di default da applicare.
     */
    protected $sanitizeListType = self::SL_BLACK;
    
    /*
     * Determina se sanatizzare i valori impostati nei metodi "set...()";
     */
    protected $setMethodsSanitizeStatus = false;
    
    /**
     * Dati di richiesta sanatizzati e validati.
     */
    protected $get = NULL;
    protected $post = NULL;
    protected $coockie = NULL;
    protected $request = NULL;
    protected $session = NULL;
    
    /*
     * Area contenente gli headers sotto forma di oggetti "GGC_HttpRequestHEader"
     */
    protected $aryObjHeader = NULL;
    
    static function create(
            $setMethodsSanitizeStatus = false,
            $arySanitizeVarType = NULL,
            $sanitizeSourceType = 'INPUT_ARRAY',
            $arySanitizeOptions = NULL,
            $arySanitizeList = NULL,
            $sanitizeListType = NULL,
            $instanceName = 'default') {
        
        if(!isset(self::$instance)) {
            self::$instance = new GGC_HttpRequest(
                    $setMethodsSanitizeStatus,
                    $arySanitizeVarType,
                    $sanitizeSourceType,
                    $arySanitizeOptions,
                    $arySanitizeList,
                    $sanitizeListType);
            
            parent::RequestStackUpdate(self::$instance, $instanceName);
        }
    }
    
    function __construct(
            $setMethodsSanitizeStatus = false,
            $arySanitizeVarType = NULL,
            $sanitizeSourceType = 'INPUT_ARRAY',
            $arySanitizeOptions = NULL,
            $arySanitizeList = NULL,
            $sanitizeListType = NULL) {
        parent::__construct();
        
        /**
         * Recupero eventuale prefisso e suffisso.
         */
        $this->setParamPrefixSuffix();
        
        /*
         * Per la gestione della sanitizzazione dei metodi Set.
         */
        $this->setMethodsSanitizeStatus = $setMethodsSanitizeStatus;
        
        /*
         * Sanitizzazione input.
         */
        $this->sanitize(
                $arySanitizeVarType,
                $sanitizeSourceType,
                $arySanitizeOptions,
                $arySanitizeList,
                $sanitizeListType);
        
        /*
         * Sezionamento input per una gestione più umana.
         */
        $this->requestSectioning();
    }
    
    protected function setParamPrefixSuffix() {
        self::$workParamPrefix = trim(GGC_ConfigManager::getValue('General', 'WorkParamRequestPrefix'));
        self::$workParamSuffix = trim(GGC_ConfigManager::getValue('General', 'WorkParamRequestSuffix'));
    }

    protected function setRequestType() {
        /*
         * Si prende il valore passato nella request. se esiste
         */
        $requestType = $this->get(self::workParamFormat('RequestType'));

        /*
         * NOTA :
         * Poi prende il valore di una determinata sezione di entità o pagina se esiste
         */
        if (empty($requestType)) {
            //...
        }
        
        /*
         * Si prende il valore di default
         */
        if (empty($requestType)) {
            $requestType = GGC_ConfigManager::getValue('General', 'RequestType');
        }
        
        $this->aryWorkParameters[GGC_HttpRequest::workParamFormat('RequestType')] = $requestType;
    }
    
    protected function setResponseDataType() {
        $responseDataType =
                $this->get(GGC_HttpRequest::workParamFormat('ResponseDataType'));
        
        if (empty($responseDataType)) {
            $requestType = $this->getRequestType();
            
            if ($requestType == 'sync') {
                /*
                 * Si prende il valore di default.
                 */
                $responseDataType =
                        GGC_ConfigManager::getValue('General', 'ResponseDataTypeSync');
                
                /*
                 * Poi si controlla il valore inerente l'entità in questione.
                 */
                //...
                
                /*
                 * Alla fine sè è stata chiamata una funzione/action specifica, si controlla
                 * se nel "config.ini" esiste un "ResponseType" per quella determinata
                 * funzione.
                 */
                //...
                
                /*
                 * Alla fine se non si riesce proprio a calcolare il tipo dati risposta,
                 * si assegna direttamente.
                 */
                if (empty($responseDataType)) {
                    $responseDataType = 'html';
                }
                
            } else {
                /*
                 * Si prende il valore di default.
                 */
                $responseDataType =
                        GGC_ConfigManager::getValue('General', 'ResponseDataTypeAsync');
                
                /*
                 * Poi si controlla il valore inerente l'entità in questione.
                 */
                //...
                
                /*
                 * Alla fine sè è stata chiamata una funzione/action specifica, si controlla
                 * se nel "config.ini" esiste un "ResponseType" per quella determinata
                 * funzione.
                 */
                //...
                
                /*
                 * Alla fine se non si riesce proprio a calcolare il tipo dati risposta,
                 * si assegna direttamente.
                 */
                if (empty($responseDataType)) {
                    $responseDataType = 'json';
                }
            }
        }
        
        $this->aryWorkParameters[GGC_HttpRequest::workParamFormat('ResponseDataType')] = $responseDataType;
    }
    
    protected function setResponseDataExchangeProtocol() {
        $responseDataExchangeProtocol = $this->
                get(GGC_HttpRequest::workParamFormat('ResponseDataExchangeProtocol'));
        
        if (empty($responseDataExchangeProtocol)) {
            $requestType = $this->getRequestType();
            
            if ($requestType == 'sync') {
                /*
                 * Si prende il valore di default.
                 */
                $responseDataExchangeProtocol = GGC_ConfigManager::getValue('General',
                                'ResponseDataExchangeProtocolSync');
                
                /*
                 * Poi si controlla il valore inerente l'entità in questione.
                 */
                //...
                
                /*
                 * Alla fine sè è stata chiamata una funzione/action specifica, si controlla
                 * se nel "config.ini" esiste un "ResponseType" per quella determinata
                 * funzione.
                 */
                //...
                
                /*
                 * Alla fine se non si riesce proprio a calcolare il tipo dati risposta,
                 * si assegna direttamente.
                 */
//                if (empty($this->responseDataExchangeProtocol)) {
//                    $this->responseDataExchangeProtocol = 'html';
//                }
                
            } else {
                /*
                 * Si prende il valore di default.
                 */
                $responseDataExchangeProtocol = GGC_ConfigManager::getValue('General',
                                'ResponseDataExchangeProtocolAsync');
                
                /*
                 * Poi si controlla il valore inerente l'entità in questione.
                 */
                //...
                
                /*
                 * Alla fine sè è stata chiamata una funzione/action specifica, si controlla
                 * se nel "config.ini" esiste un "ResponseType" per quella determinata
                 * funzione.
                 */
                //...
                
                /*
                 * Alla fine se non si riesce proprio a calcolare il tipo dati risposta,
                 * si assegna direttamente.
                 */
                if (empty($responseDataExchangeProtocol)) {
                    $responseDataExchangeProtocol = 'json';
                }
            }
        }
        
        $this->aryWorkParameters[GGC_HttpRequest::workParamFormat('ResponseDataExchangeProtocol')] = $responseDataExchangeProtocol;
    }

    /*
     * Se viene passata l'uri da analizzare la si utilizza, altrimenti si
     * determina la fonte della richiesta e la si analizza.
     */
//    function requestSectioning($uri = NULL) {
//        $aryQuery = NULL;
//        
//        if (!empty($uri)) {
//            parent::requestSectioning($uri);
//            
//        } else {
//            /*
//             * Considero come fonte il get sanatizzato di questa classe.
//             */
//            if ($this->getMethod() == 'GET') {
//                $aryQuery = &$this->get;
//
//            /*
//             * Considero come fonte il post sanatizzato di questa classe.
//             */    
//            } elseif ($this->getMethod() == 'POST') {
//                $aryQuery = &$this->post;
//            }
//
//            if (is_array($aryQuery)) {
//                $this->addRequestStack(self::RSDT_URI, $this->getURL());
//
//                /*
//                 * Conterrà le coppie chiave=>Valore dei parametri.
//                 */
//                $parameters = array();
//
//                foreach ($aryQuery as $key => $value) {
//                    if ($key == 'entity') {
//                        $this->addRequestStack(self::RSDT_ENTITY, $value);
//                    } elseif ($key == 'action') {
//                        $this->addRequestStack(self::RSDT_ACTION, $value);
//                    } elseif ($key == 'type_oper') {
//                        $this->addRequestStack(self::RSDT_TYPE_OPER, $value);
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
//                $this->addRequestStack(self::RSDT_PARAMETERS, $parameters);
//            }
//        }
//    }
//    protected function requestSectioning($uri = NULL) {
//        $aryQuery = NULL;
//        
//        if (!empty($uri)) {
//            parent::requestSectioning($uri);
//            
//        } else {
//            /*
//             * Considero come fonte il get sanatizzato di questa classe.
//             */
//            if ($this->getMethod() == 'GET') {
//                $aryQuery = &$this->get;
//
//            /*
//             * Considero come fonte il post sanatizzato di questa classe.
//             */    
//            } elseif ($this->getMethod() == 'POST') {
//                $aryQuery = &$this->post;
//            }
//
//            if (is_array($aryQuery)) {
//                $this->uri = $this->getURL();
//
//                /*
//                 * Conterrà le coppie chiave=>Valore dei parametri.
//                 */
//                $parameters = array();
//
//                foreach ($aryQuery as $key => $value) {
//                    if ($key == 'entity') {
//                        $this->entity = $value;
//                    } elseif ($key == 'action') {
//                        $this->action = $value;
//                    } elseif ($key == 'type_oper') {
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
//        $aryQuery = NULL;
//        
//        /**
//         * Determinazione request type, response data type e response data type
//         * exchange protocol
//         */
//        $this->setRequestType();
//        $this->setResponseDataType();
//        $this->setResponseDataExchangeProtocol();
//        
//        /*
//         * Determinazione entity, action e tutti gli altri parametri.
//         */
//        if (!empty($uri)) {
//            parent::requestSectioning($uri);
//            
//        } else {
//            /*
//             * Considero come fonte il get sanatizzato di questa classe.
//             */
//            if ($this->getMethod() == 'GET') {
//                $aryQuery = &$this->get;
//
//            /*
//             * Considero come fonte il post sanatizzato di questa classe.
//             */    
//            } elseif ($this->getMethod() == 'POST') {
//                $aryQuery = &$this->post;
//            }
//            
//            /*
//             * Assegnazione uri/url.
//             */
//            $this->uri = $this->getURL();
//            
//            /**
//             * Recupero eventuale prefisso e suffisso.
//             */
//            $paramPrefix = trim(GGC_ConfigManager::getValue('General', 'WorkParamRequestPrefix'));
//            $paramSuffix = trim(GGC_ConfigManager::getValue('General', 'WorkParamRequestSuffix'));
//
//            if (!empty($aryQuery)) {
//                /**
//                 * Recupero eventuale prefisso e suffisso.
//                 */
////                $paramPrefix = trim(GGC_ConfigManager::getValue('General', 'WorkParamRequestPrefix'));
////                $paramSuffix = trim(GGC_ConfigManager::getValue('General', 'WorkParamRequestSuffix'));
//
//                foreach ($aryQuery as $key => $value) {
//                    /*
//                     * Assegnazione parametri primari.
//                     */
//                    if ($key == $paramPrefix . 'entity' . $paramSuffix) {
//                        $this->entity = $value;
//                    } elseif ($key == $paramPrefix . 'action' . $paramSuffix) {
//                        $this->action = $value;
//                    } elseif ($key == $paramPrefix . 'type_oper' . $paramSuffix) {
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
//               $this->entity = GGC_ResourceBinding::getDefaultEntity();
//               $this->aryWorkParameters[$paramPrefix . 'entity' . $paramSuffix] = $this->entity;
//            }
//        }
//    }
    protected function requestSectioning($uri = NULL) {
        $aryQuery = NULL;
        
        /**
         * Determinazione request type, response data type e response data type
         * exchange protocol
         */
        $this->setRequestType();
        $this->setResponseDataType();
        $this->setResponseDataExchangeProtocol();
        
        /*
         * Determinazione entity, action e tutti gli altri parametri.
         */
        if (!empty($uri)) {
            parent::requestSectioning($uri);
            
        } else {
            /*
             * Considero come fonte il get sanatizzato di questa classe.
             */
            if ($this->getMethod() == 'GET') {
                $aryQuery = &$this->get;

            /*
             * Considero come fonte il post sanatizzato di questa classe.
             */    
            } elseif ($this->getMethod() == 'POST') {
                $aryQuery = &$this->post;
            }
            
            /*
             * Assegnazione uri/url.
             */
            $this->uri = $this->getURL();
            
            /*
             * Eventuale iterazione array parametri request.
             */
            if (!empty($aryQuery)) {
                foreach ($aryQuery as $key => $value) {
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
             * fornire quella di default dell'entity.
             */
            $this->setDefaultAction();
            
        }
    }
    
    function get($name = NULL) {
        $result = NULL;
        
        if (!empty($name)) {
            $httpMethod = $this->getMethod();

            if ($httpMethod == 'GET') {
                $result = $this->getGet($name);
            } elseif ($httpMethod == 'POST') {
                $result = $this->getPost($name);
            }
        }
        
        return $result;
    }
    
    function getGet($name = NULL, $aryNames = NULL) {
        $result = NULL;
        
        if (!is_null($this->get)) {
            if (!empty($name)) {
                if (array_key_exists($name, $this->get))
                    $result = $this->get[$name];

            } else {
                if (!empty($aryNames)) {
                    foreach ($aryNames as $key) {
                        if (array_key_exists($key, $this->get)) {
                            $result[$key] = $this->get[$key];
                        }
                    }
                } else {
                    $result = $this->get;
                }
            }
        }
        
        return $result;
    }
    
    protected function setGet($name = NULL, $value = NULL) {
        if (!empty($value)) {
            if ($this->setMethodsSanitizeStatus) {
                $value = $this->sanitize($value, 'SET_VAR');
            }
            
            if (!empty($name)) {
                $this->get[$name] = $value;
            } else {
                $this->get[] = $value;
            }
        }
    }
    
    function getPost($name = NULL, $aryNames = NULL) {
        $result = NULL;
        
        if (!is_null($this->post)) {
            if (!empty($name)) {
                if (array_key_exists($name, $this->post))
                    $result = $this->post[$name];

            } else {
                if (!empty($aryNames)) {
                    foreach ($aryNames as $key) {
                        $result[$key] = $this->post[$key];
                    }
                } else {
                    $result = $this->post;
                }
            }
        }
        
        return $result;
    }
    
    protected function setPost($name = NULL, $value = NULL) {
        if (!empty($value)) {
            if ($this->setMethodsSanitizeStatus) {
                $value = $this->sanitize($value, 'SET_VAR');
            }
            
            if (!empty($name)) {
                $this->post[$name] = $value;
            } else {
                $this->post[] = $value;
            }
        }
    }
    
    function getCookie($name = NULL) {
        $result = NULL;
        
        if (!is_null($this->cookie)) {
            if (!empty($name)) {
                if (array_key_exists($name, $this->coockie))
                    $result = $this->coockie[$name];

            } else {
                $result = $this->coockie;
            }
        }
        
        return $result;
    }
    
    protected function setCookie($name = NULL, $value = NULL) {
        if (!empty($value)) {
            if ($this->setMethodsSanitizeStatus) {
                $value = $this->sanitize($value, 'SET_VAR');
            }
            
            if (!empty($name)) {
                $this->coockie[$name] = $value;
            } else {
                $this->coockie[] = $value;
            }
        }
    }
    
    function getRequest($name = NULL) {
        $result = NULL;
        
        if (!is_null($this->request)) {
            if (!empty($name)) {
                if (array_key_exists($name, $this->request))
                    $result = $this->request[$name];

            } else {
                $result = $this->request;
            }
        }
        
        return $result;
    }
    
    protected function setRequest($name = NULL, $value = NULL) {
        if (!empty($value)) {
            if ($this->setMethodsSanitizeStatus) {
                $value = $this->sanitize($value, 'SET_VAR');
            }
            
            if (!empty($name)) {
                $this->request[$name] = $value;
            } else {
                $this->request[] = $value;
            }
        }
    }
    
    function getSession($name = NULL) {
        $result = NULL;
        
        if (!is_null($this->session)) {
            if (!empty($name)) {
                if (array_key_exists($name, $this->session))
                    $result = $this->request[$name];

            } else {
                $result = $this->request;
            }
        }
        
        return $result;
    }
    
    function setSession($name = NULL, $value = NULL) {
        if (!empty($value)) {
            if ($this->setMethodsSanitizeStatus) {
                $value = $this->sanitize($value, 'SET_VAR');
            }
            
            if (!empty($name)) {
                $this->session[$name] = $value;
            } else {
                $this->session[] = $value;
            }
        }
    }
    
    function getMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }
    
    function getQueryString() {
        return $_SERVER['QUERY_STRING'];
    }
    
    function getURL() {
//        return $this->getPageURL();
        return static::getPageURL();
    }
    
    function getObjURL() {
        return new GGC_URL($this->getURL());
    }
    
    function getProtocol() {
        return $_SERVER["SERVER_PROTOCOL"];
    }
    
    function getHeader($name) {
        $result = NULL;
        
        $aryHeaders = $this->getHeaders();
        
        if (array_key_exists($name, $aryHeaders)) {
            $result = $aryHeaders[$name];
        }
        
        return $result;
    }
    
    function getHeaders() {
        return getallheaders();
    }
    
    function getObjHeader($name) {
        $result = NULL;
        
        if (is_null($this->aryObjHeader)) {
            $this->createObjHeaders();
        }
        
        /*
         * Versione valida per array con indice numerico e non.
         */
//        foreach ($this->aryObjHeader as $header) {
//            if ($header->getName() == $name) {
//                $result = $header;
//            }
//        }
        
        /*
         * Versione valida per array associativo con indice valorizzato
         * con i nomi delle intestazioni.
         */
        if (array_key_exists($name, $this->aryObjHeader)) {
            $result = $this->aryObjHeader[$name];
        }
        
        return $result;
    }

    function getObjHeaders() {
        if (is_null($this->aryObjHeader)) {
            $this->createObjHeaders();
        }
        
        return $this->aryObjHeader;
    }
    
    function getBody() {
        return http_get_request_body();
    }
    
    function getBodyStream() {
        return http_get_request_body_stream();
    }
    
//    function __call($name, $arguments) {
//        if (!is_null($this->_phpHttpRequest)) {
//            return call_user_method_array($name, $this->_phpHttpRequest, $arguments);
//        } else {
//            return GGC_AnomalyManagement::centralizedAnomalyManagement (
//                        array('Message' => 'Metodo non presente.'));
//        }
//    }
    
//    private function getPageURL() {
    static function getPageURL() {
        $pageURL = 'http';
        
        if (array_key_exists('HTTPS', $_SERVER) && $_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        
        $pageURL .= "://";
        
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        
        return $pageURL;
    }
   
   private function createObjHeaders() {
       $ary = $this->getHeaders();
       
       foreach ($ary as $key => $value) {
           /*
            * Nell'aggiungere gli oggetti all'array, anzichè la chiave numerica
            * creo un array associativo von il nome dell'intestazione, in modo tale
            * da poter sfruttare tale chiave per effettuare ricerche, senza ogni volta
            * ciclare l'intero array e interpellare il metodo "getName()" per
            * risalire al nome dell'intestazione.
            */
           $this->aryObjHeader[$key] = new GGC_HttpRequestHeader($key, $value);
       }
   }
   
   private function sanitize(
           $mixed = NULL,
           $sanitizeSourceType = 'INPUT_ARRAY',
           $arySanitizeOptions = NULL,
           $arySanitizeList = NULL,
           $sanitizeListType = NULL) {
       
       $result = NULL;
           
        /**
         * Per semplicità e pulizia del codice, si fà riferimento sempre ai
         * parametri passati, per operare, valorizzandoli con i valori di
         * default, in caso siano nulli.
         */
        if (empty($mixed)) {
            $mixed = &$this->arySanitizeVarType;
        }
        
        if (!is_array($arySanitizeOptions)) {
             $arySanitizeOptions = &$this->arySanitizeOptions;
        }
        
        if (!is_array($arySanitizeList)) {
            $arySanitizeList = &$this->arySanitizeList;
        }
        
        if (empty($sanitizeListType)) {
            $sanitizeListType = $this->sanitizeListType;
        }
        
        /**
         * Implementazione sanitizzazione.
         */
        /*
         * Creazione instanza sanitizzazione.
         */
        GGC_SanitizeManager::create(GGC_SanitizeProvider::SD_GGC_HTTP);
        
        /*
         * Valorizzazione black o white list.
         */
        if ($sanitizeListType == self::SL_BLACK) {
            GGC_SanitizeManager::setSanitizeBlackList($arySanitizeList);
        } elseif ($sanitizeListType == self::SL_WHITE) {
            GGC_SanitizeManager::setSanitizeWhiteList($arySanitizeList);
        }
        
        /*
         * Sanitizzazione.
         */
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                if ($sanitizeSourceType == 'INPUT_ARRAY') {
                    if ($value == self::SR_GET && $this->getMethod() == 'GET') {
                        $this->get = GGC_SanitizeManager::examinesInputArray(
                                GGC_GGCHttpSanitizeProvider::SI_GET, $arySanitizeOptions);

                    } elseif ($value == self::SR_POST && $this->getMethod() == 'POST') {
                        $this->post = GGC_SanitizeManager::examinesInputArray(
                                GGC_GGCHttpSanitizeProvider::SI_POST, $arySanitizeOptions);

                    } elseif ($value == self::SR_COOKIE /* && aggiungere tutti i metodi http per le richieste standard*/) {
                        $this->coockie = GGC_SanitizeManager::examinesInputArray(
                                GGC_GGCHttpSanitizeProvider::SI_COOKIE, $arySanitizeOptions);
                    }
                    
                } else {
                    $result[$key] = GGC_SanitizeManager::examinesArray($mixed, $arySanitizeOptions);
                }
            }
        
        } else {
            /*
             * In questo caso passare un array options solo con i flags.
             */
            //return GGC_SanitizeManager::sanitizeString($mixed, $aryOptions);
            $result = GGC_SanitizeManager::sanitizeString($mixed);
        }
        
        return $result;
   }        
    
}

?>