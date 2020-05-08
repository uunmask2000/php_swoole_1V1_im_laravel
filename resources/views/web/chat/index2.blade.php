<!DOCTYPE HTML>

<html>
<title>客服端</title>
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
                'id': id,
                'user_id': $('#set_people').text(),
                data: data
            }
            // console.log(msg)
            if (data != "") {
                // $("#msg").append('<li>Server  : ' + data + '</li>');
                $("#user_id_" + $('#set_people').text()).append('<li>Server  : ' + data + '</li>');
                // $("#" + id).append('<li>Server  : ' + data + '</li>');

            }
            ws.send(JSON.stringify(msg));

            // console.log($('#set_people').text())

            var div_id = 'user_id_' + $('#set_people').text();
            console.log(div_id)

            var div = document.getElementById(div_id);
            div.scrollTop = div.scrollHeight; //这里是关键的实现
        }



        // var id = getCookie('id') != "" ? getCookie('id') : setCookie('id', new Date().getTime(), 1);
        var id = 2;


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
                if ($("#user_id_" + received_msg.id).length == 0) {
                    //do something 
                    var id = "user_id_" + received_msg.id;
                    create_msg(id);
                }


                if (received_msg.data != "") {
                    // $("#msg").append('<li>客戶來訊 : ' + received_msg.data + '</li>'); 
                    $('#user_id_' + received_msg.id).append('<li> user_id_' + received_msg.id + '  客戶來訊 : ' +
                        received_msg.data + '</li>');

                    var __body = 'user_id_' + received_msg.id;
                    var div = document.getElementById(__body);
                    div.scrollTop = div.scrollHeight; //这里是关键的实现
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

        function __close() {

            var txt;
            var r = confirm("確認是否要結束通話???");
            if (r == true) {
                var msg = {
                    'id': id,
                    'user_id': $('#set_people').text(),
                    "data": {
                        event: "close"
                    }
                }
                ws.send(JSON.stringify(msg));
                location.reload();
            }


        }

        function Set(d) {
            var id = d;
            if ($('#' + id).length == 0) {

                // alert(d)
                create_msg(id);
            }


        }

        function create_msg(d) {
            var d = d.replace('user_id_', '').replace('user_id_', '');
            // 切換客戶標籤
            $('#set_people').html(d);

            $('#sse').append('<h1>' + d + ' 客戶 </h1><hr><ul id="user_id_' + d + '" class="dropdown nav"></ul><hr>');
            //
            if ($('#' + d + " > li").length == 0) {
                $.getJSON("http://103.117.121.146:10182/api/getlist/" + d, function (result) {
                    // $.each(result, function (i, field) {
                    //     $("p").append(field + " ");
                    // });
                    // console.log(result);
                    result.data.lists.forEach(element => {
                        var element = JSON.parse(element);
                        var msg = JSON.parse(element.msg);
                        // console.log(msg);
                        // 
                        switch (msg.id) {
                            case 2:
                                $('#user_id_' + d).append('<li>Server : ' + msg.data + '</li>');
                                break;
                            default:
                                $('#user_id_' + d).append('<li>  user_id_' + d + ' 客戶來訊 : ' + msg.data +
                                    '</li>');
                                break;
                        }

                    });
                    var __body = 'user_id_' + d;
                    var div = document.getElementById(__body);
                    div.scrollTop = div.scrollHeight; //这里是关键的实现

                });
            }
        }

        function getlist() {

            $.getJSON("http://103.117.121.146:10182/api/lists", function (result) {
                // $.each(result, function (i, field) {
                //     $("p").append(field + " ");
                // });
                // console.log(result);
                $('#lists').html("");
                result.data.lists.forEach(element => {
                    $('#lists').append('<tr><td>' + element + '</td><td><button onclick="Set(\'' +
                        element +
                        '\');">設定</button></td></tr>')
                });
            });
        }
        getlist();
        setInterval(getlist, 3000);

    </script>

</head>

<body>
    <div class="scrollit">
        <table id="lists"></table>
    </div>
    <div>
        <h2>點選客戶</h2>
        <h3 id="set_people"></h3>
    </div>
    <div id="sse">
    </div>
    <textarea name="mes" placeholder="type message here..." id="msgtext"></textarea>

    <button onclick="send()">送出</button>
    <button onclick="__close()">關閉對話</button>


</body>

</html>
