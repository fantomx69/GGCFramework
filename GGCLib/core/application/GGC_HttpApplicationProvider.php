<?php
//namespace GGC_lib\core;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GGC_Application
 * 
 * Questa classe funge da interfaccia con i valori globali del file di configu-
 * razione (sezione "General"). In questa classe dovrà essere messo anche l'autoload,
 * l'impostazione della localizzazione (scelta lingua), globalizzazione (scelta
 * formato ora, valuta, punteggiatura, ecc...), impostazione gestione errori ed
 * eccezioni centralizzate, lettura iniziale config, importazione valori sezione
 * "General", con possibilità di cambiamento temporaneo e definitivo tramite una
 * funzione "saveConfig($paramName = NULL, $paramValue = NULL), ecc... .
 *
 * Per questa classe, ma in future per tutte le classi, permettere di impostare
 * il livello di importanza/precedenza dei dati per l'inizializzazione, ovvero, 
 * mettere una proprietà con i seguenti valori : c=conf, f=field, p=param; e in
 * base a questo ordine si considerano i valori dalle rispettive fonti, e volendo,
 * anche tale scelta, può essere messa nel file file di conf, prendendo, quindi,
 * prima quei valori, dopo quelli definiti nei campi della classe, ed, eventualmente,
 * quelli passati.
 * 
 * @author Gianni Carafone
 * 
 */

class GGC_HttpApplicationProvider extends GGC_ApplicationProvider {
    protected $httpMethod = NULL;

    function __construct($serverDocumentRootPath = NULL, $frameworkRootPath = NULL,
            $applicationRootPath = NULL, $configDriver = NULL,
            $applicationName = NULL, $applicationCacheFilePath = NULL) {
        
        parent::__construct($serverDocumentRootPath, $frameworkRootPath, 
            $applicationRootPath, $applicationName, $applicationCacheFilePath);
        
        /*
         * Tipo applicazione.
         */
        $this->applicationType = 'http';
        
        /*
         * Metodo http di request.
         */
        $this->httpMethod = $_SERVER['REQUEST_METHOD'];
        
        /*
         * Driver di gestione file di configurazione.
         */
        $this->configDriver = $configDriver;
        
    }
    
    /*
     * NOTA / TODO :
     * Tutte le operazioni inerenti l'inizializzazione dovrebbero prendere spunto
     * dal file di configurazione, se l'utente non interviene. Oltre a questo, si dà
     * la possibilità di non richiamare l'init e di provvedere all'inizializzazione
     * personalemente, richiamando singolarmente le varie funzioni della classe application,
     * le quali funzioni drovranno essere parametrizzate per poter scegliere al momento
     * il comportamento voluto. Altro modo di personalizzare potrebbe essere fatto
     * tramite l'impostazione di varibaili prima di lanciare l'applicazione e quindi
     * l'init. Altro modo ancora, sarebbe quello di eseguire la derivazione della
     * classe apllication e riscrivere l'init.
     * Fare tutto cio con la seguente sequenza/importanza :
     * 1) Caricare i dati dal file di conf.
     * 2) Se sono stati settati le proprietà dell'oggetto application, prendere in
     *    considerazione, queste.
     * 3) Se sono stati passati parametri, prendere in considerazione questi.
     */
    protected function init($mixed = NULL) {
        parent::init();
        
        /*
         * Load configurazione.
         */
        $this->configLoad();    
            
        /*
         * Sincronizzazione configurazione
         */
        $this->configSync();
        
        /**
         * Recupero eventuale prefissi e suffissi entità principali del sistema.
         */
        $this->setPrefixSuffix();        
        
        /**
         * Creazione oggetto request
         */
        $this->createRequest();
        
        /*
         *  Inizializzazione sessione.
         */
        GGC_SessionManager::start();
        
        /*
         * Globalizzazione
         */
        //...
        
        /*
         * Localizzazione
         */
        //...
        
        /*
         * Autenticazione.
         */
        $this->authentication();
    }
    
    function run ($doInit = true) {
        if ($doInit)
            $this->init();
        
        $controllerDispatcher = 
            new GGC_ControllerDispatcher(GGC_HttpRequest::getInstance(),
                new GGC_HttpResponse());
        
        $controllerDispatcher->run();
    }
    
    function getHttpMethod() {
      return $this->httpMethod;  
    }
    
    /*
     * TODO :
     * Fare i controlli di forma, ovvero non permettere l'aqsseganzione di metodi 
     * non validi.
     */
    protected function setHttpMethod($value) {
        $this->httpMethod = $value;
    }
    
    /**
     * Imposta i prefissi e suffissi per gli aspetti cruciali del framework,
     * per evitare ogni volta di interrogare il sistema di configurazione.
     */
    protected function setPrefixSuffix() {
        self::$modelPrefix = trim(GGC_ConfigManager::getValue('General', 'ModelPrefix'));
        self::$modelSuffix = trim(GGC_ConfigManager::getValue('General', 'ModelSuffix'));
        
        self::$dataModelPrefix = trim(GGC_ConfigManager::getValue('General', 'DataModelPrefix'));
        self::$dataModelSuffix = trim(GGC_ConfigManager::getValue('General', 'DataModelSuffix'));
        
        self::$viewPrefix = trim(GGC_ConfigManager::getValue('General', 'ViewPrefix'));
        self::$viewSuffix = trim(GGC_ConfigManager::getValue('General', 'ViewSuffix'));
        
        self::$controllerPrefix = trim(GGC_ConfigManager::getValue('General', 'ControllerPrefix'));
        self::$controllerSuffix = trim(GGC_ConfigManager::getValue('General', 'ControllerSuffix'));
        
        self::$entityPrefix = trim(GGC_ConfigManager::getValue('General', 'EntityPrefix'));
        self::$entitySuffix = trim(GGC_ConfigManager::getValue('General', 'EntitySuffix'));
    }

    /*
     * Allinea il la configurazione proveniente da file fisico a quella iniziale
     * personalizzata.
     */
    private function configSync() {
        /*
         * Si importano i valori iniziali se diversi da quelli salvati e se
         * valorizzati.
         */
        $saveConf =false;

        if (!empty($this->serverDocumentRootPath) &&
                GGC_ConfigManager::getValue('General', 'ServerDocumentRootPath') !=
                $this->serverDocumentRootPath) {
            GGC_ConfigManager::setValue('General', 'ServerDocumentRootPath',
                $this->serverDocumentRootPath);
            $saveConf = true;
            
        } elseif (empty($this->serverDocumentRootPath)) {
            $this->serverDocumentRootPath = GGC_ConfigManager::getValue('General', 'ServerDocumentRootPath');
        }    

        if (!empty($this->frameworkRootPath) &&
                GGC_ConfigManager::getValue('General', 'FrameworkRootPath') !=
                $this->frameworkRootPath) {
            GGC_ConfigManager::setValue('General', 'FrameworkRootPath',
            $this->frameworkRootPath);
            $saveConf = true;
            
        } elseif (empty($this->frameworkRootPath)) {
            $this->frameworkRootPath = GGC_ConfigManager::getValue('General', 'FrameworkRootPath');
        }

        if (!empty($this->applicationRootPath) &&
                GGC_ConfigManager::getValue('General', 'ApplicationRootPath') !=
                $this->applicationRootPath) {
            GGC_ConfigManager::setValue('General', 'ApplicationRootPath',
            $this->applicationRootPath);
            $saveConf = true;
            
        } elseif (empty($this->applicationRootPath)) {
            $this->applicationRootPath = GGC_ConfigManager::getValue('General', 'ApplicationRootPath');
        }

        if (!empty($this->applicationName) &&
                GGC_ConfigManager::getValue('General', 'ApplicationName') !=
                $this->applicationName) {
            GGC_ConfigManager::setValue('General', 'ApplicationName',
            $this->applicationName);
            $saveConf = true;
            
        } elseif (empty($this->applicationName)) {
            $this->applicationName = GGC_ConfigManager::getValue('General', 'ApplicationName');
        }
        
        if (!empty($this->applicationCachePath) &&
                GGC_ConfigManager::getValue('General', 'ApplicationCachePath') !=
                $this->applicationCachePath) {
            GGC_ConfigManager::setValue('General', 'ApplicationCachePath',
            $this->applicationCachePath);
            $saveConf = true;
            
        } elseif (empty($this->applicationCachePath)) {
            $this->applicationCachePath = GGC_ConfigManager::getValue('General', 'ApplicationCachePath');
        }

        /*
         * Per ora disabilito la modifica del file di conf, finchè non
         * viene gestita l'autenticazione e autotizzazione degli utenti.
         */
//            if ($saveConf)
//                GGC_ConfigManager::save ();
    }
    
    private function configLoad() {
        if ($this->configDriver == GGC_ConfigProvider::SD_INI) {
            /*
             * Si aggiorna il loader con la classe specifica opportuna.
             */
            if (!GGC_Autoloader::getSmartAutoload()) {
                GGC_Autoloader::add(array('GGC_IniConfigProvider' => 
                    $this->serverDocumentRootPath . $this->frameworkRootPath .
                    'GGC_lib/core/config/GGC_IniConfigProvider.php'));
            }
        
            /*
             * Si carica il file di configurazione.
             */
            $configFileName = $this->serverDocumentRootPath .
                $this->applicationRootPath . 'config/http/ini/config.ini';
            GGC_ConfigManager::open($configFileName, GGC_ConfigProvider::SD_INI);
        }
    }
    
    private function createRequest() {
        GGC_HttpRequest::create();
    }
    
    private function authentication() {
        /*
         * Si prova a vedere se la sessione o i cookie di richiesta
         * contengono il token di autenticazione. Se è così si fornisce
         * alla procedura di login per l'autenticazione, o si prova
         * l'autenticazione guest.
         */
        $authToken = GGC_SessionManager::getValue('GGC_AuthToken');
        
        if (empty($authToken)) {
            //Si preleva dai cookie
            //$authToken = 
        }
        
        GGC_Authentication::login($authToken);
    }
    
    
//    private function createResponse() {
////        GGC_HttpResponse::create();
//    }
}

?>
