<?php



class Activity 
{
    private $db;
    private $waapi;
    private $config;
    function __construct()
    {
        $this->db = new Database();
        $this->waapi = new Waapi();
        $this->config = new Config();
    }
    public function get_waapi($instance_id)
    {
        $this->waapi->set_instance_id($instance_id);
        return $this->waapi;
    }
    public function get_account($shop, bool $create_new = true)
    {
        $query = $this->db->query("SELECT * from accounts WHERE shop='$shop'");

        if($query && $this->db->numRows($query) > 0)
        {
            // account already exist
            $row = $this->db->fetch($query);

            return $row;
        }else
        {
            if(!$create_new)
            {
                return null;
            }
            //
            //create a waapi instance
            $instance_id = $this->waapi->create_instance();
            sleep(10);
            // create one
            $query = "INSERT into accounts (waapi_id, shop) VALUES ($instance_id, '$shop')";
            $insert = $this->db->query($query);
            if($insert)
            {
                return $this->get_account($shop);
            }
            else
            {
                print_r("Unable to insert / create account");
            }
        }
    }
    public function update_message(string $shop, string $new_order_message, $abd_order_message, $full_order_message)
    {
        $new_order_message = $this->db->escape($new_order_message);
        $abd_order_message = $this->db->escape($abd_order_message);
        $query = "UPDATE accounts set message='".$new_order_message."', abd_message='".$abd_order_message."', full_message='".$full_order_message."' where shop='$shop'";
        $result = $this->db->query($query);
        if($result)
        {
            return true;
        }else
        {
            return false;
        }
    }
   
    public function getStoreEmail($shop, $access_token)
    {
        $storeInfoUrl = "https://{$shop}/admin/api/2023-01/shop.json"; // Use appropriate API version
        $response = file_get_contents($storeInfoUrl, false, stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "X-Shopify-Access-Token: {$access_token}\r\n",
            ],
        ]));

        $storeInfo = json_decode($response, true);
        $storeEmail = $storeInfo['shop']['email'] ?? null;
        
        return $storeEmail;
    }


    public function get_all_accounts()
    {
        $query = 'SELECT * from accounts;';
        $results = $this->db->query($query);
        $res = array();
        if($results && $this->db->numRows($results) > 0)
        {
            while($row = mysqli_fetch_array($results))
            {
                $this->config->set_shop($row['shop']);
                $token = $this->config->get_token();
                if(!empty($token))
                {
                    $res[] = array(
                        "waapi_id" => $row['waapi_id'],
                        "shop" => $row['shop'],
                        "message" => $row['message'],
                        "last_order_id" => empty($row['last_order_id']) ? null : $row['last_order_id'],
                        "abd_last_order_id" => empty($row['abd_last_order_id']) ? null : $row['abd_last_order_id'],
                        "full_last_order_id" => empty($row['full_last_order_id']) ? null : $row['full_last_order_id'],
                        "token"       => $token,
                    );
                }
            }
        }
        return $res;
    }

    public function get_last_shopify_order($shop, $access_token, $last_order_id = null)
    {
        // Initialize query parameters
        $queryParams = [
            'limit' => 1, // We only need one order
            'status' => 'any', // Include orders of any status
        ];

        if (!empty($last_order_id)) {
            // If $last_order_id is provided, fetch the next order after this ID
            $queryParams['since_id'] = $last_order_id;
        } 
        // Build the query string from the parameters
        $queryString = http_build_query($queryParams);

        // Set the Shopify API URL with query string
        $url = "https://{$shop}/admin/api/2024-07/orders.json?" . $queryString;

        // Initialize cURL
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-Shopify-Access-Token: ' . $access_token,
            'Accept: application/json',
        ]);

        // Execute the cURL request
        $response = curl_exec($ch);
        
        // Check for cURL errors
        if (curl_errno($ch)) {
            return [
                'error' => curl_error($ch),
                'response' => null,
            ];
        }

        // Get the HTTP status code
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Close cURL resource
        curl_close($ch);

        // If the request was successful
        if ($httpCode === 200) {
            // Parse the response
            $data = json_decode($response, true);

            // Return the order found, if available
            if (!empty($data['orders'])) {
                return $data['orders'][0];
            } else {
                return null; // No order found
            }
        } else {
            // Handle non-200 responses
            return [
                'error' => "Unexpected HTTP code: $httpCode",
                'response' => $response,
            ];
        }
    }

    public function get_last_abandoned_checkout($shop, $access_token, $last_checkout_id = null)
    {
        // Initialize query parameters
        $queryParams = [
            'limit' => 1, // We only need one checkout
            'status' => 'open', // Only include open checkouts (abandoned)
        ];

        if (!empty($last_checkout_id)) {
            // If $last_checkout_id is provided, fetch the next checkout after this ID
            $queryParams['since_id'] = $last_checkout_id;
        } 
        // Build the query string from the parameters
        $queryString = http_build_query($queryParams);

        // Set the Shopify API URL with query string
        $url = "https://{$shop}/admin/api/2024-07/checkouts.json?" . $queryString;

        // Initialize cURL
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-Shopify-Access-Token: ' . $access_token,
            'Accept: application/json',
        ]);

        // Execute the cURL request
        $response = curl_exec($ch);
        
        // Check for cURL errors
        if (curl_errno($ch)) {
            return [
                'error' => curl_error($ch),
                'response' => null,
            ];
        }

        // Get the HTTP status code
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Close cURL resource
        curl_close($ch);

        // If the request was successful
        if ($httpCode === 200) {
            // Parse the response
            $data = json_decode($response, true);

            // Return the checkout found, if available
            if (!empty($data['checkouts'])) {
                return $data['checkouts'][0];
            } else {
                return null; // No checkout found
            }
        } else {
            // Handle non-200 responses
            return [
                'error' => "Unexpected HTTP code: $httpCode",
                'response' => $response,
            ];
        }
    }

    public function get_last_fulfilled_shopify_order($shop, $access_token, $last_order_id = null)
    {
        // Initialize query parameters
        $queryParams = [
            'limit' => 1, // We only need one order
            'status' => 'any', // Include orders of any status
            'fulfillment_status' => 'shipped', // Only include fulfilled orders
        ];

        if (!empty($last_order_id)) {
            // If $last_order_id is provided, fetch the next order after this ID
            $queryParams['since_id'] = $last_order_id;
        } 

        // Build the query string from the parameters
        $queryString = http_build_query($queryParams);

        // Set the Shopify API URL with query string
        $url = "https://{$shop}/admin/api/2024-07/orders.json?" . $queryString;

        // Initialize cURL
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-Shopify-Access-Token: ' . $access_token,
            'Accept: application/json',
        ]);

        // Execute the cURL request
        $response = curl_exec($ch);
        // Check for cURL errors
        if (curl_errno($ch)) {
            return [
                'error' => curl_error($ch),
                'response' => null,
            ];
        }

        // Get the HTTP status code
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Close cURL resource
        curl_close($ch);

        // If the request was successful
        if ($httpCode === 200) {
            // Parse the response
            $data = json_decode($response, true);

            // Return the order found, if available
            if (!empty($data['orders'])) {
                return $data['orders'][0];
            } else {
                return null; // No order found
            }
        } else {
            // Handle non-200 responses
            return [
                'error' => "Unexpected HTTP code: $httpCode",
                'response' => $response,
            ];
        }
    }

    public function process_payload($shop, $order, $order_type = "new")
    {
        
        // check if shop exist
        $query = "SELECT * from accounts where shop='$shop'";
        $results = $this->db->query($query);
        if($results && $this->db->numRows($results) > 0)
        {
            $account = $this->db->fetch($results);

            $this->waapi->set_instance_id($account['waapi_id']);

            if($this->waapi->get_instance_status() !== "ready")
            {
                // Instance is not ready!
                $this->log_message(isset($order["shipping_address"]["phone"]) ? $order["shipping_address"]["phone"] : "", "Instance is not ready", $shop, "error");
                return "not ready";
            }

            $customer_name = (isset($order['customer']['first_name']) ? $order['customer']['first_name'] : "") ." ".(isset($order['customer']['first_name']) ? $order['customer']['last_name'] : "Sir/Madam");
            $customer_email = isset($order["customer"]["email"]) ? $order["customer"]["email"] :"";
            $shipping_phone = isset($order["shipping_address"]["phone"]) ? $order["shipping_address"]["phone"] : "";
            $customer_phone = isset($order["customer"]["phone"]) ? $order["customer"]['phone'] :'';

            $shipping_address = (isset($order['shipping_address']['address1']) ? $order['shipping_address']['address1'] : "")."\n"
            .(isset($order['shipping_address']['address1']) ? $order['shipping_address']['address2'] : "")."\n"
            .(isset($order['shipping_address']['province']) ? $order['shipping_address']['province'] : "")."\n"
            .(isset($order['shipping_address']['city']) ? $order['shipping_address']['city'] : "")."\n"
            .(isset($order['shipping_address']['zip']) ? $order['shipping_address']['zip'] : "")."\n"
            .(isset($order['shipping_address']['country']) ? $order['shipping_address']['country'] : "");

            $billing_address = (isset($order['billing_address']['address1']) ? $order['billing_address']['address1'] : "")."\n"
            .(isset($order['billing_address']['address1']) ? $order['billing_address']['address2'] : "")."\n"
            .(isset($order['billing_address']['province']) ? $order['billing_address']['province'] : "")."\n"
            .(isset($order['billing_address']['city']) ? $order['billing_address']['city'] : "")."\n"
            .(isset($order['billing_address']['zip']) ? $order['billing_address']['zip'] : "")."\n"
            .(isset($order['billing_address']['country']) ? $order['billing_address']['country'] : "");

            $line_items = "";
            foreach($order['line_items'] as $item)
            {
                $line_items .= $item['title']." x".$item['quantity']."\n";
            }
            $line_items = trim(rtrim($line_items,"\n"));

            $currency = $order['currency'];
            $subtotal = isset($order['current_subtotal_price']) && !empty($order['current_subtotal_price']) ?  number_format($order['current_subtotal_price'], 2, '.', ',') : null;
            $subtotal_with_currency =  empty($subtotal) ? null: $currency.$subtotal;

            $total =  isset($order['current_total_price']) && !empty($order['current_total_price'])? number_format($order['current_total_price'],2,'.',',') : null;
            $total_with_currency = empty($total) ? null : $currency.$total;

            $order_no = $order['name'];

            // abd checkout
            $checkout_link = isset($order['abandoned_checkout_url']) ? $order['abandoned_checkout_url'] : "";

            // Fulfillment 
            $fulfilled_by = isset($order['fulfillments'][0]['tracking_company']) ? $order['fulfillments'][0]['tracking_company'] : "";
            $tracking_id = isset($order['fulfillments'][0]['tracking_number']) ? $order['fulfillments'][0]['tracking_number'] : "";
            $tracking_link = isset($order['fulfillments'][0]['tracking_url']) ? $order['fulfillments'][0]['tracking_url'] : "";


            $replaceables = [
                "{{customer_name}}",
                "{{shipping_phone}}",
                "{{customer_phone}}",
                "{{customer_email}}",
                "{{shipping_address}}",
                "{{billing_address}}",
                "{{line_items}}",
                "{{subtotal}}",
                "{{subtotal_with_currency}}",
                "{{total}}",
                "{{total_with_currency}}",
                "{{order_no}}",
                "{{checkout_link}}",
                "{{fulfilled_by}}",
                "{{tracking_id}}",
                "{{tracking_link}}"
            ];
            if($order_type == "new")
            {
                $message = str_replace($replaceables, array(
                    $customer_name,
                    $shipping_phone,
                    $customer_phone,
                    $customer_email,
                    $shipping_address,
                    $billing_address,
                    $line_items,
                    $subtotal,
                    $subtotal_with_currency,
                    $total,
                    $total_with_currency,
                    $order_no,
                    $checkout_link,
                    $fulfilled_by,
                    $tracking_id,
                    $tracking_link
                ), $account['message']);
            }
            if($order_type == "abd")
            {
                $message = str_replace($replaceables, array(
                    $customer_name,
                    $shipping_phone,
                    $customer_phone,
                    $customer_email,
                    $shipping_address,
                    $billing_address,
                    $line_items,
                    $subtotal,
                    $subtotal_with_currency,
                    $total,
                    $total_with_currency,
                    $order_no,
                    $checkout_link,
                    $fulfilled_by,
                    $tracking_id,
                    $tracking_link
                ), $account['abd_message']);
            }
            if($order_type == "full")
            {
                $message = str_replace($replaceables, array(
                    $customer_name,
                    $shipping_phone,
                    $customer_phone,
                    $customer_email,
                    $shipping_address,
                    $billing_address,
                    $line_items,
                    $subtotal,
                    $subtotal_with_currency,
                    $total,
                    $total_with_currency,
                    $order_no,
                    $checkout_link,
                    $fulfilled_by,
                    $tracking_id,
                    $tracking_link
                ), $account['full_message']);
            }
            $chatId = $this->getChatId($order['shipping_address']['country_code'],$shipping_phone);
            // $chatId = ltrim(trim(str_replace(" ", "",$shipping_phone)), "+")."@c.us";
            
            if($chatId == "@c.us")
            {
                $this->log_message(isset($order["id"]) ? $order["id"] : "", "No shipping phone in order", $shop, "error");

                $send  = true;
            }else
            {
                $send = $this->waapi->send_message($message, $chatId);
            }

            
            // $send = true;
            if(!$send)
            { 
                // log message
                $this->log_message($chatId, $message, $shop, "error");
                return "not sent";
            }else
            {
                
                // log message
                $this->log_message($chatId, $message, $shop, "success");
                if($order_type == "new")
                {
                    $query2 = "Update accounts set last_order_id='".$order['id']."' where shop='$shop'";
                }
                if($order_type == "abd")
                {
                    $query2 = "Update accounts set abd_last_order_id='".$order['id']."' where shop='$shop'";
                }
                if($order_type == "full")
                {
                    $query2 = "Update accounts set full_last_order_id='".$order['id']."' where shop='$shop'";
                }
                $update = $this->db->query($query2);
                if($update)
                {
                    return "sent";
                }else
                {
                    return "sent not updated";
                }
                
            }

            
        }else
        {
            return "shop not found";
        }
    }
    
    private function log_message($reciever, $message, $shop, $status)
    {
        $message = str_replace(["'", '"'], "", $message);
        $query = "INSERT into messages_log (reciever, message, shop, status)
                Values ('$reciever', '$message', '$shop', '".$status."')";
        $this->db->query($query);
        $this->db->query("DELETE FROM messages_log WHERE timestamp < NOW() - INTERVAL 1 HOUR and `status`='error'");
        $this->db->query("DELETE FROM messages_log WHERE timestamp < NOW() - INTERVAL 24 HOUR and `status`='success'");
    }
    // Function to format the phone number with country code
    public function getChatId($countryCode, $phoneNumber) {
        // Get the numeric country code from the API
        $numericCountryCode = $this->getCountryDialCode($countryCode);
        
        if (!$numericCountryCode) {
            return "@c.us";
        }

        // Remove any non-numeric characters from the phone number
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        // If the phone number starts with the country code, strip it out
        if (strpos($phoneNumber, $numericCountryCode['dial_code']) === 0) {
            $phoneNumber = substr($phoneNumber, strlen($numericCountryCode['dial_code']));
        }

        // Remove leading zeros from the phone number
        $phoneNumber = ltrim($phoneNumber, '0');

        // Combine the numeric country code and cleaned phone number
        $formattedPhoneNumber = $numericCountryCode['dial_code'] . $phoneNumber . '@c.us';

        return $formattedPhoneNumber;
    }
    public function getCountryDialCode($countryCode) {
        // REST Countries API endpoint
        $apiUrl = "https://restcountries.com/v3.1/alpha/" . strtolower($countryCode) . "?fields=cca2,idd";
        
        // Initialize cURL session
        $curl = curl_init();
        
        curl_setopt_array($curl, [
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true
        ]);
    
        // Execute the cURL request
        $response = curl_exec($curl);
        
        // Close the cURL session
        curl_close($curl);
        
        // Decode the JSON response
        $countryData = json_decode($response, true);
    
        // Check if the response contains the needed data
        if (isset($countryData['idd']['root'])) {
            // Combine root and suffix if available
            $dialCode = ltrim($countryData['idd']['root'], '+');
            if (isset($countryData['idd']['suffixes']) && count($countryData['idd']['suffixes']) > 0) {
                // Use the first suffix (in most cases it's the correct one)
                $dialCode .= $countryData['idd']['suffixes'][0];
            }
            
            return [
                'country_code' => $countryData['cca2'],
                'dial_code' => $dialCode
            ];
        } else {
            return [
                'country_code' => "ER",
                'dial_code' => "92"
            ];
        }
    }

    public function uninstall($shop)
    {
        $account = $this->get_account($shop, false);
        if(!empty($account))
        {
            $waapi_id = $account['waapi_id'];
            $this->waapi->set_instance_id($waapi_id);
            $this->waapi->deleteInstance();
            $this->db->query("delete from accounts where shop='$shop'");
            $this->db->query("delete from apps where shop='$shop'");
            $this->db->query("delete from messages_log where shop='$shop'");
            $this->db->query("delete from billing where shop='$shop'");           
                
            
            
        }
    }

    public function verifyCharge($shopDomain, $chargeId, $accessToken) {
        $url = "https://{$shopDomain}/admin/api/2024-07/recurring_application_charges/{$chargeId}.json";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "X-Shopify-Access-Token: $accessToken",
            "Content-Type: application/json"
        ]);
    
        $response = curl_exec($ch);
        curl_close($ch);
    
        return json_decode($response, true);
    }
    public function listRecurringCharges($shop, $accessToken) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://$shop/admin/api/2024-07/recurring_application_charges.json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "X-Shopify-Access-Token: $accessToken",
            "Content-Type: application/json"
        ]);
    
        $response = curl_exec($ch);
        curl_close($ch);
    
        $charges = json_decode($response, true);
        
        if (isset($charges['recurring_application_charges'])) {
            return $charges['recurring_application_charges'];
        } else {
            return "Error fetching recurring charges: " . $response;
        }
    }
    public function createRecurringCharge($shop, $accessToken, $chargeDetails) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://$shop/admin/api/2024-07/recurring_application_charges.json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "X-Shopify-Access-Token: $accessToken",
            "Content-Type: application/json"
        ]);
        
        $data_string = json_encode(['recurring_application_charge' => $chargeDetails]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    
        $response = curl_exec($ch);
        curl_close($ch);
    
        $chargeResponse = json_decode($response, true);
        
        if (isset($chargeResponse['recurring_application_charge']['confirmation_url'])) {
            return $chargeResponse['recurring_application_charge']['confirmation_url'];
        } else {
            return "Error creating recurring charge: " . $response;
        }
    }
    
    
    public function activate($shop)
    {
        $shop = trim($shop);
        $query = "INSERT INTO billing (shop, statuscode) VALUES ('$shop', 1) ON DUPLICATE KEY UPDATE statuscode = 1";
        $this->db->query($query);
        $storeEmail = $this->getStoreEmail($shop, $this->config->get_token());
        if(!empty($storeEmail))
        {
            try
            {
                // Send the email
                $mail = @mail("clickmeget@gmail.com", "Tru App Activated $shop", "Dear Nabeel,\nStore with Email: $storeEmail has activated a billing plan for Tru Whatsapp App.");
            }catch(Exception $e)
            {
                // do nothing
            }
            
        }
    }
    public function deactivate($shop)
    {
        $shop = trim($shop);
        $this->db->query("INSERT INTO billing (shop, statuscode) VALUES ('$shop', 0) ON DUPLICATE KEY UPDATE statuscode = 0");
    }
    public function get_billing_status($shop)
    {
        $shop = trim($shop);

        $query = $this->db->query("SELECT statuscode from billing where shop='$shop'");
        
        $fetch = $this->db->fetch($query);        
        if(!empty($fetch) && $fetch['statuscode'] == 1)
        {
            return true;
        }else
        {
            return false;

        }
            
    }


    public function getAppKeys($shop)
    {

        $query = $this->db->query("SELECT * from apps where shop='$shop'");
        $res = $this->db->fetch($query);
        if(!$res || empty($res))
        {
            return false;
        }else
        {
            return $res;
        }
    }

    public function add_app($shop, $key, $secret)
    {
        $shop = trim($shop, "/");
        $query = $this->db->query("INSERT into apps (shop, api_key, api_secret) VALUES('$shop', '$key', '$secret')");
    }
}
