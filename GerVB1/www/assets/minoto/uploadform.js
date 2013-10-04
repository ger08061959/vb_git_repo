// Set up the progressbar
$(document).ready(function() {
 $("#progressBar").progressbar({value: 0});
});

// Fade progressbar in and start polling for upload progress
function beginUpload() {
    $("#progressArea").fadeIn();
    setTimeout("showUpload()", 3000);
}

function showUpload() {
    $.getJSON('', function(response) {
        if (!response)
            return;
        var percentage = Math.floor(100 * parseInt(response.upload_bytes_uploaded) / parseInt(response.upload_bytes_total));
        $("#progressBar").progressbar('option', 'value', percentage);
        $('#percentage').html(percentage + '%');
        $('#remaining').html('Approximately ' + formatRemaining(response.upload_time_remaining) + ' remaining.');
        
    });
	setTimeout("showUpload()", 3000);    
}

function formatRemaining(seconds) {
    if(seconds < 60) {
        return seconds + ' seconds';
    }

    var minutes = Math.floor(seconds / 60);
    seconds = seconds % 60;

    if(minutes < 60) {
        return minutes + ' minutes, ' + seconds + ' seconds';
    }

    var hours = Math.floor(minutes / 60);
    minutes = minutes % 60;

    return hours + ' hours, ' + minutes + ' minutes';
}