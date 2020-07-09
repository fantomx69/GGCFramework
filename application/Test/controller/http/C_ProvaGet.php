<?php
/**
 * Description of C_ProvaForward
 *
 * @author Gianni
 */
final class C_ProvaGet extends GGC_HttpControllerProvider {
    function __construct($context, $entityName = NULL, $entity = NULL) {
        parent::__construct($context, $entityName, $entity);
        
        /**
         * Eventuali inizializzazioni, se non già eseguite.
         */
        if (empty($this->entityName)) {
            $this->entityName = substr(__CLASS__, 2);
        }
    }
    
    protected function displayViewPhpFileStream($page) {
        if ($this->outputHttpResponseCacheCheck($output, NULL,
                GGC_ResponseCacheProvider::COP_PHP_FILE,
                $this->getViewPhpFileStream($page))) {
            
            echo $output;
            return;
        }
        
        require $this->getViewPhpFileStream($page);
    }
}

?>
