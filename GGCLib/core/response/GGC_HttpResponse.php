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
 * @author Gianni Carafone
 */
class GGC_HttpResponse extends GGC_Response {
    /*
     * Modalità di gestione buffer di output
     */
    const OBM_MANUAL = 1;
    const OBM_AUTO = 2;
    
    /*
     * Headers (in forma semplice/standard) da spedire al client.
     * Struttura : $_headers = array(<name> => array(<value1>, <value...n>));
     */
    private $_headers = NULL;
    
    /*
     * Headers (in forma di oggetti) da spedire al client.
     * Struttura : $_objHeaders = array(array(<objHeader1>,
     *                                             <objHeader...n>));
     */
    private $_objHeaders = NULL;
    
    /*
     * Stato replace headers
     */
    private $_headerReplaceStatus = true;

    /*
     * Array cookie (in forma semplice/standard) da spedire al client.
     * Struttura : $_cookies = array(<name> => array('name' => <...>,
     *                                                 'value' => <...>,
     *                                                 'expire' => <...>,
     *                                                 '...' => <...>));
     */
    private $_cookies = NULL;
    
    /*
     * Array oggetti "GGC_Cookie" da spedire al client.
     * Struttura : $_objCookies = array(array(<objCookie1>,
     *                                             <objCookie...n>));
     */                                            
    private $_objCookies = NULL;

    /*
     * Dati da spedire al client.
     */
    private $_data = NULL;
    
    /*
     * Determina la modalità di gestione del buffer.
     */
    private $_outputBufferMode = self::OBM_MANUAL;
    
    /*
     * Instanza di riferimento per tutta l'applicazione
     */
//    private static $_instance;
    
    function __construct() {
        parent::__construct();
        
        /*
         * Inizializzazioni.
         */
//        $this->_headers = array();
//        $this->_objHeaders = array();
//        
//        $this->_cookies = array();
//        $this->_objCookies = array();
        
        $this->setResponseDataType();
        $this->setResponseDataExchangeProtocol();
    }
    
//    static function create() {
//        if(!isset(self::$_instance))
//            self::$_instance = new GGC_HttpResponse();
//    }
    
//    static function getInstance() { 
//        self::create();
//        return self::$_instance; 
//    }
    
//    protected function setResponseDataType() {
////        $this->responseDataType =
////                GGC_HttpRequest::getInstance()->get('ResponseDataType');
//        $this->responseDataType =
//                GGC_HttpRequest::getInstance()->
//                get(GGC_HttpRequest::workParamFormat('ResponseDataType'));
//        
//        if (empty($this->responseDataType)) {
//            $requestType = GGC_HttpRequest::getInstance()->getRequestType();
//            
//            if ($requestType == 'sync') {
//                /*
//                 * Si prende il valore di default.
//                 */
//                $this->responseDataType =
//                        GGC_ConfigManager::getValue('General', 'ResponseDataTypeSync');
//                
//                /*
//                 * Poi si controlla il valore inerente l'entità in questione.
//                 */
//                //...
//                
//                /*
//                 * Alla fine sè è stata chiamata una funzione/action specifica, si controlla
//                 * se nel "config.ini" esiste un "ResponseType" per quella determinata
//                 * funzione.
//                 */
//                //...
//                
//                /*
//                 * Alla fine se non si riesce proprio a calcolare il tipo dati risposta,
//                 * si assegna direttamente.
//                 */
//                if (empty($this->responseDataType)) {
//                    $this->responseDataType = 'html';
//                }
//                
//            } else {
//                /*
//                 * Si prende il valore di default.
//                 */
//                $this->responseDataType =
//                        GGC_ConfigManager::getValue('General', 'ResponseDataTypeAsync');
//                
//                /*
//                 * Poi si controlla il valore inerente l'entità in questione.
//                 */
//                //...
//                
//                /*
//                 * Alla fine sè è stata chiamata una funzione/action specifica, si controlla
//                 * se nel "config.ini" esiste un "ResponseType" per quella determinata
//                 * funzione.
//                 */
//                //...
//                
//                /*
//                 * Alla fine se non si riesce proprio a calcolare il tipo dati risposta,
//                 * si assegna direttamente.
//                 */
//                if (empty($this->responseDataType)) {
//                    $this->responseDataType = 'json';
//                }
//            }
//        }
//    }
    protected function setResponseDataType() {
        $this->responseDataType =
                GGC_HttpRequest::getInstance()->getResponseDataType();
    }
    
    /**
     * Per ora il procedimento per calcolare il formato di scambio dati è
     * semplice, ma si possono aggiungere molte regole e valori di default
     * nel file di config, sia come valore globale di default che per singole
     * entità e azioni.
     */
//    function setResponseDataExchangeProtocol() {
//        /*
//         * Default.
//         */
////        $this->responseDataExchangeProtocol =
////                GGC_HttpRequest::getInstance()->get('ResponseDataExchangeProtocol');
//        $this->responseDataExchangeProtocol =
//                GGC_HttpRequest::getInstance()->
//                get(GGC_HttpRequest::workParamFormat('ResponseDataExchangeProtocol'));
//        
//        if (is_null($this->responseDataExchangeProtocol)) {
//            $requestType = GGC_HttpRequest::getInstance()->getRequestType();
//            
//            if ($requestType == 'sync') {
//                /*
//                 * Si prende il valore di default.
//                 */
//                $this->responseDataExchangeProtocol =
//                        GGC_ConfigManager::getValue('General',
//                                'ResponseDataExchangeProtocolSync');
//                
//                /*
//                 * Poi si controlla il valore inerente l'entità in questione.
//                 */
//                //...
//                
//                /*
//                 * Alla fine sè è stata chiamata una funzione/action specifica, si controlla
//                 * se nel "config.ini" esiste un "ResponseType" per quella determinata
//                 * funzione.
//                 */
//                //...
//                
//                /*
//                 * Alla fine se non si riesce proprio a calcolare il tipo dati risposta,
//                 * si assegna direttamente.
//                 */
////                if (empty($this->responseDataExchangeProtocol)) {
////                    $this->responseDataExchangeProtocol = 'html';
////                }
//                
//            } else {
//                /*
//                 * Si prende il valore di default.
//                 */
//                $this->responseDataExchangeProtocol =
//                        GGC_ConfigManager::getValue('General',
//                                'ResponseDataExchangeProtocolAsync');
//                
//                /*
//                 * Poi si controlla il valore inerente l'entità in questione.
//                 */
//                //...
//                
//                /*
//                 * Alla fine sè è stata chiamata una funzione/action specifica, si controlla
//                 * se nel "config.ini" esiste un "ResponseType" per quella determinata
//                 * funzione.
//                 */
//                //...
//                
//                /*
//                 * Alla fine se non si riesce proprio a calcolare il tipo dati risposta,
//                 * si assegna direttamente.
//                 */
//                if (empty($this->responseDataExchangeProtocol)) {
//                    $this->responseDataExchangeProtocol = 'json';
//                }
//            }
//        }
//    }
    function setResponseDataExchangeProtocol() {
        $this->responseDataExchangeProtocol =
                GGC_HttpRequest::getInstance()->getResponseDataExchangeProtocol();
    }
    
    function addCookie($cookie, $sendNow = true) {
        if (is_array($cookie)) {
            /*
             * Controllo presenza cookie sia nella lista di oggetti
             * che in quella del formato semplice.
             */
            if ($this->cookieExists($cookie['name']) ||
                    $this->objCookieExists($cookie['name'])) {
                return;
            }
                        
            if ($this->_outputBufferMode == self::OBM_MANUAL) {
                if ($sendNow) {
                    $this->sendCookie(NULL, true, $cookie);
                    
                } else {
                    $this->_cookies[$cookie['name']] = $cookie;
                }
                
            } else {
                $this->_cookies[$cookie['name']] = $cookie;
            }
        }
    }
    
    function setCookie($cookie, $sendNow = true) {
        if (is_array($cookie)) {
            if (array_key_exists($cookie['name'], $this->_cookies)) {
                unset($this->_cookies[$cookie['name']]);
            }
            
            if ($this->_outputBufferMode == self::OBM_MANUAL) {
                if ($sendNow) {
                    $this->sendCookie(NULL, true, $cookie);
                    
                } else {
                    $this->_cookies[$cookie['name']] = $cookie;
                }
                
            } else {
                $this->_cookies[$cookie['name']] = $cookie;
            }
        }
    }
    
    function getCookie($name) {
        $result = NULL;
        
        if (!empty($name)) {
            if (array_key_exists($name, $this->_cookies)) {
                $result = $this->_cookies[$name];
            }
        }
        
        return $result;
    }
    
    function removeCookie($name) {
        if (!empty($name) && array_key_exists($name, $this->_cookies)) {
            unset($this->_cookies[$name]);
        }
    }
    
    function sendCookie($name, $urlEncodingStatus = true, $cookie = NULL) {
        if (!empty($name)) {
            $cookie = $this->getCookie($name);
        }
            
        if (is_array($cookie)) {
            if ($urlEncodingStatus) {
                setcookie($name, $cookie['value'], $cookie['expire'],
                        $cookie['path'], $cookie['domain'], $cookie['secure'],
                        $cookie['httponly']);
            } else {
                setrawcookie($name, $cookie['value'], $cookie['expire'],
                        $cookie['path'], $cookie['domain'], $cookie['secure'],
                        $cookie['httponly']);
            }
            
            if (array_key_exists($name, $this->_cookies)) {
                unset($this->_cookies[$name]);
            }
        }
    }
    
    function sendAllCookies() {
        if (!empty($this->_cookies)) {
            foreach ($this->_cookies as $cookie) {
                $this->sendCookie(NULL, true, $cookie);
            }
        }
    }
    
    function cookieExists($name) {
        return array_key_exists($name, $this->_cookies);
    }
    
    function addObjCookie(GGC_Cookie $cookie = NULL, $sendNow = true) {
        if (!is_null($cookie)) {
            /*
             * Controllo presenza cookie sia nella lista di oggetti
             * che in quella del formato semplice.
             */
            if ($this->objCookieExists($cookie->getName()) ||
                    $this->cookieExists($cookie->getName())) {
                return;
            }
                        
            if ($this->_outputBufferMode == self::OBM_MANUAL) {
                if ($sendNow) {
                    $cookie->send();
                    
                } else {
                    $this->_objCookies[] = $cookie;
                }
                
            } else {
                $this->_objCookies[] = $cookie;
            }
        }
    }
    
    function setObjCookie(GGC_Cookie $cookie = NULL, $sendNow = true) {
        if (!is_null($cookie)) {
            foreach ($this->_objCookies as $key => $value) {
                if ($value->getName() == $cookie->getName()) {
                    $this->removeCookie($key);
                    break;
                }
            }
                        
            if ($this->_outputBufferMode == self::OBM_MANUAL) {
                if ($sendNow) {
                    $cookie->send();
                    
                } else {
                    $this->_objCookies[] = $cookie;
                }
                
            } else {
                $this->_objCookies[] = $cookie;
            }
        }
    }
    
    function getObjCookie($name) {
        $result = NULL;
        
        foreach ($this->_objCookies as $cookie) {
            if ($cookie->getName() == $name) {
                $result = $cookie;
                break;
            }
        }
        
        return $result;
    }
    
    function removeObjCookie($index = NULL, $name = NULL) {
        if (!empty($index)) {
            if (array_key_exists($index, $this->_objCookies)) {
                unset($this->_objCookies[$index]);
            }
            
        } elseif (!empty ($name)) {
            foreach ($this->_objCookies as $key => $value) {
                if ($value->getName() == $name) {
                    unset($this->_objCookies[$key]);
                    break;
                }
            }
        }
    }
    
    function objCookieExists($name) {
        $result = false;
        
        foreach ($this->_objCookies as $value) {
            if ($value->getName() == $name) {
                $result = true;
                break;
            }
        }
        
        return $result;
    }
    
    function sendObjCookie($name) {
       $cookie = $this->getObjCookie($name);
       
       if (!is_null($cookie)) {
           $cookie->send();
           unset($cookie);
       }
    }
    
    function sendAllObjCookies() {
        if (!empty($this->_objCookies)) {
            foreach ($this->_objCookies as &$cookie) {
                $cookie->send();
                unset($cookie);
            }
        }
    }

    function setOutputBufferMode($obMode = self::OBM_MANUAL) {
        $this->_outputBufferMode = $obMode;
    }
    
    function getOutputBufferMode() {
        return $this->_outputBufferMode;
    }
    
    function addHeader($name, $value, $sendNow = true) {
        if (!empty($name) && !empty($value)) {
            if ($this->_outputBufferMode == self::OBM_MANUAL) {
                if ($sendNow) {
                    header($name . ': ' . $value, $this->_headerReplaceStatus);
                    return;
                }
            }
            
            if (!array_key_exists($name, $this->_headers) || $this->_headerReplaceStatus) {
                $this->_headers[$name] = array($value);
                
            } else {
                if (!in_array($value, $this->_headers[$name])) {
                    $this->_headers[$name][] = $value;
                }
            }
         }
     }
    
    function setHeader($name, $value, $oldValue = NULL) {
        if (!empty($name) && !empty($value)) {
            if (array_key_exists($name, $this->_headers)) {
                if (!empty($oldValue)) {
                    foreach ($this->_headers[$key] as &$currValue) {
                        if ($currValue == $oldValue) {
                            $currValue = $value;
                        }
                    }
                    
                } else {
                    $this->_headers[$name] = array($value);
                }
            }    
        }
    }
    
    function getHeader($name) {
        $result = NULL;
        
        if (!empty($name)) {
            if (array_key_exists($name, $this->_headers)) {
                $result = array($name => $this->_headers[$name]);
            }
        }
        
        return $result;
    }
    
    function getHeaderValue($name, $index = 0) {
        $result = NULL;
        
        if (!empty($name)) {
            if (array_key_exists($name, $this->_headers)) {
                $result = $this->_headers[$name][$index];
            }
        }
        
        return $result;
    }
    
    function removeHeader($name) {
        $result = false;
        
        if (!empty($name)) {
            if (array_key_exists($name, $this->_headers)) {
                unset($this->_headers[$name]);
                
                /*
                 * header_remove() lo posso chiamare anche se l'header non esiste.
                 */
                if ($this->_outputBufferMode == self::OBM_MANUAL) {
                    header_remove($name);
                }
                
                $result = true;
            }
        }
        
        return $result;
    }
    
    function headerExists($name) {
        return array_key_exists($name, $this->_headers);
    }
    
    function setHeaders($aryVar, $sendNow = true) {
        if (is_array($aryVar)) {
            if ($this->_outputBufferMode == self::OBM_MANUAL) {
                if ($sendNow) {
                    foreach ($aryVar as $name => $value) {
                        foreach ($value as $hvalue) {
                            header($name . ': ' . $hvalue);
                        }
                    }
                    
                    return;
                }
            }    
                
            $this->_headers = $aryVar;
        }
    }
    
    function getHeaders() {
        return $this->_headers;
    }
    
    function getHeaderNames() {
        return array_keys($this->_headers);
    }
    
    function sendHeader($name) {
        if (array_key_exists($name, $this->_headers)) {
            foreach ($this->_headers[$name] as $value) {
                header($name . ': ' . $value, $this->_headerReplaceStatus);
            }
            
            unset($this->_headers[$name]);
        }
    }
    
    function sendAllHeaders() {
        if (!empty($this->_headers)) {
            foreach ($this->_headers as $name => $value) {
                $this->sendHeader($name);
            }
        }
    }
    
    function getHeaderReplaceStatus() {
        return $this->_headerReplaceStatus;
    }
    
    function setHeaderReplaceStatus($value) {
        $this->_headerReplaceStatus = (bool)$value;
    }
    
    function addObjHeader(GGC_HttpResponseHeader $header = NULL, $sendNow = true) {
        if (!is_null($header)) {
            if ($this->_outputBufferMode == self::OBM_MANUAL) {
                if ($sendNow) {
                    $header->send();
                    return;
                }
            }
            
            if (!$this->objHeaderExists($header->getName()) || $this->_headerReplaceStatus) {
                $this->_objHeaders[] = $header;
                
            } else {
                foreach ($this->_objHeaders as &$currHeader) {
                    if ($currHeader->getName() == $header->getName() &&
                            $currHeader->getValue() == $header->getValue()) {
                        $currHeader = $header;
                    }
                }
            }
        }
    }
    
    function setObjHeader(GGC_HttpResponseHeader $header = NULL,
            GGC_HttpResponseHeader $oldHeader = NULL) {
        if (!is_null($header) && !is_null($oldHeader)) {
            if ($this->objHeaderExists($header->getName())) {
                foreach ($this->_objHeaders as &$currHeader) {
                    if ($currHeader->getName() == $oldHeader->getName() &&
                            $currHeader->getValue() == $oldHeader->getValue()) {
                        $currHeader = $header;
                    }
                }
            }    
        }
    }
    
    function getObjHeader($name) {
        $result = NULL;
        
        if (!empty($name)) {
            foreach ($this->_objHeaders as $header) {
                if ($header->getName() == $name) {
                    $result = $header;
                    break;
                }
            }
        }
        
        return $result;
    }
    
    function removeObjHeader($name) {
        $result = false;
        
        if (!empty($name)) {
            foreach ($$this->_objHeaders as &$header) {
                if ($header->getName() == $name) {
                    unset($header);
                }
            }

            /*
             * header_remove() lo posso chiamare anche se l'header non esiste.
             */
            if ($this->_outputBufferMode == self::OBM_MANUAL) {
                header_remove($name);
            }

            $result = true;
        }
        
        return $result;
    }
    
    function objHeaderExists($name, $value = NULL) {
        $result = false;
        
        /*
         * Anzichè controllare se non è vuoto o null con empty(),
         * controllo direttamente se è un array.
         */
        if (is_array($this->_objHeaders)) {
            foreach ($this->_objHeaders as $header) {
                if ((!empty($value) && $header->getName() == $name && $header->getValue() == $value) ||
                        $header->getName() == $name) {
                    $result = true;
                    break;
                }
            }
        }
        
        return $result;
    }
    
    function sendObjHeader($name) {
        $header = $this->getObjHeader($name);
       
       if (!is_null($header)) {
           $header->send();
           unset($header);
       }
    }
    
    function sendAllObjHeaders() {
        if (!empty($this->_objHeaders)) {
            foreach ($this->_objHeaders as &$header) {
                $header->send();
                unset($header);
            }
        }
    }
    
    function setData($value) {
        $this->_data = $value;
    }
    
    function getData() {
        return $this->_data;
    }
    
    function sendRedirect($url, $exit = false) {
        if (GGC_SanitizeManager::validateUrl($url)) {
            header('location: ' . $url);

            if ($exit) {
                exit();
            }
        }
    }
    
    function sendCode($code, $msg = NULL) {
        if (!empty($code)) {
            $result = GGC_HttpRequest::getInstance()->getProtocol() . ' ' .
                    $code . ' ' . $msg;
            
            header($result);
            exit();
        }
    }
    
    function sendData() {
        /*
         * Eventuale avvio buffering output.
         */
        if ($this->_outputBufferMode == self::OBM_AUTO) {
            ob_start();
        }    
            
        /*
         * Invio cookies.
         */
        $this->sendAllCookies();
        $this->sendAllObjCookies();

        /*
         * Invio headers.
         */
        $this->sendAllHeaders();
        $this->sendAllObjHeaders();

        /*
         * Applicazione formato scambio dati.
         */
        $this->responseDataExchangeProtocolFormat($this->_data, true);
        
        /*
         * Invio dati
         */
        echo $this->_data;
        
        /*
         * Eventuale chiusura buffering output.
         */
        if ($this->_outputBufferMode == self::OBM_AUTO) {
            ob_end_flush();
        }    
    }
    
    function sendFile($file, $defaultHeaders = true) {
        if (file_exists($file)) {
            if ($defaultHeaders) {
                if ($this->_outputBufferMode == self::OBM_AUTO) {
                    ob_start();
                }
                
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename='.basename($file));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                
                if ($this->_outputBufferMode == self::OBM_AUTO) {
                    ob_clean();
                    flush();
                }
            }
            
            readfile($file);
            exit;
        }
    }
}

?>
