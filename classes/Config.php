<?php
// this is config file

class Config 
{
    private $base_url;
    private $api_key;
    private $api_secret;
    private $shop;
    private $scopes;
    private $token;
    private $api_version = "2024-07";
    private $webhooks;
    private $fulfillment;
    public function __construct()
    {
        if (php_sapi_name() !== 'cli') {
            // session config
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
        }
        
        $this->token = new Tokens();


    }
    public function set_webhooks($webhooks)
    {
        $this->webhooks = $webhooks;
    }
    public function set_fulfillment($fulfillment)
    {
        $this->fulfillment = $fulfillment;
    }
    public function set_scopes(array $scopes)
    {
        $scopes = implode(",", $scopes);
        $this->scopes = $scopes;
    }
    public function get_scopes()
    {
        return $this->scopes;
    }
    public function set_api($api_key, $api_secret)
    {
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
    }
    public function get_api_key()
    {
        return $this->api_key;
    }
    public function get_api_secret()
    {
        return $this->api_secret;
    }
    public function get_shop()
    {
        return $this->shop ? $this->shop : $_GET['shop'];
    }
    public function set_shop($shop)
    {
        $this->shop = $shop;
    }
    public function base_url()
    {
        return $this->base_url ? $this->base_url : "https://example.com";
    }

    public function set_base_url($base_url)
    {
        $this->base_url = $base_url;
    }
    public function get_token()
    {
        $shop = $this->get_shop() ? $this->get_shop() : $_GET['shop'];
        return $this->token->get_token($shop);
    }
    public function verifyShopifyHmac($data = null) {
        if($this->get_token() == false)
        {
            $params = $_GET;
            if(!isset($_GET['hmac']) && !isset($_SESSION['X-Shopify-Hmac-Sha256']))
            {
                http_response_code(401); // Unauthorized
                print_r($_COOKIE);
                print_r($_GET);
                print_r($this->get_token());
                echo "hmac not found";
                exit;
            }
            if($data !== null)
            {
                $hmac = isset($_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256']) ? $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'] : '';
                $query = $data;
            }else
            {
                $hmac = $_GET['hmac'];
                unset($params['hmac']);
    
                ksort($params);
                $query = http_build_query($params);
            }
            
            $calculated_hmac = hash_hmac('sha256', $query, $this->get_api_secret());
            if(!hash_equals($hmac, $calculated_hmac))
            {
                http_response_code(401); // Unauthorized
                echo "Invalid HMAC signature";
                exit;
            }
        }
    }
    public function install_app()
    {
        $shop = $this->get_shop() ? $this->get_shop() : $_GET['shop'];
        $api_key =  $this->get_api_key();
        $scopes = $this->get_scopes();
        $redirect_uri = $this->base_url()."oauth/";
        $install_url = "https://$shop/admin/oauth/authorize?client_id=$api_key&scope=$scopes&redirect_uri=$redirect_uri";
        
        header("Location: $install_url");
        exit;
    }
    public function oauth()
    {
        $code = $_GET['code'] ? $_GET['code'] :'';
        $shop = $this->get_shop() ? $this->get_shop() : $_GET['shop'];
        $api_key = $this->get_api_key();
        $api_secret = $this->get_api_secret();
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://$shop/admin/oauth/access_token");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'client_id' => $api_key,
            'client_secret' => $api_secret,
            'code' => $code
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = 'cURL error: ' . curl_error($ch);
        } 

        curl_close($ch);

        if (isset($error)) {
            http_response_code(401); // Unauthorized
            echo "Unable to perform Oauth";
            echo $error;
            exit;
        }

        $response = json_decode($response, true);
        if(isset($response['access_token']))
        {
            $access_token = $response['access_token'];
            $this->token->set_token($access_token, $shop);
            $this->registerWebhooks();
            if(isset($this->fulfillment))
            {
                $this->createFulfillment($this->fulfillment);
            }
            header('location:'. $this->base_url()."?shop=$shop");
        }else
        {
            http_response_code(401); // Unauthorized
            echo "Unable to generate access token in Oauth";
            exit;
        }
        
    }
    
    private function isWebhookRegistered($webhook)
    {
        $shop = $this->get_shop() ? $this->get_shop() : $_GET['shop'];
        $api_version = $this->api_version;
        $access_token = $this->get_token();
        $url = "https://{$shop}/admin/api/{$api_version}/webhooks.json";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "X-Shopify-Access-Token: {$access_token}",
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($http_status !== 200) {
            // echo "Failed to fetch webhooks: " . $response . "\n";
            curl_close($ch);
            return false;
        }

        $webhooks = json_decode($response, true)['webhooks'];

        foreach ($webhooks as $existingWebhook) {
            if ($existingWebhook['topic'] === $webhook['topic'] && $existingWebhook['address'] === $webhook['address']) {
                curl_close($ch);
                return true;
            }
        }

        curl_close($ch);
        return false;
    }
    public function registerWebhooks()
    {
        $webhooks = $this->webhooks;
        $shop = $this->get_shop() ? $this->get_shop() : $_GET['shop'];
        $api_version = $this->api_version;
        $token = $this->get_token();
        
        foreach ($webhooks as $webhook) {
            if ($this->isWebhookRegistered($webhook)) {
                // echo "Webhook already registered: " . $webhook['topic'] . " at " . $webhook['address'] . "\n";
                continue;
            }
            $url = "https://{$shop}/admin/api/{$api_version}/webhooks.json";
            // remove extra values
            unset($webhook['method']);
            $data = [
                'webhook' => $webhook
            ];

            $jsonData = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'X-Shopify-Access-Token: '.$token
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

            $response = curl_exec($ch);
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($http_status === 201) {
                // echo "Webhook registered successfully: " . $webhook['topic'] . "\n";
            } else {
                // echo "Failed to register webhook: " . $webhook['topic'] . "\n";
                // echo "Response: " . $response . "\n";
            }

            curl_close($ch);
        }
    }
    private function isFulfillmentExist($serviceName)
    {
        $shop = $this->get_shop() ? $this->get_shop() : $_GET['shop'];
        $api_version = $this->api_version;
        $token = $this->get_token();
        $url = "https://{$shop}/admin/api/{$api_version}/fulfillment_services.json";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "X-Shopify-Access-Token: {$token}",
            'Content-Type: application/json',
        ]);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception('Request Error: ' . curl_error($ch));
        }
        curl_close($ch);

        $fulfillmentServices = json_decode($response, true);

        if (isset($fulfillmentServices['fulfillment_services'])) {
            foreach ($fulfillmentServices['fulfillment_services'] as $service) {
                if ($service['name'] === $serviceName) {
                    return true;
                }
            }
        }

        return false;
    }
    public function createFulfillment($serviceName)
    {
        if($this->isFulfillmentExist($serviceName))
        {
            echo "this $serviceName exist";
            return true;
        }
        $shop = $this->get_shop() ? $this->get_shop() : $_GET['shop'];
        $api_version = $this->api_version;
        $token = $this->get_token();
        $callbackUrl = $this->base_url()."fulfillment/";
        $url = "https://{$shop}/admin/api/{$api_version}/fulfillment_services.json";

        $data = [
            'fulfillment_service' => [
                'name' => $serviceName,
                'callback_url' => $callbackUrl,
                'inventory_management' => false,
                'tracking_support' => false,
                'requires_shipping_method' => false,
                'format' => 'json',
            ],
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "X-Shopify-Access-Token: {$token}",
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception('Request Error: ' . curl_error($ch));
        }
        curl_close($ch);

        $fulfillmentService = json_decode($response, true);
        print_r($fulfillmentService);
        return isset($fulfillmentService['fulfillment_service']) ? true:false;
    }

}
