
<script>
    fbq('track', 'ViewContent');
</script>
<div class="container mt-4 bg-white flex justify-between w-3/5 shadow-lg rounded-lg p-14 hidden screen scan">
        <ol class="list-decimal my-auto">
            <li class="leading-10 text-xl p-2 text-sm">Open WhatsApp on your phone</li>
            <li class="leading-10 text-xl p-2 text-sm">Tap <strong>Menu <span><svg height="24px" viewBox="0 0 24 24" width="24px" class="inline"><rect fill="#f2f2f2" height="24" rx="3" width="24"></rect><path d="m12 15.5c.825 0 1.5.675 1.5 1.5s-.675 1.5-1.5 1.5-1.5-.675-1.5-1.5.675-1.5 1.5-1.5zm0-2c-.825 0-1.5-.675-1.5-1.5s.675-1.5 1.5-1.5 1.5.675 1.5 1.5-.675 1.5-1.5 1.5zm0-5c-.825 0-1.5-.675-1.5-1.5s.675-1.5 1.5-1.5 1.5.675 1.5 1.5-.675 1.5-1.5 1.5z" fill="#818b90"></path></svg></span></strong> on Android, or <strong>Settings <span class="x1rg5ohu x16dsc37"><svg width="24" height="24" viewBox="0 0 24 24" class="inline"><rect fill="#F2F2F2" width="24" height="24" rx="3"></rect><path d="M12 18.69c-1.08 0-2.1-.25-2.99-.71L11.43 14c.24.06.4.08.56.08.92 0 1.67-.59 1.99-1.59h4.62c-.26 3.49-3.05 6.2-6.6 6.2zm-1.04-6.67c0-.57.48-1.02 1.03-1.02.57 0 1.05.45 1.05 1.02 0 .57-.47 1.03-1.05 1.03-.54.01-1.03-.46-1.03-1.03zM5.4 12c0-2.29 1.08-4.28 2.78-5.49l2.39 4.08c-.42.42-.64.91-.64 1.44 0 .52.21 1 .65 1.44l-2.44 4C6.47 16.26 5.4 14.27 5.4 12zm8.57-.49c-.33-.97-1.08-1.54-1.99-1.54-.16 0-.32.02-.57.08L9.04 5.99c.89-.44 1.89-.69 2.96-.69 3.56 0 6.36 2.72 6.59 6.21h-4.62zM12 19.8c.22 0 .42-.02.65-.04l.44.84c.08.18.25.27.47.24.21-.03.33-.17.36-.38l.14-.93c.41-.11.82-.27 1.21-.44l.69.61c.15.15.33.17.54.07.17-.1.24-.27.2-.48l-.2-.92c.35-.24.69-.52.99-.82l.86.36c.2.08.37.05.53-.14.14-.15.15-.34.03-.52l-.5-.8c.25-.35.45-.73.63-1.12l.95.05c.21.01.37-.09.44-.29.07-.2.01-.38-.16-.51l-.73-.58c.1-.4.19-.83.22-1.27l.89-.28c.2-.07.31-.22.31-.43s-.11-.35-.31-.42l-.89-.28c-.03-.44-.12-.86-.22-1.27l.73-.59c.16-.12.22-.29.16-.5-.07-.2-.23-.31-.44-.29l-.95.04c-.18-.4-.39-.77-.63-1.12l.5-.8c.12-.17.1-.36-.03-.51-.16-.18-.33-.22-.53-.14l-.86.35c-.31-.3-.65-.58-.99-.82l.2-.91c.03-.22-.03-.4-.2-.49-.18-.1-.34-.09-.48.01l-.74.66c-.39-.18-.8-.32-1.21-.43l-.14-.93a.426.426 0 00-.36-.39c-.22-.03-.39.05-.47.22l-.44.84-.43-.02h-.22c-.22 0-.42.01-.65.03l-.44-.84c-.08-.17-.25-.25-.48-.22-.2.03-.33.17-.36.39l-.13.88c-.42.12-.83.26-1.22.44l-.69-.61c-.15-.15-.33-.17-.53-.06-.18.09-.24.26-.2.49l.2.91c-.36.24-.7.52-1 .82l-.86-.35c-.19-.09-.37-.05-.52.13-.14.15-.16.34-.04.51l.5.8c-.25.35-.45.72-.64 1.12l-.94-.04c-.21-.01-.37.1-.44.3-.07.2-.02.38.16.5l.73.59c-.1.41-.19.83-.22 1.27l-.89.29c-.21.07-.31.21-.31.42 0 .22.1.36.31.43l.89.28c.03.44.1.87.22 1.27l-.73.58c-.17.12-.22.31-.16.51.07.2.23.31.44.29l.94-.05c.18.39.39.77.63 1.12l-.5.8c-.12.18-.1.37.04.52.16.18.33.22.52.14l.86-.36c.3.31.64.58.99.82l-.2.92c-.04.22.03.39.2.49.2.1.38.08.54-.07l.69-.61c.39.17.8.33 1.21.44l.13.93c.03.21.16.35.37.39.22.03.39-.06.47-.24l.44-.84c.23.02.44.04.66.04z" fill="#818b90"></path></svg></span></strong> on iPhone</li>
            <li class="leading-10 text-xl p-2 text-sm">Tap Linked devices and then Link a device</li>
            <li class="leading-10 text-xl p-2 text-sm">Point your phone at this screen to capture the QR code</li>
        </ol>
        <img class ="qrcode" src="<?=$qr;?>" alt="" width="280px" height="280px">

    </div>

    <div class="container mt-4 bg-white text-center justify-between w-3/5 shadow-lg rounded-lg p-10 hidden screen final">
        <h1 class="text-2xl font-semibold text-[#3B4A5A]">Congratulations</h1>
        <p class="text-gray-600 mt-2 text-xs">Your Notifications Are Working Seamlessly</p>
         <!-- Accordion Trigger Button -->
         <button id="new_order_btn" class="bg-[#43CD66] px-4 py-1 font-semibold rounded-full hover:bg-green-400 focus:outline-none text-sm mt-4">
            New Order
        </button>
        <button id="abd_order_btn" class="bg-[#43CD66] px-4 py-1 font-semibold rounded-full hover:bg-green-400 focus:outline-none text-sm mt-4">
            Abandoned Checkouts
        </button>
        <button id="full_order_btn" class="bg-[#43CD66] px-4 py-1 font-semibold rounded-full hover:bg-green-400 focus:outline-none text-sm mt-4">
            Fulfillment
        </button>
        <div class="flex justify-between mt-4">
            <!-- Left Section: Message Input -->
            <div class="bg-[#F0F2F5] p-6 rounded-md shadow-md w-1/2 text-left mr-4"  id="new_order_div">
                <h2 class="text-base font-semibold text-gray-600 mb-4">New Order Notifications</h2>
                <textarea name="new_order_message" id="new_order_message"
                    class="text-xs w-full h-32 resize-none p-4 border border-gray-300 rounded-md focus:outline-none focus:border-blue-400"
                    placeholder="Type your message for new order here. If empty, no notifications for new order will be sent!"><?=trim($account['message']);?></textarea>
                <div class="flex items-center justify-between mt-4">
                    <button 
                        class="save_msg bg-[#43CD66] px-7 py-3 font-semibold rounded-full hover:bg-green-400 focus:outline-none text-sm"
                        type="button">
                        Update
                    </button>
                    <span class="text-gray-500 text-sm">Count: <span id="new_counting">500</span></span>
                </div>
            </div>
            <div class="bg-[#F0F2F5] p-6 rounded-md shadow-md w-1/2 text-left mr-4 hidden"  id="abd_order_div">
                <h2 class="text-base font-semibold text-gray-600 mb-4">Abandoned Checkouts Notifications</h2>
                <textarea name="abd_order_message" id="abd_order_message"
                    class="text-xs w-full h-32 resize-none p-4 border border-gray-300 rounded-md focus:outline-none focus:border-blue-400"
                    placeholder="Type your message for Abandoned Checkouts here. If empty, no notifications for abandoned checkout will be sent!"><?=trim($account['abd_message']);?></textarea>
                <div class="flex items-center justify-between mt-4">
                    <button 
                        class=" save_msg bg-[#43CD66] px-7 py-3 font-semibold rounded-full hover:bg-green-400 focus:outline-none text-sm"
                        type="button">
                        Update
                    </button>
                    <span class="text-gray-500 text-sm">Count: <span id="abd_counting">500</span></span>
                </div>
            </div>
            <div class="bg-[#F0F2F5] p-6 rounded-md shadow-md w-1/2 text-left mr-4 hidden"  id="full_order_div">
                <h2 class="text-base font-semibold text-gray-600 mb-4">Fulfillment Notifications</h2>
                <textarea name="full_order_message" id="full_order_message"
                    class="text-xs w-full h-32 resize-none p-4 border border-gray-300 rounded-md focus:outline-none focus:border-blue-400"
                    placeholder="Type your message for Fulfillment here. If empty, no notifications for Fulfillment will be sent!"><?=trim($account['full_message']);?></textarea>
                <div class="flex items-center justify-between mt-4">
                    <button 
                        class=" save_msg bg-[#43CD66] px-7 py-3 font-semibold rounded-full hover:bg-green-400 focus:outline-none text-sm"
                        type="button">
                        Update
                    </button>
                    <span class="text-gray-500 text-sm">Count: <span id="full_counting">500</span></span>
                </div>
            </div>
            <!-- Right Section: Dynamic Fields -->
            <div class="bg-[#F0F2F5] p-6 rounded-md shadow-md w-1/2 text-center">
                <h2 class="text-base font-semibold text-gray-600 mb-4">Use Dynamic Fields</h2>
                <div class="space-y-2 h-40 overflow-y-auto p-5 " id="new_dynamic">
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="order_no">Order #</button>  
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="customer_name">Customer Name</button>
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="shipping_phone">Shipping Phone</button>
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="customer_email">Customer Email</button>
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="shipping_address">Shipping Address</button>
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="billing_address">Billing Address</button>
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="line_items">Line Items</button>
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="subtotal">Subtotal</button>
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="subtotal_with_currency">Subtotal with Currency</button>
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="total">Total Amount</button>
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="total_with_currency">Total with Currency</button>                     
                </div>
                <div class="space-y-2 h-40 overflow-y-auto p-5  hidden" id="abd_dynamic">
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="customer_name">Customer Name</button>
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="shipping_phone">Shipping Phone</button>
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="customer_email">Customer Email</button>
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="line_items">Line Items</button>
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="subtotal">Subtotal</button>
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="subtotal_with_currency">Subtotal with Currency</button>
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="total">Total Amount</button>
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="total_with_currency">Total with Currency</button>
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="checkout_link">Checkout Link</button>
                </div>
                <div class="space-y-2 h-40 overflow-y-auto p-5  hidden" id="full_dynamic">
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="customer_name">Customer Name</button>
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="shipping_phone">Shipping Phone</button>
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="customer_email">Customer Email</button>
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="shipping_address">Shipping Address</button>
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="billing_address">Billing Address</button>
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="line_items">Line Items</button>
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="subtotal">Subtotal</button>
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="subtotal_with_currency">Subtotal with Currency</button>
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="total">Total Amount</button>
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="total_with_currency">Total with Currency</button>
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="fulfilled_by">Fulfilled By</button>
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="tracking_id">Tracking Id</button>
                    <button class="dynamic_feild w-full bg-white py-2 px-4 rounded-md shadow-sm text-gray-700 text-xs" value="tracking_link">Tracking Link</button>
                </div>
            </div>
        </div>
    </div>