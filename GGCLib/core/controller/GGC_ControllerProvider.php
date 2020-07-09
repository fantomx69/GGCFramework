<?php
//namespace GGC_lib\core\controller;

/**
 * Description of GGC_ControllerProvider
 * 
 * Classe base dove vengono implementate le funzionalità comuni e dati comuni
 * a tutti i diversi tipi base di controller in base al tipo di protocollo che
 * nel 99% è "Http";
 *
 * @author Gianni Carafone
 */
abstract class GGC_ControllerProvider extends GGC_Provider {
    /*
     * Contesto
     */
    protected $context = NULL;

    /*
     * Riferimento eventuale oggetto entity.
     */
    protected $entity = NULL;
    
    /*
     * Nome eventuale oggetto entity. Se fosse instanziato un oggetto entity,
     * questo campo non servirebbe, ma si potrebbe scegliere, di restituire
     * informazioni anche senza avere un oggetto entity dietro le quinte.
     */
    protected $entityName = NULL;
    
    function __construct($context, $entityName = NULL, $entity = NULL) {
        parent::__construct();
        
        $this->context = $context;
        $this->context->setController($this);
        
        $this->entityName = $entityName;
        $this->entity = $entity;
    }

    abstract function run(/*$entity*/);
//    abstract function get(/*$entity*/);
    
    function getEntity() {
        return $this->entity;
    }
    
    function getEntityName() {
        return $this->entityName;
    }
    
//    function setEntity($value = NULL) {
//        $this->entity = $value;
//    }
    
    protected function outputResponseCacheCheck(
            &$cacheSaveProvider = NULL,
            &$cacheOriginProvider = NULL,
            &$updateInterval = 5,
            &$updateByParams = NULL,
            &$updateByControls = NULL,
            &$updateByContentEncodings = NULL,
            &$aryUpdateByCustom = NULL,
            &$instanceName = NULL) {
        
        $result = true;
        
        /*
         * Si controlla se gestire la cache di output o meno.
         */
        if (!GGC_ConfigManager::getValue($this->entityName . '->ResponseCache', 'OutputCache') &&
                !GGC_ConfigManager::getValue('General->ResponseCache', 'OutputCache')) {
            
            $result = false;
        }
        
        /*
         * Determinazione cache save provider.
         */
        if ($result) {
            if (empty($cacheSaveProvider)) {
                $cacheSaveProvider = GGC_ConfigManager::getValue($this->entityName . '->ResponseCache->Output', 'CacheSaveProvider');
            }
            
            if (empty($cacheSaveProvider)) {
                $cacheSaveProvider = GGC_ConfigManager::getValue('General->ResponseCache->Output', 'CacheSaveProvider');
            }
            
            if (empty($cacheSaveProvider)) {
                $result = false;
            }
        }
        
        /*
         * Controllo integrità cache origin provider.
         */
        if ($result) {
             if (!empty($cacheOriginProvider)) {
                 if ($cacheOriginProvider == GGC_ResponseCacheProvider::COP_PHP_FILE &&
                         !GGC_ConfigManager::getValue($this->entityName . '->ResponseCache->Output', 'PhpFileCacheOrigin') &&
                         !GGC_ConfigManager::getValue('General->ResponseCache->Output', 'PhpFileCacheOrigin')) {
                     $result = false;
                 }
                 
                if ($cacheOriginProvider == GGC_ResponseCacheProvider::COP_SMARTY_TEMPLATE_FILE &&
                         !GGC_ConfigManager::getValue($this->entityName . '->ResponseCache->Output', 'PhpFileCacheOrigin') &&
                         !GGC_ConfigManager::getValue('General->ResponseCache->Output', 'PhpFileCacheOrigin')) {
                     $result = false;
                 }
                 
             } else {
                 $result = false;
             }
        }
        
        /*
         * Determinazione discriminanti aggiornamento.
         */
        if ($result) {
            if (empty($updateInterval)) {
                $updateInterval = GGC_ConfigManager::getValue($this->entityName . '->ResponseCache->Output', 'UpdateInterval');
            }
            
            if (empty($updateInterval)) {
                $updateInterval = GGC_ConfigManager::getValue('General->ResponseCache->Output', 'UpdateInterval');
            }
            
            if (empty($updateInterval)) {
                $result = false;
            }
        }
        
        if ($result) {
            if (empty($updateByParams)) {
                $updateByParams = GGC_ConfigManager::getValue($this->entityName . '->ResponseCache->Output', 'UpdateByParams');
            }
            
            if (empty($updateByParams)) {
                $updateByParams = GGC_ConfigManager::getValue('General->ResponseCache->Output', 'UpdateByParams');
            }
        }
        
        if ($result) {
            if (empty($instanceName)) {
                $instanceName = $this->entityName;
            }
        }
        
        return $result;
    }
    
}

?>
