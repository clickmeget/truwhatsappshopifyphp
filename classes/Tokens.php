<?php

class Tokens {
    private $tokenDir;

    public function __construct() {
        $this->tokenDir = __DIR__ . "/../.tokens/";
    }

    public function set_token($token, $shop) {
        // Ensure the directory exists
        if (!is_dir($this->tokenDir)) {
            mkdir($this->tokenDir);
        }
        // Write the token to the file
        file_put_contents($this->tokenDir.$shop, $token);
    }

    public function get_token($shop) {
        $filePath = $this->tokenDir . $shop;
        
        // Check if the token file exists
        if (file_exists($filePath)) {
            return file_get_contents($filePath);
        }
    
        // If the token doesn't exist or has expired, return false
        return false;
    }
}
