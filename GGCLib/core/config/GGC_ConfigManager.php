<?php
/**
 * Description of GGC_ConfigManager
 * 
 * Questa classe serve per avere un'interfaccia standard ai diversi sistemi
 * di gestione/memorizzazione della configurazione. Per la gestione verranno utilizzati
 * sempre gli stessi metodi, con gli stessi nomi, ma ovviamente, con funzionalità
 * implementative diverse. Questo lo si può ottenere o facendo specificare il tipo
 * di driver con uin parametro, ad esempio "driver" e poi nei relativi metodi
 * fare i controlli a seconda del driver utilizzato, oppure sfruttando il concetto
 * o pattern "Provider Model" e "Factory/Dependency Injection".
 * Questa classe rappreseta la classe manager per istanziare i provider concreti
 * e richiamarne i metodi e settare le proprietà.
 * Questa dovrebbe essere una classe statica.
 * Siccome la questione di serializzare inmodo strutturato potrebbe essere fruttato
 * da varie entità n el programma, si è creato un codice standard centralizzato
 * nella cartella 'structured_data_serializatione'.
 * Ora, le classi dichiarate in config e in qualunque altra parte del programma
 * che ha bisogno di un sisistema struttrato di salvataggio, sono solo classi vuote
 * che derivano da quelle centralizzate standard. Volendo, dalle classi 'manager'
 * si potrebbero richiamare direttamente le classi di serializzazione centralizzate.
 * Si può optaree sia per richiamare quelle classi direttamente che crearene di nuove
 * anche se vuote che derivano da esse e utilizzare queste ultime. Magari potrebbero
 * avere ancora più senso se dobbiamo personalizzare una qualche operazione
 * che le versioni standard non fanno come vorremmo. Comunque è  tutto molto
 * elastico e configurabile nel modo di utilizzare le classi e il framework.
 * 
 * NOTA :
 * Questa classe può essere implementata in due modi :
 * 1) Per istanze restituite, ovvero la classe crea l'istanza opportuna e la
 *    restituisce al chiamante, il quale si dovrà preoccupare di salvarla e 
 *    riutilizzarla ogni qualvolta voglia usare il sistema di configurazione,
 *    passandola a questa classe o utilizzando l'istanza direttamente.
 * 2) La classe stessa si occupa di salvare localmente le istanze create, tramite
 *    un array [nome => istanza], e il chiamante dovrà solo passare il nome
 *    dell'istanza che vuole utilizzare, o non passare niente se vuole utilizzare
 *    quella predefinita, ovvero quella creata senza specificare un nome.
 *
 * @author Gianni Carafone
 */

/*
 * Versione dove si lavora per istanze restituite.
 */
//class GGC_ConfigManager /*extends GGC_Object*/ {
//    
//    static function openConf($fileName, $driver) {
//        $result = NULL;
//        
//        if ($driver == 'ini') {
//            $result =  GGC_IniFileConfigProvider::openConf ($fileName);
//        } elseif ($driver == 'xml') {
//            $result =  GGC_XmlFileConfigProvider::openConf ($fileName);
//        }
//        
//        return $result;
//    }
//        
//    static function loadConf(GGC_ConfigProvider $config) {
//        $config->loadConf();
//    }
//    
//    static function saveConf(GGC_ConfigProvider $config) {
//        $config->saveConf();
//    }
//    
//    static function getValue(GGC_ConfigProvider $config, $groupName, $valueName) {
//        return $config->getValue($groupName, $valueName);
//    }
//    
//    static function setValue(GGC_ConfigProvider $config, $groupName, $valueName, $value) {
//        $config->setValue($groupName, $valueName, $value);
//    }
//    
//    static function getGroup(GGC_ConfigProvider $config, $name) {
//        return $config->getGroup($name);
//    }
//    
//    static function setGroup(GGC_ConfigProvider $config, $name, $arrayValue) {
//        $config->setGroup($name, $arrayValue);
//    }
//}

class GGC_ConfigManager {
    private static $_aryInstances = array();
    
//    static function open($fileName, $driver, $configName = 'default') {
//        if (array_key_exists($configName, self::$_aryInstances)) {
//            // Per ora faccio solo return, ma in base alla gestione errori
//            // ci si deve comportare.
//            return;
//        }
//                
//        if ($driver == GGC_ConfigProvider::SD_INI) {
//            self::$_aryInstances[$configName] = new GGC_IniConfigProvider($fileName);
//        } elseif ($driver == GGC_ConfigProvider::SD_XML) {
//            self::$_aryInstances[$configName] = new GGC_XmlConfigProvider($fileName);
//        } elseif ($driver == GGC_ConfigProvider::SD_MEM_GGC) {
//            self::$_aryInstances[$configName] = new GGC_MemConfigProvider();
//        }    
//    }
    static function open($fileName, $driver, $isInheritance = true,
            $aryInheritanceFile = NULL, $configName = 'default') {
        if (array_key_exists($configName, self::$_aryInstances)) {
            // Per ora faccio solo return, ma in base alla gestione errori
            // ci si deve comportare.
            return;
        }
        
        /*
         * Si controlla se calcolre il percorso del file di configurazione base.
         */
        if ($isInheritance && empty($aryInheritanceFile)) {
            $baseFileName = GGC_ConfigManager::getValue('General', 'ServerDocumentRootPath', 'Init') .
                    GGC_ConfigManager::getValue('General', 'FrameworkRootPath', 'Init') .
                    GGC_ConfigManager::getValue('General', 'FrameworkLibPath', 'Init') .
                    GGC_ConfigManager::getValue('General', 'FrameworkLibCorePath', 'Init') .
                    'config/' . GGC_ApplicationManager::getApplicationType() . '/';
        }
                
        if ($driver == GGC_ConfigProvider::SD_INI) {
            if ($isInheritance) {
                if (empty($aryInheritanceFile)) {
                    $baseFileName .= 'ini/Config.ini';
                    
                    self::$_aryInstances[$configName][] = new GGC_IniConfigProvider($baseFileName);
                } else {
                    foreach ($aryInheritanceFile as $cfgFile) {
                        self::$_aryInstances[$configName][] = new GGC_IniConfigProvider($cfgFile);
                    }
                }
            } 
            
            self::$_aryInstances[$configName][] = new GGC_IniConfigProvider($fileName);
            
            /*
             * Reverse elementi array per avere nella prima posizione l'elemento più
             * specifico della gerarchia.
             */
            self::$_aryInstances[$configName] = array_reverse(self::$_aryInstances[$configName]);
            
        } elseif ($driver == GGC_ConfigProvider::SD_XML) {
            self::$_aryInstances[$configName][] = new GGC_XmlConfigProvider($fileName);
            
        } elseif ($driver == GGC_ConfigProvider::SD_MEM_GGC) {
            self::$_aryInstances[$configName][] = new GGC_MemConfigProvider();
            
        }    
    }
    
    static function getInstance($configName = 'default') {
        $result = NULL;
        
        if (array_key_exists($configName, self::$_aryInstances)) {
            $result = self::$_aryInstances[$configName][0];
        }
        
        return $result;
    }
    
    /**
     * Inizio implementazione interfaccia "GGC_COnfigProvider"
     */
        
//    static function load($force = false, $configName = 'default') {
//        self::$_aryInstances[$configName]->load($force);
//    }
    static function load($force = false, $configName = 'default') {
        foreach (self::$_aryInstances[$configName] as $cfgFile) {
            $cfgFile->load($force);
        }
    }
    
//    static function save($configName = 'default') {
//        self::$_aryInstances[$configName]->save();
//    }
//    static function save($configName = 'default') {
//        foreach (self::$_aryInstances[$configName] as $cfgFile) {
//            $cfgFile->save();
//        }
//    }
    /*
     * Per le impostazioni, settaggi e salvataggi, ci si riferisce sempre aa file
     * di configurazione dell'applicazione e non ai file da cui si eredita.
     * 
     * NOTA* :
     * In futuro si può aggiungere un parametro inerente il nome file specifico
     * su cui operare nella gerarchia dei file di configurazione.
     */
    static function save($configName = 'default') {
        self::$_aryInstances[$configName][0]->save();
    }
    
//    static function keyExists($group, $key, $configName = 'default') {
//        return self::$_aryInstances[$configName]->keyExists($group, $key);
//    }
    static function keyExists($group, $key, $configName = 'default') {
        $result = false;
        
        foreach (self::$_aryInstances[$configName] as $cfgFile) {
            $result = $cfgFile->keyExists($group, $key);
            
            if ($result) break;
        }
        
        return $result;
    }
    
//    static function valueExists($group, $value, $configName = 'default') {
//        return self::$_aryInstances[$configName]->valueExists($group, $value);
//    }
    static function valueExists($group, $value, $configName = 'default') {
        $result = false;
        
        foreach (self::$_aryInstances[$configName] as $cfgFile) {
            return $cfgFile->valueExists($group, $value);
            
            if ($result) break;
        }
        
        return $result;
    }
    
//    static function getValue($group, $key, $configName = 'default') {
//        return self::$_aryInstances[$configName]->getValue($group, $key);
//    }
    static function getValue($group, $key, $configName = 'default') {
        $result = NULL;
        
        foreach (self::$_aryInstances[$configName] as $cfgFile) {
            $result = $cfgFile->getValue($group, $key);
            
            if (!is_null($result)) break;
        }
        
        return $result;
    }
    
//    static function setValue($group, $key, $value, $configName = 'default') {
//        self::$_aryInstances[$configName]->setValue($group, $key, $value);
//    }
//    static function setValue($group, $key, $value, $configName = 'default') {
//        foreach (self::$_aryInstances[$configName] as $cfgFile) {
//            if ($cfgFile->keyExists($group, $key)) {
//                $cfgFile->setValue($group, $key, $value);
//            }
//        }
//    }
    /*
     * Per le impostazioni, settaggi e salvaaggi, ci si riferisce sempre aa file
     * di configurazione dell'applicazione e non ai file da cui si eredita.
     * 
     * NOTA* :
     * In futuro si può aggiungere un parametro inerente il nome file specifico
     * su cui operare nella gerarchia dei file di configurazione.
     */
    static function setValue($group, $key, $value, $configName = 'default') {
        self::$_aryInstances[$configName][0]->setValue($group, $key, $value);
    }    
    
    
//    static function removeValue($group, $key, $configName = 'default') {
//        return self::$_aryInstances[$configName]->removeValue($group, $key);
//    }
//    static function removeValue($group, $key, $configName = 'default') {
//        foreach (self::$_aryInstances[$configName] as $cfgFile) {
//            if ($cfgFile->keyExists($group, $key)) {
//                return $cfgFile->removeValue($group, $key);
//            }
//        }
//    }
    /*
     * Per le impostazioni, settaggi e salvataggi, ci si riferisce sempre aa file
     * di configurazione dell'applicazione e non ai file da cui si eredita.
     * 
     * NOTA* :
     * In futuro si può aggiungere un parametro inerente il nome file specifico
     * su cui operare nella gerarchia dei file di configurazione.
     */
    static function removeValue($group, $key, $configName = 'default') {
        return self::$_aryInstances[$configName][0]->removeValue($group, $key);
    }

    
//    static function getGroup($key, $configName = 'default') {
//        return self::$_aryInstances[$configName]->getGroup($key);
//    }
    static function getGroup($key, $configName = 'default') {
        $result = NULL;
        
        foreach (self::$_aryInstances[$configName] as $cfgFile) {
            $result = $cfgFile->getGroup($key);
            
            if (!is_null($result)) break;
        }
        
        return $result;
    }
    
//    static function setGroup($key, $aryValue,
//            $setDeepMode = false, $setEmptyMode = false, $configName = 'default') {
//        self::$_aryInstances[$configName]->setGroup($key, $aryValue,
//                $setDeepMode, $setEmptyMode);
//    }
    static function setGroup($key, $aryValue,
            $setDeepMode = false, $setEmptyMode = false, $configName = 'default') {
        
        self::$_aryInstances[$configName][0]->
                setGroup($key, $aryValue, $setDeepMode, $setEmptyMode);
    }
    
//    static function removeGroup($key, $configName = 'default') {
//        return self::$_aryInstances[$configName]->removeGroup($key);
//    }
    static function removeGroup($key, $configName = 'default') {
        return self::$_aryInstances[$configName][0]->removeGroup($key);
    }
    
//    static function get($group = NULL, $key = NULL, $configName = 'default') {
//        return self::$_aryInstances[$configName]->get($group, $key);
//    }
    static function get($group = NULL, $key = NULL, $configName = 'default') {
        return self::$_aryInstances[$configName][0]->get($group, $key);
    }
    
//    static function set($group, $key = NULL, $value = NULL, $setDeepMode = false,
//            $setEmptyMode = false, $configName = 'default') {
//        self::$_aryInstances[$configName]->set($group, $key, $value, $setDeepMode,
//                $setEmptyMode);
//    }
    static function set($group, $key = NULL, $value = NULL, $setDeepMode = false,
            $setEmptyMode = false, $configName = 'default') {
        self::$_aryInstances[$configName][0]->set($group, $key, $value, $setDeepMode,
                $setEmptyMode);
    }
    
//    static function clear($configName = 'default') {
//        return self::$_aryInstances[$configName]->clear();
//    }
    static function clear($configName = 'default') {
        return self::$_aryInstances[$configName][0]->clear();
    }
}

?>
