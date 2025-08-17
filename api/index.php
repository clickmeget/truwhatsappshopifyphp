<?php

$directory = __DIR__ . '/../classes/';

// Loop through all PHP files in the directory
foreach (glob($directory . "*.php") as $filename) {
    require_once $filename;
}
ini_set("memory_limit", "640M");

class Api 
{
    private $access_token;
    private $shop;
    private $request;
    private $data;
    private $waapi;
    private $activity;
    private $account;
    public function __construct()
    {
        header('Content-Type: application/json');
        $response = array();

        // Capture raw POST data
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);  // Decode JSON into an associative array

        if (!$data || !isset($data['api_key'])) {
            http_response_code(401);
            $response = array("status"=> "error", "message"=> "Unauthorized access");
            echo json_encode($response, JSON_PRETTY_PRINT); exit;
        }

        if (!isset($data['route'])) {
            http_response_code(401);
            $response = array("status"=> "error", "message"=> "Route missing");
            echo json_encode($response, JSON_PRETTY_PRINT); exit;
        }

        if (!isset($data['data'])) {
            http_response_code(401);
            $response = array("status"=> "error", "message"=> "Invalid Data format");
            echo json_encode($response, JSON_PRETTY_PRINT); exit;
        }

        if (!method_exists($this, $data['route'])) {
            http_response_code(404);
            $response = array("status"=> "error", "message"=> "Route Not Found");
            echo json_encode($response, JSON_PRETTY_PRINT); exit;
        }

        $this->request = $data;
        $this->data = $data['data'];
        // Verify API key before proceeding
        $this->verifyHashedToken();

        $this->activity = new Activity();
        

        $this->account = $this->activity->get_account($this->shop);

        $this->waapi = $this->activity->get_waapi($this->account['waapi_id']);
        
        
        // Call the method specified in the 'route' field
        $route = $data['route'];
        $this->$route($data['data']);  // Pass the 'data' to the method
    }

    private function reboot()
    {
        $reboot = $this->waapi->reboot();
        if($reboot)
        {
            echo json_encode(array("status"=> "success" , "message"=> "Reboot requested was successful"));
        }else
        {
            echo json_encode(array("status"=> "error" , "message"=> "Reboot was unsuccessful"));
        }
        
        exit;
    }
    private function status()
    {
        $status = $this->waapi->get_instance_status();
        echo json_encode(array("status"=> "success" ,"message"=> "", "instance_status"=>$status));
        exit;
    }
    private function qr()
    {
        $qr = $this->waapi->get_qr();
        if($qr)
        {
            echo json_encode(array("status"=> "success" ,"message"=> "", "qr"=>$qr));
            
        }else
        {
            echo json_encode(array("status"=> "error" , "message"=> "No qr need to be updated"));
        }
        exit;

    }
    private function update_message()
    {
        $new_order_message = $this->data['new_message'];
        $abd_order_message = $this->data['abd_message'];
        $full_order_message = $this->data['full_message'];
        $update = $this->activity->update_message($this->shop, $new_order_message, $abd_order_message, $full_order_message);
        if($update)
        {
            echo json_encode(array('status'=> 'success' ,'message'=> 'Message updated'));
        }else
        {
            echo json_encode(array('status'=> 'error' ,'message'=> 'unable to update message'));
        }
        exit;
    }

    private function verifyHashedToken()
    {
        $token_found = false;
        $hashed_token = $this->request['api_key'];
        $token_dir = __DIR__ . "/../.tokens/";
        $files = scandir($token_dir);
        $files = array_diff($files, array('.', '..'));

        foreach ($files as $file) {
            $absolutePath = $token_dir . '/' . $file;
            $content = file_get_contents($absolutePath);
            if ($hashed_token === md5($content)) {
                $this->access_token = $content;
                $this->shop = $file;
                $token_found = true;
                break;
            }
        }
        if (!$token_found) {
            http_response_code(401);
            $response = array("status"=> "error", "message"=> "Unauthorized access. Invalid API Key");
            echo json_encode($response, JSON_PRETTY_PRINT); exit;
        }
    }
}

new Api();
