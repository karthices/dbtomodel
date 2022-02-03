var smartUI = (function() {

    function $ajax(url, data, successcb, errorcb) {

        var callfn = function(data) {

            var req = new XMLHttpRequest();

            req.onload = function() {
                if (req.status != 200 && req.readyState == 4) { // analyze HTTP status of the response
                    if (req.status == 503 && errorcb && typeof(errorcb) == "function") return errorcb(this.responseText);
                    return errorcb(this.statusText);
                } else { // show the result
                    var rettext = this.responseText;
                    successcb(rettext);
                }
            };

            req.onerror = function() {
                errorcb(this.responseText);
            };

            req.open("POST", url, true);
            req.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
            req.send(JSON.stringify(data));

        }

        callfn(data);
    }

    return {
        $ajax: $ajax,

    }
}());