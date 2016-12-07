<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>选择房间</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0" name="viewport">
    <link rel="stylesheet" type="text/css" href="/aaa/static/css/socket/style.css"/>
    <script src="/aaa/static/javascript/jquery-1.7.2.js"></script>
    <script src="/aaa/static/javascript/ajaxfileupload.js"></script>
    <script src="/aaa/static/javascript/socket/node_modules/socket.io-client/dist/socket.io.js"></script>
</head>
<body>
<!--<div style="float: left;height: 100%;width: 10%;padding-right: 2px;position: fixed;">
    <div style="background:#3d3d3d;height: 28px; width: 100%;font-size:12px;">
        <div style="line-height: 28px;color:#fff;">
            <span style="text-align:left;margin-left:10px;" id="onlineCount">好友列表：</span>
            <div id="onlineUser" style="background:#EFEFF4; font-size:12px; margin-top:10px; margin-left:10px; color:#666;">
                <ul>
                    <?php /*foreach ($friends as $val) {
                        echo "<li><a href='go_chat?id={$val['id']}'>{$val['username']}</a></li>";
                    }
                    */?>
                </ul>
            </div>
        </div>
    </div>
</div>-->
<div id="chatbox" style="width: 100%;float: right;">
    <div style="background:#3d3d3d;height: 28px; width: 100%;font-size:12px;">
        <div style="line-height: 28px;color:#fff;">
            <span style="text-align:left;margin-left:10px;">房间列表</span>
            <span style="float:right; margin-right:10px;"><span id="showusername"><?=$username?></span> |
			<a href="javascript:logout();" style="color:#fff;">退出</a></span>
        </div>
    </div>
    <div style="width: 100%;">
        <table border="1px;" style="border-color: #2e8ece;margin: 10px 10px 10px 10px;" cellspacing="10">
            <?php
            foreach($rooms as $k=>$val){
                if($k%10==0){
                    echo "<tr style='height: 100px;'>";
                    $j = $k+10;
                }
                echo "<td style='width: 150px;text-align: center;'><a href='go_chat?id={$val['id']}&name={$val['name']}' >{$val['name']}</a></td>";
                if($k== $j && $k%10==0){
                    echo "</tr>";
                }
            }?>
        </table>
    </div>
</div>
</body>
<script>
    function logout(){
        $.get('logout',{},function(){
            location.href="index";
        });
    }

    function join_room(room_id){
//        $.get('join_room',{room:room_id},function(){});
        var name =
        location.href="go_chat?id="+room_id;
    }
</script>
</html>