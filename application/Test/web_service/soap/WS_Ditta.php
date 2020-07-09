<?php
    function sayHello($name) {
        $result = "Ciao : " . $name . ", sarai lieto di sapere che il servizio " .
                "di benvenuto, del framework GGC, associato alla ditte stà " .
                "funzionando!";
        return $result;
    }
    
    // Ovviamente il file dovrà esser recuperato in modo programmatico attraverso
    // i valori del file di config. (Questo è solo per provare)
    $wsdl_url="http://127.0.0.1/GGC_Framework/application/Test/public/web_service/soap/WS_Ditta.wsdl";
    
    $server = new SoapServer($wsdl_url);
    
    $server->addFunction("sayHello");
    $server->handle();
?>
