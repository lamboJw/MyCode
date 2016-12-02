<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>登陆</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0" name="viewport">
    <link rel="stylesheet" type="text/css" href="/aaa/static/css/socket/style.css"/>
    <script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
</head>
<body>
<div id="loginbox">
    <div style="width:360px;margin:200px auto;text-align: center;">
        <span>请先登录</span><br/>
        <span style="font-size: 12px">你还没有帐号？马上<a onclick="javascript:change();">注册</a></span>
        <span style="display: none;font-size: 12px;color: red;" id="msg"></span>
        <div id="login" style="display: block" class="on">
            <span style="display: none;font-size: 12px;color: red;" id="msg"></span>
            <br/>
            <br/>
            <table>
                <tr>
                    <td style="text-align: right">用户名：</td>
                    <td><input type="text" style="width:180px;" placeholder="请输入用户名" id="username" name="username"/></td>
                    <td><span style="color: red;font-size: 12px;" id="username_error"></span></td>
                </tr>
                <tr>
                    <td style="text-align: right">密码：</td>
                    <td><input type="password" style="width: 180px;" placeholder="请输入密码" id="password" name="password"/></td>
                    <td><span style="color: red;font-size: 12px;" id="password_error"></span></td>
                </tr>
                <tr>
                    <td colspan="3"><input type="submit" style="width:50px;" value="提交" onclick="javascript:login();"/></td>
                </tr>
            </table>
        </div>
        <div id="regist" style="display: none" class="">
            <br/>
            <br/>
            <table>
                <tr>
                    <td style="text-align: right"><span style="color: red;">*</span>用户名：</td>
                    <td><input type="text" style="width:180px;" placeholder="请输入用户名" id="username1" name="username1"/></td>
                    <td><span style="color: red;font-size: 12px;" id="username1_error"></span></td>
                </tr>
                <tr>
                    <td style="text-align: right"><span style="color: red;">*</span>密码：</td>
                    <td><input type="password" style="width: 180px;" placeholder="请输入密码" id="password1" name="password1"/></td>
                    <td><span style="color: red;font-size: 12px;" id="password1_error"></span></td>
                </tr>
                <tr>
                    <td style="text-align: right"><span style="color: red;">*</span>确认密码：</td>
                    <td><input type="password" style="width: 180px;" placeholder="请再次输入密码" id="password2" name="password2"/></td>
                    <td><span style="color: red;font-size: 12px;" id="password2_error"></span></td>
                </tr>
                <tr>
                    <td colspan="3">
                        <input type="submit" style="width: 50px;" value="注册" onclick="javascript:register()">
                        <input type="button" style="width: 50px;" onclick="javascript:change()" value="返回">
                    </td>
                </tr>
            </table>
        </div>

    </div>
</div>
</body>
<script>
    function login(){
        $.get("login",{username:$("#username").val(),password:$("#password").val()},function(data){
            data = $.parseJSON(data);
            if(data.code == 200){
                location.href="choose_room";
            } else{
                $("#username_error").text(data.msg);
            }
        })
    }

    function register(){
        var username = $("#username1").val();
        var password = $("#password1").val();
        var password1 = $("#password2").val();
        if(username == ""){
            $("#username1_error").text("用户名不能为空");
            return false;
        }
        if(password == ""){
            $("#password1_error").text("密码不能为空");
            return false;
        }
        if(password1 == ""){
            $("#password2_error").text("确认密码不能为空");
            return false;
        }
        if(password != password1){
            $("#password2_error").text("两次密码不相同");
            return false;
        }
        $.get("register",{username:username,password:password,password1:password1},function(data){
            data = $.parseJSON(data);
            if(data.code == 200){
                alert("注册成功");
                change();
            }else{
                switch (data.code){
                    case 300:{$("#username1_error").text(data.msg);break;}
                    case 301:{$("#password1_error").text(data.msg);break;}
                    case 302:{$("#password2_error").text(data.msg);break;}
                    case 303:{$("#username1_error").text(data.msg);break;}
                    case 304:{$("#password1_error").text(data.msg);break;}
                    case 305:{$("#password2_error").text(data.msg);break;}
                    case 306:{$("#msg").text(data.msg).css("display","block");break;}
                }
            }
        })
    }

    function change(){
        $(".on").css("display","none").removeClass("on").siblings("div").addClass("on").css("display","block");
    }

</script>
</html>