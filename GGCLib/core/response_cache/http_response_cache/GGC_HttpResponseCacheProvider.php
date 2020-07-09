<?php
/**
 * Description of GGC_HttpResponseCacheProvider
 *
 * @author Gianni
 */
abstract class GGC_HttpResponseCacheProvider extends GGC_ResponseCacheProvider {
    /**
     * Discriminante nomi/chiavi da confrontare con quelli salvati, per decidere
     * se aggiornare o meno la cache. Questi valori vengono anche utilizzati
     * per essere salvati a loro volta nel file .ini e riutilizzati per il
     * prossimo confronto. In questo caso,i valori corrispondenti, vengono
     * recuperati dalle rispettive fonti. I nomi, per convenzione, devono essere
     * separati da un ";". In futuro, si potrà anche far configurare.
     */
    protected $updateByHeaders = NULL;
    
    /**
     * Array ricavati dai nomi passati nelle varibili sopra elencate. Questi
     * array associativi, sono formati da chiavi contenenti i valori sopra
     * elencati e da valori ricavati, a seconda del tipo.
     */
    protected $aryUpdateByHeaders = NULL;
    
    function __construct($context, $sourceUri, $entityName, $rootPath = NULL,
            $updateInterval = 5, $updateByParams = NULL, $updateByHeaders = NULL,
            $updateByControls = NULL, $updateByContentEncodings = NULL,
            $aryUpdateByCustom = NULL) {
        
        parent::__construct($context, $sourceUri, $entityName, $rootPath,
                $updateInterval, $updateByParams, $updateByControls,
                $updateByContentEncodings, $aryUpdateByCustom);
        
        /**
         * Inizializzazione.
         */
        $this->updateByHeaders = $updateByHeaders;
    }
    
    protected function createCompareArrayBy() {
        parent::createCompareArrayBy();
        
        $this->createCompareArrayByHeaders();
    }
    
    protected function isToBeUpdatedBy() {
        $result = parent::isToBeUpdatedBy();
        
        if ($this->isToBeUpdatedByHeaders()) {
            $result = true;
        }
        
        return $result;
    }
    
    protected function updateBy() {
        parent::updateBy();
        
        $this->updateByHeaders();
    }
    
    private function createCompareArrayByHeaders() {
        
    }
    
    private function isToBeUpdatedByHeaders() {
        $result = false;
        
        
        return $result;
    }
    
    private function updateByHeaders() {
        
    }
}

?>
