<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>个人主页</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0" name="viewport">
    <script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="/aaa/static/javascript/ajaxfileupload.js"></script>
    <link rel="stylesheet" type="text/css" href="/aaa/static/css/socket/style.css"/>
</head>
<body background="/aaa/static/images/user_bg.jpg">
<div class="top_bar">
    <div class="top_bar_text">
        <span class="text_left">个人主页</span>
            <span class="text_right"><span id="showusername"><a href="user_index"><?=$username?></a></span> |
			<a href="javascript:logout();" style="color:#fff;">退出</a></span>
    </div>
</div>
<div>
    <div class="box">
        <table>
            <tr height="100px">
                <td style="text-align: right">头像：</td>
                <td>
                    <form id="imageform" method="post" action="" name="userfile" style="display: inline;margin-right: 10px;">
                        <a href="javascript:void();" class="head_file">
                            <img id="headimg" src="<?php echo $headimg;?>" style="width: 100px;height: 100px;">
                            <input id="imgSelect" type="file" name="image" accept="image/*">
                        </a>
                    </form>
                </td>
            </tr>
            <tr>
                <td class="text">用户名：</td>
                <td class="input"><input type="text" placeholder="请输入用户名" id="username" name="username" value="<?php echo $username;?>"/></td>
            </tr>
            <tr>
                <td class="text">真实姓名：</td>
                <td class="input"><input type="text" placeholder="请输入真实姓名" id="realname" name="realname" value="<?php echo $realname?>"/></td>
            </tr>
            <tr>
                <td class="text">生日：</td>
                <td class="input"><input type="text" placeholder="请输入生日" id="birthday" name="birthday" value="<?php echo $birthday?>"/></td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="submit" style="width:50px;" value="提交" onclick="javascript:save();"/>
                    <input type="button" style="width:50px;" value="返回" onclick="window.history.back();"/>
                </td>
            </tr>
        </table>
    </div>
</div>
</body>
<script>
    var myupload = function(){
        $("#imgSelect").off('change');
        $.ajaxFileUpload({
            url:'ajaxUploadHeadimg',
            secureuri:false,
            fileElementId:"imgSelect",
            dataType: 'json',
            type: 'post',
            data:{},
            success: function (data){
                if(data.code == '200'){
                   $("#headimg").attr("src",data.url);
                    alert("头像保存成功");
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

    function save(){
        var username = $("#username").val();
        var realname = $("#realname").val();
        var birthday = $("#birthday").val();
        $.ajax({
            url:"save_info",
            dataType:"json",
            type:"get",
            data:{username:username,realname:realname,birthday:birthday},
            success:function(data){
                if(data.code == 200){
                    alert("保存成功");
                }else{
                    alert(data.msg);
                }
            }
        });
    }

    function logout(){
        $.get('logout',{},function(){
            location.href="index";
        });
    }
</script>
</html>