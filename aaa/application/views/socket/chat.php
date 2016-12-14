<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="format-detection" content="email=no"/>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0"
          name="viewport">
    <title><?php echo $name ?></title>
    <link rel="stylesheet" type="text/css" href="/aaa/static/css/socket/style.css"/>
    <script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="/aaa/static/javascript/ajaxfileupload.js"></script>
    <script src="/aaa/static/javascript/socket/node_modules/socket.io-client/dist/socket.io.js"></script>
</head>
<body background="/aaa/static/images/chat_bg.jpg">
<div id="count">
    <div class="top_bar">
        <div class="top_bar_text">
            <span id="onlineCount">在线人数：</span>
            <div id="onlineUser">
                <ul>

                </ul>
            </div>
        </div>
    </div>
</div>
<div id="chatbox">
    <div class="top_bar">
        <div class="top_bar_text">
            <span id="room_name" class="text_left"></span>
            <span class="text_right"><span id="showusername"></span> |
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
                    <form id="imageform" method="post" action="UploadImg" name="userfile">
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
<div id="fullbg"></div>
<div id="dialog">
    <p class="close"><a href="javascript:void(0);" onclick="closeBg();">×</a></p>
    <div>
        <table style="margin: auto">
            <tr height="100px">
                <td class="text">头像：</td>
                <td>
                    <img id="headimg" src="" style="width: 100px;height: 100px;">
                </td>
            </tr>
            <tr>
                <td class="text">用户名：</td>
                <td class="input" id="username"></td>
            </tr>
            <tr>
                <td class="text">真实姓名：</td>
                <td class="input" id="realname"></td>
            </tr>
            <tr>
                <td class="text">生日：</td>
                <td class="input" id="birthday"></td>
            </tr>
        </table>
    </div>
</div>
<script type="text/javascript" src="/aaa/static/javascript/socket/socket_client.js"></script>
</body>
<script>
    CHAT.setUerInfo(<?php echo $uid; ?>,'<?php echo $headimg?>','<?php echo $username ?>');
    var room = <?php echo $room ?>;
    var name = '<?php echo $name ?>';
    if(room!=null && room != ""){
        CHAT.setRoom(room,name);
    }
    CHAT.usernameSubmit();

    function logout(){
        $.get('logout',{},function(){
            CHAT.logout()
        });
    }


    var myupload = function(){
        $("#imgSelect").off('change');
        $.ajaxFileUpload({
            url:'ajaxUploadPhoto',
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

    function showBg(uid) {
        $.ajax({
            url:"user_index",
            dataType:"json",
            type:"get",
            data:{ajax:true,uid:uid},
            success:function(data){
                if(data.code == 200){
                    $("#headimg").attr("src",data.data.headimg);
                    $("#username").text(data.data.username);
                    $("#realname").text(data.data.realname);
                    $("#birthday").text(data.data.birthday);
                    var bh = $("#count").height();
                    var bw = $("body").width();
                    $("#fullbg").css({
                        height:bh,
                        width:bw,
                        display:"block"
                    });
                    $("#dialog").show();
                }else{
                    alert(data.msg);
                }
            }
        });

    }
    //关闭灰色 jQuery 遮罩
    function closeBg() {
        $("#fullbg,#dialog").hide();
    }
</script>
</html>
