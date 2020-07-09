<?php
/**
 * Description of GGC_URL
 *
 * @author Gianni Carafone
 */
class GGC_URL extends GGC_URI {
    function __construct($url = NULL, array $aryOptions = array()) {
        if (empty($url) && empty($aryOptions))
            $url = GGC_HttpRequest::getPageURL();
        
        parent::__construct($url, $aryOptions);
    }
    
}

?>
