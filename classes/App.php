<?php


class App
{
    private $appDir = __DIR__ . '/../app/';
    public function load($screen, $variables = [])
    {

        $file = $this->appDir.$screen.'.php';
        
        if (file_exists($file)) {
            extract($variables);
            include $this->appDir."header.php";
            include $file;
            include $this->appDir."footer.php";
        } else {
            http_response_code(404);
            echo "Not found";
            exit;
        }
    }
}

