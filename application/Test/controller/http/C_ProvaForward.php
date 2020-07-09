<?php
/**
 * Description of C_ProvaForward
 *
 * @author Gianni
 */
final class C_ProvaForward extends GGC_HttpControllerProvider {
    function __construct($context, $entityName = NULL, $entity = NULL) {
        parent::__construct($context, $entityName, $entity);
        
        /**
         * Eventuali inizializzazioni, se non già eseguite.
         */
        if (empty($this->entityName)) {
            $this->entityName = substr(__CLASS__, 2);
        }
    }
    
    /*
     * Override per gestire la risposta da memoria, se voglio rispondere a più
     * tipi diversi.
     */
//    protected function getViewMemStream($page, $responseDataType) {
////        $responseDataTypeAvailable = array('html' => true, 'xhtml' => false,
////            'html5' => false, 'xml' => false, 'json' => false, 'rss' => false,
////            'csv' => false, 'txt' => false, 'empty' => false, 'null' => false);
//        $responseDataTypeAvailable = array('html' => true);
//        
//        $result = false;
//        
//        foreach ($responseDataTypeAvailable as $value) {
//            if ($responseDataType == $value) {
//                $result = true;
//                break;
//            }
//        }
//               
//        return $result;
//    }

    /*
     * Override per gestire la risposta da memoria, se voglio rispondere a un
     * solo tipo, in questo caso 'html', grazie alla funzione
     * 'displayViewHtmlMemStream().'
     */
    protected function getViewMemStream($page, $responseDataType) {
        return ($responseDataType == 'html');
    }
    
    protected function displayViewHtmlMemStream($page) {
        if ($page == 'vm_provaForward') {
            /*
             * Prova forward richiesta.
             */
            $requestFrom = $this->context->getRequest();
            
            $aryRequestFields = array('entity' => 'ditta',
                'action' => 'ditta',
                'type_oper' => $requestFrom->getWorkParameter('TypeOper'),
                'parameters' => $requestFrom->getParameters());
            
            $instanceName = 'ditta';
            $requestForward = GGC_ForwardRequest::create($instanceName,
                    $requestFrom, NULL, $aryRequestFields);

            if (isset($requestForward)) {
                $rd = new GGC_ControllerDispatcher($requestForward,
                        new GGC_HttpResponse());

                if (isset($rd)) {
                    $rd->run();
                }
            }
        }
    }
}

?>
