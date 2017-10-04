<?php

use classes\FileData;
use classes\Vertex;

spl_autoload_register(function($class) {
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = $_SERVER['DOCUMENT_ROOT'] . (empty($file) ? '' : DIRECTORY_SEPARATOR) . $file . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// set data-words resource
$data = new FileData('data/words.txt');
// Initialize the main worker
$vertex = new Vertex('муха', 'слон', $data);
// And action ...
$result = $vertex->run();
print_r($result);