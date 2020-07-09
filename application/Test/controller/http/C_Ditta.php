<?php
//namespace controller\http;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
final class C_Ditta extends GGC_HttpControllerProvider {
    
    function __construct($context, $entityName = NULL, $entity = NULL) {
        parent::__construct($context, $entityName, $entity);
        
        /**
         * Eventuali inizializzazioni, se non già eseguite.
         */
        if (empty($this->entityName)) {
            $this->entityName = substr(__CLASS__, 2);
        }
    }
    
//    protected function init($mixed = NULL) {
//        ;
//    }
   
    protected function displayViewPhpFileStream($page) {
        if ($this->outputHttpResponseCacheCheck($output, NULL,
                GGC_ResponseCacheProvider::COP_PHP_FILE,
                $this->getViewPhpFileStream($page))) {
            
            echo $output;
            return;
        }
        
        require $this->getViewPhpFileStream($page);
    }
    
    protected function displayViewHtmlFileStream($page) {
        require $this->getViewHtmlFileStream($page);
    }
    
    protected function displayViewSmartyTemplateFileStream($page) {
        $smarty = new Smarty;

        //$smarty->force_compile = true;
        //$smarty->debugging = true;
        $smarty->caching = true;
        $smarty->cache_lifetime = 120;

        $smarty->setTemplateDir(GGC_ConfigManager::getValue('General', 'ServerDocumentRootPath') .
                    GGC_ConfigManager::getValue('General', 'ApplicationRootPath') .
                    GGC_ConfigManager::getValue('Template->Smarty', 'SmartyTemplateDir'))
                ->setCompileDir(GGC_ConfigManager::getValue('General', 'ServerDocumentRootPath') .
                    GGC_ConfigManager::getValue('General', 'ApplicationRootPath') .    
                    GGC_ConfigManager::getValue('Template->Smarty', 'SmartyCompileDir'))
                ->setCacheDir(GGC_ConfigManager::getValue('General', 'ServerDocumentRootPath') .
                    GGC_ConfigManager::getValue('General', 'ApplicationRootPath') .
                    GGC_ConfigManager::getValue('Template->Smarty', 'SmartyCacheDir'));

        $smarty->display(GGC_ConfigManager::getValue('General', 'ServerDocumentRootPath') .
                    GGC_ConfigManager::getValue('General', 'ApplicationRootPath') . 
                    GGC_ConfigManager::getValue('Template->Smarty', 'SmartyTemplateDir') .
                    GGC_ConfigManager::getValue('General', 'ViewPrefix') . $page . '_' . $this->getResponseDataType() . '.' .
                    GGC_ConfigManager::getValue('Template->Smarty', 'SmartyFileExt'));
    }
}

?>