<!DOCTYPE HTML>

<html>
<title>客戶端</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script src="https://code.jquery.com/jquery-2.2.4.js" integrity="sha256-iT6Q9iMJYuQiMWNd9lDyBUStIq/8PuOW33aOqmvFpqI="
    crossorigin="anonymous"></script>
<link rel="stylesheet" type="text/css" href="css/main.css" />
<script src='js/inputEmoji.js'></script>
<script>
    $(function () {
        $('textarea').emoji({
            place: 'after'
        });
    })
</script>

<head>

    <script type="text/javascript">
        function _uuid() {
            var d = Date.now();
            if (typeof performance !== 'undefined' && typeof performance.now === 'function') {
                d += performance.now(); //use high-precision timer if available
            }
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
                var r = (d + Math.random() * 16) % 16 | 0;
                d = Math.floor(d / 16);
                return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
            });
        }



        var id = getCookie('id') != "" ? getCookie('id') : setCookie('id', _uuid(), 1);
        $(function () {

            $("#name").text('客戶編號 :' + id);
        });


        function delete_cookie(name) {
            document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
        }

        function getCookie(cname) {
            var name = cname + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i].trim();
                if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
            }
            return "";
        }

        function setCookie(cname, cvalue, exdays) {
            var d = new Date();
            d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
            var expires = "expires=" + d.toGMTString();
            document.cookie = cname + "=" + cvalue + "; " + expires;
        }

        function push_data(ws, data, id) {

            var msg = {
                id: id,
                data: data
            }
            if (data != "") {
                $("#msg").append('<li>clinet : ' + data + '</li>');
            }

            if (msg.id == undefined) {
                location.reload();
            }
            // console.log(msg);
            ws.send(JSON.stringify(msg));

            var div = document.getElementById('msg');
            div.scrollTop = div.scrollHeight; //这里是关键的实现
        }






        if ($("#msg > li").length == 0) {
            $.getJSON("http://103.117.121.146:10182/api/getlist/" + id, function (result) {
                // $.each(result, function (i, field) {
                //     $("p").append(field + " ");
                // });
                console.log(result);
                result.data.lists.forEach(element => {
                    var element = JSON.parse(element);
                    var msg = JSON.parse(element.msg);
                    // 
                    switch (msg.id) {
                        case 2:
                            $('#msg').append('<li>Server : ' + msg.data + '</li>');
                            break;
                        case 3:
                            // 强制离线
                            delete_cookie('id');
                            break;
                        default:
                            $('#msg').append('<li>clinet  : ' + msg.data + '</li>');
                            break;
                    }

                });
                var div = document.getElementById('msg');
                div.scrollTop = div.scrollHeight; //这里是关键的实现

            });
        }




        if ("WebSocket" in window) {
            // alert("WebSocket is supported by your Browser!");

            // Let us open a web socket
            var ws = new WebSocket("ws://103.117.121.146:10184/");

            ws.onopen = function () {
                push_data(ws, '', id);
                // Web Socket is connected, send data using send()
                // var push_data = push_data();
                // ws.send("Message to send");
                // alert("Message is sent...");
            };

            ws.onmessage = function (evt) {
                var received_msg = JSON.parse(evt.data);
                // alert("Message is received..." + received_msg);
                if (received_msg.data != "" && typeof received_msg.data !== 'object') {
                    $("#msg").append('<li>Server : ' + received_msg.data + '</li>');

                    var div = document.getElementById('msg');
                    div.scrollTop = div.scrollHeight; //这里是关键的实现

                } else if (typeof received_msg.data === 'object') {
                    // 是object 
                    switch (received_msg.data.event) {
                        case 'close':
                            // 刪除cookie
                            delete_cookie('id');
                            ws.close();
                            break;

                        default:
                            break;
                    }

                }

            };



            ws.onclose = function () {

                // websocket is closed.
                // alert("Connection is closed...");
                location.reload();
            };
        } else {

            // The browser doesn't support WebSocket
            alert("WebSocket NOT supported by your Browser!");
        }

        function send() {
            var msg = document.getElementById("msgtext").value;
            push_data(ws, msg, id);
            // console.log(msg);
        }
    </script>

</head>

<body>
    <div id="name"></div>
    <div id="sse">
        <ul id="msg" class="dropdown nav"></ul>
    </div>
    <textarea name="mes" placeholder="type message here..."  id="msgtext" ></textarea> 
    <button onclick="send()">送出</button>

 
</body>

</html>