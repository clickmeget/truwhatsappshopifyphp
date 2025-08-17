<?php


$directory = __DIR__ . '/../classes/';

// Loop through all PHP files in the directory
foreach (glob($directory . "*.php") as $filename) {
    require_once $filename;
}
$data = file_get_contents('php://input');

// log details
$headers = getallheaders();
$_SESSION['X-Shopify-Hmac-Sha256'] = $headers['[X-Shopify-Hmac-Sha256'];
// Get the request body
$body = file_get_contents('php://input');
// Log the headers and body to a file
$logFile = __DIR__.'/logs/webhook_'.strtotime("now").'.log';
$logData = "Headers:\n" . print_r($headers, true) . "\n";
$logData .= "Body:\n" . $body . "\n\n";


file_put_contents($logFile, $logData, FILE_APPEND);



$payload = json_decode($data, true);
$shopDomain = isset($payload['shop_domain']) ? $payload['shop_domain'] : (isset($headers['X-Shopify-Shop-Domain']) ? $headers['X-Shopify-Shop-Domain'] : 'unknown');
$config = new Config();
$config->set_shop($shopDomain);
$config->verifyShopifyHmac($data);

$activity = new Activity();

header('Content-Type: application/json');