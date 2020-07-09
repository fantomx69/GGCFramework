<?php
//namespace controller\http;

/**
 * Main application class.
 */
final class C_Index extends GGC_HttpControllerProvider {
    
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
//        if (!GGC_SessionManager::existsKey('Name')) {
//            header('Location: index.php?GGC_Entity=login');
//            exit();
//        }
//    }
//    protected function init($mixed = NULL) {
//        parent::init($mixed);
//    }
//    
//    function run() {
//        parent::run();
//    }

    //**************************************************************************
    //  Inizio funzioni "display...()" da stream file.
    //**************************************************************************
    
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
    
    protected function displayViewPlainTextFileStream($page) {
        $header = '<!DOCTYPE html><html><head><title></title>
                     <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                     <META HTTP-EQUIV="REFRESH" CONTENT="10; URL=http://localhost/GGC_Framework/">
                     </head><body>';
        $footer = '</body></html>';
        
        echo $header;
        require $this->getViewPlainTextFileStream($page);
        echo $footer;
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

        $smarty->assign('UserName', GGC_SessionManager::getValue('Name'));

        $smarty->display(GGC_ConfigManager::getValue('General', 'ServerDocumentRootPath') .
                    GGC_ConfigManager::getValue('General', 'ApplicationRootPath') . 
                    GGC_ConfigManager::getValue('Template->Smarty', 'SmartyTemplateDir') .
                    $this->getResponseDataType() . '/' . 
                    GGC_ConfigManager::getValue('General', 'ViewPrefix') . $page . '.' . 
                    GGC_ConfigManager::getValue('Template->Smarty', 'SmartyFileExt'));
    }
    
    //**************************************************************************
   
    //**************************************************************************
    //  Inizio funzioni "dispaly...()" da stream mem.
    //**************************************************************************
    /*
     * NOTA :
     * Qui, ovviamente, ho messo contemporaneamente sia le funzioni di view
     * da file che da memoria per fare prove di funzionamento, ma solo un tipo
     * deve essere presente, o la richiesta, la action, viene consumata da una
     * view rappresentata da un file fisico, o da una view proveniente dalla
     * memoria.
     */
    
    
    /*
     * Override per gestire la risposta da memoria
     */
    protected function getViewMemStream($page, $responseDataType) {
        $responseDataTypeAvailable = array('html' => false, 'xhtml' => false,
            'html5' => false, 'xml' => false, 'json' => true, 'rss' => false,
            'csv' => false, 'txt' => false, 'empty' => false, 'null' => false);
        
        $result = false;
        
        foreach ($responseDataTypeAvailable as $value) {
            if ($responseDataType == $value) {
                $result = true;
                break;
            }
                
        }
               
        return $result;
    }
        
    /*
     * NOTA :
     * Il parametro $page, se gestiamo diverse actions, potrebbe servire
     * per risponder dalla memoria in modo diversificato, a seconda della page
     * richiesta e non trovata fisicamente, ma espressa solo ne config.ini
     * quindi una page virtuale e quindi gestita in memoria.
     */
    protected function displayViewJsonMemStream($page) {
        //---
        // NOTA :
        // La logica di generazione della pagina/risposta in questo caso
        // è stata implementata qui, ma per cose serie, dovrebbe essere messa nel
        // "model" o in qualche altra classe o file che contenga una funzione
        // che restituisca il contenuto da visualizzare, magari, anzi senza dubbio, 
        // richiamando funzioni dal model.
        //---
        
        if ($page == 'vm_getDateTime') {
            echo json_encode('Salve risposta in formato JSON a chiamata AJAX tramite memory view.' .
                ' Sono le ore : ' . date("H:i:s d m Y"));
        } elseif ($page == 'vm_getGreeting') {
            //echo json_encode('Salve signor : ' . $_SESSION['Name']);
            //echo json_encode('Salve signor : ' . GGC_SessionManager::getValue('Name'));
            echo json_encode('Salve signor : ' .
//                    GGC_AuthenticationManager::getUser()->getUserName());
                    GGC_Authentication::getUser()->getUserName());
        }    
  
    }
    
    protected  function displayViewHtmlMemStream($page) {
        //parent::displayViewHtmlMemStream();
        
        $header = '<!DOCTYPE html><html><head><title></title>
                     <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                     <META HTTP-EQUIV="REFRESH" CONTENT="3; URL=' . $_SERVER['PHP_SELF'] . '">
                     </head><body>';
        
        $footer = '</body></html>';
        
        echo $header;
        echo '<br/>';
        echo 'Salve risposta in formato HTML tramite memory  view.' .
             ' Sono le ore : ' . date("H:i:s d m Y");
        
        echo '<br/>';
//        echo 'URI : ' . GGC_HttpRequest::getInstance()->getURI();
        echo 'URI : ' . $this->context->getRequest()->getURI();
        echo '<br/>';
//        echo 'Request Entity : ' . GGC_HttpRequest::getInstance()->getEntity();
        echo 'Request Entity : ' . $this->context->getRequest()->getEntity();
        echo '<br/>';
//        echo 'Action : ' . GGC_HttpRequest::getInstance()->getAction();
        echo 'Action : ' . $this->context->getRequest()->getAction();
        
        echo $footer;
    }


    //**************************************************************************
    
    /*
     * NOTA :
     * Esegue la funzione associata alla action. Se da questa funzione risulterà,
     * in seguito a prove, difficile convertire l'output restituito, nel formato
     * del "ResponseType", allora, il controllo del "ResponseType" deve essere
     * fatto nella funzione chiamata, magari mettendosi d'accordo con un parametro
     * su chi effettua la conversione, tra questa funzione e quella chiamata.
     */
    protected function runFunction($func) {
        $boIndex = new M_Index();

        //Si controlla il ResponseType
        $responseType = $this->getResponseDataType();
        
        $result = $boIndex->$func();
        
        if ($responseType == 'json') {
            echo json_encode($result);
            
        } elseif ($responseType == 'html') {
            $header = '<!DOCTYPE html><html><head><title></title>
                     <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                     <META HTTP-EQUIV="REFRESH" CONTENT="3; URL=' . $_SERVER['PHP_SELF'] . '">
                     </head><body>';
        
            $footer = '</body></html>';
            
            echo $header . $result . $footer;
        }
    }
    
}

?>
