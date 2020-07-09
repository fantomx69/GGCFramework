<?php
/**
 * NOTA* : I valori statici di classe dovranno in futuro essere collocati
 * in un file di configurazione esterno 'Authentication.ini' , per avere una
 * gestione elegante e configurabile senza alterare i sorgenti.
 *
 * @author Gianni
 */
class GGC_Authentication {
    static $enableGuest = true;
    static $mandatoryPassword = true;
    static $cryptoAlgorithm;
    static $hashAlgorithm;
    
    /**
     * Provider User e UserProfile
     */
    static $userSerializationProvider = GGC_UserProvider::SP_FILE_INI;
    
    /*
     * Riferimento oggetto user.
     */
    static protected $user = NULL;
    
    /**
     * Salvataggio stato.
     */
    static protected $stateSession = false;
    static protected $stateCookie = false;

    static function createToken($userName, $password) {
        return $userName;
    }

    static function isAuthenticated() {
        return !is_null(static::$user) && !static::$user->isGuest();
    }
    
    static function isGuest() {
        return !is_null(static::$user) && static::$user->isGuest();
    }
    
    static function login($token = NULL) {
        $result = false;
        
        GGC_UserManager::create(static::$userSerializationProvider);
        
        $user = GGC_UserManager::getUser($token, GGC_UserProvider::UFN_TOKEN);
        
        if (!empty($user)) {
            if ($user->isEnabled() && !$user->isWaiting() &&
                    !$user->isSuspended() && !$user->isDeleted()) {
                
                if ((!empty($token) && $token == $user->getToken()) ||
                        (static::$enableGuest && empty($token) && $user->isGuest())) {
                    static::$user = $user;

                    $result = true;
                }
            }
        }
        
        return $result;
    }    
    
    static function logout() {
        static::$user = NULL;
    }
    
    static function getUserName() {
        $result = NULL;
        
        if (!is_null(static::$user)) {
            $result = static::$user->getUserName();
        }
        
        return $result;
    }
    
    static function getUser() {
        return static::$user;
    }

    static function getUserProfile() {
        $result = NULL;

        if (!is_null(static::$user)) {
            $result = static::$user->getUserProfile();
        }
            
        return $result;
    }

    static function setStateSession($flag) {
        static::$stateSession = (bool)$flag;
        
        if (static::$stateSession) {
            GGC_SessionManager::setValue('GGC_AuthToken', static::$user->getToken());
        } else {
            GGC_SessionManager::unsetKey('GGC_AuthToken');
        }
    }
    
    static function isStateSession() {
        return static::$stateSession;
    }
    
    static function setStateCookie($flag) {
        
    }
    
    static function isStateCookie() {
        return static::$stateCookie;
    }
    
    static function hash($value) {
        return $value;
    }
}

?>
