<?php
/**
 * Description of GGC_Response
 * 
 * NOTA/TODO :
 * Decidere se spostare la logica di controllo se utilizzare la cache di output
 * o la cache dati, dai vari controller nella classe Response/HttpResponse, in
 * modo da centralizzare la gestione. Valutare però se questo potrebbe sporcare
 * le classi Response, le quali dovrebbero solo occuparsi di gestire la
 * restituzione dei risultati al client.
 *
 * @author Gianni Carafone
 */
class GGC_Response extends GGC_Object {
    /*
     * Cache/buffer volatile response, per qualunque utilizzo e necessità.
     * Dura il tempo delle richiesta e risposta.
     */
    private $_cache = NULL;
    
    /**
     * Gestione del caching della risorsa per velocizzare future richieste.
     * Questa cache verrà serializzata in qualche forma e su doversi supporti
     * che possono essere, oltre che file su disco, anche memoria che vive oltre
     * l'applicazione e quindi le informazioni rimangono disposnibili tra diverse
     * richieste.
     */
    /*
     * Abilita o meno la gestione della chache.
     */
//    private $_outputCacheStatus = false;
    
    /**
     * IDEM come sopra ma per i soli dati dinamici che possono provenire da,
     * file, db, ecc... .
     * Cache dati risposta.
     */
//    private $_dataCacheStatus = false;
    
    /*
     * Array associativo (chiave=>valore) di valori di confronto, per decidere
     * se prelevare di nuovo il contenuto del file. Questi rappresentano un
     * altro sistemza, oltre alla data e ora, per discernere se aggiornare o meno
     * la cache. Di solito sono rappresentati da parametri, della quary, o vaori
     * contenuti in controlli di input, o altro.
     */
//    private $_comparisonValuesUpdateCache = NULL;
    
//    private $_dataCache = NULL; //oggetto cache.
    
    /*
     * Tipo risposta Sync/Async.
     */
    protected $responseDataType = NULL;
    
    /*
     * Protocollo scambio dati : JSON, SOAP, RSS, ecc...
     */
    protected $responseDataExchangeProtocol = NULL;

    function __construct() {
        parent::__construct();
        
//        $this->_cache = new GGC_DataStruct();
    }
    
    function getResponseDataType() {
        return $this->responseDataType;
    }
    
    function getResponseDataExchangeProtocol() {
        return $this->responseDataExchangeProtocol;
    }

    /**
     * TODO :
     * Funzioni gestione salvataggio e recupero risorse salvate precedentemente
     * per il caching risorse e maggiore velocità di risposta.
     */
    //...
    
    /**
     * Funzioni per la gestione della cache volatile.
     */
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
    
    protected function responseDataExchangeProtocolFormat(&$value, $isByRef = false) {
        $result = NULL;
        
        if ($this->responseDataExchangeProtocol == 'json') {
            if ($isByRef) {
                $value = json_encode($value);
            } else {
                $result = json_encode($value);
            }
            
//        } elseif (/*.....*/) {
            
        }
        
        return $result;
    }
}

?>
