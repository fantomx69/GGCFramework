<?php
/**
 * Description of GGC_File
 *
 * @author : Laravel Framework
 *           Gianni Carafone - Alcune modifiche.
 */
class GGC_File {
    /**
     * Controlla se il file o la dir esiste.
     * 
     * @param string $path
     * @return bool
     */
    public static function exists($path)
    {
        return file_exists($path);
    }

    /**
     * Ottiene il contenuto del file se esiste, oppure il valore di fallback
     * passato con $default.
     * 
     * @param string $path
     * @param type $default
     * @return type
     */
    public static function get($path, $fallbackResult = null)
    {
        return (file_exists($path)) ? file_get_contents($path) : value($fallbackResult);
    }

    /**
     * Scrive sul file.
     *
     * @param  string  $path
     * @param  string  $data
     * @return int
     */
    public static function put($path, $data)
    {
        return file_put_contents($path, $data, LOCK_EX);
    }

    /**
     * Accoda il contenuto passato al file.
     *
     * @param  string  $path
     * @param  string  $data
     * @return int
     */
    public static function append($path, $data)
    {
        return file_put_contents($path, $data, LOCK_EX | FILE_APPEND);
    }

    /**
     * Cancella un file.
     *
     * @param  string  $path
     * @return bool
     */
    public static function delete($path)
    {
        $result = false;
        
        if (static::exists($path)) $result = @unlink($path);
        
        return $result;
        
    }

    /**
     * Rinomina/muove un file/dir in nuovo nome/posizione.
     *
     * @param  string  $path
     * @param  string  $target
     * @return bool
     */
    public static function move($path, $target)
    {
        $result = false;
        
        if (static::exists($path)) $result = @rename($path, $target);
        
        Return $result;
    }

    /**
     * Copia un file in una altra posizione o sessa posizione con nome diverso.
     *
     * @param  string  $path
     * @param  string  $target
     * @return bool
     */
    public static function copy($path, $target)
    {
        $result = false;
        
        if (static::exists($path)) $result = @copy($path, $target);
        
        return $result;
    }

    /**
     * Recupera l'estensione di un file.
     *
     * @param  string  $path
     * @return string
     */
    public static function extension($path, $fallbackResult = NULL)
    {
        $result = $fallbackResult;
                
        if (static::exists($path)) $result = pathinfo($path, PATHINFO_EXTENSION);
        
        return $result;
    }

    /**
     * Ottiene il tipo di un file.
     *
     * @param  string  $path
     * @return string
     */
    public static function type($path, $fallbackResult = NULL)
    {
        $result = $fallbackResult;
        
        if (static::exists($path)) $result = filetype($path);
        
        return $result;
    }

    /**
     * Ottiene la dimensione di un file.
     *
     * @param  string  $path
     * @return int
     */
    public static function size($path, $fallbackResult = false)
    {
        $result = $fallbackResult;
        
        if (static::exists($path)) $result = filesize($path);
        
        return $result;
    }

    /**
     * Ottiene il time di ultima modifica di un file.
     *
     * @param  string  $path
     * @return int
     */
    public static function modified($path, $fallbackResult = false)
    {
        $result = $fallbackResult;
        
        if (static::exists($path)) $result = filemtime($path);
        
        return $result;
    }

    /**
     * Ottiene il MIME type dall'estensione.
     *
     * <code>
     *      // Determine the MIME type for the .tar extension
     *      $mime = File::mime('tar');
     *
     *      // Return a default value if the MIME can't be determined
     *      $mime = File::mime('ext', 'application/octet-stream');
     * </code>
     *
     * @param  string  $extension
     * @param  string  $fallbackResult
     * @return string
     */
    public static function mime($extension, $fallbackResult = 'application/octet-stream')
    {
        $result = GGC_ConfigManager::getValue('MimeTypes', $extension);
        
        if (!$result) {
            $result = $fallbackResult;
        }
       
        return $result;
    }

    /**
     * Determina se il file è di un certo tipo.
     *
     * The Fileinfo PHP extension is used to determine the file's MIME type.
     *
     * <code>
     *      // Determine if a file is a JPG image
     *      $jpg = File::is('jpg', 'path/to/file.jpg');
     *
     *      // Determine if a file is one of a given list of types
     *      $image = File::is(array('jpg', 'png', 'gif'), 'path/to/file');
     * </code>
     *
     * @param  array|string  $extensions
     * @param  string        $path
     * @return bool
     */
    public static function is($extensions, $path)
    {
        $mimes = Config::get('mimes');

        $mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);

        // The MIME configuration file contains an array of file extensions and
        // their associated MIME types. We will spin through each extension the
        // developer wants to check and look for the MIME type.
        foreach ((array) $extensions as $extension)
        {
            if (isset($mimes[$extension]) and in_array($mime, (array) $mimes[$extension]))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Crea una directory.
     *
     * @param  string  $path
     * @param  int     $chmod
     * @return void
     */
    public static function mkdir($path, $chmod = 0777)
    {
        return ( ! is_dir($path)) ? mkdir($path, $chmod, true) : true;
    }

    /**
     * Sposta una dir.
     *
     * @param  string  $source
     * @param  string  $destination
     * @param  int     $options
     * @return void
     */
    public static function mvdir($source, $destination,
            $options = FilesystemIterator::SKIP_DOTS)
    {
        return static::cpdir($source, $destination, true, $options);
    }

    /**
     * Copia il contenuto di una dir in un'altra dir in modo ricorsivo.
     *
     * @param  string  $source
     * @param  string  $destination
     * @param  bool    $delete
     * @param  int     $options
     * @return void
     */
    public static function cpdir($source, $destination, $delete = false,
            $options = FilesystemIterator::SKIP_DOTS)
    {
        if ( ! is_dir($source)) return false;

        if ( ! is_dir($destination))
        {
            mkdir($destination, 0777, true);
        }

        $items = new FilesystemIterator($source, $options);

        foreach ($items as $item)
        {
            $location = $destination.DS.$item->getBasename();

            // If the file system item is a directory, we will recurse the
            // function, passing in the item directory. To get the proper
            // destination path, we'll add the basename of the source to
            // to the destination directory.
            if ($item->isDir())
            {
                $path = $item->getRealPath();

                if (! static::cpdir($path, $location, $delete, $options)) return false;
                if ($delete) @rmdir($item->getRealPath());
            }
            // If the file system item is an actual file, we can copy the
            // file from the bundle asset directory to the public asset
            // directory. The "copy" method will overwrite any existing
            // files with the same name.
            else
            {
                if(! copy($item->getRealPath(), $location)) return false;

                if ($delete) @unlink($item->getRealPath());
            }
        }

        unset($items);
        if ($delete) @rmdir($source);

        return true;
    }

    /**
     * Cancella una dir in modo ricorsivo.
     *
     * @param  string  $directory
     * @param  bool    $preserve
     * @return void
     */
    public static function rmdir($directory, $preserve = false)
    {
        if ( ! is_dir($directory)) return;

        $items = new FilesystemIterator($directory);

        foreach ($items as $item)
        {
            // If the item is a directory, we can just recurse into the
            // function and delete that sub-directory, otherwise we'll
            // just deleete the file and keep going!
            if ($item->isDir())
            {
                static::rmdir($item->getRealPath());
            }
            else
            {
                @unlink($item->getRealPath());
            }
        }

        unset($items);
        if ( ! $preserve) @rmdir($directory);
    }

    /**
     * Cancella il contenuto di una dir.
     *
     * @param  string  $directory
     * @return void
     */
    public static function cleandir($directory)
    {
        return static::rmdir($directory, true);
    }

    /**
     * Recupera l'ultimo file modificato in una dir.
     *
     * @param  string       $directory
     * @param  int          $options
     * @return SplFileInfo
     */
    public static function latest($directory,
            $options = FilesystemIterator::SKIP_DOTS)
    {
        $time = 0;

        $items = new FilesystemIterator($directory, $options);

        // To get the latest created file, we'll simply spin through the
        // directory, setting the latest file if we encounter a file
        // with a UNIX timestamp greater than the latest one.
        foreach ($items as $item)
        {
            if ($item->getMTime() > $time) $latest = $item;
        }

        return $latest;
    }
}

?>
