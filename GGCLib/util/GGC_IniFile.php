<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GGC_IniConfig
 *
 * @author Gianni Carafone
 */
class GGC_IniFile extends GGC_DataStruct {
    protected $fileName = NULL;
    protected $filePath = NULL;
    protected $forceCreation = false;

    function __construct($fileName, $forceCreation = false) {
        if (!empty($fileName)) {
            $this->fileName = $fileName;
            
            $this->filePath = dirname($fileName);
        }
        
        $this->forceCreation = $forceCreation;
        
        if (file_exists($this->fileName)) {
            $this->load();
            
        } else {
            if ($forceCreation) {
                if (!is_dir($this->filePath)) {
                    mkdir($this->filePath);
                }
                
                file_put_contents($fileName, '');
            }
        }

        if (!empty($this->_data)) {
            return true;
        } else {
            return false;
        } 
    }
    
    function load($force = false) {
        if (empty($this->_data) || $force)
            $this->_data = parse_ini_file($this->fileName, true);
        
        if (!empty($this->_data)) {
            return true;
        } else {
            return false;
        }
    }
    
    function save($fileName = NULL)
    {
        if(empty($fileName))
            $fileName = $this->fileName;

        if(!empty($fileName)) {
            if( is_writeable( $fileName ) ) {
                $SFfdescriptor = fopen( $fileName, "w" );

                foreach($this->_data as $section => $array){
                    fwrite( $SFfdescriptor, "[" . $section . "]\n" );

                    foreach( $array as $key => $value ) {
                        fwrite( $SFfdescriptor, "$key = '$value'\n" );
                    }
                    fwrite( $SFfdescriptor, "\n" );
                }

                fclose( $SFfdescriptor );
                return true;

            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

?>
