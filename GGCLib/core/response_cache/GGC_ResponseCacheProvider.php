<?php
/**
 * TODO :
 * Continuare con l'implementazione delle altre funzioni di isToBeUpdated...() e
 * updateBy...() e createCompareArrayBy...().
 * 
 * NOTA :
 * In futuro si potrebbe pensare di incapsulare la logica di delle disciminanti
 * complesse, dentro una classe, in modo tale da passare un istanza di tale
 * classe anzichè tutti questi parametri. L'alternativa potrebbe essere l'utilizzo
 * di un array.
 * 
 * Description :
 * Il sistema di caching dell'output (ma in futuro anche data) funziona nel
 * seguente modo : abbiamo il caching semplice, ovvero solo con l'ausilio dell'
 * UpdateInterval, cioè, un valore temporale espresso in minuti, scaduto il quale
 * il file di cache viene aggiornato. Con la cache semplice, il framework crea un
 * solo file di cache, con il nome uguale a quello dell'entità in questione.
 * Poi abbiamo il caching complesso con, oltre all'UpdateInterval, aggiunge altre
 * discriminanti provenienti dai parametri, controlli, intestazioni, encoding e
 * custom. Tutti questi valori, possono essere forniti tramite il file di config,
 * o passati direttamente nel codice. I valori per le discimninanti UpdateByParams,
 * UpdateByHeaders, UpdateByControls e UpdateByContentEncodings possono essere
 * espressi nei seguenti modi :
 * 
 * ? = creazione di un file di cache per ogni richiesta avente nuovi nomi parametri.
 * In questo caso se l'utente effettua una richiesta con stessi parametri e stessi
 * valori, il file di cache verrà aggiornato solo se l'UpdateInteval è scaduto.
 * Se l'utente effettua, invece, una richiesta con stessi nomi parametri ma almeno
 * un valore diverso, anche se l'UpdateInterval non è scaduto, il file di cache
 * verrà ugualmente aggiornato.
 * 
 * * = creazione tanti file di cache quante sono le varianti date dai nomi
 * parametri + i rispettivi valori. In questo caso, l'aggiornamento dei file
 * di cache avviene solo se scade l'UpdateInterval, visto che se ne crea uno
 * nuovo anche se i nomi parametri sono uguali ma basta un solo valore diverso.
 * 
 * <nome parametro>=><valore> = come (*) ma solo sui parametri scelti e non su
 * tutti indiscriminatamente.
 * 
 * Ovviamente, quando si specificano i valori (?) e (*), i parametri e i rispettivi
 * valori sranno recuperati dalle rispettive fonti.
 * Per quel che riguarda UpdateByCustom, verrà trattato come una delle altre
 * fonti quando si è nella condizione di specificare direttamente i nomi e i
 * valori dei parametri.
 * 
 * Quando usiamo il sistema complesso, oltre ai file di cache, viene creato, per
 * ogni entità, un file di configurazione di nome ("<entità>_UpdateBy"), che contiene tante sezioni quanti i tipi
 * di discriminenti complesse precedentemente elencate, e in ogni sezione le
 * varie coppie <nome parametro>=<valore>. Questo file serve pere determinare se
 * nell'ultima richiesta, sono stati passati nuovi o minori parametri, oppure
 * stessi parametri ma con valori diversi, e questo confronto tra la precedente
 * e corrente richiesta, andrà a contribuire alla logica di determinazione del 
 * processo decisionale per l'aggiornamento dei file di cache.
 * 
 * Quando si utilizza il fragment caching, tramite l'ausilio delle get request,
 * ovviamente, si dovrebbe sisabilitar il caching della pagina che ospita
 * i frammenti, altrimenti appunto, il caching della pagina principale, offuscherà
 * il caching dei blocchi importati. Deto questo, si possono ance tenere entrambi,
 * e nell'occasione del rinfresco della cace pagina princiaple, verranno aggiornate
 * anche le cache dei sottoblocchi; ma è un qualcosa di poco csenso, però
 * se uno vuole lo può fare.
 *  
 * @author Gianni
 */
abstract class GGC_ResponseCacheProvider extends GGC_Provider {
    /**
     * Cache type provider.
     */
    const CTP_OUTPUT = 1;
    const CTP_DATA = 2;
    
    /**
     * Tipi di salvataggio cache.
     * CP = Cache save provider.
     */
    const CSP_FILE = 11;
    const CSP_DB = 12;
    //...
    
    /**
     * Tipi di file ai quali applicare il sistema di caching.
     * CS = Cache origin provider.
     */
    const COP_PHP_FILE = 21;
    const COP_SMARTY_TEMPLATE_FILE = 22;
    
    /*
     * Tipo di caching.
     */
    protected $cacheTypeProvider = NULL;

    /*
     * Risorsa fisica o stream di cui fare il caching.
     */
    protected $sourceUri = NULL;

    /*
     * Nome entità/risorsa oggetto del caching.
     */
    protected $entityName = NULL;
    
    /*
     * Discriminante temporale per aggiornare il contenuto della cache.
     */
    protected $updateInterval;
    
    /**
     * Discriminante nomi/chiavi da confrontare con quelli salvati, per decidere
     * se aggiornare o meno la cache. Questi valori vengono anche utilizzati
     * per essere salvati a loro volta nel file .ini e riutilizzati per il
     * prossimo confronto. In questo caso,i valori corrispondenti, vengono
     * recuperati dalle rispettive fonti. I nomi, per convenzione, devono essere
     * separati da un ";". In futuro, si potrà anche far configurare.
     */
    protected $updateByParams = NULL;
//    protected $updateByHeaders = NULL;
    protected $updateByControls = NULL;
    protected $updateByContentEncodings = NULL;
    
    /**
     * Array ricavati dai nomi passati nelle varibili sopra elencate. Questi
     * array associativi, sono formati da chiavi contenenti i valori sopra
     * elencati e da valori ricavati, a seconda del tipo.
     */
    protected $aryUpdateByParams = NULL;
//    protected $aryUpdateByHeaders = NULL;
    protected $aryUpdateByControls = NULL;
    protected $aryUpdateByContentEncodings = NULL;
    
    /*
     * IDEM come sopra, ma in questo caso vengono passati sia i nomi che i valori.
     */
    protected $aryUpdateByCustom = NULL;
    
    /*
     * Oggetto rappresentante i valori salvati nel file ini.
     */
    protected $objUpdateBy = NULL;
    
    /*
     * Root Path struttura cache.
     */
    protected $rootPath = NULL;
    
    /*
     * Contesto
     */
    protected $context = NULL;


    function __construct($context, $sourceUri, $entityName, $rootPath = NULL,
            $updateInterval = 5, $updateByParams = NULL, /*$updateByHeaders = NULL,*/
            $updateByControls = NULL, $updateByContentEncodings = NULL,
            $aryUpdateByCustom = NULL) {
        
        /**
         * Inizializzazione.
         */
        $this->context = $context;
        
        $this->sourceUri = $sourceUri;
        $this->entityName = $entityName;
        $this->updateInterval = $updateInterval * 60;
        $this->updateByParams = $updateByParams;
//        $this->updateByHeaders = $updateByHeaders;
        $this->updateByControls = $updateByControls;
        $this->updateByContentEncodings = $updateByContentEncodings;
        $this->aryUpdateByCustom = $aryUpdateByCustom;
        
        $this->rootPath = $rootPath;

        if (empty($this->rootPath)) {
            $this->rootPath = sys_get_temp_dir() . '/' .
                GGC_ApplicationManager::getApplicationName() . '/ResponseCache/';
        }
                
        /**
         * Controllo integrità.
         */
        $errMsg = $this->integrityCheck();
        
        if (!empty($errMsg)) {
            return GGC_AnomalyManagement::centralizedAnomalyManagement (NULL,
                    array('Message' => $errMsg));
        }
        
    }
    
    protected function init($mixed = NULL) {
        $this->createPath();
    }
    
    protected function createCompareArrayBy() {
        $this->createCompareArrayByParams();
//        $this->createCompareArrayByHeaders();
        $this->createCompareArrayByControls();
        $this->createCompareArrayByContentEncodings();
    }

    protected function isToBeUpdatedBy() {
        $result = false;
        
        if ($this->isToBeUpdatedByParams()) {
            $result = true;
        }
//        if ($this->isToBeUpdatedByHeaders()) {
//            $result = true;
//        }
        if ($this->isToBeUpdatedByControls()) {
            $result = true;
        }
        if ($this->isToBeUpdatedByContentEncodings()) {
            $result = true;
        }
        if ($this->isToBeUpdatedByCustom()) {
            $result = true;
        }
        
        return $result;
    }
    
    protected function updateBy() {
        $this->updateByParams();
//        $this->updateByHeaders();
        $this->updateByControls();
        $this->updateByContentEncodings();
        $this->updateByCustom();
    }

    private function isToBeUpdatedByParams() {
        $result = false;

        /*
         * Determinazione nome file valori di confronto
         */
        $updateByFileName = $this->getUpdateByFileName();

        /*
         * Se il file non esiste, già mi basta per dire che devo aggiornare
         * la cache. Se invece esiste, si ocnfrontano i valori che contiene
         * con quelli passati dall'applicazione.
         */
        if (!file_exists($updateByFileName)) {
            $result = true;

        } else {
            /**
             * Creazione oggetto rappresentante il file che contiene i valori
             * precedentemente salvati e confronto con i valori recuperati
             * per mezzo dei nomi determinati in precedenza.
             */
            if (!empty($this->aryUpdateByParams)) {
                $this->objUpdateBy =
                        new GGC_IniStructuredDataSerializationProvider($updateByFileName);

                if (!is_null($this->objUpdateBy)) {
                    $arySavedParams = $this->objUpdateBy->getGroup('UpdateByParams');

                    foreach ($this->aryUpdateByParams as $key => $value) {
                        /*
                         * Si controlla se non esiste, oppure se esiste ed è
                         * diverso da quello già presente.
                         */
                        if (!array_key_exists($key, $arySavedParams) ||
                                $value != $arySavedParams[$key]) {
                            $result = true;
                            break;
                        }
                    }

                    $this->objUpdateBy = NULL;
                }
            }
        }

        return $result;
    }
    
//    private function isToBeUpdatedByHeaders() {
//        $result = false;
//        
//        
//        return $result;
//    }
    
    private function isToBeUpdatedByControls() {
        $result = false;
        
        
        return $result;
    }
    
    private function isToBeUpdatedByContentEncodings() {
        $result = false;
        
        
        return $result;
    }
    
    private function isToBeUpdatedByCustom() {
        $result = false;
        
        
        return $result;
    }
    
    private function updateByParams() {
        if (!empty($this->aryUpdateByParams)) {
            /*
             * Determinazione nome file valori di confronto
             */
            $updateByFileName = $this->getUpdateByFileName();
            
            /**
             * Creazione oggetto rappresentante il file che contiene i valori
             * precedentemente salvati e confronto con i valori recuperati
             * per mezzo dei nomi determinati in precedenza.
             */
            $this->objUpdateBy =
                    new GGC_IniStructuredDataSerializationProvider($updateByFileName, true);

            if (!is_null($this->objUpdateBy)) {
                foreach ($this->aryUpdateByParams as $key => $value) {
                    $this->objUpdateBy->setValue('UpdateByParams', $key, $value);
                }
                
                $this->objUpdateBy->save();
                $this->objUpdateBy = NULL;
            }
        }
    }

//    private function updateByHeaders() {
//        
//    }

    private function updateByControls() {
        
    }
    
    private function updateByContentEncodings() {
        
    }
    
    private function updateByCustom() {
        
    }
    
//    private function createCompareArrayByParams() {
//        if (!empty($this->updateByParams)) {
//            /*
//             * Determinazione nomi parametri da controllare e lettura valori
//             * corrisponsenti. Il tutto viene messo dentro un array associativo,
//             * il quale sarà confrontato con i valori gia salvati sul file ini.
//             */
//            if ($this->updateByParams == '?' || $this->updateByParams == '*') {
//                $this->aryUpdateByParams = GGC_HttpRequest::getInstance()->getGet();
//            } else {
//                $this->aryUpdateByParams = GGC_HttpRequest::getInstance()->
//                        getGet(NULL, explode(';', $this->updateByParams));
//            }
//        }
//    }
    private function createCompareArrayByParams() {
        if (!empty($this->updateByParams)) {
            /*
             * Determinazione nomi parametri da controllare e lettura valori
             * corrisponsenti. Il tutto viene messo dentro un array associativo,
             * il quale sarà confrontato con i valori gia salvati sul file ini.
             */
            if ($this->updateByParams == '?' || $this->updateByParams == '*') {
                $this->aryUpdateByParams =
                        $this->context->getRequest()->getAllParameters();
            } else {
                $this->aryUpdateByParams =
                        $this->context->getRequest()->
                            getAllParameters(explode(';', $this->updateByParams));
            }
        }
    }
    
//    private function createCompareArrayByHeaders() {
//        
//    }
    
    private function createCompareArrayByControls() {
        
    }
    
    private function createCompareArrayByContentEncodings() {
        
    }

    private function integrityCheck($varName = NULL) {
        $result = NULL;
        
        if ((empty($varName) || $varName == 'sourceUri') &&
                empty($this->sourceUri)) {
            $result = '[Source Uri] non presente.';
        }
        
        if ((empty($varName) || $varName == 'entityName') &&
                empty($this->entityName)) {
            $result .= '[Entity Name] non presente.';
        }
        
        return $result;
    }
    
    private function getUpdateByFileName() {
        $result = $this->rootPath;

        if ($this->cacheTypeProvider == self::CTP_OUTPUT) {
            $result .= 'OutputCache/';
        } elseif ($this->cacheTypeProvider == self::CTP_DATA) {
            $result .= 'DataCache/';
        }

        $result .= $this->entityName . '/' . $this->entityName . '_UpdateBy';
        
        return $result;
    }
    
    private function createPath() {
        $path = $this->rootPath;

        if ($this->cacheTypeProvider == self::CTP_OUTPUT) {
            $path .= 'OutputCache/';
        } elseif ($this->cacheTypeProvider == self::CTP_OUTPUT) {
            $path .= 'DataCache/';
        }
        
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }
    
}

?>
