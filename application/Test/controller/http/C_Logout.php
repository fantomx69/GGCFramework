<?php
//namespace controller\http;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
final class C_Logout extends GGC_HttpControllerProvider {
    
    function __construct($context, $entityName = NULL, $entity = NULL) {
        parent::__construct($context, $entityName, $entity);
        
        /**
         * Eventuali inizializzazioni, se non già eseguite.
         */
        if (empty($this->entityName)) {
//            $this->entityName = 'E_' . substr(__CLASS__, 2);
            $this->entityName = substr(__CLASS__, 2);
        }
    }
    
    protected function displayViewPhpFileStream($page) {
        require $this->getViewPhpFileStream($page);
    }
    
    protected function displayViewHtmlFileStream($page) {
        //---
        // NOTA :
        // 
        // Logica applicativa, che in questo caso metto qui, ma potrei
        // creare un altr file .php da richiamare, come anche un'altra classe
        // oppure utlizzare il file di model.
        //---
        GGC_SessionManager::end();
        
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