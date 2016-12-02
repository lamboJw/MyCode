(function () {
	var d = document,
		w = window,
		p = parseInt,
		dd = d.documentElement,
		db = d.body,
		dc = d.compatMode == 'CSS1Compat',
		dx = dc ? dd: db,
		ec = encodeURIComponent;


	w.CHAT = {
		msgObj:$("#message"),
		//screenheight:w.innerHeight ? w.innerHeight : dx.clientHeight,
		username:null,
		userid:null,
		socket:null,
		room:null,
		room_name:null,
		headimg:null,
		//让浏览器滚动条保持在最低部
		scrollToBottom:function(){
			w.scrollTo(0, this.msgObj.height()+300);
		},
		setUerInfo: function (uid,headimg,username) {
			this.userid = uid;
			this.headimg = headimg;
			this.username = username;
		},
		setRoom: function (room,name) {
			this.room = room;
			this.room_name = name;
		},
		//退出，本例只是一个简单的刷新
		logout:function(){
			this.socket.emit('disconnect');
			location.href="index";
		},
		//提交聊天消息内容
		submit:function(){
			var content = $("#content").val();
			if(content != ''){
				var obj = {
					userid: this.userid,
					username: this.username,
					headimg:this.headimg,
					content: content
				};
				/*$.ajax({
				 url:'set_chat_record',
				 type:'post',
				 dataType:'json',
				 data:{
				 content: content,
				 type:'message'
				 },
				 success:function(){

				 }
				 });*/
				this.socket.emit('message', obj);
				$("#content").val("");
			}else{
				alert("不能发送空消息");
				return false;
			}
		},
		sendImage:function(data){
			if(data.url!=''){
				var obj = {
					userid: this.userid,
					username: this.username,
					headimg:this.headimg,
					url: data.imgUrl,
					thumb_url:data.imgUrl_thumb
				};
				/*$.ajax({
				 url:'set_chat_record',
				 type:'post',
				 dataType:'json',
				 data:{
				 url: data.imgUrl,
				 thumb_url:data.imgUrl_thumb,
				 type:'image'
				 },
				 success:function(){

				 }
				 });*/
				this.socket.emit('image',obj);
			}else{
				alert("请选择图片图片");
				return false;
			}
		},
		//更新系统消息，本例中在用户加入、退出的时候调用
		updateSysMsg:function(o, action){
			//当前在线用户列表
			var onlineUsers = o.onlineUsers;
			//当前在线人数
			var onlineCount = o.onlineCount;
			//新加入用户的信息
			var user = o.user;
			//更新在线人数
			var userhtml = '';
			$("#onlineCount").text('在线人数： '+onlineCount+' 人');
			var rhtml = "<a href='choose_room'>所有房间</a>&nbsp;&nbsp;>&nbsp;&nbsp;"+ o.room_name;
			$("#room_name").html(rhtml);
			for(var key in onlineUsers) {
				if(onlineUsers.hasOwnProperty(key)){
					userhtml += "<li>"+onlineUsers[key]['username']+"</li>";
				}
			}
			$("#onlineUser ul").html(userhtml);

			if(action == "login" || action == "logout"){
				//添加系统消息
				var html = '';
				html += '<div class="msg-system">';
				html += user.username;
				if(action == 'login'){
					html += '加入了聊天室';
				}else if(action == 'logout'){
					html += '退出了聊天室';
				}
				html += '</div>';
				var section = "<section class='system J-mjrlinkWrap J-cutMsg'>"+html+"</section>"
				this.msgObj.append(section);
				this.scrollToBottom();
			}

		},
		//第一个界面用户提交用户名
		usernameSubmit:function(){
			this.init();
		},
		init:function(){
			/*
			 客户端根据时间和随机数生成uid,这样使得聊天室用户名称可以重复。
			 实际项目中，如果是需要用户登录，那么直接采用用户的uid来做标识就可以
			 */

			$("#showusername").html("<a href='user_index'>"+this.username+"</a>");
			//this.msgObj.style.minHeight = (this.screenheight - db.clientHeight + this.msgObj.clientHeight) + "px";
			this.scrollToBottom();

			//连接websocket后端服务器
			this.socket = io.connect('ws://lambojw.3w.dkys.org:3000');

			//告诉服务器端有用户登录
			this.socket.emit('login', {userid:this.userid, username:this.username ,room:this.room,room_name:this.room_name,headimg:this.headimg});

			//监听新用户登录
			this.socket.on('login', function(o){
				CHAT.updateSysMsg(o, o.action);
			});

			this.socket.on('disconnect1',function(){
				document.cookie="uid=0;path=/";
				alert('您的帐号在其他地方被登录了！');
				CHAT.socket.emit('disconnect1');
				setTimeout(function(){location.href="index";},500);
			});

			this.socket.on('disconnect',function(){
				document.cookie="uid=0;path=/";
				alert("与服务器断开连接");
				location.href="index";
			});

			//监听用户退出
			this.socket.on('logout', function(o){
				CHAT.updateSysMsg(o, o.action);
			});

			//监听消息发送
			this.socket.on('message', function(obj){
				var isme = (obj.userid == CHAT.userid) ? true : false;
				var contentDiv = '<div>'+obj.content+'</div>';
				var headimg = '<img	src="'+obj.headimg+'" class="headimg" style="width:30px;height:30px;">';
				var usernameDiv = '<span>'+headimg+'<br/>'+obj.username+'</span>';
				if(isme){
					var className = 'user';
				} else {
					className = 'service';
				}
				var section = "<section class='"+className+"'>"+contentDiv + usernameDiv +"</section>";
				CHAT.msgObj.append(section);
				CHAT.scrollToBottom();
			});

			this.socket.on('image', function(obj){
				var isme = (obj.userid == CHAT.userid) ? true : false;
				var contentDiv = '<div><img	src="'+obj.thumb_url+'"></div>';
				var headimg = '<img	src="'+obj.headimg+'" class="headimg" style="width:30px;height:30px;">';
				var usernameDiv = '<span>'+headimg+'<br/>'+obj.username+'</span>';
				if(isme){
					var className = 'user';
				} else {
					className = 'service';
				}
				var section = "<section class='"+className+"'>"+contentDiv + usernameDiv +"</section>";
				CHAT.msgObj.append(section);
				setTimeout(function(){
					window.scrollTo(0,$("#message").height());
				},50);

			});
		}
	};
	//通过“回车”提交信息
	$(document).keydown(function(e) {
		e = e || event;
		if (e.ctrlKey && e.which == 13) {
			CHAT.submit();
		}
	});
})();