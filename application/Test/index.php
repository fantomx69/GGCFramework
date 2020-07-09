<?php
/*
 * Inclusione dati e operazioni preliminari.
 */
require 'init.php';

/*
 * Aggiornamento autoloader in base al tipo esatto di applicazione, sempre che
 * non sia attivo lo smart autload.
 * 
 * TODO :
 * Questo sistema di controllare se è attivo lo smart autoload, altrimenti
 * provvedere a caricare le classi di interesse, dovrebbe essere effettuato
 * in tutto il programma.
 */
if (!GGC_Autoloader::getSmartAutoload()) {
    if ($applicationType == 'http') {
        GGC_Autoloader::add(array('GGC_HttpApplicationProvider' => 
            GGC_ConfigManager::getValue('General', 'ServerDocumentRootPath', 'Init') . 
            GGC_ConfigManager::getValue('General', 'FrameworkRootPath', 'Init') .
            'GGC_lib/core/application/GGC_HttpApplicationProvider.php'));
        
    } elseif ($applicationType == 'cli') {
        //...
    }
}

/**
 * Creazione applicazione, configurazione percorsi e avvio.
 */
if ($applicationType == 'http') {
    GGC_ApplicationManager::create('GGC_HttpApplicationProvider', NULL, 
        GGC_ConfigManager::getValue('General', 'ServerDocumentRootPath', 'Init'),
        GGC_ConfigManager::getValue('General', 'FrameworkRootPath', 'Init'),
        GGC_ConfigManager::getValue('General', 'ApplicationRootPath', 'Init'),
        GGC_ConfigManager::getValue('General', 'ConfigDriver', 'Init'),
        GGC_ConfigManager::getValue('General', 'ApplicationName', 'Init'),
        GGC_ConfigManager::getValue('General', 'ApplicationCachePath', 'Init'));
}

GGC_ApplicationManager::run();

/*
 * Operazioni finali per entità create prima di application, altrimenti si
 * dovrebbe utilizzare un qualche metdo distrutture, finalizzatore, ecc...,
 * appunto, di application.
 */
require 'finalize.php';

?>
