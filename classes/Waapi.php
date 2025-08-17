<?php

use GuzzleHttp\Client;
class Waapi 
{
    private $instance_id;
    private $token = "jnjKVVmyc5fzpMKfmbw7dpIWkndBzTr3as9Rx2GY5fc502b2";
    private $client;
    public function __construct()
    {
       
    }
    public function set_instance_id($instance_id)
    {
        $this->instance_id = $instance_id;
    }

    public function create_instance()
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://waapi.app/api/v1/instances',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'accept: application/json',
                'authorization: Bearer ' . $this->token,
            ],
            CURLOPT_POST => true,
        ]);
        
        $response = curl_exec($curl);
        curl_close($curl);

        $body = json_decode($response, true);
        
        return $body['instance']['id'];
    }
    

    public function get_instance_status()
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://waapi.app/api/v1/instances/'.$this->instance_id.'/client/status',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'accept: application/json',
                'authorization: Bearer ' . $this->token,
            ],
        ]);
        
        $response = curl_exec($curl);
        curl_close($curl);

        $body = json_decode($response, true);
        $status = isset($body['clientStatus']['instanceStatus']) ? $body['clientStatus']['instanceStatus'] : "not ready";
        
        return $status;
    }

    public function get_qr()
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://waapi.app/api/v1/instances/'.$this->instance_id.'/client/qr',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'accept: application/json',
                'authorization: Bearer ' . $this->token,
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $body = json_decode($response, true);

        return isset($body['qrCode']['data']['qr_code']) ? $body['qrCode']['data']['qr_code'] : false;
    }

    public function reboot()
    {
        $current_instance = @$this->get_instance_status();
        if($current_instance && $current_instance !== "booting")
        {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://waapi.app/api/v1/instances/'.$this->instance_id.'/client/action/reboot',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'accept: application/json',
                    'authorization: Bearer ' . $this->token,
                ],
                CURLOPT_POST => true,
            ]);
    
            $response = curl_exec($curl);
            curl_close($curl);
    
            $body = json_decode($response, true);
    
            return $body['data']['status'] === 'success';
        }else
        {
            return true;
        }
        
    }

    public function send_message(string $message, $chatId)
    {
        if (empty(trim($message))) {
            return true;
        }

        $body_message = json_encode([
            'chatId' => $chatId,
            'message' => $message,
        ]);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://waapi.app/api/v1/instances/'.$this->instance_id.'/client/action/send-message',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'accept: application/json',
                'authorization: Bearer ' . $this->token,
                'content-type: application/json',
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body_message,
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $body = json_decode($response, true);
        
        return isset($body['data']['status']) && $body['data']['status'] == 'success' ? true : false;
    }

    public function logout() {
        $instanceId = $this->instance_id;
        $url = "https://waapi.app/api/v1/instances/$instanceId/client/action/logout";
    
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true, // POST request for logout
            CURLOPT_HTTPHEADER => [
                'accept: application/json',
                'authorization: Bearer ' . $this->token,
            ],
        ]);
    
        $response = curl_exec($curl);
        curl_close($curl);
    
        $body = json_decode($response, true);
    
        // Return the response or handle it as needed
        return ($body['status'] == "success" ? true : false);
    }

    public function deleteInstance()
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://waapi.app/api/v1/instances/'.$this->instance_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_HTTPHEADER => [
                'authorization: Bearer ' . $this->token,
            ],
        ]);

        curl_exec($curl);
        curl_close($curl);

        return true;
    }
}
