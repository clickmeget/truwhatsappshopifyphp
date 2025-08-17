<?php


include_once __DIR__."/hook_config.php";

if(isset($_GET['request_type']))
{
    switch ($_GET['request_type']) {
        case 'customer-data-equest':
            http_response_code(200);
            echo json_encode(array("status"=> 200, "message"=> "Data request recieved"), JSON_PRETTY_PRINT);
            break;
        case 'customer-data-erasure':
            http_response_code(200);
            echo json_encode(array("status"=> 200, "message"=> "Data erasure request recieved"), JSON_PRETTY_PRINT);
            break;
        case 'shop-data-erasure':
            http_response_code(200);
            echo json_encode(array("status"=> 200, "message"=> "Shop data erasure request recieved"), JSON_PRETTY_PRINT);
            break;
        default:
            http_response_code(404);
            echo json_encode(array("status"=> 404, "message"=> "no endpoint found"), JSON_PRETTY_PRINT);
            break;
    }
}else
{
    http_response_code(401);
    echo json_encode(array("status"=> 401, "message"=> "no request recieved"), JSON_PRETTY_PRINT);
}