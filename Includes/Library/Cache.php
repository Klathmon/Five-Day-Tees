<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Greg
 * Date: 10/1/13
 * Time: 11:26 AM
 * To change this template use File | Settings | File Templates.
 */


/**
 * Class HashTable
 *
 * HashTable File Layout:
 *
 * BlockSize TableSize [key expireTime updating data]...
 *
 * No spaces in between anything
 */
class Cache
{
    /** int Number of bytes to leave for each number stored in the file */
    const INT_LENGTH = 10;
    /** int Max key length */
    const MAX_KEY_LENGTH = 32;
    /** int Default number of bytes per block */
    const DEFAULT_BLOCK_SIZE = 1024;
    /** int Default number of blocks in the table */
    const DEFAULT_TABLE_SIZE = 43;

    /** @var resource The file resource */
    private $file;
    /** @var int the number of bytes per block (including header information) */
    private $blockSize;
    /** @var int The max number of blocks in the table */
    private $tableSize;

    public function __construct($fileName)
    {
        $fileName .= '.hashTable';

        $fileExists = file_exists($fileName);

        $this->file = fopen($fileName, 'c+');

        if(!$fileExists){
            $this->reset();
        }

        rewind($this->file);

        $this->blockSize = fread($this->file, self::INT_LENGTH);

        fseek($this->file, self::INT_LENGTH, SEEK_SET);

        $this->tableSize = fread($this->file, self::INT_LENGTH);
    }

    public function fetch($key)
    {
        $key = substr($key, 0, self::MAX_KEY_LENGTH);

        fseek($this->file, $this->getFilePosition($key), SEEK_SET);

        flock($this->file, LOCK_SH);
        $block = fread($this->file, $this->blockSize);
        flock($this->file, LOCK_UN);

        if(is_null($block)){
            throw new Exception('No Data at that key', 1);
        }

        $blockKey   = substr($block, 0, self::MAX_KEY_LENGTH);
        $expireTime = substr($block, self::MAX_KEY_LENGTH, self::INT_LENGTH);
        $updating   = (substr($block, self::MAX_KEY_LENGTH + self::INT_LENGTH, 1) == 1 ? TRUE : FALSE);
        $data       = substr($block, self::MAX_KEY_LENGTH + self::INT_LENGTH + 1);

        unset($block);

        if($key != $blockKey){
            throw new Exception('Key does not match what was returned', 1);
        } elseif($expireTime > time() && $updating === FALSE){
            throw new Exception('Cache is stale', 2);
        } else{
            return unserialize($data);
        }
    }

    public function store($key, callable $function, $ttl = '1 hour')
    {
        $key        = substr($key, 0, self::MAX_KEY_LENGTH);
        $blockStart = $this->getFilePosition($key);

        /* Read the Key from the file, and set it's updating flag */
        fseek($this->file, $blockStart, SEEK_SET);
        flock($this->file, LOCK_EX);
        $currentKey = fread($this->file, self::MAX_KEY_LENGTH);
        fwrite($this->file, 1);
        flock($this->file, LOCK_UN);


        /* Execute the function */
        $functionResult = $function();


        /* Build the block */
        $block = str_pad($key, self::MAX_KEY_LENGTH);
        $block .= str_pad((new DateTime('now'))->modify('+' . $ttl)->format('U'), self::INT_LENGTH);
        $block .= 1;
        $block .= serialize($functionResult);


        if(trim($currentKey) != $key){
            //Collision, call the resize function here (2n + 1) the table size
        }elseif(strlen($block) > $this->blockSize){
            //Block is too big, call the resize function to make blocks larger than this block
        }

        fseek($this->file, $blockStart, SEEK_SET);
        flock($this->file, LOCK_EX);
        fwrite($this->file, $block, $this->blockSize);
        flock($this->file, LOCK_UN);

        return $functionResult;
    }

    public function delete($key)
    {
        // TODO: Implement delete() method.
    }

    public function reset()
    {
        $this->blockSize = self::DEFAULT_BLOCK_SIZE;
        $this->tableSize = self::DEFAULT_TABLE_SIZE;

        $this->createFile();
    }

    private function createFile()
    {
        ftruncate($this->file, 0);

        $fileSize = self::INT_LENGTH * 2;

        $fileSize += $this->blockSize * $this->tableSize;

        ftruncate($this->file, $fileSize);

        fseek($this->file, 0, SEEK_SET);

        fwrite($this->file, $this->blockSize);

        fseek($this->file, self::INT_LENGTH, SEEK_SET);

        fwrite($this->file, $this->tableSize);
    }

    private function getFilePosition($key)
    {
        $blockNumber = crc32($key) % $this->tableSize;

        return $this->blockSize * $blockNumber;
    }
}