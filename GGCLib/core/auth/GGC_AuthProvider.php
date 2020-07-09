<?php
/**
 * Description of GGC_AuthProvider
 *
 * @author Gianni
 */
abstract class GGC_AuthProvider  extends GGC_Provider {
    /**
     * Serialization Provider.
     */
    const SP_FILE_INI = 1;
    const SP_FILE_XML = 2;
//    const SP_DB_ = 11;
    
    /*
     * Riferiemento oggetto config.
     */
    protected $config = NULL;
}

?>
