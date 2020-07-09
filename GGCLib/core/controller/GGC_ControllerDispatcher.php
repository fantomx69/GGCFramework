<?php
//namespace GGC_lib\core\controller;

/**
 * Description of GGC_ControllerDispatcher
 * 
 * TODO :
 * Separare questa classe dai controller, metterla in una propria cartella
 * e creare una classe base di nome Dispatcher, con due classi derivate, questa
 * e una per i filtri di request (RequestFilter).
 *
 * @author Gianni Carafone
 */

final class GGC_ControllerDispatcher extends GGC_Object {
    private $_context = NULL;
    
    public function __construct($request, $response = NULL) {
        parent::__construct();
        
        $this->_context = $this->getContext($request, $response);
        
        /**
         * 
         * Controllo integritrà.
         */
        $errMsg = $this->integrityCheck();
        
        if (!empty($errMsg)) {
            return GGC_AnomalyManagement::centralizedAnomalyManagement (NULL,
                    array('Message' => $errMsg));
        }
    }
    
    private function init() {
        ;
    } 

    /**
     * Funzione con il duplice scopo :
     * 1) Eseguire il dispatcher per la richiesta esterna Http Principale.
     * 2) Eseguire i forwrds inerenti richieste interne.
     */
    function run() {
        $this->init();
        $this->runController($this->getEntity());
    }
    
    /**
     * Funzione che serve per acquisire il risultato di un'altra entity.
     * 
     * @return string, mixed
     */
    function get() {
        ob_start();
        $this->run();
        $result = ob_get_clean();
//        ob_end_clean();
        
        return $result;
    }
    
    /**
     * Funzione che serve per incorporare direttamente nel codice il risultato
     * di un'altra entity.
     */
    function incorporate() {
        $output = $this->get();
        
        $tmpFilePath = sys_get_temp_dir() . '/' .
                GGC_ApplicationManager::getApplicationName() . '/' .
//                'Temp/DispatcherIncorporate';
                'Temp';
        
        if (!is_dir($tmpFilePath)) {
            mkdir($tmpFilePath, 0777, TRUE);
        }
                        
        $tmpFileName = tempnam($tmpFilePath, "GGC_");

        $tmpFileHandle = fopen($tmpFileName, "w");
        fwrite($tmpFileHandle, $output);
        fclose($tmpFileHandle);
        
        include $tmpFileName;
        
        unlink($tmpFileName);
    }

    private function runController($entity) {
        /*
         * Estrapolazione nome controller.
         */
        $controllerName = $this->getControllerName($entity);

        if (!empty($controllerName)) {
            /**
             * Creazione e avvio controller, con nome instanza uguale al nome
             * del controller.
             */
            GGC_ControllerManager::create($controllerName, $this->_context,
                    NULL, NULL, $controllerName);
            
//            GGC_ControllerManager::init($controllerName);
            GGC_ControllerManager::run($controllerName);
            
        } else
            die('Controller non presente per l\'entità espressa');

    }

    private function hasController($entity) {
        return file_exists($this->getControllerName($entity, true, true));
    }

    private function getControllerName($entity, $isPath = false, $isExt = false) {
        $result = NULL;

        if (GGC_ConfigManager::getValue('RealEntityToControllerBinding', $entity)) {
            if ($isPath) {
                $result = GGC_ConfigManager::getValue ('General', 'ServerDocumentRootPath') .
                    GGC_ConfigManager::getValue ('General', 'ApplicationRootPath') .
                    GGC_ConfigManager::getValue ('General', 'ApplicationControllerPath');
            }        
                
            $result .= GGC_ApplicationManager::controllerNameFormat(GGC_ConfigManager::getValue(
                            'RealEntityToControllerBinding', $entity));
        
        } else {
            //---
            // Questo else e il codice che segue, per come è fatto "getEntity()"
            // non serve più, altrimenti tutte le regole in "getEntity()", sarbbero
            // vane.
            //---
            if ($isPath) {
                $result = GGC_ConfigManager::getValue ('General', 'ServerDocumentRootPath') .
                    GGC_ConfigManager::getValue ('General', 'ApplicationRootPath') .
                    GGC_ConfigManager::getValue ('General', 'ApplicationControllerPath');
            }        
                
            $result .= GGC_ApplicationManager::controllerNameFormat($entity);
        }
        
        if ($isExt) $result .= '.php';
        
        return $result;
    }

    private function getEntity() {
        $result = NULL;
        
        /*
         * Si prende l'entità esistente nelle richiesta.
         */
        $entityParam = $this->getEntityParam();

        /*
         * Se l'entità della richiesta è inesistente, si prende quella di
         * default del sistema.
         */
        if (empty($entityParam) && array_key_exists('DefaultEntity',
               GGC_ConfigManager::getGroup('General'))) {

               $entityParam = GGC_ConfigManager::getValue('General',
                       'DefaultEntity');
        }

        /*
         * Si controlla la presenza d valori nella lista filtro entità virtuali
         * se non è consentito l'accesso diretto alla lista valori entità reali.
         */
        if (!empty($entityParam) && count(GGC_ConfigManager::getGroup('ValidVirtualEntities')) > 0 && 
               !in_array($entityParam, GGC_ConfigManager::getGroup('ValidVirtualEntities'))) {

           $entityParam = NULL;
        }

        /*
         * Una volta accertato il parametro entity e se questo appartiene alla
         * eventuale lista di filtro "ValidVirtualEntities" se l'accesso diretto
         * ai valori reali non è possibile, ci accingiamo a proseguire nei controlli.
         */
        if (!empty($entityParam)) {
           /*
            * Se il valore è presente nella lista di quelli virtuali, si controlla
            * la tabella di associazione tra valori virtuali e quelli reali.
            */
           if (array_key_exists($entityParam, GGC_ConfigManager::getGroup('VirtualEntityToRealEntityBinding')) &&
                   (GGC_ConfigManager::getValue('VirtualEntityToRealEntityBinding', $entityParam)) &&
                   array_key_exists(GGC_ConfigManager::getValue('VirtualEntityToRealEntityBinding', $entityParam),
                           GGC_ConfigManager::getGroup('RealEntityToControllerBinding')) &&
                   (GGC_ConfigManager::getValue('RealEntityToControllerBinding',
                           GGC_ConfigManager::getValue('VirtualEntityToRealEntityBinding', $entityParam)))) {

               if (count(GGC_ConfigManager::getGroup('ValidRealEntities')) > 0) {
                   if (in_array($entityParam, GGC_ConfigManager::getGroup('ValidRealEntities'))) {
                       $result = GGC_ConfigManager::getValue('VirtualEntityToRealEntityBinding', $entityParam);
                   }

               } else {
                   $result = GGC_ConfigManager::getValue('VirtualEntityToRealEntityBinding', $entityParam);
               }
           }

           if (empty($result)) {
               /*
                * Se è permesso l'accesso diretto ai valori reali, si controllano
                * questi ultimi, ma non prima di aver controllato l'eventuale lista
                * di filtro dei valori reali.
                */
               if (GGC_ConfigManager::getValue('General', 'RealEntityNameDirectAccess') == 1) {
                   if (count(GGC_ConfigManager::getGroup('ValidRealEntities')) > 0) {
                       if (in_array($entityParam, GGC_ConfigManager::getGroup('ValidRealEntities')) &&
                               array_key_exists($entityParam, GGC_ConfigManager::getGroup('RealEntityToControllerBinding')) &&
                               (GGC_ConfigManager::getValue('RealEntityToControllerBinding', $entityParam))) {

                           $result = $entityParam;
                       }

                   } else {
                       if (array_key_exists($entityParam, GGC_ConfigManager::getGroup('RealEntityToControllerBinding')) &&
                               (GGC_ConfigManager::getValue('RealEntityToControllerBinding', $entityParam))) {

                           $result = $entityParam;
                       }
                   }
               }
           }
        }
        
        return $this->checkEntity($result);
    }

    private function checkEntity($entity) {
        // TODO :
        // Spostare in sanitizeVars()
        if (!preg_match('/^[a-z0-9-]+$/i', $entity)) {
            // TODO log attempt, redirect attacker, ...
            die('Unsafe entity "' . $entity . '" requested');
        }

        if (!$this->hasController($entity)) {
            // TODO log attempt, redirect attacker, ...
            die('Controller "' . $entity . '" not found');
        }

        return $entity;
    }
    
    /*
     * Controlla il tipo di protocollo di richiesta e lo resituisce.
     * 
     * NOTA :
     * Togliere da qui questo controllo perchè deve essere fatto all'inizio
     * e il risultato posto nell'oggetto application e volendo nell'oggetto
     * di config iniziale.
     */
    private function getApplicationType() {
        return GGC_ApplicationManager::getApplicationType();
    }
    
    private function getEntityParam() {
        return $this->_context->getRequest()->getEntity();
    }
    
//    private function getControllerName($entity) {
//        $result = NULL;
//        
//        if (array_key_exists($entity,
//            GGC_ConfigManager::getGroup('RealEntityToControllerBinding')) &&
//            (GGC_ConfigManager::getValue('RealEntityToControllerBinding', $entity))) {
//            
//            $result = GGC_ConfigManager::getValue('RealEntityToControllerBinding', $entity);
//        }
//        
//        return $result;
//    }
    
    private function getContext($request, $response = NULL) {
        if (!isset($response)) {
            $appType = $this->getApplicationType();

            if ($appType == 'http') {
                $response = new GGC_HttpResponse();
            }
        }
        
        return new GGC_Context($request, $response);
    }

    private function integrityCheck($varName = NULL) {
        $result = NULL;
        
        if ((empty($varName) || $varName == '_context') &&
                !isset($this->_context)) {
            $result = '[context] non creato.';
        }
        
        return $result;
    }
}

?>
