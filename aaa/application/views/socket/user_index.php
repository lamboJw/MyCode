<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>个人主页</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0" name="viewport">
    <script src="/aaa/static/javascript/jquery-1.7.2.js"></script>
    <script src="/aaa/static/javascript/ajaxfileupload.js"></script>
    <link rel="stylesheet" type="text/css" href="/aaa/static/css/socket/style.css"/>
    <style>
        .head_file {
            position: relative;
            display: inline-block;
            overflow: hidden;
            color: #1E88C7;
            text-decoration: none;
            text-indent: 0;
            line-height: 20px;
            height: 100px;
        }
        .head_file input {
            position: absolute;
            font-size: 100px;
            right: 0;
            top: 0;
            opacity: 0;
        }
        .head_file:hover {
            text-decoration: none;
        }
    </style>
</head>
<body>
<div style="background:#3d3d3d;height: 28px; width: 100%;font-size:12px;">
    <div style="line-height: 28px;color:#fff;">
        <span style="text-align:left;margin-left:10px;">个人主页</span>
            <span style="float:right; margin-right:10px;"><span id="showusername"><a href="user_index"><?=$username?></a></span> |
			<a href="javascript:logout();" style="color:#fff;">退出</a></span>
    </div>
</div>
<div>
    <div style="width:360px;margin:200px auto;text-align: center;">
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
                <td style="text-align: right">用户名：</td>
                <td><input type="text" style="width:180px;" placeholder="请输入用户名" id="username" name="username" value="<?php echo $username;?>"/></td>
            </tr>
            <tr>
                <td style="text-align: right">真实姓名：</td>
                <td><input type="text" style="width: 180px;" placeholder="请输入真实姓名" id="realname" name="realname" value="<?php echo $realname?>"/></td>
            </tr>
            <tr>
                <td style="text-align: right">生日：</td>
                <td><input type="text" style="width: 180px;" placeholder="请输入生日" id="birthday" name="birthday" value="<?php echo $birthday?>"/></td>
            </tr>
            <tr>
                <td><input type="submit" style="width:50px;" value="提交" onclick="javascript:save();"/></td>
                <td><input type="button" style="width:50px;" value="返回" onclick="window.history.back();"/></td>
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
</script>
</html>