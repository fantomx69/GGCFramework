<?php
/**
 * Description of GGC_IniFileProvider
 *
 * @author Gianni Carafone
 */
class GGC_IniStructuredDataSerializationProvider
    extends GGC_StructuredDataSerializationProvider {
    
    public function __construct($fileName, $forceCreation = false) {
        parent::__construct();
        
        $this->instance = new GGC_IniConfig($fileName, $forceCreation);
    }
    
}

?>
