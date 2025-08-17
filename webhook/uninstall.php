<?php

include("hook_config.php");

$activity->deactivate($config->get_shop());
$activity->uninstall($config->get_shop());
// remove token
unlink(__DIR__."/../.tokens/".$shopDomain);
echo "Uninstall Successful";
http_response_code(200);