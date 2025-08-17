<?php
$directory = __DIR__ . '/classes/';

// Loop through all PHP files in the directory
foreach (glob($directory . "*.php") as $filename) {
    require_once $filename;
}

if(isset($_POST['submit']))
{
    $shop = $_POST['shop'];
    $api_key = $_POST['api_key'];
    $api_secret = $_POST['api_secret'];
    $activity = new Activity();
    $activity->add_app($shop, $api_key, $api_secret);
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add App Keys</title>
</head>
<body>
    <form action="#" method="POST">
        <p>Enter Api Key:  
            <input type="text" name="api_key" id="api_key">
            <br>
            Enter Api Secret:
            <input type="password" name="api_secret" id="api_secret">
            <br>
            Enter Shop:
            <input type="text" name="shop" id="shop">
            <br>
            <input type="submit" value="Save App" name="submit">
        </p>
    </form>
</body>
</html>