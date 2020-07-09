<?php
//namespace GGC_lib\core\controller;

/**
 * Description of GGC_Controller
 *
 * Controller base per le richieste attraverso il protocollo Http.
 * 
 * NOTA :
 * Vedere s eil caso di implementare le funzioni corrispondenti ai metd http, e
 * gestire il loro richiamo.
 * 
 * @author Gianni Carafone
 */
abstract class GGC_HttpControllerProvider extends GGC_ControllerProvider {
    
    function __construct($context, $entityName = NULL, $entity = NULL) {
        parent::__construct($context, $entityName, $entity);
    }
    
    protected function init($mixed = NULL) {
        if (!$this->isAuthorizedUser()) {
//            header('Location: index.php?GGC_Entity=login');
            GGC_AnomalyManagement::centralizedAnomalyManagement (NULL,
                    array('Message' => "Utente NON Autorizzato a compiere codesta
                        azione inerente l'entità in questione."));
            exit();
        }
    }
    
    protected function isAuthorizedUser() {
        /*
         * Controllo autenticazione.
         */
        $result = GGC_Authentication::isAuthenticated() ||
            GGC_Authentication::isGuest();
        
        /*
         * Controllo autorizzazione.
         */
        if ($result) {
            GGC_Authorization::init();
            GGC_Authorization::setACLCache(TRUE);
            $actionName = $this->getAction($this->entityName);
//            $actionParameterName = $this->getActionParam();
            
            $result = GGC_Authorization::isAuthorized($this->entityName,
                    $actionName/*, $actionParameterName*/);
        }
                
        return $result;
    }

    /**
     * Run
     */
    function run(/*$entity*/) {
        //---
        // NOTA / TODO :
        // Si controlla se l'action dell'entità ha una funzione associata, oppure
        // una pagina associata. Per il modo in cui è fatta la funzione "dispalyView()",
        // anche se una pagina non esiste fisicamente, la richiesta potrebbe essere
        // intercettata dalle funzioni che operano in memoria; quindi si controlla
        // prima che la "action" non sia una funzione e poi una pagina, a meno di
        // cambiare la suddetta routine e quindi il modo di richiamare la visualizzazione
        // delle pagine da file e da memoria, magari in questa sequenza :
        // 1. visualizzazione da file
        // 2. esecuzione funzione
        // 3. visualizzazione da memoria.
        // 
        // TODO :
        // Implementare anche una funzione che mi restituisca i parametri della
        // eventuale funzione da richiamare, sottoforma di array, e passare
        // l'array alla "runFunction()" il quale si occuperà di iterare l'array
        // e costruire in modo opportuno la chiamata alla funzione.
        //---
        
        $this->init(/*$entity*/);
        
//        $funcName = $this->getFunctionName($entity);
        $funcName = $this->getFunctionName($this->entityName);
        
        if ($funcName) {
            $this->runFunction($funcName);
        } else {
//            $pageName = $this->getPageName($entity);
            $pageName = $this->getPageName($this->entityName);
            
            if ($pageName) {
                $this->runPage($pageName);
            } else {
                die('L\'azione specificata non corrisponde a nessuna pagina o funzione!');
            }
            
        }
        
    }
    
//    function get(/*$entity*/) {
//        ob_start();
//        
//        $this->run(/*$entity*/);
//        
//        $result = ob_get_contents();
//        
//        ob_end_clean();
//        
//        return $result;
//    }

    protected function runPage($page) {
        $this->displayView($page); 
    }
    
    /*
     * Esegue la funzione corrispondente all'azione.
     * Ridefinirla nel controller specifico, se l'entità associata espone
     * funzioni al richiamo dall'esterno.
     */
    protected function runFunction($func) {
         die('Definire la funzione di gestione richiesta. Funzione : ' . $func);
    }

    /*
     * Per quando riguarda l'html, per ora viene considerato solo quello
     * semplice, volendo si possono aggiungere anche html5, xhtml, ecc... .
     * Stesso discorso per glia ltri formati,per lo meno per quelli che hanno
     * senso essere resitutiti tramite file fisico.
     */
    private function displayView($page) {
        //---
        // Se esiste il template si utlizza il template, atrimenti il php, 
        // l'html o il plain text.
        //---
        if ($this->hasViewTemplateFileStream($page)) {
            $this->displayViewTemplateFileStream($page);
        
        } elseif ($this->hasViewPhpFileStream($page)) {
            $this->displayViewPhpFileStream($page);

        } elseif ($this->hasViewHtmlFileStream($page)) {
            $this->displayViewHtmlFileStream($page);

        } elseif ($this->hasViewPlainTextFileStream($page)) {
            $this->displayViewPlainTextFileStream($page);

        } elseif ($this->hasViewMemStream($page)) {
            $this->displayViewMemStream($page);
            
        } else {
            die('Page "' . $page . '" has neither plain html nor template!');
        }

   }
   
    private function displayViewTemplateFileStream($page) {
        if (GGC_ConfigManager::getValue('Template', 'TemplateName') == 'Smarty') {
            $this->displayViewSmartyTemplateFileStream($page);

        } elseif (GGC_ConfigManager::getValue('Template', 'TemplateName') == 'Twig') {    
            $this->displayViewTwigTemplateFileStream($page);
            
        } elseif (GGC_ConfigManager::getValue('Template', 'TemplateName') == 'GGC') {
            $this->displayViewGGCTemplateFileStream($page);
        }
        
     }
    
    protected function getViewTemplateFileStream($page) {
        if (GGC_ConfigManager::getValue('Template', 'TemplateName') == 'Smarty') {
//            return GGC_ConfigManager::getValue('General', 'ServerDocumentRootPath') .
//                   GGC_ConfigManager::getValue('General', 'ApplicationRootPath') .
//                   GGC_ConfigManager::getValue('Template->Smarty', 'SmartyTemplateDir') .
//                   '/' . $this->getResponseDataType() . '/' .
//                   GGC_ConfigManager::getValue('General', 'ViewPrefix') .
//                   $page . '.' . GGC_ConfigManager::getValue('Template->Smarty', 'SmartyFileExt');
            return GGC_ConfigManager::getValue('General', 'ServerDocumentRootPath') .
                   GGC_ConfigManager::getValue('General', 'ApplicationRootPath') .
                   GGC_ConfigManager::getValue('Template->Smarty', 'SmartyTemplateDir') .
                   '/' . $this->getResponseDataType() . '/' .
                   GGC_ApplicationManager::viewNameFormat($page) .
                   '.' . GGC_ConfigManager::getValue('Template->Smarty', 'SmartyFileExt');
            
        } elseif (GGC_ConfigManager::getValue('Template', 'TemplateName') == 'Twig') {
            //...

        } elseif (GGC_ConfigManager::getValue('Template', 'TemplateName') == 'GGC') {
            //...
        }
    }
    
    protected function getViewPhpFileStream($page) {
//        return GGC_ConfigManager::getValue('General', 'ServerDocumentRootPath') .
//               GGC_ConfigManager::getValue('General', 'ApplicationRootPath') .
//               GGC_ConfigManager::getValue('General', 'ApplicationViewPath') .
//               GGC_ConfigManager::getValue('General', 'ApplicationViewPhpPath') .
//               $this->getResponseDataType() . '/' .
//               GGC_ConfigManager::getValue('General', 'ViewPrefix') .
//               $page . '.php';
        return GGC_ConfigManager::getValue('General', 'ServerDocumentRootPath') .
               GGC_ConfigManager::getValue('General', 'ApplicationRootPath') .
               GGC_ConfigManager::getValue('General', 'ApplicationViewPath') .
               GGC_ConfigManager::getValue('General', 'ApplicationViewPhpPath') .
               $this->getResponseDataType() . '/' .
               GGC_ApplicationManager::viewNameFormat($page) . '.php';
    }
    
    protected function getViewHtmlFileStream($page) {
//        return GGC_ConfigManager::getValue('General', 'ServerDocumentRootPath') .
//               GGC_ConfigManager::getValue('General', 'ApplicationRootPath') .
//               GGC_ConfigManager::getValue('General', 'ApplicationViewPath') .
//               GGC_ConfigManager::getValue('General', 'ApplicationViewHtmlPath') .
//               $this->getResponseDataType() . '/' .
//               GGC_ConfigManager::getValue('General', 'ViewPrefix') .
//               $page . '.html';
        return GGC_ConfigManager::getValue('General', 'ServerDocumentRootPath') .
               GGC_ConfigManager::getValue('General', 'ApplicationRootPath') .
               GGC_ConfigManager::getValue('General', 'ApplicationViewPath') .
               GGC_ConfigManager::getValue('General', 'ApplicationViewHtmlPath') .
               $this->getResponseDataType() . '/' .
               GGC_ApplicationManager::viewNameFormat($page) . '.html';
    }
    
    protected function getViewPlainTextFileStream($page) {
        return GGC_ConfigManager::getValue('General', 'ServerDocumentRootPath') .
               GGC_ConfigManager::getValue('General', 'ApplicationRootPath') .
               GGC_ConfigManager::getValue('General', 'ApplicationViewPath') .
               GGC_ConfigManager::getValue('General', 'ApplicationViewTxtPath') .
               $this->getResponseDataType() . '/' .
               //GGC_ConfigManager::getValue('General', 'ViewPrefix') . $page;
               GGC_ApplicationManager::viewNameFormat($page);
    }
    
    /*
     * Funzione che controlla la gestione o meno dei diversi tipi di view.
     * NOTA :
     * In quesa classe base, potrei anche evitare di scrivere tutto ciò,
     * resituendo semplicemente false. Questa implementazione è importante
     * nelle classi derivate.
     * L'utilizzo dell'array, mi consente di evitare l'implementazione delle
     * funzioni "hasView...()" e "getView...()" per tutti i "ResponseDataType".
     * 
     * NOTA :
     * Il parametro $page, se gestiamo diverse actions, potrebbe servire
     * per risponder dalla memoria in modo diversificato, a seconda della page
     * richiesta e non trovata fisicamente, ma espressa solo ne config.ini
     * quindi una page virtuale e quindi gestita in memoria, questo ovviamente
     * nei controller derivati.
     */
    protected function getViewMemStream($page, $responseDataType) {
        //---
        // Volendo, ma non consigliabile per non rovinare lo stile e la pulizia
        // di implementazione del framework, si potrebbe già utilizzare $page
        // per fare o meno alcune cose.
        //---
        //...
        
        $responseDataTypeAvailable = array('html' => false, 'xhtml' => false,
            'html5' => false, 'xml' => false, 'json' => false, 'rss' => false,
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
    
    protected function getRequestType() {
//        return GGC_HttpRequest::getInstance()->getRequestType();
        return $this->context->getRequest()->getRequestType(); 
    }
    
    protected function getResponseDataType() {
//        return GGC_HttpResponse::getInstance()->getResponseDataType();
        return $this->context->getResponse()->getResponseDataType();
    }

    protected function hasViewTemplateFileStream($page) {
        return file_exists($this->getViewTemplateFileStream($page));
    }
    
    protected function hasViewPhpFileStream($page) {
        return file_exists($this->getViewPhpFileStream($page));
    }
    
    protected function hasViewHtmlFileStream($page) {
        return file_exists($this->getViewHtmlFileStream($page));
    }
    
    protected function hasViewPlainTextFileStream($page) {
        return file_exists($this->getViewPlainTextFileStream($page));
    }
    
    /*
     * NOTA :
     * Il parametro $page, se gestiamo diverse actions, potrebbe servire
     * per risponder dalla memoria in modo diversificato, a seconda della page
     * richiesta e non trovata fisicamente, ma espressa solo ne config.ini
     * quindi una page virtuale e quindi gestitain memoria.
     */
    private function hasViewMemStream($page) {
        //---
        // Volendo, ma non consigliabile per non rovinare lo stile e la pulizia
        // di implementazione del framework, si potrebbe già utilizzare $page
        // per fare o meno alcune cose.
        //---
        //...
        
        return $this->getViewMemStream($page, $this->getResponseDataType());
    }
    
    protected function displayViewPhpFileStream($page) {
        die('Definire la logica di visualizzazione della pagina : ' . $page);
    }
    
    protected function displayViewHtmlFileStream($page) {
        die('Definire la logica di visualizzazione della pagina : ' . $page);
    }
    
    protected function displayViewPlainTextFileStream($page) {
        die('Definire la logica di visualizzazione della pagina : ' . $page);
    }
    
    protected function displayViewSmartyTemplateFileStream($page) {
        die('Definire la logica di visualizzazione della pagina : ' . $page); 
    }
    
    protected function displayViewTwigTemplateFileStream($page) {
        die('Definire la logica di visualizzazione della pagina : ' . $page);
    }
    
    protected function displayViewGGCTemplateFileStream($page) {
        die('Definire la logica di visualizzazione della pagina : ' . $page);
    }
    
    /*
     * Inizio funzioni "dispaly...()" da stream in memoria.
     */
    private function displayViewMemStream($page) {
        $displayViewTypeMemStream = 'displayView' . 
            ucfirst($this->getResponseDataType()) . 'MemStream';
        
        $this->$displayViewTypeMemStream($page);
    }
    
    /*
     * NOTA :
     * Il parametro $page, se gestiamo diverse actions, potrebbe servire
     * per risponder dalla memoria in modo diversificato, a seconda della page
     * richiesta e non trovata fisicamente, ma espressa solo ne config.ini
     * quindi una page virtuale e quindi gestitain memoria, ovviamente nei
     * controller derivati, magari, anzi senza dubbio, richiamando funzioni dal
     * model.
     */
    protected function displayViewHtmlMemStream($page) {
        die('Definire la logica di visualizzazione dalla memoria per la pagina : ' . $page);
    }
    
    protected function displayViewXhtmlMemStream($page) {
        die('Definire la logica di visualizzazione dalla memoria per la pagina : ' . $page);
    }
    
    protected function displayViewHtml5MemStream($page) {
        die('Definire la logica di visualizzazione dalla memoria per la pagina : ' . $page);
    }
    
    protected function displayViewXmlMemStream($page) {
        die('Definire la logica di visualizzazione dalla memoria per la pagina : ' . $page);
    }
    
    protected function displayViewJsonMemStream($page) {
        die('Definire la logica di visualizzazione dalla memoria per la pagina : ' . $page);
    }
    
    protected function displayViewRssMemStream($page) {
        die('Definire la logica di visualizzazione dalla memoria per la pagina : ' . $page);
    }
    
    protected function displayViewCsvMemStream($page) {
        die('Definire la logica di visualizzazione dalla memoria per la pagina : ' . $page);
    }
    
    protected function displayViewEmptyMemStream($page) {
        die('Definire la logica di visualizzazione dalla memoria per la pagina : ' . $page);
    }
    
    protected function displayViewNullMemStream($page) {
        die('Definire la logica di visualizzazione dalla memoria per la pagina : ' . $page);
    }
    
    /**
     * Ritorna la pagina opportuna, in base ai parametri della request
     */
    protected function getPageName($entity) {
        $action = $this->getAction($entity);
        
        $pageName = $action;
        
        if (array_key_exists($action, GGC_ConfigManager::getGroup($entity . '->RealActionToPageBinding')) &&
                (GGC_ConfigManager::getValue($entity . '->RealActionToPageBinding', $action)))
            $pageName = GGC_ConfigManager::getValue($entity . '->RealActionToPageBinding', $action);
        
        return $pageName;
    }
    
    /**
     * Ritorna la funzione opportuna, in base ai parametri della request
     */
    protected function getFunctionName($entity) {
        //---
        // Il controllo della funzione potrebbe essere fatto tramite la
        // reflection, ma per ora e forse anche in futuro verrà fatta tramite
        // il file di configurazione, anche perchè è più veloce e chiaro, e non
        // c'e il rischio che venga richiamata una funzione inopportuna.
        // 
        // TODO :
        // qui, prima della restituzione del nome funzione, si dovrebbe fare
        // il controllo anche dei parametri.
        //---
        $funcName = NULL;
        
        $action = $this->getAction($entity);
        
        if (array_key_exists($action, GGC_ConfigManager::getGroup($entity . '->RealActionToFunctionBinding')) &&
                (GGC_ConfigManager::getValue($entity . '->RealActionToFunctionBinding', $action)))
            $funcName = GGC_ConfigManager::getValue($entity . '->RealActionToFunctionBinding', $action);
        
        return $funcName;
    }
    
//    private function getAction($entity) {
//        $result = NULL;
//        
//        /*
//         * Se è presente come parametro, viene considerato;
//         */
//        $actionParam = $this->getActionParam();
//
//        /*
//         * Se action è null, viene ricavato dal file di config.
//         */
//        if (empty($actionParam)) {
//            if (array_key_exists('DefaultAction', GGC_ConfigManager::getGroup($entity)) &&
//                    (GGC_ConfigManager::getValue($entity, 'DefaultAction')))
//                $actionParam = GGC_ConfigManager::getValue($entity, 'DefaultAction');
//        }
//
//        /*
//         * Si controlla la presenza d valori nella lista filtro actions virtuali
//         * se non è consentito l'accesso diretto alla lista valori actions reali.
//         */
//        if ($actionParam != NULL &&
//                count(GGC_ConfigManager::getGroup($entity . '->ValidVirtualActions')) > 0 && 
//                !in_array($actionParam, GGC_ConfigManager::getGroup($entity . '->ValidVirtualActions'))) {
//
//            $actionParam = NULL;
//        }    
//
//        /*
//         * Una volta accertato il parametro action e se questo appartiene alla
//         * eventuale lista di filtro "ValidVirtualEntities" se l'accesso diretto
//         * ai valori reali non è possibile, ci accingiamo a proseguire nei controlli.
//         */
//        if ($actionParam != NULL) {
//           //---
//           // Se il valore è presente nella lista di quelli virtuali, si controlla
//           // la tabella di associazione tra valori virtuali e quelli reali.
//           //---
//            if ( array_key_exists($actionParam, GGC_ConfigManager::getGroup($entity . '->VirtualActionToRealActionBinding')) &&
//                    (GGC_ConfigManager::getValue($entity . '->VirtualActionToRealActionBinding', $actionParam) != '') && ((
//                            array_key_exists(GGC_ConfigManager::getValue($entity . '->VirtualActionToRealActionBinding', $actionParam), 
//                                    GGC_ConfigManager::getGroup($entity . '->RealActionToPageBinding')) &&
//                            (GGC_ConfigManager::getValue($entity . '->RealActionToPageBinding', GGC_ConfigManager::getValue($entity . '->VirtualActionToRealActionBinding', $actionParam)) != '')) ||
//                            (array_key_exists(GGC_ConfigManager::getValue($entity . '->VirtualActionToRealActionBinding', $actionParam), 
//                    GGC_ConfigManager::getGroup($entity . '->RealActionToFunctionBinding')) &&
//                                    (GGC_ConfigManager::getValue($entity . '->RealActionToFunctionBinding', GGC_ConfigManager::getValue($entity . '->VirtualActionToRealActionBinding', $actionParam)) != '')))) {
//
//                if (count(GGC_ConfigManager::getGroup($entity . '->ValidRealActions')) > 0) {
//                    if (in_array(GGC_ConfigManager::getValue($entity . '->VirtualActionToRealActionBinding', $actionParam), 
//                           GGC_ConfigManager::getGroup($entity . '->ValidRealActions')))
//                       
//                            $result = GGC_ConfigManager::getValue($entity . '->VirtualActionToRealActionBinding', $actionParam);
//              } else              
//                  $result = GGC_ConfigManager::getValue($entity . '->VirtualActionToRealActionBinding', $actionParam);
//          }  
//
//          if ($result == NULL) {
//             //---
//             // Se è permesso l'accesso diretto ai valori reali, si controllano
//             // questi ultimi, ma non prima di aver controllato l'eventuale lista
//             // di filtro dei valori reali.
//             //---
//              if (GGC_ConfigManager::getValue('General', 'RealActionNameDirectAccess') == 1 || 
//                     GGC_ConfigManager::getValue($entity, 'RealActionNameDirectAccess') == 1) {
//                  if (count(GGC_ConfigManager::getGroup($entity . '->ValidRealActions')) > 0) {
//                      if (in_array($actionParam, GGC_ConfigManager::getGroup($entity . '->ValidRealActions')) && ((
//                              array_key_exists($actionParam, GGC_ConfigManager::getGroup($entity . '->RealActionToPageBinding')) &&
//                              (GGC_ConfigManager::getValue($entity . '->RealActionToPageBinding', $actionParam) != '')) || 
//                              (array_key_exists($actionParam, GGC_ConfigManager::getGroup($entity . '->RealActionToFunctionBinding')) &&
//                              (GGC_ConfigManager::getValue($entity . '->RealActionToFunctionBinding', $actionParam) != ''))))
//                      $result = $actionParam;
//
//                } else {
//                    if ((array_key_exists($actionParam, GGC_ConfigManager::getGroup($entity . '->RealActionToPageBinding')) &&
//                            (GGC_ConfigManager::getValue($entity . '->RealActionToPageBinding', $actionParam) != '')) ||
//                            (array_key_exists($actionParam, GGC_ConfigManager::getGroup($entity . '->RealActionToFunctionBinding')) &&
//                                    (GGC_ConfigManager::getValue($entity . '->RealActionToFunctionBinding', $actionParam) != '')))
//                      $result = $actionParam;
//                }
//
//             }
//             
//          }  
//
//       }    
//
//       return $this->checkAction($result);
//    }
    private function getAction($entity) {
        $result = NULL;
        
        /*
         * Se è presente come parametro, viene considerato;
         */
        $actionParam = $this->getActionParam();

        /*
         * Se il parametro action è null, viene ricavato dal file di config.
         */
        if (empty($actionParam)) {
           if (array_key_exists('DefaultAction', GGC_ConfigManager::getGroup($entity)) &&
                   (GGC_ConfigManager::getValue($entity, 'DefaultAction'))) {

               $actionParam = GGC_ConfigManager::getValue($entity, 'DefaultAction');
               
               
           }
        }

        /*
         * Si controlla la presenza d valori nella lista filtro actions virtuali
         * se non è consentito l'accesso diretto alla lista valori actions reali.
         */
        if ($actionParam != NULL &&
               count(GGC_ConfigManager::getGroup($entity . '->ValidVirtualActions')) > 0 && 
               !in_array($actionParam, GGC_ConfigManager::getGroup($entity . '->ValidVirtualActions'))) {

           $actionParam = NULL;
        }

        /*
        * Una volta accertato il parametro action e se questo appartiene alla
        * eventuale lista di filtro "ValidVirtualEntities" se l'accesso diretto
        * ai valori reali non è possibile, ci accingiamo a proseguire nei controlli.
        */
        if (!empty($actionParam)) {
            /*
             * Se il valore è presente nella lista di quelli virtuali, si controlla
             * la tabella di associazione tra valori virtuali e quelli reali.
             */
            if (array_key_exists($actionParam, GGC_ConfigManager::getGroup($entity . '->VirtualActionToRealActionBinding')) &&
                    (GGC_ConfigManager::getValue($entity . '->VirtualActionToRealActionBinding', $actionParam) != '') &&
                    ((array_key_exists(GGC_ConfigManager::getValue($entity . '->VirtualActionToRealActionBinding', $actionParam),
                            GGC_ConfigManager::getGroup($entity . '->RealActionToPageBinding')) &&
                            (GGC_ConfigManager::getValue($entity . '->RealActionToPageBinding', GGC_ConfigManager::getValue($entity . '->VirtualActionToRealActionBinding', $actionParam)) != '')) ||
                    (array_key_exists(GGC_ConfigManager::getValue($entity . '->VirtualActionToRealActionBinding', $actionParam),
                            GGC_ConfigManager::getGroup($entity . '->RealActionToFunctionBinding')) &&
                            (GGC_ConfigManager::getValue($entity . '->RealActionToFunctionBinding', GGC_ConfigManager::getValue($entity . '->VirtualActionToRealActionBinding', $actionParam)) != '')))) {

                if (count(GGC_ConfigManager::getGroup($entity . '->ValidRealActions')) > 0) {
                    if (in_array(GGC_ConfigManager::getValue($entity . '->VirtualActionToRealActionBinding', $actionParam),
                            GGC_ConfigManager::getGroup($entity . '->ValidRealActions'))) {

                        $result = GGC_ConfigManager::getValue($entity . '->VirtualActionToRealActionBinding', $actionParam);
                    }

                } else {
                    $result = GGC_ConfigManager::getValue($entity . '->VirtualActionToRealActionBinding', $actionParam);
                }
            }  

            if (empty($result)) {
                /*
                 * Se è permesso l'accesso diretto ai valori reali, si controllano
                 * questi ultimi, ma non prima di aver controllato l'eventuale lista
                 * di filtro dei valori reali.
                 */
                if (GGC_ConfigManager::getValue('General', 'RealActionNameDirectAccess') == 1 ||
                        GGC_ConfigManager::getValue($entity, 'RealActionNameDirectAccess') == 1) {

                    if (count(GGC_ConfigManager::getGroup($entity . '->ValidRealActions')) > 0) {
                        if (in_array($actionParam, GGC_ConfigManager::getGroup($entity . '->ValidRealActions')) &&
                                ((array_key_exists($actionParam, GGC_ConfigManager::getGroup($entity . '->RealActionToPageBinding')) &&
                                (GGC_ConfigManager::getValue($entity . '->RealActionToPageBinding', $actionParam) != '')) ||
                                (array_key_exists($actionParam, GGC_ConfigManager::getGroup($entity . '->RealActionToFunctionBinding')) &&
                                (GGC_ConfigManager::getValue($entity . '->RealActionToFunctionBinding', $actionParam) != '')))) {

                            $result = $actionParam;
                        }

                    } else {
                        if ((array_key_exists($actionParam, GGC_ConfigManager::getGroup($entity . '->RealActionToPageBinding')) &&
                                (GGC_ConfigManager::getValue($entity . '->RealActionToPageBinding', $actionParam) != '')) ||
                                (array_key_exists($actionParam, GGC_ConfigManager::getGroup($entity . '->RealActionToFunctionBinding')) &&
                                        (GGC_ConfigManager::getValue($entity . '->RealActionToFunctionBinding', $actionParam) != ''))) {

                            $result = $actionParam;
                        }
                    }
                }
            }
        }
        
        return $this->checkAction($result);
    }
    
    private function getActionParam() {
        return $this->context->getRequest()->getAction();
    }

    private function checkAction($action) {
        if (empty($action))
            die('Action non corretta!');
        
        return $action;
    }
    
    protected function outputHttpResponseCacheCheck(
            &$output = NULL,
            $cacheSaveProvider = NULL,
            $cacheOriginProvider = NULL,
            $sourceUri = NULL,
            $updateInterval = 5,
            $updateByParams = NULL,
            $updateByHeaders = NULL,
            $updateByControls = NULL,
            $updateByContentEncodings = NULL,
            $aryUpdateByCustom = NULL,
            $instanceName = NULL) {
        
        $result = parent::outputResponseCacheCheck(
                $cacheSaveProvider,
                $cacheOriginProvider,
                $updateInterval,
                $updateByParams,
                $updateByControls,
                $updateByContentEncodings,
                $aryUpdateByCustom,
                $instanceName);
                
        if ($result) {
            GGC_OutputHttpResponseCacheManager::create(
                    $this->context,
                    $cacheSaveProvider,
                    $cacheOriginProvider,
                    $sourceUri,
                    $this->entityName,
                    NULL,
                    $updateInterval,
                    $updateByParams,
                    $updateByHeaders,
                    $updateByControls,
                    $updateByContentEncodings,
                    $aryUpdateByCustom,
                    $instanceName);

            GGC_OutputHttpResponseCacheManager::init($instanceName);

            $output = GGC_OutputHttpResponseCacheManager::get($instanceName);
        }
        
        return $result;
    }

}
