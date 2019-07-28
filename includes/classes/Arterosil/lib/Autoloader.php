<?php
/**
 * PHP Autoloader Class.
 */
class Autoloader
{
    //Default extension for files
    private static $extension = '.php';

    //for setting path
    private static $path = '/';

    /**
     * Load the files.
     *
     * @param $path => folder
     *
     * @return bool | mix-data
     */
    public static function Load($path)
    {

        if (isset($path) && !empty($path)) {
            self::$path = $path.'/';

            spl_autoload_register(

                function ($file) {
                    $file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
                    if (file_exists(self::$path.$file.Autoloader::$extension))
                        require_once Autoloader::$path.$file.Autoloader::$extension;
                }
            );
        } else {
            return false;
        }
    }
}
