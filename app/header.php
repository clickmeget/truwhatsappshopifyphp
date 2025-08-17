<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="shopify-api-key" content="<?=$app_api_key;?>" />
    <script src="https://cdn.shopify.com/shopifycloud/app-bridge.js"></script>
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->
    <meta name="viewport" content="width=800, initial-scale=0.8">

    <title><?=$title;?></title>
    <script src="<?=$base_url;?>/assets/js/tailwind.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="<?=$base_url;?>/assets/css/style.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <script src="<?=$base_url;?>/assets/js/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <!-- Toastr -->
    <link rel="stylesheet" href="<?=$base_url;?>/assets/css/toastr.css"  crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="<?=$base_url;?>/assets/js/toastr.min.js"  crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    
    <!-- microsoft clarity -->
    <script type="text/javascript">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "ofhz69erzc");
    </script>

    <!-- Facebook Pixel -->
     <!-- Meta Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '1083686889793729');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=1083686889793729&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Meta Pixel Code -->

</head>
<body class="bg-[url('<?=$base_url;?>assets/images/background.png')] h-full overflow-y-auto flex items-center bg-fixed bg-center bg-cover justify-center py-14" style="background-image: url('<?=$base_url;?>assets/images/background.png');">
    <header class="bg-[#103928] fixed inset-x-0 top-0">
        <nav class="mx-auto flex max-w-full items-center justify-between p-2">
            <div class="flex lg:flex-1">
                <img src="<?=$base_url;?>/assets/images/logo.svg" alt="" class="h-5 w-auto">
            </div>
            <div class="flex lg:flex-2 items-center justify-between w-72 <?=isset($store_status) && $store_status == "inactive" ? "hidden": "";?>">
                <p class="text-white font-semibold text-sm">Still facing any issue?</p>
                <button id="reboot" class="bg-[#43CD66] px-5 py-2 font-semibold rounded-full hover:bg-white text-sm">Reboot App</button>
            </div>
        </nav>
    </header>
    
    <input type="hidden" name="api_key" id="api_key" value="<?=$api_key;?>">
    <input type="hidden" name="base_url" id="base_url" value="<?=$base_url;?>">
    


