<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GGC_GGCHttpSanitize
 *
 * @author Gianni Carafone
 */
class GGC_GGCHttpSanitizeProvider extends GGC_GGCSanitizeProvider {
    /*
     * Opzioni tipo input
     */
    const SI_GET = 1;
    const SI_POST = 2;
    const SI_COOKIE = 3;
    const SI_REQUEST = 4;
    const SI_SERVER = 5;
    const SI_ENV = 6;
        
    static function create() {
        return new GGC_GGCHttpSanitizeProvider();
    }
    
    function init($mixed = NULL) {
        ;
    }
    
    /*
     * Punto di raccolata richieste di sanitizzazione e validazione. Questo metodo
     * può essere usato sia per validare valori che array di input, a seconda se
     * si valorizza la variabile '$inputVarName'.
     */
    function sanitizeInput($inputOption, $inputVarName, $filterType, $aryOptions = NULL) {
        $result = NULL;
        
        if (is_array($aryOptions)) {
            switch ($inputOption) {
                case self::SI_GET :
                    $result = $this->performInput(INPUT_GET, $inputVarName, $filterType, $aryOptions);
                    break;

                case self::SI_POST :
                    $result = $this->performInput(INPUT_POST, $inputVarName, $filterType, $aryOptions);
                    break;

                case self::SI_COOKIE :
                    $result = $this->performInput(INPUT_COOKIE, $inputVarName, $filterType, $aryOptions);
                    break;

                case self::SI_REQUEST :
                    $result = parent::perform($_REQUEST[$inputVarName], $filterType, $aryOptions);
                    break;

                case self::SI_SERVER :
                    $result = $this->performInput(INPUT_SERVER, $inputVarName, $filterType, $aryOptions);
                    break;

                case self::SI_ENV :
                    $result = $this->performInput(INPUT_ENV, $inputVarName, $filterType, $aryOptions);
                    break;

                default:
                    break;
            }
        }
        
        return $result;
    }
    
    function examinesInputArray($inputOption, $aryOptions) {
        if ($inputOption == self::SI_REQUEST)
            return parent::perform($_REQUEST, $aryOptions);
        else
            return $this->sanitizeInput($inputOption, NULL, NULL, $aryOptions);
    }

    function validateInput($inputOption, $inputVarName, $filterType, $aryOptions = NULL) {
        return $this->sanitizeInput($inputOption, $inputVarName, $filterType, $aryOptions);
    }
    
    protected function performInput($inputType, $inputVarName = NULL, $filterType = NULL, $aryOptions = NULL) {
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
        if (!is_null($aryOptions) && !$this->checkFlags($aryOptions))
            return GGC_AnomalyManagement::centralizedAnomalyManagement (NULL,
                    array('Message' =>
                        'Classe : [ ' . __CLASS__ . ' ]' . PHP_EOL .
                        'Metodo : [ ' . __METHOD__ .' ]' . PHP_EOL .
                        'Flags di sanitizzazione inopportuni.'));
        
        /*
         * Controllo tipo valore di input
         */
        if (!is_null($inputType) && !is_null($inputVarName)) {
            $varType = 'INPUT_VAR';
            
        } elseif (!is_null($inputType) && is_null($inputVarName)) {
            $varType = 'INPUT_ARRAY';
            
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
        if ($varType == 'INPUT_VAR') {
            $operationType = $this->getOperationType($filterType);
            
            if ($operationType == 'SANITIZE') {
                $mixedResult = $this->_sanitize($inputType, $inputVarName, $filterType, $aryOptions);
            } elseif ($operationType == 'VALIDATE') {
                $mixedResult = $this->_validate($inputType, $inputVarName, $filterType, $aryOptions);
            }
            
        } else {
            $mixedResult = $this->_examinesArray($inputType, $aryOptions);
        }
        
        return $mixedResult;
    }
        
    private function _sanitize($inputType, $inputVarName, $filterType, $aryOptions = NULL) {
        $result = NULL;
        
        /*
         * Si controlla se è stato specificato solo il tipo di filtro e quindi
         * se l'array di opzioni è nullo.
         */
        if (!is_null($aryOptions)) {
            if ($filterType == GGC_SanitizeProvider::S_FILTER_STRING ||
                    $filterType == GGC_SanitizeProvider::S_FILTER_REGEXP ||
                    ($filterType == GGC_SanitizeProvider::S_FILTER_CALLBACK &&
                        array_key_exists('add_params', $aryOptions) &&
                            is_array($aryOptions['add_params']))) {
                
                /*
                 * Si richiama il metodo della classe base
                 */
                $result = parent::perform(
                        $this->_getInputValue($inputType, $inputVarName),
                        $filterType, $aryOptions);

            } else {
                $flag = NULL;

                foreach ($aryOptions['flags'] as $flagValue) {
                    $flag |= $this->getPhpFilterMap('sanitize_flags', $flagValue);
                }

                $result = filter_input($inputType, $inputVarName,
                        $this->getPhpFilterMap('sanitize_filters', $filterType), $flag);
            }
            
        } else {
            if ($filterType == GGC_SanitizeProvider::S_FILTER_STRING) {
                $result = filter_input($inputType, $inputVarName, FILTER_SANITIZE_STRING);

            } else {   
                $result = filter_input($inputType, $inputVarName,
                        $this->getPhpFilterMap('sanitize_filters', $filterType));
            }
        }
        
        return $result;
    }
    
    private function _validate($inputType, $inputVarName, $filterType, $aryOptions = NULL) {
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

                $result = filter_input($inputType, $inputVarName,
                        $this->getPhpFilterMap('validate_filters', $filterType),
                        $aryTemp);
                
            } else {
                $result = filter_input($inputType, $inputVarName,
                        $this->getPhpFilterMap('validate_filters', $filterType),
                        $flag);
            }
            
        } else {
            $result = filter_input($inputType, $inputVarName,
                    $this->getPhpFilterMap('validate_filters', $filterType));
        }
        
        return $result;
    }    
    
    /*
     * TODO :
     * Implementare tutti gli opportuni controlli.
     */
    private function _examinesArray($inputType, $aryOptions) {
        $aryResult = array();
        
        $aryValues = $this->_getInput($inputType);
        
        if (GGC_Array::countDimension($aryOptions) > 2) {
            /*
             * Condizione (1).
             */
            foreach ($aryValues as $key => $value) {
                if (array_key_exists($key, $aryOptions)) {
                    $operationType = $this->getOperationType($aryOptions[key]['filter']);
                    
                    if ($operationType == 'SANITIZE') {
                        $aryResult[$key] = $this->_sanitize($inputType, $key,
                                $aryOptions[key]['filter'], 
                                array('flags' => $aryOptions[key]['flags'], 
                                    'options' => $aryOptions[key]['options']));
                    
                    } else {
                        $aryResult[$key] = $this->_validate($inputType, $key,
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
            foreach ($aryValues as $key => $value) {
                $operationType = $this->getOperationType($aryOptions['filter']);

                if ($operationType == 'SANITIZE') {
                    $aryResult[$key] = $this->_sanitize($inputType, $key, $aryOptions['filter'],
                            array('flags' => $aryOptions['flags'], 
                                    'options' => $aryOptions['options']));

                } else {
                    $aryResult[$key] = $this->_validate($inputType, $key, $aryOptions['filter'],
                            array('flags' => $aryOptions['flags'], 
                                    'options' => $aryOptions['options']));
                }
            }
                
        /*
         * Condizione (3).
         */    
        } else {
            foreach ($aryValues as $key => $value) {
                $operationType = $this->getOperationType($aryOptions[$key]);

                if ($operationType == 'SANITIZE') {
                    $aryResult[$key] = $this->_sanitize($inputType, $key, $aryOptions[$key]);

                } else {
                    $aryResult[$key] = $this->_validate($inputType, $key, $aryOptions[$key]);
                }
            }
        }
        
        return $aryResult;
    }
    
    private function _getInputValue($inputType, $inputVarName) {
        $result = NULL;
        
        if ($inputType == INPUT_GET)
            $result = $_GET[$inputVarName];
        elseif ($inputType == INPUT_POST)
            $result = $_POST[$inputVarName];
        elseif ($inputType == INPUT_COOKIE)
            $result = $_COOKIE[$inputVarName];
        elseif ($inputType == INPUT_SERVER)
            $result = $_SERVER[$inputVarName];
        elseif ($inputType == INPUT_ENV)
            $result = $_ENV[$inputVarName];
        
        return $result;
    }
    
    private function _getInput($inputType) {
        $result = array();
        
        if ($inputType == INPUT_GET)
            $result = $_GET;
        elseif ($inputType == INPUT_POST)
            $result = $_POST;
        elseif ($inputType == INPUT_COOKIE)
            $result = $_COOKIE;
        elseif ($inputType == INPUT_SERVER)
            $result = $_SERVER;
        elseif ($inputType == INPUT_ENV)
            $result = $_ENV;
        
        return $result;
    }

}

?>
