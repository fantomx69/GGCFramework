<?php
/**
 * @author Gianni
 */
abstract class GGC_OutputHttpResponseCacheProvider extends GGC_HttpResponseCacheProvider {
    
    function __construct($context, $sourceUri, $entityName, $rootPath,
            $updateInterval = 5, $updateByParams = NULL, $updateByHeaders = NULL,
            $updateByControls = NULL, $updateByContentEncodings = NULL,
            $aryUpdateByCustom = NULL) {
        
        parent::__construct($context, $sourceUri, $entityName, $rootPath,
                $updateInterval, $updateByParams, $updateByHeaders,
                $updateByControls, $updateByContentEncodings, $aryUpdateByCustom);
        
        $this->cacheTypeProvider = GGC_ResponseCacheProvider::CTP_OUTPUT;
        
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
        parent::init();
    }
    
    abstract function get();
    abstract function update();
    abstract function clear();
    abstract function getExpiresDateTime();
    
    private function integrityCheck($varName = NULL) {
        $result = NULL;
        
        if ((empty($varName) || $varName == 'updateInterval') &&
                empty($this->updateInterval)) {
            $result .= '[Update Interval] non presente.';
        }
        
        return $result;
    }

}

?>
