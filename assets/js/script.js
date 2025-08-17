// Initial check on page load
checkScreenSize();

const base_url = $("#base_url").val().trim();
const apiUrl = base_url+"/api/";

const api_key = $('#api_key').val().trim();
var qrCode = ''; // Variable to store the QR code
screen('loading');
var que_function = [];
var reqInProcess = false;
var pendingAjaxRequests = [];
const maxCount = 500;

function que(fn) {
    que_function.push(fn);
    if (!reqInProcess) {
        processQue();
    }
}

function processQue() {
    if (que_function.length > 0 && !reqInProcess) {
        reqInProcess = true;
        var current_que_function = que_function.shift();
        current_que_function();
    }
}

function screen(screen) {
    $(".screen").addClass("hidden");
    $("." + screen).removeClass("hidden");

    if (screen == "loading") {
        const background_image = base_url + "assets/images/loading.png";
        $('body').css("background-image", "url('" + background_image + "')");
    } else {
        const background_image = base_url + "assets/images/background.png";
        $('body').css("background-image", "url('" + background_image + "')");
    }
    
}

function reboot() {
    screen("loading");
    $('#reboot').attr("disabled", true);

    abortPendingRequests(); // Abort any pending requests before reboot

    const data = {
        "api_key": api_key,
        "route"  : "reboot",
        "data"   : []
    };
    $.ajax({
        url: apiUrl,
        type: 'POST',
        dataType: 'json',
        data: JSON.stringify(data),
        success: function(response) {
            setTimeout(function() {
                
                que(get_status);
            }, 10000)
        },
        error: function(jqXHR, textStatus, errorThrown) {
            $('#reboot').removeAttr("disabled");
            toastr['danger']("Error Fetching Request. Please Reboot App..");
            console.log('Error:', textStatus, errorThrown);
        },
        complete: function(jqXHR, textStatus) {
            $('#reboot').removeAttr("disabled");
            reqInProcess = false;
            processQue(); // Continue processing the queue
        }
    });
}

function get_status(reboot = false) {
    const data = {
        "api_key": api_key,
        "route"  : "status",
        "data"   : []
    };
    var ajaxRequest = $.ajax({
        url: apiUrl,
        type: 'POST',
        dataType: 'json',
        data: JSON.stringify(data),
        success: function(response) {
            if (response.status == "success") {
                console.log("Instance Status", response.instance_status);
                if (response.instance_status == "qr" || response.instance_status == "auth_failure" || response.instance_status == "disconnected") {
                    if (qrCode.trim() == "") {
                        que(get_qr);
                    }
                    $('.qrcode').attr("src", qrCode);
                }
                if (response.instance_status == "ready" || response.instance_status == "authenticated") {
                    screen("final");
                }
                if (response.instance_status == "booting" || response.instance_status == "loading_screen") {
                    screen("loading");
                }
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('Error:', textStatus, errorThrown);
        },
        complete: function(jqXHR, textStatus) {
            reqInProcess = false;
            processQue(); // Continue processing the queue
        }
    });

    pendingAjaxRequests.push(ajaxRequest);
}

function get_qr() {
    const data = {
        "api_key": api_key,
        "route"  : "qr",
        "data"   : []
    };
    var ajaxRequest = $.ajax({
        url: apiUrl,
        type: 'POST',
        dataType: 'json',
        data: JSON.stringify(data),
        success: function(response) {
            if (response.status == "success") {
                qrCode = response.qr; // Save the QR code in the variable
                if(response.qr !== "")
                {
                    screen("scan");   
                }
                $('.qrcode').attr("src", qrCode);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            qrCode = './images/qrcode.png';
            $('.qrcode').attr("src", qrCode);
            toastr['error']("Error Fetching Request. Please Reboot App..");
            console.log('Error:', textStatus, errorThrown);
        },
        complete: function(jqXHR, textStatus) {
            reqInProcess = false;
            processQue(); // Continue processing the queue
        }
    });

    pendingAjaxRequests.push(ajaxRequest);

    return qrCode; // Return the saved QR code
}

function abortPendingRequests() {
    while (pendingAjaxRequests.length > 0) {
        var request = pendingAjaxRequests.pop();
        request.abort();
    }
}

// Refresh the QR code every 15 seconds
setInterval(function() {
    que(get_qr);
}, 15000);

// Refresh the status every 3 seconds
setInterval(function() {
    que(get_status);
}, 3000);

$(document).ready(function() {
    console.log("document is ready");
    que(get_status);
});

$("#reboot").on("click", function() {
    abortPendingRequests(); // Abort any pending requests before reboot
    que(reboot);
});
$("#new_order_message").on("input", function() {
    // Extract the user-typed content only
    let userContent = $(this).val().replace(/{{.*?}}/g, "").trim(); 
    const remainingCount = maxCount - userContent.length;

    // Ensure the count doesn't go below 0
    if (remainingCount >= 0) {
        $("#new_counting").text(remainingCount);
    } else {
        // If the count goes below 0, trim the user-typed content
        userContent = userContent.substring(0, maxCount);
        $(this).val(userContent + $(this).val().match(/{{.*?}}/g)?.join(" ") || "");
        $("#new_counting").text(0);
    }
});
$("#abd_order_message").on("input", function() {
    // Extract the user-typed content only
    let userContent = $(this).val().replace(/{{.*?}}/g, "").trim(); 
    const remainingCount = maxCount - userContent.length;

    // Ensure the count doesn't go below 0
    if (remainingCount >= 0) {
        $("#abd_counting").text(remainingCount);
    } else {
        // If the count goes below 0, trim the user-typed content
        userContent = userContent.substring(0, maxCount);
        $(this).val(userContent + $(this).val().match(/{{.*?}}/g)?.join(" ") || "");
        $("#abd_counting").text(0);
    }
});
$("#full_order_message").on("input", function() {
    // Extract the user-typed content only
    let userContent = $(this).val().replace(/{{.*?}}/g, "").trim(); 
    const remainingCount = maxCount - userContent.length;

    // Ensure the count doesn't go below 0
    if (remainingCount >= 0) {
        $("#full_counting").text(remainingCount);
    } else {
        // If the count goes below 0, trim the user-typed content
        userContent = userContent.substring(0, maxCount);
        $(this).val(userContent + $(this).val().match(/{{.*?}}/g)?.join(" ") || "");
        $("#full_counting").text(0);
    }
});

$(".dynamic_feild").on("click", function() {
    const val = `{{` + $(this).val() + `}}`;
    var message_val;
    
    const new_order = !$("#new_order_div").hasClass("hidden");
    const abd_order = !$("#abd_order_div").hasClass("hidden");
    const full_order = !$("#full_order_div").hasClass("hidden"); 

    if(new_order)
    {
        message_val = $("#new_order_message");
        
    }
    if(abd_order)
    {
        message_val = $("#abd_order_message");
        
    }
    if(full_order)
    {
        message_val = $("#full_order_message");
        
    }
      // Get the current value, cursor position, and current scroll position
      const message = message_val.val();
      const cursorPosition = message_val[0].selectionStart;
      const currentScroll = message_val.scrollTop();
  
      // Insert the dynamic field value at the cursor position
      const newMessage = message.slice(0, cursorPosition) + val + message.slice(cursorPosition);
      message_val.val(newMessage);
  
      // Restore focus and move the cursor to after the inserted text
      message_val.focus();
      message_val[0].selectionStart = message_val[0].selectionEnd = cursorPosition + val.length;
  
      // Scroll back to the previous scroll position
      message_val.scrollTop(currentScroll);
});



$("#new_order_btn").on("click", function(){
    $("#new_order_div").removeClass("hidden");
    $("#abd_order_div").addClass("hidden");
    $("#full_order_div").addClass("hidden");
    $("#new_dynamic").removeClass("hidden");
    $("#abd_dynamic").addClass("hidden");
    $("#full_dynamic").addClass("hidden");
});

$("#abd_order_btn").on("click", function(){
    $("#new_order_div").addClass("hidden");
    $("#full_order_div").addClass("hidden");
    $("#abd_order_div").removeClass("hidden");
    $("#new_dynamic").addClass("hidden");
    $("#full_dynamic").addClass("hidden");
    $("#abd_dynamic").removeClass("hidden");
});
$("#full_order_btn").on("click", function(){
    $("#new_order_div").addClass("hidden");
    $("#abd_order_div").addClass("hidden");
    $("#full_order_div").removeClass("hidden");
    $("#new_dynamic").addClass("hidden");
    $("#abd_dynamic").addClass("hidden");
    $("#full_dynamic").removeClass("hidden");
});
$('.save_msg').on("click", function() {
    const btn = $(this);
    btn.attr("disabled", true);
    const new_order_msg = $('#new_order_message').val().trim();
    const abd_order_msg = $('#abd_order_message').val().trim();
    const full_order_msg = $('#full_order_message').val().trim();
    $(this).attr("disabled", true);

    const data = {
        "api_key": api_key,
        "route"  : "update_message",
        "data"   : {
            "new_message": new_order_msg,
            "abd_message": abd_order_msg,
            "full_message": full_order_msg
        }
    };
    var ajaxRequest = $.ajax({
        url: apiUrl,
        type: 'POST',
        dataType: 'json',
        data: JSON.stringify(data),
        success: function(response) {
            if (response.status == "success") {
                toastr['success']("Message updated!");
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('Error:', textStatus, errorThrown);
            toastr['error']("Unknown error. Retry!");
        },
        complete: function(jqXHR, textStatus) {
            btn.removeAttr("disabled");
        }
    });

    pendingAjaxRequests.push(ajaxRequest);
});
// Function to check if the screen size is mobile based on width and height
function checkScreenSize() {
    // Check if the width is less than the height
    if (window.innerWidth < window.innerHeight) {
        alert("Use your Desktop to setup this app");
        location.reload(true);
    }
}

// Event listener for window resize
window.addEventListener('resize', checkScreenSize);

