<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/13/13
 */

/**
 * Class Cache
 * 
 * 
 * This is going to be a shared data cache, currently stored on the filesystem but i'm looking into in-memory storage.
 * 
 * Easy Usage:
 * 
 * try{
 *     $object = $cache->get('keyName');
 * }catch(Exception $e){
 *     $object = $cache->set('keyName', new Object($parameters));
 * }
 * 
 */
class Cache
{
    /** @var string */
    private $directory;
    /** @var string */
    private static $extension = '.cache';

    /**
     * Creates the cache object. The constructor does the cache age checking at startup.
     * 
     * @param string $directory
     * @param string $maxCacheAge
     */
    public function __construct($directory, $maxCacheAge = '1 hour')
    {
        if(substr($directory, -1) == '/'){
            $this->directory = substr($directory, 0, -1);
        }else{
            $this->directory = $directory;
        }

        $this->cleanCacheDir($maxCacheAge);
    }

    /**
     * Returns the cached data if the key is found, throws an Exception if not.
     * 
     * @param string $key
     *
     * @return mixed
     * @throws Exception
     */
    public function get($key)
    {
        if($this->is_cached($key)){
            return $this->unserialize(file_get_contents($this->getFilename($key)));
        }else{
            throw new Exception('Key not found');
        }
    }

    /**
     * Set the given key to the value of $data. This returns data for an easy inline syntax.
     * 
     * @param string $key
     * @param mixed $data
     *
     * @return mixed
     */
    public function set($key, $data)
    {
        file_put_contents($this->getFilename($key), $this->serialize($data));
        
        return $data;
    }

    /**
     * Returns true if the key is found
     * 
     * @param string $key
     *
     * @return bool
     */
    public function is_cached($key)
    {
        return is_readable($this->getFilename($key));
    }

    private function getFilename($key)
    {
        return $this->directory . '/' . crc32($key) . self::$extension;
    }
    
    private function cleanCacheDir($maxAge)
    {
        $directory = new DirectoryIterator($this->directory);

        $expireTime= new DateTime('now');
        $expireTime->modify('-' . $maxAge);

        foreach($directory as $file){
            /** @var $file DirectoryIterator */
            if($file->isFile() && (DateTime::createFromFormat('U', $file->getMTime()) <= $expireTime)){
                unlink($file->getPathName());
            }
        }
    }
    
    private function unserialize($data)
    {
        return unserialize($data);
    }
    
    private function serialize($data)
    {
        return serialize($data);
    }
}