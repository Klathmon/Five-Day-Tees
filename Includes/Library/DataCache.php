<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/14/13
 */


class DataCache
{
    const FILE_EXTENSION = '.cache';
    
    private $directory;

    public function __construct($directoryName)
    {
        $this->directory = $directoryName;
    }

    public function fetch($key)
    {
        $file = $this->getFileFromKey($key, 'read');
        
        $file->flock(LOCK_SH);
        $line = $file->fgets();
        $file->flock(LOCK_UN);

        if ($line == '') {
            throw new Exception('No data exists for that key');
        }

        list($data, $expiresDateTime) = unserialize($line);

        if ($expiresDateTime < (new DateTime('now'))) {
            
            $file->flock(LOCK_EX);
            $file->ftruncate(0);
            $file->flock(LOCK_UN);
            
            throw new Exception('Cache is expired');
        }
        
        unset($file);

        return $data;
    }

    public function store($key, $value, $ttl = '1 hour')
    {
        $expiresTime = new DateTime('now');
        $expiresTime->modify('+' . $ttl);

        $file = $this->getFileFromKey($key, 'write');

        $line = serialize([$value, $expiresTime]);

        $file->flock(LOCK_EX);
        $file->fwrite($line);
        $file->flock(LOCK_UN);
        
        unset($file);
    }

    public function delete($key)
    {
        $file = $this->getFileFromKey($key);

        if ($file->isWritable()) {
            $file->ftruncate(0);
        }
    }

    /**
     * This function gets the file corresponding to the given key.
     *
     * @param string $key
     * @param string $type
     *
     * @return SplFileObject
     */
    private function getFileFromKey($key, $type = 'read')
    {
        $fileName = $this->directory . md5($key) . self::FILE_EXTENSION;

        $code = ($type == 'read' ? 'r' : 'w+');
        
        return new SplFileObject($fileName, $code);
    }
}