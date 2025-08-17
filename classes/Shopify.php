<?php

class Shopify 
{
    private $shop;
    private $token;
    private $version = "2024-07";

    public function __construct($shop, $access_token)
    {
        $this->shop = $shop;
        $this->token = $access_token;   
    }

    private function makeRequest($endpoint, $method = 'GET', $data = [])
    {
        $url = "https://{$this->shop}/admin/api/{$this->version}/{$endpoint}";
       
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "X-Shopify-Access-Token: {$this->token}"
        ]);
        
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        } elseif ($method == 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }
        
        curl_close($ch);
        
        return json_decode($response, true);
    }

    public function get_orders(array $ids)
    {
        $ids_str = implode(',', $ids);
        $endpoint = "orders.json?ids={$ids_str}";
        return $this->makeRequest($endpoint);
    }

    public function get_order_by_id($id)
    {
        $endpoint = "orders/{$id}.json";
        return $this->makeRequest($endpoint);
    }
}

