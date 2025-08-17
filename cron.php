<?php

if (php_sapi_name() !== 'cli') {
    http_response_code(403); // Optional: Send a 403 Forbidden status
    exit('Access denied.');
}

ini_set("memory_limit", "640M");

$lockFile = __DIR__ . '/cron.lock';

// Check if the lock file exists
if (file_exists($lockFile)) {
    // Read the PID from the lock file
    $pid = (int)file_get_contents($lockFile);
    
    // Check if the process with that PID is running
    if (posix_kill($pid, 0)) {
        echo "Another instance (PID: $pid) is already running.\n";
        exit;
    } else {
        // If not running, remove the stale lock file
        unlink($lockFile);
    }
}

// Create a new lock file with the current PID
file_put_contents($lockFile, getmypid());

$directory = __DIR__ . '/classes/';

// Loop through all PHP files in the directory
foreach (glob($directory . "*.php") as $filename) {
    require_once $filename;
}

echo "\nstarting cron!\n\n";
$activity = new Activity();

$accounts = $activity->get_all_accounts();

foreach ($accounts as $account) {
    echo "Doing for " . $account['shop'] . " \n";
    $all_done = true;

    $last_sent_id = $account['last_order_id'];
    do {
        sleep(5);
        
        $order = $activity->get_last_shopify_order($account['shop'], $account['token'], $last_sent_id);

        if (isset($order['id'])) {


            if ($last_sent_id == $order['id']) {
                $all_done = true;
            } else {
                if (!empty($order)) {
                    // send message to customer
                    $result = $activity->process_payload($account['shop'], $order);
                    $last_sent_id = $order['id'];

                    switch ($result) {
                        case 'not ready':
                            echo $result . " \n";
                            $all_done = true;
                            break;
                        case 'sent':
                            
                            echo $result . " \n";
                            $all_done = false;
                            break;
                        case 'not sent':
                            echo $result . " \n";
                            $all_done = false;
                            break;

                        default:
                            echo "Default" . " \n";
                            $all_done = true;
                            break;
                    }
                } else {
                    echo "Empty Order" . " \n";
                    $all_done = true;
                    print_r($order);
                }
            }
        } else {
            print_r($order);
            echo "Error order id not found" . " \n";
            $all_done = true;
        }

    } while ($all_done == false);
    echo "completed for " . $account['shop'] . " \n";
}

echo "+++ Abandond Orders +++ \n";

foreach ($accounts as $account) {
    echo "Doing for " . $account['shop'] . " \n";
    $all_done = true;
    $last_sent_id = $account['abd_last_order_id'];
    do {
        sleep(5);
        
        $order = $activity->get_last_abandoned_checkout($account['shop'], $account['token'], $last_sent_id);
        if (isset($order['id'])) {


            if ($last_sent_id == $order['id']) {
                $all_done = true;
            } else {
                if (!empty($order)) {
                    // send message to customer
                    $result = $activity->process_payload($account['shop'], $order, "abd");
                    $last_sent_id = $order['id'];

                    switch ($result) {
                        case 'not ready':
                            echo $result . " \n";
                            $all_done = true;
                            break;
                        case 'sent':
                            
                            echo $result . " \n";
                            $all_done = false;
                            break;
                        case 'not sent':
                            echo $result . " \n";
                            $all_done = false;
                            break;

                        default:
                            echo "Default" . " \n";
                            $all_done = true;
                            break;
                    }
                } else {
                    echo "Empty Order" . " \n";
                    $all_done = true;
                    print_r($order);
                }
            }
        } else {
            print_r($order);
            echo "Error order id not found" . " \n";
            $all_done = true;
        }

    } while ($all_done == false);
    echo "completed for " . $account['shop'] . " \n";
}

echo "+++ Fulfillments +++ \n";

foreach ($accounts as $account) {
    echo "Doing for " . $account['shop'] . " \n";
    $all_done = true;
    $last_sent_id = $account['full_last_order_id'];
    do {
        sleep(5);
        
        $order = $activity->get_last_fulfilled_shopify_order($account['shop'], $account['token'], $last_sent_id);
        if (isset($order['id'])) {


            if ($last_sent_id == $order['id']) {
                $all_done = true;
            } else {
                if (!empty($order)) {
                    // send message to customer
                    $result = $activity->process_payload($account['shop'], $order, "full");
                    $last_sent_id = $order['id'];

                    switch ($result) {
                        case 'not ready':
                            echo $result . " \n";
                            $all_done = true;
                            break;
                        case 'sent':
                            
                            echo $result . " \n";
                            $all_done = false;
                            break;
                        case 'not sent':
                            echo $result . " \n";
                            $all_done = false;
                            break;

                        default:
                            echo "Default" . " \n";
                            $all_done = true;
                            break;
                    }
                } else {
                    echo "Empty Order" . " \n";
                    $all_done = true;
                    print_r($order);
                }
            }
        } else {
            print_r($order);
            echo "Error order id not found" . " \n";
            $all_done = true;
        }

    } while ($all_done == false);
    echo "completed for " . $account['shop'] . " \n";
}


// Remove the lock file when done
unlink($lockFile);
