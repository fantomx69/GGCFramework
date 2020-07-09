<?php

class GGC_MemStructuredDataSerializationProvider
    extends GGC_StructuredDataSerializationProvider {
    
    public function __construct() {
        parent::__construct();
        
        $this->instance = new GGC_MemConfig();
    }
}
?>
