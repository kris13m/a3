<?php

 namespace App\Core;

class Logger {
    private $file;

    public function __construct($filePath) {
        $this->file = $filePath;
    }

    public function log($message) {
        $time = date('Y-m-d H:i:s');
        file_put_contents($this->file, "[$time] $message\n", FILE_APPEND);
    }
}