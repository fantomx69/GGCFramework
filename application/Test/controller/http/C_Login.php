<?php
//namespace controller\http;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
final class C_Login extends GGC_HttpControllerProvider {
    
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

//    protected function init($mixed = NULL) {
//        $name = GGC_HttpRequest::getInstance()->getPost('Name');
//        if (!empty($name)) {
//            GGC_SessionManager::setValue ('Name', GGC_HttpRequest::getInstance()->getPost('Name'));
//        }    
//
//        if (GGC_SessionManager::existsKey('Name')) {
//            header('Location: index.php');
//            exit();
//        }
//    }
    protected function init($mixed = NULL) {
        $userName = GGC_HttpRequest::getInstance()->getPost('Name');
        
        if (!empty($userName)) {
            $password = GGC_HttpRequest::getInstance()->getPost('Password');
            
            if (!empty($password)) {
//                $password = GGC_AuthenticationManager::hash($password);
                $password = GGC_Authentication::hash($password);
            }

//            $authToken = GGC_AuthenticationManager::createToken($userName, $password);
            $authToken = GGC_Authentication::createToken($userName, $password);

            if (!empty($authToken)) {
//                GGC_AuthenticationManager::login($authToken);
                GGC_Authentication::login($authToken);

//                if (GGC_AuthenticationManager::isAuthenticated()) {
//                    GGC_AuthenticationManager::setStateSession(true);
                if (GGC_Authentication::isAuthenticated()) {
                    GGC_Authentication::setStateSession(true);

                    header('Location: index.php');
                    exit();
                }
            }
        }
    }

    protected function displayViewPhpFileStream($page) {
        require $this->getViewPhpFileStream($page);
    }
    
    protected function displayViewHtmlFileStream($page) {
        require $this->getViewHtmlFileStream($page);
    }
    
    protected function displayViewSmartyTemplateFileStream($page) {
        if ($this->outputHttpResponseCacheCheck($output, NULL,
                GGC_ResponseCacheProvider::COP_SMARTY_TEMPLATE_FILE, $page)) {
            
            echo $output;
            return;
        }
        
        $smarty = new Smarty;

        $smarty->force_compile = true;
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
                    '/' . $this->getResponseDataType() . '/' .
                    GGC_ConfigManager::getValue('General', 'ViewPrefix') . $page . '.' .
                    GGC_ConfigManager::getValue('Template->Smarty', 'SmartyFileExt'));
    }
    
}

?>
