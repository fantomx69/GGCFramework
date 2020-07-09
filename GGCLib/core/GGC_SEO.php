<?php
/**
 * Possiamo utilizzare questa classe per ottenere gli url canonici (e viceversa)
 * da utlizzare come link all'inteno delle pagine costruite dinamicamente.
 * Gli url ottenuti possono appartenere o al sistema interno "FriendlyURL"
 * o a quello del server "ModRewrite".
 *
 * @author Gianni Carafone
 */
class GGC_SEO {
    static function isFriendlyURLActive() {
        return (bool)GGC_ConfigManager::getValue('General', 'FriendlyURL');
    }
    
//    static function setFriendlyURLActive($value) {
//        
//    }
    
    static function getFriendlyURLFormat() {
        return GGC_ConfigManager::getValue('General', 'FriendlyURLFormat');
    }
    
    static function getFriendlyURLShortFormat($resultTypeArray = false) {
        $result = GGC_ConfigManager::getValue('General', 'FriendlyURLShortFormat');
        
        if ($resultTypeArray && !empty($result)) {
            $result = explode('/', $result);
        }
        
        return $result;
    }
    
//    static function setFiendlyURLShortScheme($scheme) {
//        
//    }    

    static function getFriendlyURL($url = NULL, GGC_URL $oURL = NULL,
            $resultTypeObject = false) {
        $result = NULL;
        
        if (!empty($url))
            $oURL = new GGC_URL($url);
        
        if (!is_null($oURL)) {
            $oURL->setUserFriendly(true);
            
            if ($resultTypeObject) {
                $result = $oURL;
            } else {
                $result = $oURL->getQuery();
            }
        }
        
        return $result;
    }
    
    static function getReverseFriendlyURL($friendlyURL = NULL,
            GGC_URL $oFriendlyURL = NULL, $resultTypeObject = false) {
        $result = NULL;
        
        if (!empty($friendlyURL))
            $oURL = new GGC_URL($friendlyURL, array(GGC_URL::USER_FRIENDLY => true));
            
        if (!is_null($oURL)) {
            $oURL->setUserFriendly(false);
            
            if ($resultTypeObject) {
                $result = $oURL;
            } else {
                $result = $oURL->getQuery();
            }
        }
        
        return $result;
    }
    
    static function isModRewriteURLActive() {
        
    }
    
    static function getModRewriteURL($url) {
        
    }
    
    static function getReverseModRewriteURL($modRewriteURL) {
        
    }
    
}

?>
