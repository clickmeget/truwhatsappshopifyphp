<?php

    $store_handle = explode(".", $config->get_shop());
    $store_handle = $store_handle[0];
    $app_handle = "whatsapp-notifications-4";
    $plan_selection_page = "https://admin.shopify.com/store/$store_handle/charges/$app_handle/pricing_plans";
    

    if(empty($chargeId))
    {
        // create recurring charge
        $current_charges = $activity->listRecurringCharges($config->get_shop(), $config->get_token());

        if(isset($current_charges[0]['id']))
        {
            
            $chargeStatus = $current_charges[0]['status'];
            if ($chargeStatus === 'accepted' || $chargeStatus === 'active') {
                // Charge has been accepted or activated
                $activity->activate($config->get_shop());
                header("location:".$config->base_url()."?shop=".$config->get_shop());
            }
            
        }else
        {
            $activity->deactivate(shop: $config->get_shop());
        }
        
        
    }else
    {
        $chargeInfo = $activity->verifyCharge($config->get_shop(), $chargeId, $config->get_token());
        // Check the charge status
        if ($chargeInfo && isset($chargeInfo['recurring_application_charge']['status'])) {
            $chargeStatus = $chargeInfo['recurring_application_charge']['status'];
    
            if ($chargeStatus === 'accepted' || $chargeStatus === 'active') {
                // Charge has been accepted or activated
                $activity->activate($config->get_shop());
                header("location:".$config->base_url()."?shop=".$config->get_shop());
            } else {
                $activity->deactivate($config->get_shop());
            }
        } else {
            $activity->deactivate($config->get_shop());
        }
    }

?>
<div class="container mt-4 bg-white  w-3/5 shadow-lg rounded-lg p-14">
    <div class="flex justify-between">
    <h1>
    No Plan selected:
    </h1>
    <button onclick="select_plan();" class="bg-[#43CD66] px-5 py-2 font-semibold rounded-full hover:bg-white text-sm">Select Plan</button>

    </div>
<span style="font-size:9pt;"><i>Select plan then Refresh this page!</i></span>
</div>

<script>
    function select_plan()
    {
        const plan_selection_page = '<?=$plan_selection_page;?>';
        fbq('track', 'Subscribe', {
            value: 20.99,
            currency: 'USD'
        });
        window.open(plan_selection_page, "_blank");
        // Close the current tab
        window.close();
    }
</script>