<?php

namespace classes;

use interfaces\Data;

/**
 * FileData
 * Load words from file
 */
class FileData implements Data
{
    private $path;
    private $data;

    /**
     * Init base properties for FileData
     * @param string $path
     * @throws \Exception
     */
    public function __construct($path)
    {
        $this->path = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $path;
        if (!file_exists($this->path)) {
            throw new \Exception('File not exist');
        }
    }

    /**
     * Return all data (words) from file
     * @return array
     */
    public function getData()
    {
        if ($this->data === null) {
            $read_result = file_get_contents($this->path);
            $this->data = explode("\r\n", $read_result);
        }
        return $this->data;
    }
}