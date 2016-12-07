<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="format-detection" content="email=no"/>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0"
          name="viewport">
    <title>多人聊天室</title>
    <link rel="stylesheet" type="text/css" href="/aaa/static/css/socket/style.css"/>
    <script src="/aaa/static/javascript/jquery-1.7.2.js"></script>
    <script src="/aaa/static/javascript/ajaxfileupload.js"></script>
    <script src="/aaa/static/javascript/socket/node_modules/socket.io-client/dist/socket.io.js"></script>
</head>
<body>
<div style="float: left;height: 100%;width: 10%;padding-right: 1px;position: fixed;">
    <div style="background:#3d3d3d;height: 28px; width: 100%;font-size:12px;">
        <div style="line-height: 28px;color:#fff;">
            <span style="text-align:left;margin-left:10px;" id="onlineCount">在线人数：</span>
            <div id="onlineUser" style="background:#EFEFF4; font-size:12px; margin-top:10px; margin-left:10px; color:#666;">
                <ul>

                </ul>
            </div>
        </div>
    </div>
</div>
<div id="chatbox" style="width: 90%;float: right;">
    <div style="background:#3d3d3d;height: 28px; width: 100%;font-size:12px;">
        <div style="line-height: 28px;color:#fff;">
            <span style="text-align:left;margin-left:10px;" id="room_name"></span>
            <span style="float:right; margin-right:10px;"><span id="showusername"></span> |
			<a href="javascript:logout();" style="color:#fff;">退出</a></span>
        </div>
    </div>
    <div id="doc">
        <div id="chat">
            <div id="message" class="message">

            </div>
            <div class="input-box">
                <div class="input">
                    <textarea name="content" id="content" cols="30" rows="10" placeholder="请输入聊天内容，按Ctrl+Enter提交"></textarea>
<!--                    <input type="text" maxlength="140" placeholder="请输入聊天内容，按Enter提交" id="content" name="content">-->
                </div>
                <div class="action">
                    <form id="imageform" method="post" action="UploadImg" name="userfile" style="display: inline;margin-right: 10px;">
                        <a href="javascript:void();" class="file">
                            <input id="imgSelect" type="file" name="image" accept="image/*">
                        </a>
                    </form>
                    <button type="button" id="mjr_send" onclick="CHAT.submit();">提交</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="/aaa/static/javascript/socket/socket_client.js"></script>
</body>
<script>
    CHAT.setUerid(<?php echo $uid; ?>);
    var room = <?php echo $room ?>;
    var name = '<?php echo $name ?>';
    if(room!=null && room != ""){
        CHAT.setRoom(room,name);
    }
    CHAT.usernameSubmit('<?php echo $username; ?>');

    function logout(){
        $.get('logout',{},function(){
            CHAT.logout()
        });
    }

    var myupload = function(){
        $("#imgSelect").off('change');
        $.ajaxFileUpload({
            url:'ajaxUploadGoods',
            secureuri:false,
            fileElementId:"imgSelect",
            dataType: 'json',
            type: 'post',
            data:{},
            success: function (data){
                if(data.code == '200'){
                    CHAT.sendImage(data);
                }
                else
                {
                    alert(data.message);
                }
                $("#imgSelect").on("change",myupload);
            },
            error: function (data, status, e)//服务器响应失败处理函数
            {
                alert(e);
            }
        });
    }


    $("#imgSelect").on("change",myupload);
</script>
</html>
