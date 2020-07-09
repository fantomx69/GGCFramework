<?php
/**
 * Description of GGC_Autoloader
 *
 * @author Gianni Carafone
 */

/*
 * TODO :
 * Implementare oltre che il salvataggio deleyed della cache autoload, anche il
 * salvataggio incrementale operando nelle funzioni di 'add()', 'remove()', ecc... .
 * Implementare i metodi "removePlus()" e "removeSmart()".
 */
class GGC_Autoloader {
    /*
     * Buffer percorso classi.
     */
    private static $_aryClasses = array();
    
    /*
     * Indica/imposta se è attivo o meno l'autocaricamento.
     */
    private static $_autoloadActive = false;

    /*
     * Gestione cache autoload.
     * Meccanismo che permette di salvare da qualche parte la lista dei
     * percorsi alle classi da utilizzare, per evitare ogni volta di doverli
     * caricare manualmente o tramite smart-autoload.
     */
    private static $_autoloadCacheStatus = false;

    /*
     * TODO :
     * Far configurare il driver e il nome file per la cache autooad anche nel file di configurazione
     * e leggerlo all'avvio applicazione.
     */
    private static $_autoloadCacheDriver = 1;
    private static $_autoloadCacheFilePath = NULL;
    private static $_autoloadCacheFileName = 'AutoloadCache.ini';
    
    /*
     * Timeout prima di di aggiornare il file di cache.
     * 
     * TODO :
     * Questo valore deve essere salvato nella configurazione dell'applicazione
     * per poi essere riletto ad ogni avvio.
     */
    private static $_autoloadCacheTimeOut = -1;
    
    /*
     * Configurazione tipo di commit cache autoload. Indica se si deve salvare
     * la cache di autoload sul rispettivo supporto di memorizzazione ad ogni
     * modifica, oppure solo alla fine della sessione o applicazione.
     * 
     * TODO :
     * In futuro mettere un timeout anche per il salvataggio della cache, in modo
     * tale che ogni tot tempo viene fatto il commit. Questo timeout deve valere
     * solo quando questa variabile è true.
     */
    private static $_autoloadCacheDelayedCommitStatus = true;
    
    /*
     * Indica se si deve aggiornare la cache ad ogni cambiamento dell'array
     * di classi locale, opuure farlo prima di disattivare l'utilizzo della cache.
     */
    private static $_autoloadCacheDelayedSyncStatus = true;
    
    /*
     * Indica se la cache oltre che leggerla la si deve anche aggiornare.
     */
    private static $_autoloadCacheSyncStatus = true;
    
    /*
     * Implementa il caricamento veloce della cache di autoload, ovvero,
     * valorizza il buffer delle classi, senza controllare uno per uno se il
     * è già presente nel buffer, appunto.
     */
    private static $_fastAutoloadCacheStatus = false;


    /*
     * Per la gestione del caricamento intelligente.
     */
    private static $_smartAutoloadActive = false;
    
    /**
     * Varibili per i riferimenti base.
     * Le seguenti variabili : $_serverDocumentRootPath, $_frameworkRootPath,
     * $_applicationName, $_applicationRootPath, sono obbligatori per far
     * funzionare lo smartAdd e il posizionamento del file cacheAutoload.
     * Le altre se fornite aiutano a velocizzare ilpcesso di caricamento delle
     * classi.
     * 
     * TODO :
     * Aggiungere tutte le altre variabili per l'applicazione.
     */
    private static $_serverDocumentRootPath = NULL;
    
    private static $_frameworkRootPath = NULL;
    private static $_frameworkLibPath = NULL;
    private static $_frameworkLibCorePath = NULL;
    private static $_frameworkLibUtilPath = NULL;
    
    private static $_applicationName = NULL;
    private static $_applicationRootPath = NULL;
    private static $_applicationLibPath = NULL;
    private static $_applicationLibCorePath = NULL;
    private static $_applicationLibUtilPath = NULL;
    private static $_applicationModelPath = NULL;
    private static $_applicationViewPath = NULL;
    private static $_applicationControllerPath = NULL;
    private static $_applicationEntityPath = NULL;
    
    /**
     * Costanti per facilitare la gestione classi da caricare.
     */
    const AL_FRAMEWORK_INIT         = 1;
    const AL_FRAMEWORK_LIB_CORE     = 2;
    const AL_FRAMEWORK_LIB_UTIL     = 4;
    const AL_FRAMEWORK_ALL          = 7;

    const AL_APPLICATION_LIB_CORE   = 8;
    const AL_APPLICATION_LIB_UTIL   = 16;
    const AL_APPLICATION_MODEL      = 32;
    const AL_APPLICATION_VIEW       = 64;
    const AL_APPLICATION_CONTROLLER = 128;
    const AL_APPLICATION_ENTITY     = 256;
    const AL_APPLICATION_ALL        = 504;

    const AL_SMARTY_LIB             = 512;

    const AL_ALL                    = 1023;


    static function init(
            $serverDocumentRootPath = NULL,
            $frameworkRootPath = NULL,
            $applicationName = NULL,
            $applicationRootPath = NULL,
            $frameworkLibPath = NULL,
            $frameworkLibCorePath = NULL,
            $frameworkLibUtilPath = NULL,
            
            $isAutoloadCache = true,
            $isFastAutoloadCache = false,
            $isSmartAutoload = true,
            $alLevel = 513 /* INIT + SMART LIB */) {
        
        /**
         * Inizializzazioni.
         */
        self::$_serverDocumentRootPath = $serverDocumentRootPath;
        self::$_frameworkRootPath = $frameworkRootPath;
        self::$_applicationName = $applicationName;
        self::$_applicationRootPath = $applicationRootPath;
        self::$_frameworkLibPath = $frameworkLibPath;
        self::$_frameworkLibCorePath = $frameworkLibCorePath;
        self::$_frameworkLibUtilPath = $frameworkLibUtilPath;
        
        self::$_autoloadCacheStatus = (bool) $isAutoloadCache;
        self::$_fastAutoloadCacheStatus = (bool) $isFastAutoloadCache;
        self::$_smartAutoloadActive = (bool) $isSmartAutoload;
        
        if (self::$_autoloadCacheStatus) {
            self::$_autoloadCacheFilePath = sys_get_temp_dir();
        } 
        
        /*
         * Eventuale valorizzazione iniziale buffer classi.
         */
        if (!empty($alLevel)) {
            self::addPlus($alLevel);
        }
    }

    static function start() {
        /**
         * Controllo integrità.
         */
        $errMsg = self::integrityCheck();
        
        if (!empty($errMsg)) {
            return GGC_AnomalyManagement::centralizedAnomalyManagement (NULL,
                    array('Message' => $errMsg));
        }
        
        spl_autoload_register(array(__CLASS__, 'loadClass'));
        self::$_autoloadActive = true;
        
        self::syncAutoloadCache();
    }
    
    static function started() {
        return self::$_autoloadActive;
    }

    static function end() {
        spl_autoload_unregister(array(__CLASS__, 'loadClass'));
        
        self::$_autoloadActive = false;
        self::syncAutoloadCache();
    }
    
    static function add($aryValue) {
//        if (self::$_smartAutoloadActive) {
//            return;
//        }

        foreach ($aryValue as $key => $value) {
            if (!array_key_exists($key, self::$_aryClasses))
                self::$_aryClasses[$key] = $value;    
        }

    }
    
    /**
     * Funzione che dato un nome classe, mi carica l'intera gerarchia dei
     * genitori a cui la classe appartiene.
     * 
     * @param string $className
     * @param objRef $obj
     */
    static function addHierarchy($className = NULL, $obj = NULL) {
//        if (self::$_smartAutoloadActive) {
//            return;
//        }
        
        if (!empty($className)) {
            $aryParentClasses = class_parents($className);
            
            self::addSmart($className);

            foreach ($aryParentClasses as $parentClassName) {
                self::addSmart($parentClassName);
            }

        } elseif (!empty($obj)) {
            self::add(array($obj->getClassName() =>
                    $obj->getClassDirPath() . $obj->getClassFileName()));
        }
    }
    
    /*
     * Aggiunta classi in modo guidato, semplificato e più potente rispetto alla
     * semplice funzione add().
     */
    static function addPlus($alLevel) {
//        if (self::$_smartAutoloadActive) {
//            return;
//        }
        if (($alLevel & self::AL_ALL) == self::AL_ALL) {
            self::addSmart();
            
        } else {
            if (($alLevel & self::AL_FRAMEWORK_ALL) == self::AL_FRAMEWORK_ALL) {
                self::addSmart(NULL, self::$_serverDocumentRootPath .
                        self::$_frameworkRootPath . self::$_frameworkLibPath);
                
            } else {
                if (($alLevel & self::AL_FRAMEWORK_INIT) == self::AL_FRAMEWORK_INIT) {
                    self::add(array('GGC_Object' => 'GGC_Object.php',
                        'GGC_IObject' => 'GGC_IObject.php',
                        'GGC_IObserver' => 'GGC_IObserver.php',
                        'GGC_Provider' => 'GGC_Provider.php',
                        'GGC_ApplicationManager' => 'application/GGC_ApplicationManager.php',
                        'GGC_ApplicationProvider' => 'application/GGC_ApplicationProvider.php',
                        'GGC_ControllerDispatcher' => 'controller/GGC_ControllerDispatcher.php',
                        'GGC_ControllerManager' => 'controller/GGC_ControllerManager.php',
                        'GGC_ControllerProvider' => 'controller/GGC_ControllerProvider.php',
                        'GGC_AnomalyManagement' => 'GGC_AnomalyManagement.php',
                        'GGC_DataStruct' => 'GGC_DataStruct.php',
                        'GGC_Entity' => 'GGC_Entity.php',
                        'GGC_Control' => 'GGC_Control.php',
                        'GGC_Component' => 'GGC_Component.php',
                        'GGC_ResourceBinding' => 'GGC_ResourceBinding.php',
                        'GGC_Context' => 'context/GGC_Context.php',
                        'GGC_Exception' => 'exception/GGC_Exception.php',
                        'GGC_Error' => 'error/GGC_Error.php',
                        'GGC_ConfigManager' => 'config/GGC_ConfigManager.php',
                        'GGC_ConfigProvider' => 'config/GGC_ConfigProvider.php',
                        'GGC_MemConfigProvider' => 'config/GGC_MemConfigProvider.php',
                        'GGC_AutoloaderCacheManager' => 'autoloader_cache/GGC_AutoloaderCacheManager.php',
                        'GGC_AutoloaderCacheProvider' => 'autoloader_cache/GGC_AutoloaderCacheProvider.php',
                        'GGC_IniAutoloaderCacheProvider' => 'autoloader_cache/GGC_IniAutoloaderCacheProvider.php',
                        'GGC_StructuredDataSerializationProvider' => 'structured_data_serialization/GGC_StructuredDataSerializationProvider.php',
                        'GGC_IniStructuredDataSerializationProvider' => 'structured_data_serialization/GGC_IniStructuredDataSerializationProvider.php',
                        'GGC_MemStructuredDataSerializationProvider' => 'structured_data_serialization/GGC_MemStructuredDataSerializationProvider.php',
                        'GGC_XmlStructuredDataSerializationProvider' => 'structured_data_serialization/GGC_XmlStructuredDataSerializationProvider.php'
                        ));
                } 
                
                if (($alLevel & self::AL_FRAMEWORK_LIB_CORE) == self::AL_FRAMEWORK_LIB_CORE) {
                    self::addSmart(NULL, self::$_serverDocumentRootPath .
                            self::$_frameworkRootPath .
                            self::$_frameworkLibPath .
                            self::$_frameworkLibCorePath);
                }
                
                if (($alLevel & self::AL_FRAMEWORK_LIB_UTIL) == self::AL_FRAMEWORK_LIB_UTIL) {
                    self::addSmart(NULL, self::$_serverDocumentRootPath .
                            self::$_frameworkRootPath .
                            self::$_frameworkLibPath .
                            self::$_frameworkLibUtilPath);
                }
            }
            
            if (($alLevel & self::AL_APPLICATION_ALL) == self::AL_APPLICATION_ALL) {
                self::addSmart(NULL, self::$_serverDocumentRootPath .
                        self::$_applicationRootPath);
                
            } else {
                if (($alLevel & self::AL_APPLICATION_LIB_CORE) == self::AL_APPLICATION_LIB_CORE) {
                    self::addSmart(NULL, self::$_serverDocumentRootPath .
                            self::$_applicationRootPath .
                            self::$_applicationLibPath .
                            self::$_applicationLibCorePath);
                }
                
                if (($alLevel & self::AL_APPLICATION_LIB_UTIL) == self::AL_APPLICATION_LIB_UTIL) {
                    self::addSmart(NULL, self::$_serverDocumentRootPath .
                            self::$_applicationRootPath .
                            self::$_applicationLibPath .
                            self::$_applicationLibUtilPath);
                }
                
                if (($alLevel & self::AL_APPLICATION_MODEL) == self::AL_APPLICATION_MODEL) {
                    self::addSmart(NULL, self::$_serverDocumentRootPath .
                            self::$_applicationRootPath .
                            self::$_applicationModelPath);
                }
                
                if (($alLevel & self::AL_APPLICATION_VIEW) == self::AL_APPLICATION_VIEW) {
                    self::addSmart(NULL, self::$_serverDocumentRootPath .
                            self::$_applicationRootPath .
                            self::$_applicationViewPath);
                }
                
                if (($alLevel & self::AL_APPLICATION_CONTROLLER) == self::AL_APPLICATION_CONTROLLER) {
                    self::addSmart(NULL, self::$_serverDocumentRootPath .
                            self::$_applicationRootPath .
                            self::$_applicationControllerPath);
                }
                
                if (($alLevel & self::AL_APPLICATION_ENTITY) == self::AL_APPLICATION_ENTITY) {
                    self::addSmart(NULL, self::$_serverDocumentRootPath .
                            self::$_applicationRootPath .
                            self::$_applicationEntityPath);
                }
                
            }
            
            if (($alLevel & self::AL_SMARTY_LIB) == self::AL_SMARTY_LIB) {
                self::add(array('Smarty' => 'Smarty.class.php',
                    'Smarty_Internal_Write_File' => 'Smarty.class.php',
                    'Smarty_Internal_TemplateCompilerBase' => 'Smarty.class.php',
                    'Smarty_Internal_Templatelexer' => 'Smarty.class.php',
                    'Smarty_Internal_Templateparser' => 'Smarty.class.php',
                    'Smarty_Internal_CompileBase' => 'Smarty.class.php'));
            }
        }
    }
    
    /**
     * TODO :
     * Aggiungere la possibilità di cercare anche nell'applicazione corrente,
     * questo per permettere all'utente di fornire le sue classi specializzate e
     * al contempo fornigli un sistema di loading delle sue classi, totalmente
     * trasparente, nel qual caso, l'utente, non abbia già provveduto a caricare
     * le sue classi tramite le costanti inerenti lo spazio applicativo.
     * 
     * @param string $className
     * @param string $initPath
     */
    private static function addSmart($className = NULL, $initPath = NULL) {
        /*
         * Si prende, se esiste, il percorsao passato come parametro.
         */
        $path = $initPath;
        
        /*
         * Se per errore si è arrivati a questo punto e la path ancora non esiste,
         * si considera, ovvero il framework e l'applicazione.
         */
        if (empty($path)) {
            $path = self::$_serverDocumentRootPath . self::$_frameworkRootPath;
        }
        
        $objects = 
            new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path),
                    RecursiveIteratorIterator::SELF_FIRST);
        
        foreach($objects as $name => $object){
            if ($object->getExtension() == 'php') {
                $fileName = $object->getFilename();
                $fileExtPos = strrpos($fileName, ".", 1);
                
                if (!empty($className)) {
                    if ( strtolower($className) == 
                            strtolower(substr($fileName, 0, strlen($fileName) - (strlen($fileName) - $fileExtPos))) ) {
                        self::add(array($className => $name));
                        break;
                    }
                } else {
                    $className = substr($fileName, 0, strlen($fileName) - (strlen($fileName) - $fileExtPos));
                    self::add(array($className => $name));
//                    break;
                }
            }   
        }
        
    }
    
    static function remove($key) {
        if (array_key_exists($key, self::$_aryClasses))
            unset(self::$_aryClasses[$key]);
    }
    
    /*
     * TODO :
     * Da implementare e rendere pubblica.
     */
    private static function removePlus($alLevel) {
        
    }
    
    static function update($aryValue) {
        foreach ($aryValue as $key => $value) {
            if (array_key_exists($key, self::$_aryClasses))
                self::$_aryClasses[$key] = $value;    
        }
        
    }
    
    static function exists($key) {
        return array_key_exists($key, self::$_aryClasses);
    }
    
    static function clear() {
        unset(self::$_aryClasses);
    }
    
    static function loadClass($name) {
        if (!array_key_exists($name, self::$_aryClasses) &&
                self::$_smartAutoloadActive) {
            self::addSmart($name, self::getPathFromClassName($name));
        }
                
        if (!array_key_exists($name, self::$_aryClasses)) {
            die('Class "' . $name . '" not found.');
        } else {
            require_once self::$_aryClasses[$name];
        }
    }
    
    static function getValue($key) {
        if (array_key_exists($key, self::$_aryClasses))
            return self::$_aryClasses[$key];
    }
    
    static function getValues() {
        return self::$_aryClasses;
    }
    
    static function getServerDocumentRootPath() {
        return self::$_serverDocumentRootPath;
    }
    static function setServerDocumentRootPath($value) {
        self::$_serverDocumentRootPath = $value;
    }
    
    static function getFrameworkRootPath() {
        return self::$_frameworkRootPath;
    }
    static function setFrameworkRootPath($value) {
        self::$_frameworkRootPath = $value;
    }
    
    static function getFrameworkLibPath() {
        return self::$_frameworkLibPath;
    }
    static function setFrameworkLibPath($value) {
        self::$_frameworkLibPath = $value;
    }

    static function getFrameworkLibCorePath() {
        return self::$_frameworkLibCorePath;
    }
    static function setFrameworkLibCorePath($value) {
        self::$_frameworkLibCorePath = $value;
    }
    
    static function getFrameworkLibUtilPath() {
        return self::$_frameworkLibUtilPath;
    }
    static function setFrameworkLibUtilPath($value) {
        self::$_frameworkLibUtilPath = $value;
    }
    
    static function getApplicationName() {
        return self::$_applicationName;
    }
    static function setApplicationName($value) {
        self::$_applicationName = $value;
    }

    static function getApplicationRootPath() {
        return self::$_applicationRootPath;
    }
    static function setApplicationRootPath($value) {
        self::$_applicationRootPath = $value;
    }
    
    static function getApplicationLibPath() {
        return self::$_applicationLibPath;
    }
    static function setApplicationLibtPath($value) {
        self::$_applicationLibPath = $value;
    }
    
    static function getApplicationLibCorePath() {
        return self::$_applicationLibCorePath;
    }
    static function setApplicationLibCoretPath($value) {
        self::$_applicationLibCorePath = $value;
    }
    
    static function getApplicationLibUtilPath() {
        return self::$_applicationLibUtilPath;
    }
    static function setApplicationLibUtiltPath($value) {
        self::$_applicationLibUtilPath = $value;
    }
    
    static function getApplicationModelPath() {
        return self::$_applicationModelPath;
    }
    static function setApplicationModeltPath($value) {
        self::$_applicationModelPath = $value;
    }
    
    static function getApplicationViewPath() {
        return self::$_applicationViewPath;
    }
    static function setApplicationViewtPath($value) {
        self::$_applicationViewPath = $value;
    }
    
    static function getApplicationControllerPath() {
        return self::$_applicationControllerPath;
    }
    static function setApplicationControllerPath($value) {
        self::$_applicationControllerPath = $value;
    }
    
    static function getApplicationEntityPath() {
        return self::$_applicationEntityPath;
    }
    static function setApplicationEntitytPath($value) {
        self::$_applicationEntityPath = $value;
    }
    
    static function getSmartAutoload() {
        return self::$_smartAutoloadActive;
    }
    
    static function setSmartAutoload($value) {
        self::$_smartAutoloadActive = (bool)$value;
    }
    
    /**
     * Gestione cache autoload
     */
    static function getAutoloadCache() {
        return self::$_autoloadCacheStatus;
    }
    
    static function setAutoloadCache($value) {
        self::$_autoloadCacheStatus = (bool) $value;
    }
    
    static function getAutoloadCacheDriver() {
        return self::$_autoloadCacheDriver;
    }
    
//    static function setAutoloadCacheDriver($value) {
//        self::$_autoloadCacheDriver = $value;
//    }
    
    static function getAutoloadCacheTimeout() {
        return self::$_autoloadCacheTimeOut;
    }
    
//    static function setAutoloadCacheTimeout($value) {
//        self::$_autoloadCacheTimeOut = $value;
//    }
    
    static function getAutoloadCacheDelayedCommit() {
        return self::$_autoloadCacheDelayedCommitStatus;
    }
    
    static function setAutoloadCacheDelayedCommit($value) {
        self::$_autoloadCacheDelayedCommitStatus = (bool) $value;
    }
    
    static function getAutoloadCacheDelayedSync() {
        return self::$_autoloadCacheDelayedSyncStatus;
    }
    
    static function setAutoloadCacheDelayedSync($value) {
        self::$_autoloadCacheDelayedSyncStatus = (bool) $value;
    }
    
    static function getAutoloadCacheSync() {
        return self::$_autoloadCacheSyncStatus;
    }
    
    static function setAutoloadCacheSync($value) {
        self::$_autoloadCacheSyncStatus = (bool) $value;
    }

    static function addAutoloadCache($aryVar = NULL, $alLevel = NULL) {
        if (is_array($aryVar)) {
            foreach ($aryVar as $key => $value) {
                if (!GGC_AutoloaderCacheManager::keyExists('AutoloadCache', $key)) {
                    GGC_AutoloaderCacheManager::setValue('AutoloadCache', $key, $value);
                }
            }
            
        } elseif (!empty($alLevel)) {
            //...
        }
    }
    
    static function removeAutoloadCache($key = NULL, $alLevel = NULL) {
        if (!empty($key)) {
            GGC_AutoloaderCacheManager::removeValue('AutoloadCache', $key);
            
        } elseif (!empty($alLevel)) {
            //...
        }
    }
    
    static function clearAutoloadCache() {
        GGC_AutoloaderCacheManager::clear();
    }
    
    protected static function openAutoloadCache() {
        GGC_AutoloaderCacheManager::open(
                    self::$_autoloadCacheDriver,
                    self::$_autoloadCacheFilePath . '/' . 
                    self::$_applicationName . '/' .
                    self::$_autoloadCacheFileName, true);
    }
    
    protected static function syncAutoloadCache() {
        if (self::$_autoloadCacheStatus) {
            if (!GGC_AutoloaderCacheManager::opened()) {
                self::openAutoloadCache();
            }
            
            $aryVar = GGC_AutoloaderCacheManager::getGroup('AutoloadCache');
            
            if (!empty($aryVar)) {
                if (self::$_fastAutoloadCacheStatus) {
                    self::$_aryClasses = $aryVar;
                } else {
                    self::add($aryVar);
                }
                
            }
            
        } else {
            if (self::$_autoloadCacheSyncStatus &&
                    self::$_autoloadCacheDelayedSyncStatus &&
                    count(self::$_aryClasses) > 0) {
                
                self::addAutoloadCache(self::$_aryClasses);
                GGC_AutoloaderCacheManager::save();
            }
        }
    }
    
    protected static function integrityCheck($varName = NULL) {
        $result = NULL;
        
        if ((empty($varName) || $varName == '_serverDocumentRootPath') &&
                empty(self::$_serverDocumentRootPath)) {
            $result = '[serverDocumentRootPath] non presente.';
        }
        
        if ((empty($varName) || $varName == '_frameworkRootPath') &&
                empty(self::$_frameworkRootPath)) {
            $result .= PHP_EOL . '[frameworkRootPath] non presente.';
        }
        
        if ((empty($varName) || $varName == '_applicationName') &&
                empty(self::$_applicationName)) {
            $result .= PHP_EOL . '[applicationName] non presente.';
        }
        
        if ((empty($varName) || $varName == '_applicationRootPath') &&
                empty(self::$_applicationRootPath)) {
            $result .= PHP_EOL . '[applicationRootPath] non presente.';
        }
        
        return $result;
    }
    
    /**
     * 
     * @param string $className
     */
    protected static function getPathFromClassName($className = NULL) {
        $result = NULL;
        
        if (!empty($className)) {
            if (substr($className, 0, 4) == 'GGC_') {
                $result = self::$_serverDocumentRootPath .
                    self::$_frameworkRootPath . self::$_frameworkLibPath;
                
            } else {
                $result = self::$_serverDocumentRootPath .
                    self::$_applicationRootPath;
            }
        }
        
        return $result;
    }
    
}

?>
