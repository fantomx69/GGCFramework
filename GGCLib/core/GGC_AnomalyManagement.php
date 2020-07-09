<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GGC_AnomalyManagement
 *
 * @author Gianni Carafone
 */
class GGC_AnomalyManagement {
    /*
     * Per conoscere lo stato di gestione.
     */
    private static $exceptionHandlerActive = false;
    private static $errorHandlerActive = false;
    
    /*
     * Costanti per accendere e spegnere le varie gestioni eccezioni e/o errori.
     */
    const M_EXCEPTION = '01';
    const M_ERROR = '10';
    const M_ALL = '11';
    
    static function start($level) {
        if ($level === self::M_ALL || $level === self::M_EXCEPTION) {
            set_exception_handler(array(__CLASS__, 'exceptionHandler'));
            self::$exceptionHandlerActive = true;
        }
        
        if ($level === self::M_ALL || $level === self::M_ERROR) {
            set_error_handler(array(__CLASS__, 'errorHandler'));
            self::$errorHandlerActive = true;
        }
    }
    
    static function end($level) {
        if ($level === self::M_ALL || $level === self::M_EXCEPTION) {
            restore_exception_handler();
            self::$exceptionHandlerActive = false;
        }
        
        if ($level === self::M_ALL || $level === self::M_ERROR) {
            restore_error_handler();
            self::$errorHandlerActive = false;
        }
    }
    
    static function started($level) {
        return ($level === self::M_EXCEPTION && self::$exceptionHandlerActive) ||
            ($level === self::M_ERROR && self::$errorHandlerActive) ||
            ($level === self::M_ALL && self::$exceptionHandlerActive && 
                self::$errorHandlerActive) || false;
    }

    /*
     * Gestione centralizzata eccezioni.
     */
    static function exceptionHandler(Exception $ex) {
        self::centralizedAnomalyManagement($ex);
    }
    
    /*
     * Gestione centralizzata errori.
     *
     * NOTA :
     * volendo si può lanciare anche sotto-tipi di "ErrorException", in base
     * a dove è avvenuto l'errore.
     */
    static function errorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
        //throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
//        if (GGC_ApplicationManager::getApplicationType() == 'http') {
           throw new GGC_HttpError($errstr, 0, $errno, $errfile, $errline);
//        }
    }
    
    /*
     * NOTA :
     * Qualunque parte del programma che va in errore o che deve lanciare una
     * condizione di errore deve ar riferimento a questa funzione, la quale, in
     * base all'entità, alla pagina, alla action, ecc..., farà determinate cose,
     * leggendo prima il comportamento generale dal file  configurazione e poi,
     * se esiste, quello inerente l'entità in questione.
     * Tutte le informazioni sopra elencate, dovranno essere passate tramite l'og
     * getto di tipo "GGC_Exception" o "GGC_Error". Se invece si tratta di un
     * errore di php allora si dovrà fare l'operazione impostata sempre tramite
     * il file di configurazione e sempre con gli stessi criteri.
     * 
     * Questa funzione può essere richiamata anche senza l'ausilio di una eccezione,
     * passando direttamente un array di info.
     * array(
     *  'Code' => ...
     *  'Message' => ...
     *  
     *  'Entity' => ...
     *  'Action' => ...
     *  'TipoOper' => ...
     *  'Parameters' => array()
     * --- oppure ---
     *  'Request' => objRequest
     *  'Response' => objResponse
     * 
     *  'ResponseAction' => ...  (Ha la precedenza su quello globale e di sezione entità)
     *  'Verbosity' => ...  (Ha la precedenza su quello globale e di sezione entità)
     *  'Log' => ...  (Ha la precedenza su quello globale e di sezione entità)
     * 
     *  'RedirectEntity' => ...
     *  'ForwardEntity' => ...
     *
     * ---Servono quando si utilizza l'array senza l'ausilio di un erore o eccezione ---
     * ---In modo che se si sceglie come'ResponseType' di far generare un errore o eccezione---
     * ---possiamo passare o il tipo e farlo creare da questa routine, o passare direttamente l'oggetto---
     *  'GGC_ErrorType'
     *  'GGC_Error...'
     *  'GGC_ExceptionType'
     *  'GGC_Exception...'
     * )
     */
    static function centralizedAnomalyManagement(Exception $ex = NULL, $aryAnomaly = NULL) {
        /*
         * Operazioni da fare in caso di eccezione.
         */
        if ($ex !== NULL) {
            /*
             * Operazioni da fare in caso di eccezione personalizzate del framework.
             */
            if ($ex instanceof GGC_Error || $ex instanceof GGC_Exception) {
                //...
            
            /*
             * Operazioni da fare in caso di altri tipi di eccezione (php, ecc...).
             */
            } else {
                $responseAction = GGC_ConfigManager::getValue('General->OtherThrowAnomalies', 'ResponseAction');
            
                if (GGC_ConfigManager::getValue($aryAnomaly['Entity'] . '->OtherThrowAnomalies', 'ResponseAction') != '') {
                    $responseAction = GGC_ConfigManager::getValue($aryAnomaly['Entity'] . '->OtherThrowAnomalies', 'ResponseAction');
                }
                
                switch ($responseAction) {
                    case "die":
                    case "exit": 
                        die('Anomaly Management GGC Framework!<br/>' . $ex->getMessage());
                        break;
                    
                    case "redirect" :
                        $redirectEntity = GGC_ConfigManager::getValue('General->OtherThrowAnomalies', 'RedirectEntity');

                        if (GGC_ConfigManager::getValue($aryAnomaly['Entity'] . '->OtherThrowAnomalies', 'RedirectEntity') != '') {
                            $redirectEntity = GGC_ConfigManager::getValue($aryAnomaly['Entity'] . '->OtherThrowAnomalies', 'RedirectEntity');
                        }

                        header('Location: index.php?GGC_Entity=' . $redirectEntity);

                        break;

                    case "forward" :
                        //...
                        break;
                    
                    default:
                        echo $ex->getMessage(); 
                }

            }
          
        /*
         * Operazioni da fare in caso di richiamo diretto d codesta funzione.
         */
        } elseif ($aryAnomaly !== NULL && is_array($aryAnomaly)) {
            $responseAction = GGC_ConfigManager::getValue('General->FrameworkThrowAnomalies', 'ResponseAction');
            
            if (array_key_exists('Entity', $aryAnomaly) &&
                    GGC_ConfigManager::getValue($aryAnomaly['Entity'] . '->FrameworkThrowAnomalies', 'ResponseAction') !== NULL) {
                $responseAction = GGC_ConfigManager::getValue($aryAnomaly['Entity'] . '->FrameworkThrowAnomalies', 'ResponseAction');
            }
            
            if (array_key_exists('ResponseAction', $aryAnomaly) &&
                    $aryAnomaly['ResponseAction'] != '') {
                $responseAction = $aryAnomaly['ResponseAction'];
            }
            
            switch ($responseAction) {
            case "false":
                return false;
                break;
            
            case "NULL":
                return NULL;
                break;
            
            case "die":
            case "exit": 
                die('Anomaly Management GGC Framework!<br/>' . $aryAnomaly['Message']);
                break;
            
            case "error" :
                //...
                break;
            
            case "exception" :
                //...
                break;
            
            case "redirect" :
                $redirectEntity = GGC_ConfigManager::getValue('General->FrameworkThrowAnomalies', 'RedirectEntity');
                
                if (GGC_ConfigManager::getValue($aryAnomaly['Entity'] . '->FrameworkThrowAnomalies', 'RedirectEntity') !== NULL) {
                    $redirectEntity = GGC_ConfigManager::getValue($aryAnomaly['Entity'] . '->FrameworkThrowAnomalies', 'RedirectEntity');
                }

                if ($aryAnomaly['RedirectEntity'] != '') {
                    $redirectEntity = $aryAnomaly['RedirectEntity'];
                }
                
                header('Location: index.php?GGC_Entity=' . $redirectEntity);
                
                break;
                
            case "forward" :
                //...
                break;
            }
        } else {
            $responseAction = GGC_ConfigManager::getValue('General->FrameworkThrowAnomalies', 'ResponseAction');
            
            switch ($responseAction) {
            case "false":
                return false;
                break;
            
            case "die":
            case "exit": 
                die('Anomaly Management GGC Framework!<br/>' . GGC_ConfigManager::getValue('General->FrameworkThrowAnomalies', 'Message'));
                break;
            
            case "error" :
                //...
                break;
            
            case "exception" :
                //...
                break;
            
            case "redirect" :
                $redirectEntity = GGC_ConfigManager::getValue('General->FrameworkThrowAnomalies', 'RedirectEntity');
                
                header('Location: index.php?GGC_Entity=' . $redirectEntity);
                
                break;
                
            case "forward" :
                //...
                break;
            }
        }
    }
}

?>
