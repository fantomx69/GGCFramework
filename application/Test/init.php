<?php
    /**
     * Determinazione tipo di applicazione.
     */
    $applicationType = '';
    
    if (isset($_SERVER['SERVER_PROTOCOL']) &&
            strpos($_SERVER['SERVER_PROTOCOL'], 'HTTP') !== false) {
        $applicationType = 'http';
    } else {
        $applicationType = 'cli';
    }

    /**
     * Determinazione automatica struttura directories framework e applicazione, 
     * sempre che si siano rispettate le regole di struttura directories.
     * Se talune regole non sono state rispettate, assegnare manualmente tali
     * valori.
     */
    $aryDir = explode('/', dirname($_SERVER["SCRIPT_NAME"]));
        
    $serverDocumentRootPath = $_SERVER['DOCUMENT_ROOT'] . '/';
    $frameworkRootPath = $aryDir[1] . '/';
    $frameworkLibPath = 'GGC_lib/';
    $frameworkLibCorePath = 'core/';
    $frameworkLibUtilPath = 'util/';
    $applicationRootPath = $frameworkRootPath . $aryDir[2] . '/' .
            $aryDir[3] . '/';
    $applicationName = 'Test';
    $applicationCachePath = sys_get_temp_dir();
    
    /*
     * Impostazione percorso personalizzato salvataggio sessione/i.
     */
    //$sessionSavePath = session_save_path() . '/' . $applicationName . '/';
    $sessionSavePath = 'C:/xampp/tmp/' . $applicationName . '/';
       
    /*
     * Inclusione file/classe autoloader.
     */
    require $serverDocumentRootPath . $frameworkRootPath . 
        'GGC_lib/core/GGC_Autoloader.php';
     
    /*
     * Si caricano le classi principali.
     * NOTA :
     * Volendo si potrebbe anche omettere il caricamento delle classi più
     * importanti se si utilizza lo smart autoload e/o il caching autoload.
     */
    GGC_Autoloader::init(
            $serverDocumentRootPath,
            $frameworkRootPath,
            $applicationName,
            $applicationRootPath,
            
            $frameworkLibPath,
            $frameworkLibCorePath,
            $frameworkLibUtilPath
            );
    
    /*
     * Avvio autoloader.
     */
    GGC_Autoloader::start();
    
    /**
     * Inizializzazione parametri di configurazione iniziali nella struttura
     * di configurazione in memoria (volatile). Viene fatto per simulare l'accesso al
     * fle fisico di configurazione, che in questo momento non possiamo ancora
     * avere, visto che si deve ancora decidere il tipo applicazione e il driver
     * da utilizzare per caricare la configurazione.
     */
    GGC_ConfigManager::open('', GGC_ConfigProvider::SD_MEM_GGC, false, NULL, 'Init');

    GGC_ConfigManager::setValue('General', 'ConfigDriver', 
        GGC_ConfigProvider::SD_INI, 'Init');
    GGC_ConfigManager::setValue('General', 'ServerDocumentRootPath', 
        $serverDocumentRootPath, 'Init');
    GGC_ConfigManager::setValue('General', 'FrameworkRootPath', 
        $frameworkRootPath, 'Init');
    GGC_ConfigManager::setValue('General', 'FrameworkLibPath', 
        $frameworkLibPath, 'Init');
    GGC_ConfigManager::setValue('General', 'FrameworkLibCorePath', 
        $frameworkLibCorePath, 'Init');
    GGC_ConfigManager::setValue('General', 'FrameworkLibUtilPath', 
        $frameworkLibUtilPath, 'Init');
    GGC_ConfigManager::setValue('General', 'ApplicationRootPath', 
        $applicationRootPath, 'Init');
    GGC_ConfigManager::setValue('General', 'ApplicationName', 
        $applicationName, 'Init');
    GGC_ConfigManager::setValue('General', 'ApplicationCachePath', 
        $applicationCachePath, 'Init');
    
    GGC_ConfigManager::setValue('General->Session', 'SavePath', 
        $sessionSavePath, 'Init');
?>
