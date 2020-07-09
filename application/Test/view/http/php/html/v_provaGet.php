<?php
    $header = '<!DOCTYPE html><html><head><title>Prova Get</title>
                     <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                     </head><body>';
    $footer = '</body></html>';
    $body = '<h1>Prova Get!!!</h1>';
    $body .= '<br /><h3><a href="index.php">Home!</a></h3>';

    /**
     * Prova inclusione richiesta.
     */
    $requestFrom = $this->context->getRequest();
    //$uri = 'index.php?GGC_Entity=ditta&GGC_Action=ditta';
    $uri = 'index.php?GGC_Entity=ditta';
    $instanceName = 'ditta';
    $requestGet = GGC_GetRequest::create($instanceName, $requestFrom, $uri);
    
    if (isset($requestGet)) {
        $rd = new GGC_ControllerDispatcher($requestGet,
                new GGC_HttpResponse());
        
        if (isset($rd)) {
            /*
             * 1) Forma :
             * Otteniene il risultato in una variabile.
             */
            $outputDitta = $rd->get();
            
            $body .= '<br /><br />' . $outputDitta;
            
            /* 2) Forma :
             * Include il risultato direttamente nella pagina.
             */
//            $rd->incorporate();
        }
    }

    $response = $this->context->getResponse();
    
    $response->setData($header . $body . $footer);
    $response->sendData();
?>