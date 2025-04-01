
openOnair = function() {
    $.post('system/box/onair.php', {}, function(response) {
        showEmptyModal(response, 360);
    });
}
userOnair = function() {
    $.post(FU_Ajax_Requests_File(), {
        f: 'action_member',
        s: 'user_onair',
        token: utk,
        user_onair: $('#set_user_onair').val(),
    }, function(res) {
        if (res.status == 200) {
            $("#broadcast_windows").dialog("close");
            initializeDialog('onair');
            callSaved(res.msg, 1);
        } else if (res.status == 100) {
            $("#broadcast_windows").dialog("close");
            callSaved(res.msg, 3);
        } else {
            $("#broadcast_windows").dialog("close");
            callSaved(res.msg, 3);
        }

    });

}
start_dj = function(media_type, media_url) {
    $.post(FU_Ajax_Requests_File(), {
        f: 'action_member',
        s: 'start_dj',
        token: utk,
        media_type: media_type,
        media_url: media_url,
    }, function(res) {
        if (res.status == 200) {
           callSaved(res.msg, 1);
        } else if (res.status == 100) {
            //callSaved(res.msg, 3);
        } else {
            //callSaved(res.msg, 3);
        }

    });

}
end_dj = function(end) {
    $.post(FU_Ajax_Requests_File(), {
        f: 'action_member',
        s: 'end_dj',
        token: utk,
        end: end,
    }, function(res) {
        if (res.status == 200) {
            $('#mediaUrl').val('');
            callSaved(res.msg, 1);
        } else {
            callSaved(res.msg, 3);
        }

    });
    console.clear();
}
broadcast_box = function(elm) {
    $.post('system/dj/admin_broadcast.php', {
        user_onair: $('#set_user_onair').val(),
    }, function(res) {
        $(elm).html(res)
    });
}
// Function to initialize the dialog
function initializeDialog(ref) {
    $("#broadcast_windows").dialog({
        draggable: true,
        resizable: true,
        modal: false, // Non-modal dialog
        autoOpen: false, // Prevent auto-open on page load
        width: $(window).width() <= 600 ? '100%' : 420,
        open: function(event, ui) {
            broadcast_box(this); // Call the onOpen function when the dialog opens
            $(this).dialog("option", "title", "DJ Control Panel"); // Update the dialog title
        },
        buttons: {
            "Close": function() {
                $(this).dialog("close");
            }
        }
    });

    $("#open-boradcast_panel").click(function() {
        $("#broadcast_windows").dialog("open");
    });
    if(ref=='onair'){
         $("#broadcast_windows").dialog("open");
    }
}

function validateUrl(url, type) {
    var regex;
    switch (type) {
        case 'youtube':
            // Regex for YouTube URLs
            regex = /^(https?:\/\/(www\.)?(youtube\.com\/(?:watch\?v=|embed\/|v\/|shorts\/)|youtu\.be\/))[\w-]+(?:\?.*)?$/;
            break;
        case 'soundcloud':
            // Regex for SoundCloud URLs
            regex = /^https?:\/\/(www\.)?soundcloud\.com\/[\w-]+\/[\w-]+$/;
            break;
        case 'mp4':
            // Regex for MP4 URLs
            regex = /^https?:\/\/.+\.mp4$/;
            break;
        case 'mp3':
            // Regex for MP3 URLs
            regex = /^https?:\/\/.+\.mp3$/;
            break;
        default:
            return false;
    }

    return regex.test(url);
}

function detectMediaType(url) {
    var type;
    // Define regex patterns for different media types
    var youtubeRegex = /^(https?:\/\/(www\.)?(youtube\.com\/(?:watch\?v=|embed\/|v\/|shorts\/)|youtu\.be\/))[\w-]+(?:\?.*)?$/;
    var soundcloudRegex = /^https?:\/\/(www\.)?soundcloud\.com\/[\w-]+\/[\w-]+$/;
    var mp4Regex = /^https?:\/\/.+\.mp4$/;
    var mp3Regex = /^https?:\/\/.+\.mp3$/;
    // Determine the media type based on the URL pattern
    if (youtubeRegex.test(url)) {
        type = 'youtube';
    } else if (soundcloudRegex.test(url)) {
        type = 'soundcloud';
    } else if (mp4Regex.test(url)) {
        type = 'mp4';
    } else if (mp3Regex.test(url)) {
        type = 'mp3';
    } else {
        type = ''; // No valid type detected
    }

    return type;
}

function validateMediaUrl(is_live) {
    var mediaType = $('#mediaType').val(); // Get selected media type
    var mediaUrl = $('#mediaUrl').val().trim(); // Get media URL
    var media_alert = $('#media_alert');
    if (!mediaUrl && is_live =='0') {
        media_alert.html('Please enter a media URL.');
        return false;
    }
    
    if (!validateUrl(mediaUrl, mediaType) && is_live =='0') {
            media_alert.html('The entered URL does not match the selected media type.');
            return false;
    }
    console.log('Broadcasting media:', mediaType, mediaUrl,is_live);
    // Add functionality to start broadcasting
    return true;
}

$(document).ready(function() {
    var is_live = $('#is_livestream').val();
    // Listen for changes in the media URL input field
    $(document).on('input', '#mediaUrl', function() {
        var url = $(this).val().trim();
        if(is_live=='0'){
            is_live =0;
            var detectedType = detectMediaType(url);
            if (detectedType) {
                $('#mediaType').selectBoxIt('selectOption', detectedType);
            } else {
                $('#mediaType').selectBoxIt('selectOption', ''); // Optionally, clear the selection if no valid type detected
        }
        }  
    });

    $(document).on('change', '#mediaType', function() {
        if (is_live === '0') {
            validateMediaUrl(is_live);
        }
    });
    // Validate URL when the broadcast button is clicked
        $(document).on('click', '#broadcastBtn', function() {
            var is_live = $('#is_livestream').val(); // Make sure this returns the correct value
            // Validate media URL and type only if not livestream
            if (is_live === '0') {
                if(validateMediaUrl(is_live)) {
                    // Retrieve media type and URL from the form
                    var mediaType = $('#mediaType').val();
                    var mediaUrl = $('#mediaUrl').val().trim();
                    // Call the start_dj function with the validated data
                    start_dj(mediaType, mediaUrl);
                }
            }else if (is_live === '1') {
                alert('is live');
                start_dj("live", "null");
            }
        });


    $(document).on('click', '#end_broadcast', function() {
        end_dj("end");
    });

    initializeDialog();
});