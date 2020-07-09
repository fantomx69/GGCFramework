<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Questa classe serve per trasformare richieste standard in richieste di
 * entità da importare in altre pagine, ma seguendo sempre l'iter, e quindi,
 * i controlli di integrità effettuati per le richieste esterne. Serve anche
 * per implementare il fragment-caching, ovvero mettere in cache solo porzioni
 * di pagina.
 *
 * @author Gianni
 */
class GGC_ForwardRequest extends GGC_InternalRequest {
    static function create($instanceName, $objRequestFrom, $uri = NULL,
            $aryRequestFields = NULL) {
        
        $result = NULL;
        
        /**
         * Controllo integrità parametri.
         */
        $errMsg = NULL;
        
        if (empty($instanceName)) {
            $errMsg = '[instanceName] non presente.';
        }
        
        if (!empty($errMsg)) {
            return GGC_AnomalyManagement::centralizedAnomalyManagement (NULL,
                    array('Message' => $errMsg));
            
        } else {
            $result = new GGC_ForwardRequest($objRequestFrom, $uri, $aryRequestFields);
            self::requestStackUpdate($result, $instanceName);
        }
        
        return $result;
    }
    
    function __construct($objRequestFrom, $uri = NULL, $aryRequestFields = NULL) {
        parent::__construct($objRequestFrom);
        
        /**
         * Controllo integrità parametri.
         */
        $errMsg = NULL;
        
        if (empty($uri) && empty($aryRequestFields)) {
            $errMsg = '[uri] e [aryRequestFields] entrambi non presenti.';
        }
        
        /*
         * Per ora mi fido e disabilito questo ulteriore controllo.
         */
//        if (empty($errMsg) && !empty($aryRequestFields) &&
//                !is_array($aryRequestFields)) {
//            $errMsg = '[aryRequestFields] deve essere un array.';
//        }
        
        if (!empty($errMsg)) {
            return GGC_AnomalyManagement::centralizedAnomalyManagement (NULL,
                    array('Message' => $errMsg));
            
        } else {
            $this->requestSectioning($uri, $aryRequestFields);

            /**
             * Controllo integrità oggetto.
             */
            $errMsg = self::integrityCheck();

            if (!empty($errMsg)) {
                return GGC_AnomalyManagement::centralizedAnomalyManagement (NULL,
                        array('Message' => $errMsg));
            }
        }
    }
    
    protected function requestSectioning($uri = NULL, $aryRequestFields = NULL) {
        if (!empty($uri)) {
            parent::requestSectioning($uri);
            
        } elseif (!empty($aryRequestFields)) {
            $this->aryWorkParameters[self::workParamFormat(self::RFT_ENTITY)] =
                    $aryRequestFields['entity'];
            $this->aryWorkParameters[self::workParamFormat(self::RFT_ACTION)] =
                    $aryRequestFields['action'];
            $this->aryWorkParameters[self::workParamFormat('TypeOper')] =
                    $aryRequestFields['type_oper'];
            $this->aryParameters = $aryRequestFields['parameters'];
            
            /*
             * Se non è stata fornita nessuna entità, si prova a
             * fornire quella di default del sistema.
             */
            $this->setDefaultEntity();
            
            /*
             * Se non è stata fornita nessuna action, si prova a
             * fornire quella di default del sistema.
             */
            $this->setDefaultAction();
        }
    }
    
    protected function integrityCheck($varName = NULL) {
        /*
         * Controllo integrità parent.
         */
        $result = parent::integrityCheck();
        
        /*
         * Controllo integrità. Se si è già in uno stato di errore, non serve
         * effettuare anche il controllo per questa classe.
         */
        if (empty($result)) {
            //...
        }
        
        return $result;
    }    
}

?>
