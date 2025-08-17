<?php
class Route {
    private $baseURL;
    private $routes = [];
    private $redirectURL = null;  // To store redirect URL


    public function __construct($baseURL) {
        $this->baseURL = rtrim($baseURL, '/');
    }

    public function add($path, $function) {
        if (is_callable($function)) {
            $this->routes[$this->trimPath($path)] = $function;
        } else {
            throw new InvalidArgumentException("The provided callback for the path '{$path}' is not callable.");
        }
    }
    public function get() {
        return $this->getCurrentPath();
    }

    public function run() {
        // If a redirect URL is set, redirect to it
        if ($this->redirectURL) {
            header("Location: " . $this->redirectURL);
            exit; // Ensure the script stops after the redirect
        }

        $currentPath = $this->getCurrentPath();
        
        if (array_key_exists($currentPath, $this->routes)) {
            call_user_func($this->routes[$currentPath]);
        } else {
            $this->handleNotFound();
        }
    }
    public function redirect($redirectURL) {
        $this->redirectURL = $redirectURL;  // Set the redirect URL
    }

    private function getCurrentPath() {
        $currentURL = $_SERVER['REQUEST_URI'];
        
        // Remove query string if it exists
        $parsedUrl = parse_url($currentURL);
        $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '/';
        
        // Remove base URL and trim the path
        $path = str_replace($this->baseURL, '', $path);
        return $this->trimPath($path);
    }

    private function trimPath($path) {
        return trim($path, '/');
    }

    private function handleNotFound() {
        http_response_code(404);
        echo "404 Not Found";
    }
}