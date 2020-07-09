<?php
/**
 * Classe che rappresenta l'applicazione.
 * 
 * TODO :
 * Aggiungere anche gli altri metodi per la gestione della cache di
 * applicazione, che trovano riscontro nella classe "GGC_IniFile".
 *
 * @author Gianni Carafone
 */
abstract class GGC_ApplicationProvider extends GGC_Provider {
    /*
     * Cache/buffer persistente applicazione, per qualunque utilizzo e necessità.
     * 
     * NOTA :
     * La creazione della cache avviene tramite un meccanismo di delayed creation,
     * ovvero, la creazione avviene al primo tentativo di utilizzo, in modo tale
     * da non sprecare risorse inutili.
     */
    private $_cache = NULL;

    /**
     * Nome e tipo applicazione
     */
    protected $applicationName = NULL;
    protected $applicationType = NULL;
    
    /*
     * Driver di gestione sistema di configurazione
     */
    protected $configDriver = NULL;
    
    /**
     * Dati percorsi principali delle entità più importanti del sistema.
     */
    protected $serverDocumentRootPath = NULL;
    protected $frameworkRootPath = NULL;
    protected $applicationRootPath = NULL;
    
    protected $applicationCacheFilePath = NULL;
    
    /**
     * Per non interrogare ogni volta il sistema di configurazione.
     *
     * @var string
     */
    protected static $modelPrefix = NULL;
    protected static $modelSuffix = NULL;
    protected static $dataModelPrefix = NULL;
    protected static $dataModelSuffix = NULL;
    protected static $viewPrefix = NULL;
    protected static $viewSuffix = NULL;
    protected static $controllerPrefix = NULL;
    protected static $controllerSuffix = NULL;
    protected static $entityPrefix = NULL;
    protected static $entitySuffix = NULL;
    
    function __construct($serverDocumentRootPath = NULL, 
        $frameworkRootPath = NULL, $applicationRootPath = NULL,
        $applicationName = NULL, $applicationCacheFilePath = NULL) {
        parent::__construct();
        
        /*
         * Percorso root application server su cui gira il framework e
         * l'applicazione.
         */
        $this->serverDocumentRootPath = $serverDocumentRootPath;
        
        /*
         * Percorso root del framework.
         */
        $this->frameworkRootPath = $frameworkRootPath;
        
        /*
         * Percorso root applicazione.
         */
        $this->applicationRootPath = $applicationRootPath;
        
        /*
         * Nome applicazione.
         */
        $this->applicationName = $applicationName;
        
        /*
         * Percorso file cache multiuso, applicazione.
         */
        $this->applicationCacheFilePath = $applicationCacheFilePath;
    }
    
    function __destruct() {
        $this->saveCache();
    }
    
    protected function init($mixed = NULL) {
        /*
         * Vebosità errori
         */
        error_reporting(E_ALL | E_STRICT);
        
        /*
         * Encoding
         */
        mb_internal_encoding('UTF-8');
        
        /**
         * Gestione eccezioni ed errori.
         */
//        if (!GGC_AnomalyManagement::started(GGC_AnomalyManagement::M_EXCEPTION))
//            GGC_AnomalyManagement::start (GGC_AnomalyManagement::M_EXCEPTION);
//        
//        if (!GGC_AnomalyManagement::started(GGC_AnomalyManagement::M_ERROR))
//            GGC_AnomalyManagement::start (GGC_AnomalyManagement::M_ERROR);
    }
    
    abstract function run();
    
    static function modelNameFormat($value) {
        return self::$modelPrefix . $value . self::$modelSuffix;
    }
    
    static function dataModelNameFormat($value) {
        return self::$dataModelPrefix . $value . self::$dataModelSuffix;
    }
    
    static function viewNameFormat($value) {
        return self::$viewPrefix . $value . self::$viewSuffix;
    }
    
    static function controllerNameFormat($value) {
        return self::$controllerPrefix . $value . self::$controllerSuffix;
    }
    
    static function entityNameFormat($value) {
        return self::$entityPrefix . $value . self::$entitySuffix;
    }

    function getApplicationType() {
        return $this->applicationType;
    }

    function getConfigDriver() {
        return $this->configDriver;
    }
    
    protected function setConfigDriver($value) {
        $this->configDriver = $value;
    }
    
    function getServerDocumentRootPath() {
        return $this->serverDocumentRootPath;
    }
    
    protected function setServerDocumentRootPath($value) {
        $this->ServerDocumentRootPath = $value;
    }
    
    function getFrameworkRootPath() {
        return $this->frameworkRootPath;
    }
    
    protected function setFrameworkRootPath($value) {
        $this->_frameworkRootPath = $value;
    }
    
    function getApplicationRootPath() {
        return $this->applicationRootPath;
    }
    
    protected function setApplicationRootPath($value) {
        $this->applicationRootPath = $value;
    }
    
    function getApplicationName() {
        return $this->applicationName;
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
    
    function saveCache() {
        if (!is_null($this->_cache)) {
            $this->_cache->save();
        }
    }
    
    private function initCache() {
        if (is_null($this->_cache)) {
            $fileName = $this->applicationCacheFilePath . '/' .
                    $this->applicationName . '/' .
                    'ApplicationCache.ini';

            $this->_cache = new GGC_IniFile($fileName, true);
        }
    }
    
}

?>