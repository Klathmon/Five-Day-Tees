<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/14/13
 */


class DataCache
{
    const DIRECTORY = 'Cache/DataCache/';
    const EXTENSION = '.cache';

    public static function store($key, $data, $ttl = '1 hour')
    {
        $expiresTime = new DateTime('now');
        $expiresTime->modify('+' . $ttl);

        $data     = serialize([$data, $expiresTime]);
        $fileName = self::getFileName($key);

        /* Open the file */
        $fileHandler = fopen($fileName, 'w');
        /* Lock the file */
        flock($fileHandler, LOCK_EX);
        /* Write to the file */
        fwrite($fileHandler, $data);
        /* Unlock the file */
        flock($fileHandler, LOCK_UN);
        /* Close the file */
        fclose($fileHandler);
    }

    public static function fetch($key)
    {
        //TODO: Add file read locking here!
        $fileName = self::getFileName($key);

        if (!is_readable($fileName)) {
            throw new Exception('File does not exist or is not readable');
        }

        list($data, $expiresDateTime) = unserialize(file_get_contents($fileName));

        if ($expiresDateTime < (new DateTime('now'))) {
            unlink($fileName);
            throw new Exception('File is expired');
        }

        return $data;
    }

    public static function delete($key)
    {
        $fileName = self::getFileName($key);

        if (is_writable($fileName)) {
            unlink($fileName);
        }
    }
    
    public static function clearCache()
    {
        $directory = new DirectoryIterator(self::DIRECTORY);
        foreach($directory as $file){
            /** @var $file DirectoryIterator */
            if($file->isFile()){
                unlink($file->getPathName());
            }
        }
    }

    private static function getFileName($key)
    {
        return self::DIRECTORY . crc32($key) . self::EXTENSION;
    }
}