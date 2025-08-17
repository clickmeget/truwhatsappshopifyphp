<?php

ini_set("memory_limit", "640M");

$directory = __DIR__ . '/classes/';

// Loop through all PHP files in the directory
foreach (glob($directory . "*.php") as $filename) {
    require_once $filename;
}
// error_reporting(E_ALL);
// ini_set("display_errors", 1);
$activity = new Activity();
$config = new Config();
// App settings
$config->set_base_url("https://truwhatsapp.clickmeget.com/");
$public_app = false;
if(isset($_GET['shop']))
{
    $app_api_keys = $activity->getAppKeys(trim($_GET['shop']));
    if(!$app_api_keys)
    {
        $public_app = true;
        // use public app keys
        $app_api_keys = ['api_key' => "300c3e81de9a8767c57c8f1c991b206f", "api_secret"=> "d9b92ec1ba4989b2d1f8186956d5b682"];
        // echo "<h1>This domain is not registered!";exit;
    }
    // set configs
    $config->set_api($app_api_keys['api_key'], $app_api_keys['api_secret']);
}else
{
    header("location: https://apps.shopify.com/tru-whatsapp-order-notifier");
    echo "shop not present in request."; exit;
}

$config->set_scopes(["read_store","read_orders","write_orders","customer_read_customers","customer_read_orders", "read_products"]);

// webhooks
$webhooks = [
    [
        'topic' => 'app/uninstalled',
        'address' => $config->base_url()."webhook/uninstall.php",
        'format' => 'json'
    ]
];
$config->set_webhooks($webhooks);

// Verify Shopify HMAC
$config->verifyShopifyHmac();


if(isset($_GET["shop"]) && $config->get_token() == false && !isset($_GET['code'])) {
    $config->set_shop($_GET['shop']);
    $config->install_app();
}


// get routes
$route = new Route($config->base_url());

// oauth
$route->add('oauth', [$config, 'oauth']);


if($route->get() !== "oauth" && $route->get() !== "subscribe" && $route->get() !== "pricing_plans")
{
    if($public_app)
    {
        //verify billing
        if(!$activity->get_billing_status($config->get_shop()))
        {
            
            $route->redirect("subscribe/?shop=".$config->get_shop());
        }
    }
    
}

// Main app
$app = new App();

$route->add("subscribe", function() use ($activity, $config, $app) {

    $chargeId = $_GET['charge_id'] ?? '';
    $data['title'] = "Subscribe to Plan - Tru&copy; WhatsApp Notifier";
    $data['app_api_key'] = $config->get_api_key();
    $data['api_key'] = md5($config->get_token() ? $config->get_token(): "");
    $data['shop'] = $config->get_shop();
    $data['base_url'] = $config->base_url();
    $data['config'] = $config;
    $data['activity'] = $activity;
    $data['chargeId'] = $chargeId;
    $data['store_status'] = "inactive";
    $app->load("subscribe", $data);
});



$route->add('', function() use ($app, $config, $activity) {
    
    
    $data['title'] = "Tru&copy; WhatsApp Notifier";
    $data['app_api_key'] = $config->get_api_key();
    $data['api_key'] = md5($config->get_token() ? $config->get_token(): "");
    $data['shop'] = $config->get_shop();
    $data['base_url'] = $config->base_url();
    $account = $activity->get_account($config->get_shop());
    $data['account'] = $account;
    $waapi = $activity->get_waapi($account['waapi_id']);
    if($waapi->get_instance_status() == "qr")
    {
        $waapi->reboot();
    }
    $data['qr'] = "";
    $app->load('truwhatsapp', $data);
    
});





$route->add("privacy-policy", function() use ($app, $config) {
    $data['title'] = "Privacy Policy - Tru&copy; WhatsApp Notifier";
    $data['app_api_key'] = $config->get_api_key();
    $data['api_key'] = md5($config->get_token() ? $config->get_token(): "");
    $data['shop'] = $config->get_shop();
    $data['base_url'] = $config->base_url();
    $app->load('privacy-policy', $data);
});

$route->add("dosndonts", function() use ($app, $config) {
    $data['title'] = "Dos &amp; Don'ts - Tru&copy; WhatsApp Notifier";
    $data['app_api_key'] = $config->get_api_key();
    $data['api_key'] = md5($config->get_token() ? $config->get_token(): "");
    $data['shop'] = $config->get_shop();
    $data['base_url'] = $config->base_url();
    $app->load('privacy-policy', $data);
});

$route->run();

