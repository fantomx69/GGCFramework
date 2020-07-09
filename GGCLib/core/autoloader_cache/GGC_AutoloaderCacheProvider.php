<?php
/**
 * Description of GGC_IniFileProvider
 *  
 * @author Gianni Carafone
 */
abstract class GGC_AutoloaderCacheProvider
    extends GGC_StructuredDataSerializationProvider {
    
    /*
     * Costanti per il timeout. Ma il timeout può essere specificato
     * anche senza costanti, ma attraverso un valore numerico e l'apposita
     * funzione.
     */
    const ACT_NEVER = 0;
    const ACT_APPLICATION_LIFE = -1;
    
}

?>
