/**
 *
 */
var rpcPath = "rpc.php?file=";
var lastSize = [];
var filter = [];
var currentFilterButton = null;

/**
 * Call RPC
 */
function callRpc(button, file, callback) {
    $("#" + button).html('<i class="fa fa-spinner fa-spin"></i> loading').addClass("disabled");
    $.ajax({
        type: "GET",
        url: rpcPath + file + '&lastsize=' + lastSize[file] + getFilter(file),
        success: function(msg){
            var obj = jQuery.parseJSON(msg);
            lastSize[file] = obj.lastsize;
            $.each(obj.data, function(key, value) {
                $("#" + callback).append('<nobr>' + value + '</nobr><br />');
            });
            $("#" + callback).animate({ scrollTop: $("#" + callback)[0].scrollHeight}, 1000);
            $("#" + button).html('<i class="fa fa-refresh"></i>').removeClass("disabled");
        }
    });

}

/**
 * If Filter for file exists, return it
 */
function getFilter(file) {
    if(filter[file] === undefined || filter[file].length == 0) {
        return '';
    } else {
        return '&filter=' + filter[file]; 
    }
}

function clickAllButtons() {
    //$(":button").click();
    $(".autorefresh").click();
}

function refreshPause(button, buttonpause) {
    var btn = $("#" + button);
    var btnpause = $("#" + buttonpause);
    if(btn.hasClass("autorefresh")) {
        btn.removeClass("autorefresh");
        btnpause.html('<i class="fa fa-play"></i> autorefresh paused').addClass("btn-primary");
    } else {
        btn.addClass("autorefresh");
        btnpause.html('<i class="fa fa-pause"></i>').removeClass("btn-primary");
    }
}

function setFilter(file, filename, button) {
    currentFilterButton = button;
    $('#filterModal .modal-title').html("Set Filter for " + filename);
    $('#filterFile').val(file);
    if(filter[file] != undefined && filter[file].length>0) {
        $('#filterValue').val(filter[file]);
    } else {
        $('#filterValue').val('');    
    }
    $('#filterModal').modal('show');
}

function writeFilter() {
    filterVal = $('#filterValue').val();
    fileNumber = $('#filterFile').val();
    
    if(filterVal != '') {
        filter[fileNumber] = filterVal;
        $('#' + currentFilterButton).html('<i class="fa fa-filter"></i> "' + filterVal + '"').addClass("btn-info");
    } else {
        filter[fileNumber] = '';   
        $('#' + currentFilterButton).html('<i class="fa fa-filter"></i>').removeClass("btn-info");
    }
    console.log(filter);
    $('#filterModal').modal('hide');
    
    $('#filterValue').val('');
    $('#filterFile').val('');
}