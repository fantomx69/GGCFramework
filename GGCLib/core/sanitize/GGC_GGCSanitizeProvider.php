<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GGC_GGCSanitizeProvider
 *
 * @author Gianni Carafone
 */
abstract class GGC_GGCSanitizeProvider extends GGC_SanitizeProvider {
    /*
     * -------------------------------------------------------------------------
     * Metodi wrapper di pubblico utilizzo.
     * 
     * TODO :
     * Aggiungere acnhe i metodi per entrambe le categorie che fungano da wrapper
     * per l'utilizzo di tutti i flag disponibili.
     * -------------------------------------------------------------------------
     */
    
    /*
     * ----------------
     * Metodi sanitize.
     * ----------------
     */
    function sanitizeString($mixedVar, $aryOptions = NULL) {
        return $this->perform($mixedVar, self::S_FILTER_STRING, $aryOptions);
    }
    
    function sanitizeNumFloat($mixedVar, $aryOptions = NULL) {
        return $this->perform($mixedVar, self::S_FILTER_NUM_FLOAT, $aryOptions);
    }
    
    function sanitizeNumInt($mixedVar, $aryOptions = NULL) {
        return $this->perform($mixedVar, self::S_FILTER_NUM_INT, $aryOptions);
    }
    
    function sanitizeEMail($mixedVar, $aryOptions = NULL) {
        return $this->perform($mixedVar, self::S_FILTER_EMAIL, $aryOptions);
    }
    
    function sanitizeUrl($mixedVar, $aryOptions = NULL) {
        return $this->perform($mixedVar, self::S_FILTER_URL, $aryOptions);
    }
    
    function sanitizeRegExp($mixedVar, $aryOptions = NULL) {
        return $this->perform($mixedVar, self::S_FILTER_REGEXP, $aryOptions);
    }
    
    function sanitizeCallBack($mixedVar, $aryOptions = NULL) {
        return $this->perform($mixedVar, self::S_FILTER_CALLBACK, $aryOptions);
    }
    
    /*
     * ----------------
     * Metodi validate.
     * ----------------
     */
    function validateBoolean($mixedVar, $aryOptions = NULL) {
        return $this->perform($mixedVar, self::V_FILTER_BOOLEAN, $aryOptions);
    }
    
    function validateNumFloat($mixedVar, $aryOptions = NULL) {
        return $this->perform($mixedVar, self::V_FILTER_NUM_FLOAT, $aryOptions);
    }
    
    function validateNumInt($mixedVar, $aryOptions = NULL) {
        return $this->perform($mixedVar, self::V_FILTER_NUM_INT, $aryOptions);
    }
    
    function validateEMail($mixedVar, $aryOptions = NULL) {
        return $this->perform($mixedVar, self::V_FILTER_EMAIL, $aryOptions);
    }
    
    function validateUrl($mixedVar, $aryOptions = NULL) {
        return $this->perform($mixedVar, self::V_FILTER_URL, $aryOptions);
    }
    
    function validateIP($mixedVar, $aryOptions = NULL) {
        return $this->perform($mixedVar, self::V_FILTER_IP, $aryOptions);
    }
    
    function validateRegExp($mixedVar, $aryOptions = NULL) {
        return $this->perform($mixedVar, self::V_FILTER_REGEXP, $aryOptions);
    }
    
    function validateCallBack($mixedVar, $aryOptions = NULL) {
        return $this->perform($mixedVar, self::V_FILTER_CALLBACK, $aryOptions);
    }
    
    /*
     * Metodo che fare entrambe le cose
     */
    function examinesArray($aryValues, $aryOptions) {
        return $this->perform($aryValues, NULL, $aryOptions);
    }    
    
    /*
     * -------------------
     * Funzioni di lavoro.
     * -------------------
     */
    /*
     * Metodo dispatcher chiamate di sanitizzazione e validazione.
     */
    protected function perform($mixedVar, $filterType = NULL, $aryOptions = NULL) {
        $varType = NULL;
        $mixedResult = NULL;
        
        /*
         * Controllo integrità parametri/chiamata.
         */
        if (is_null($filterType) && is_null($aryOptions))
            return GGC_AnomalyManagement::centralizedAnomalyManagement (NULL,
                    array('Message' =>
                        'Classe : [ ' . __CLASS__ . ' ]' . PHP_EOL .
                        'Metodo : [ ' . __METHOD__ .' ]' . PHP_EOL .
                        'I parametri [filterType] e [aryOptions] ' .
                        'sono entrambi nulli.'));
        
        /*
         * Controllo integrità eventuali flags.
         */
        if (!is_null($aryOptions) && !$this->checkFlags($aryOptions, $filterType))
            return GGC_AnomalyManagement::centralizedAnomalyManagement (NULL,
                    array('Message' =>
                        'Classe : [ ' . __CLASS__ . ' ]' . PHP_EOL .
                        'Metodo : [ ' . __METHOD__ .' ]' . PHP_EOL .
                        'Flags di sanitizzazione inopportuni.'));
        
        /*
         * Controllo tipo valore di input
         */
        if (!is_array($mixedVar)) {
            $varType = 'VAR';
            
        } elseif(!GGC_Array::isMultiDimesional($mixedVar)) {
            $varType = 'ARRAY';
            
        } else {
            return GGC_AnomalyManagement::centralizedAnomalyManagement (NULL,
                    array('Message' =>
                        'Classe : [ ' . __CLASS__ . ' ]' . PHP_EOL .
                        'Metodo : [ ' . __METHOD__ .' ]' . PHP_EOL .
                        'Impossibile determinare il tipo di variabile da sanatizzare.'));
        }
        
        /*
         * Scelta oprazione da eseguire.
         */
        if ($varType == 'VAR') {
            $operationType = $this->getOperationType($filterType);
            
            if ($operationType == 'SANITIZE') {
                $mixedResult = $this->_sanitize($mixedVar, $filterType, $aryOptions);
            } elseif ($operationType == 'VALIDATE') {
                $mixedResult = $this->_validate($mixedVar, $filterType, $aryOptions);
            }
            
        } else {
            $mixedResult = $this->_examinesArray($mixedVar, $aryOptions);
        }
        
        return $mixedResult;
        
    }
    
    /*
     * -----------------
     * Metodi di lavoro.
     * -----------------
     */
    
    protected function checkFlags($aryOptions, $filterType = NULL) {
        $result = true;
        
        if (GGC_Array::md_array_key_exists('flags', $aryOptions)) {
            if (GGC_Array::countDimension($aryOptions) > 2) {
                    foreach ($aryOptions as $aryValue) {
                        $operationType = $this->getOperationType($aryValue['filter']);

                        foreach ($aryValue['flags'] as $value) {
                            if (($operationType == 'SANITIZE' && ($value < self::S_FLAG_STRING_STRIP_LOW ||
                                        $value > self::S_FLAG_NUM_FLOAT_ALLOW_SCIENTIFIC)) ||
                                    $operationType == 'VALIDATE' && ($value < self::V_FLAG_BOOLEAN_NULL_ON_FAILURE ||
                                        $value > self::V_FLAG_IP_NO_RES_RANGE)) {

                                $result = false;
                            }
                        }
                    }

            } elseif (GGC_Array::countDimension($aryOptions) == 2) {
                if (!is_null($filterType)) {
                    $operationType = $this->getOperationType($filterType);
                } else {
                    $operationType = $this->getOperationType($aryOptions['filter']);
                }    
                
                foreach ($aryOptions['flags'] as $value) {
                    if (($operationType == 'SANITIZE' && ($value < self::S_FLAG_STRING_STRIP_LOW ||
                                $value > self::S_FLAG_NUM_FLOAT_ALLOW_SCIENTIFIC)) ||
                            $operationType == 'VALIDATE' && ($value < self::V_FLAG_BOOLEAN_NULL_ON_FAILURE ||
                                $value > self::V_FLAG_IP_NO_RES_RANGE)) {

                        $result = false;
                    }
                }
            }
        }
        
        return $result;
    }
    
    /*
     * NOTA :
     * Quando viene esaminata una singola variabile, l'array options può avere
     * uno dei seguenti formati :
     * array('flags' => array(flag1, flag2, flag...n)); oppure
     * array('flags' => array(flag1, flag2, flag...n),
     *       'options' => array('opt1' => ...., 'opt2' => ....));
     * 
     */
    private function _sanitize($var, $filterType, $aryOptions = NULL) {
        $result = NULL;
        
        if (!empty($aryOptions)) {
            if ($filterType == GGC_SanitizeProvider::S_FILTER_STRING) {
                
                if (count($aryOptions['flags'])) {
                    $result = $var;

                    foreach ($aryOptions['flags'] as $flagValue) {
                        switch ($flagValue) {
                            case GGC_SanitizeProvider::S_FLAG_STRING_ENCODE_LOW:
                            case GGC_SanitizeProvider::S_FLAG_STRING_ENCODE_HIGH:
                                $result = filter_var($result, FILTER_SANITIZE_ENCODED, 
                                        $this->getPhpFilterMap('sanitize_flags', $flagValue));
                                break;

                            case GGC_SanitizeProvider::S_FLAG_STRING_STRIP_LOW:
                            case GGC_SanitizeProvider::S_FLAG_STRING_STRIP_HIGH:
                            case GGC_SanitizeProvider::S_FLAG_STRING_ENCODE_AMP:
                                $result = filter_var($result, FILTER_SANITIZE_STRING, 
                                        $this->getPhpFilterMap('sanitize_flags', $flagValue));
                                break;

                            case GGC_SanitizeProvider::S_FLAG_STRING_ENCODE_QUOTES:
                                $result = filter_var($result, FILTER_SANITIZE_MAGIC_QUOTES,
                                        $this->getPhpFilterMap('sanitize_flags', $flagValue));
                                break;

                            case GGC_SanitizeProvider::S_FLAG_STRING_SPECIAL_CHARS:
                                $result = filter_var($result, FILTER_SANITIZE_SPECIAL_CHARS,
                                        $this->getPhpFilterMap('sanitize_flags', $flagValue));
                                break;

                            case GGC_SanitizeProvider::S_FLAG_STRING_FULL_SPECIAL_CHARS:
                                $result = filter_var($result, FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                                        $this->getPhpFilterMap('sanitize_flags', $flagValue));
                                break;

                            case GGC_SanitizeProvider::S_FLAG_STRING_ENTITIES:
                                $result = htmlentities($result, ENT_NOQUOTES);
                                break;

                            case GGC_SanitizeProvider::S_FLAG_STRING_MIDDLE_ENTITIES:
                                $result = htmlentities($result, ENT_COMPAT);
                                break;

                            case GGC_SanitizeProvider::S_FLAG_STRING_FULL_ENTITIES:
                                $result = htmlentities($result, ENT_QUOTES);
                                break;

                            case GGC_SanitizeProvider::S_FLAG_STRING_LTRIM:
                                $result = ltrim($result);
                                break;

                            case GGC_SanitizeProvider::S_FLAG_STRING_RTRIM:
                                $result = rtrim($result);
                                break;

                            case GGC_SanitizeProvider::S_FLAG_STRING_TRIM:
                                $result = trim($result);
                                break;

                            case GGC_SanitizeProvider::S_FLAG_STRING_CLEAN_WHITE_LIST:
                                if (count($this->_sanitizeWhiteList) > 0) {
                                    $result = preg_replace("/[^" . 
                                            preg_quote(implode('', $this->_sanitizeWhiteList), '/') . 
                                            "]/i", "", $result);
                                }
                                break;

                            case GGC_SanitizeProvider::S_FLAG_STRING_CLEAN_BLACK_LIST:
                                if (count($this->_sanitizeBlackList) > 0) {
                                    $result = preg_replace("/[" .
                                            preg_quote(implode('', $this->_sanitizeBlackList), '/') .
                                            "]/i", "", $result);
                                }
                                break;

                            default:
                                break;
                        }
                    }
                    
                } else {
                    $result = filter_var($var, FILTER_SANITIZE_STRING);
                }

            /*
             * TODO :
             * Controllare l'esistenza di 'pattern' e 'replacement'.
             */
             //...
            } elseif ($filterType == GGC_SanitizeProvider::S_FILTER_REGEXP) {
                $result = preg_replace($aryOptions['options']['pattern'],
                        $aryOptions['options']['replacement'], $var);

            /*
             * Callback personalizzato che permette il passaggio di più parametri.
             * 
             * NOTA :
             * In questo caso la struttura dell'array 'options', deve essere il seguente :
             * array('callback' => <nome_func_to_call>,
             *       'add_params' => array(<param1>, <param2>, <param..n>));
             * 
             * TODO :
             * Controllare l'esistenza di 'callback' e 'add_params'.
             */
            } elseif ($filterType == GGC_SanitizeProvider::S_FILTER_CALLBACK &&
                    array_key_exists('add_params', $aryOptions) && is_array($aryOptions['add_params'])) {
                array_unshift($aryOptions['add_params'], $var);
                $result = call_user_func_array($aryOptions['callback'], $aryOptions['add_params']);

            } else {
                $flag = NULL;

                foreach ($aryOptions['flags'] as $flagValue) {
                    $flag |= $this->getPhpFilterMap('sanitize_flags', $flagValue);
                }

                $result = filter_var($var, $this->getPhpFilterMap('sanitize_filters', $filterType), $flag);
            }
            
        } else {
            if ($filterType == GGC_SanitizeProvider::S_FILTER_STRING) {
                $result = filter_var($var, FILTER_SANITIZE_STRING);
                
            } else {
                $result = filter_var($var, $this->getPhpFilterMap('sanitize_filters', $filterType));
            }
        }
        
        return $result;
    }
    
    private function _validate($var, $filterType, $aryOptions = NULL) {
        /*
         * Si controlla se è stato specificato solo il tipo di filtro e quindi
         * se l'array di opzioni è nullo.
         */
        if (!is_null($aryOptions)) {
            $flag = NULL;

            foreach ($aryOptions['flags'] as $flagValue) {
                $flag |= $this->getPhpFilterMap('sanitize_flags', $flagValue);
            }

            /*
             * Si controlla se ci sono opzioni da passare, se cos' fosse, si crea
             * un array tempèoraneo strutturato in modo conforme alla funzione
             * 'filter_var()', il quale si aspetta il campo flags, come un valore
             * bitwise e non come viene gestito dal framework, ovvero, un array.
             */
            if (isset($aryOptions['options']) /*&& is_array($aryOptions['options'])*/) { 
                $aryTemp = array('flags' => $flag,
                    'options' => $aryOptions['options']);

                $result = filter_var($var, $this->getPhpFilterMap('validate_filters', $filterType), $aryTemp);
                
            } else {
                $result = filter_var($var, $this->getPhpFilterMap('validate_filters', $filterType), $flag);
            }
            
        } else {
            $result = filter_var($var, $this->getPhpFilterMap('validate_filters', $filterType));
        }
        
        return $result;
    }    
    
    /*
     * NOTA :
     * Questo metodo può essere usato in tre modi :
     * 1) Passare un array di options multi-dimensionale, che come chiavi ha le
     *    stesse dell'array di valori da controllare e come valori ha nel primo
     *    elemento il pattern e nel secondo un altro array contenente la lista
     *    dei flag da applicare. Così facendo, ogni elemento dell'array valori
     *    da controllare può avere il suo pattern e il suo set di flag da applicare.
     *    Esempio :array(<chiave-valore> => 
     *                   array('filter' => <...>, 
     *                         'flags' => array('flag1', '...'), 
     *                         'options' => array(<chiave> => <valore>))).
     * 
     * 2) Passare un array solo con le opzioni e/o flag da applicare per tutti
     *    i valori dell'array da sanatizzare e un solo pattern.
     *    Esempio : array('filter' => <...>,
     *                    'flags' => array('flag1', '...'),
     *                    'options' => array(<chiave> => <valore>)).
     * 
     * 
     * 3) Passare un array con un filtro, senza flags e opzioni, per ogni valore
     *    dell'array valori.
     *    Esempio : array('product_id' => FILTER_SANITIZE_ENCODED,
     *                    'product_number' => FILTER_VALIDATE_INT,
     *                    '<.....> => '<....>');
     *    
     */
    private function _examinesArray($aryVar, $aryOptions) {
        $aryResult = array();
        
        if (GGC_Array::countDimension($aryOptions) > 2) {
            /*
             * Condizione (1).
             */
            foreach ($aryVar as $key => $value) {
                if (array_key_exists($key, $aryOptions)) {
                    $operationType = $this->getOperationType($aryOptions[key]['filter']);
                    
                    if ($operationType == 'SANITIZE') {
                        $aryResult[$key] = $this->_sanitize($value,
                                $aryOptions[key]['filter'], 
                                array('flags' => $aryOptions[key]['flags'], 
                                    'options' => $aryOptions[key]['options']));
                        
                    } else {
                        $aryResult[$key] = $this->_validate($value,
                                $aryOptions[key]['filter'], 
                                array('flags' => $aryOptions[key]['flags'], 
                                    'options' => $aryOptions[key]['options']));
                        
                    }
                }
            }

        /*
         * Condizione (2).
         */
        } elseif (GGC_Array::countDimension($aryOptions) == 2) {
            foreach ($aryVar as $key => $value) {
                $operationType = $this->getOperationType($aryOptions['filter']);

                if ($operationType == 'SANITIZE') {
                    $aryResult[$key] = $this->_sanitize($value, $aryOptions['filter'],
                            array('flags' => $aryOptions['flags'], 
                                    'options' => $aryOptions['options']));

                } else {
                    $aryResult[$key] = $this->_validate($value, $aryOptions['filter'],
                            array('flags' => $aryOptions['flags'], 
                                    'options' => $aryOptions['options']));

                }
            }
                
        /*
         * Condizione (3).
         */    
        } else {
            foreach ($aryVar as $key => $value) {
                $operationType = $this->getOperationType($aryOptions[$key]);

                if ($operationType == 'SANITIZE') {
                    $aryResult[$key] = $this->_sanitize($value, $aryOptions[$key]);

                } else {
                    $aryResult[$key] = $this->_validate($value, $aryOptions[$key]);
                }
            }
        }
        
        return $aryResult;
    }
    
    protected function getOperationType($filterType) {
        $result = NULL;
        
        if (!empty($filterType)) {
            if ($filterType >= self::S_FILTER_STRING &&
                    $filterType <= self::S_FILTER_CALLBACK) {
                $result = 'SANITIZE';

            } elseif ($filterType >= self::V_FILTER_BOOLEAN &&
                    $filterType <= self::V_FILTER_CALLBACK) {
                $result = 'VALIDATE';
            } 
            
        } else {
            return GGC_AnomalyManagement::centralizedAnomalyManagement (NULL,
                    array('Message' =>
                        'Classe : [ ' . __CLASS__ . ' ]' . PHP_EOL .
                        'Metodo : [ ' . __METHOD__ .' ]' . PHP_EOL .
                        'Impossibile determinare il tipo operazione sanitizzazione.'));
        }
        
        return $result;
    }
    
}

?>
