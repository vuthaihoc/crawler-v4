var page = require('webpage').create(),
    system = require('system'),address;

address = system.args[1];
var headers = [];
var errors = [];
var last_url = '';
var last_code = 0;

page.onError = function (msg, trace) {
    errors.push(msg);
}

page.settings.userAgent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.117 Safari/537.36";
page.settings.loadImages = false;

page.onResourceReceived = function(response) {
    // console.log(response.url, address);
    // console.log('Response (#' + response.id + ', stage "' + response.stage + '"): ' + JSON.stringify(response));
    // if (address.replace(/\/$/, "") === response.url.replace(/\/$/, "")){
    //     console.log(response.url);
    // }
    if (response.url === last_url){
        headers = response.headers;
        last_code = response.status;
    }
};

page.onNavigationRequested = function(url, type, willNavigate, main) {
    last_url = url;
    last_code = response.status;
};

page.open(address, function(status) {
    var response = {
        headers : parseHeader(headers),
        status : last_code,
        status_text : status,
        url : last_url,
        errors : errors,
        html: ""
    };
    // console.log(JSON.stringify(response));
    if (status === 'success') {
        response.html = page.evaluate(function() {
            return document.documentElement.innerHTML;
        });
    }
    console.log(JSON.stringify(response));
    phantom.exit();
});

function parseHeader(headers) {
    var max_i = headers.length, _headers = {};
    for (var i = 0; i < max_i; i++ ){
        _headers[headers[i].name] = headers[i].value;
    }
    return _headers;
}