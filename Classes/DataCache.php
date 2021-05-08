<?php
/**
 * Created by PhpStorm.
 * User: n.balashov
 * Date: 19.01.2021
 * Time: 15:48
 */

class DataCache
{
    private $cache;
    private $cacheFileName;
    private $cacheFolder;
    private $cacheFullFileName;
    private $cacheFullFilePath;
    private $cacheEnabled;
    private $cacheTime;
    private $dataName;

    const DEFAULT_CACHE_PATH = '/cache_data/'; # путь к папке с файлами для хранения кешированных данных
    const DEFAULT_CACHE_TIME = 6;	# время кеширования данных, секунды

    #-------------------------------------
    public function __construct($dataName)
    {

        $this->setCacheOn();
//        $this->setCacheTime = 600;
        $this->cacheTime = 60000;

        $this->cacheFolder = $_SERVER['DOCUMENT_ROOT'] .'/sportforlive/cache_data/';
        //$this->cacheFolder = $_SERVER['DOCUMENT_ROOT'] .'/noz_self/self_engine/cache_data/';
        //c_deb($this->cacheFolder);
        $this->dataName = $dataName;
        $md5hash = md5($this->dataName);
        $this->cacheFileName = substr($md5hash, 2, 30);
        $this->cacheFullFilePath = $this->cacheFolder . substr($md5hash, 0, 1) . '/' . substr($md5hash, 1, 1) . '/';
        $this->cacheFullFileName = $this->cacheFullFilePath . $this->cacheFileName;
        //c_deb($this->cacheFullFileName);
        return true;
    }

    #---------------------------
    public function setCacheOn()
    {
        $this->cacheEnabled = true;
    }

    #---------------------------
    public function setCacheOff()
    {
        $this->cacheEnabled = false;
    }

    #--------------------------------
    public function getCacheEnabled()
    {
        return $this->cacheEnabled;
    }

    #-------------------------------------------
    public function setCacheTime( $seconds)
    {
        echo 'setCacheTime';
        $this->cacheTime = $seconds;
    }

    #------------------------------
    public function initCacheData()
    {
        if (!$this->cacheEnabled) { return false; }

        $cacheOld = time() - @filemtime($this->cacheFullFileName);
//        deb($cacheOld);
//        dev($this->cacheTime);
        if($cacheOld < $this->cacheTime) {
            $fp = @fopen($this->cacheFullFileName, "r");
            $this->cache = @fread($fp, filesize($this->cacheFullFileName));
            @fclose($fp);
            return true;
        }
        return false;
    }

    #-----------------------------
    public function getCacheData()
    {
        //echo 'getCacheData';
        if (!$this->cacheEnabled) { return false; }

        if (empty($this->cache)) {
            return false;
        } else {
            // deb('return');
            //c_deb('from cache');
            return unserialize($this->cache);
        }
    }

    #----------------------------------------
    public function updateCacheData($newData)
    {
        //echo "updateCacheData";
        if (!$this->cacheEnabled) { return false; }
        $this->cache = $newData;
        //c_deb('set cache');
        $output = serialize($this->cache);
        if(!@file_exists($this->cacheFullFilePath)) { @mkdir($this->cacheFullFilePath, 0777, true); }
        $fp = @fopen($this->cacheFullFileName, "w");
        @fwrite($fp, $output);
        @fclose($fp);

        return true;
    }
}