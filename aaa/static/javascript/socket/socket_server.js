var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io').listen(http);
var fs = require('fs');
app.get('/', function (req, res) {
    res.send('<h1>Welcome Realtime Server</h1>');
});
//在线用户
var onlineUsers = {};
//当前在线人数
var onlineCount = 0;
//在线的socket_id，一个userid对应依个socket_id
var socket_ids = {};
//待下线列表
var waitting_disconnect = {};
//房间信息，包括房间名，房间内的成员id和用户名
var rooms_info = {};
io.on('connection', function (socket) {
    //监听新用户加入
    var room = "default";
    socket.on('login', function (obj) {
        //将新加入用户的唯一标识当作socket的名称，后面退出的时候会用到
        socket.name = obj.userid;
        if(!socket_ids.hasOwnProperty(obj.userid)){
            socket_ids[obj.userid] = socket.id;
        }else{
            write( new Date().toLocaleString() + " ------>  发现一个帐号多处登录");
            if(io.sockets.sockets.hasOwnProperty(socket_ids[obj.userid])){
                io.sockets.sockets[socket_ids[obj.userid]].emit('disconnect1');
            }
            socket_ids[obj.userid] = socket.id;
        }
        //向所有客户端广播用户加入
        if(waitting_disconnect.hasOwnProperty(obj.userid)){
            room = waitting_disconnect[obj.userid];
            delete waitting_disconnect[obj.userid];
            socket.join(room);
            io.to(room).emit('login', {onlineUsers: rooms_info[room]['user'], onlineCount: rooms_info[room]['count'], user: obj, action:'reconnect',room_name:rooms_info[room]['name']});
            write( new Date().toLocaleString() + " ------>  " +obj.username + '重新连接');
        }else{
            if(obj.room!=null && obj.room!=''){
                room = "room:"+obj.room;
                if(rooms_info[room] != null){
                    if(!rooms_info[room]['user'].hasOwnProperty('uid:'+obj.userid)){
                        var user = {};
                        user.username = obj.username;
                        user.userid = obj.userid;
                        user.headimg = obj.headimg;
                        rooms_info[room]['user']["uid:"+obj.userid] = user;
                        rooms_info[room]['count']++;
                        rooms_info[room]['name'] = obj.room_name;
                    }
                }else{
                    rooms_info[room] = {};
                    rooms_info[room]['user'] = {};
                    rooms_info[room]['count'] = 0;
                    var user = {};
                    user.username = obj.username;
                    user.userid = obj.userid;
                    user.headimg = obj.headimg;
                    rooms_info[room]['user']["uid:"+obj.userid] = user;
                    rooms_info[room]['count']++;
                    rooms_info[room]['name'] = obj.room_name;
                }
                socket.join(room);
            }
            io.to(room).emit('login', {onlineUsers: rooms_info[room]['user'], onlineCount: rooms_info[room]['count'], user: obj, action:'login',room_name:rooms_info[room]['name']});
            write( new Date().toLocaleString() + " ------>  " +obj.username + '加入了聊天室');
        }
    });

    //监听用户退出
    socket.on('disconnect', function () {
        //将退出的用户添加进待下线数组
        waitting_disconnect[socket.name] = room;
        //两秒内重新连接就不下线，否则下线
        setTimeout(function(){
            if(waitting_disconnect.hasOwnProperty(socket.name)){
                if (rooms_info.hasOwnProperty(room) && rooms_info[room].hasOwnProperty('user') && rooms_info[room]['user'].hasOwnProperty("uid:"+socket.name)) {
                    //删除退出用户的socket_id
                    if(socket_ids.hasOwnProperty(socket.name)){
                        delete socket_ids[socket.name];
                    }
                    //退出用户的信息
                    var obj = {userid: socket.name, username: rooms_info[room]['user']["uid:"+socket.name]['username']};
                    //删除
                    delete rooms_info[room]['user']["uid:"+socket.name];
                    rooms_info[room]['count']--;
                    //向所有客户端广播用户退出
                    if(rooms_info[room]['count']!= 0){
                        io.to(room).emit('logout', {onlineUsers: rooms_info[room]['user'], onlineCount: rooms_info[room]['count'], user: obj, action: 'logout',room_name:rooms_info[room]['name']});
                    }
                    write( new Date().toLocaleString() + " ------>  " + obj.username + '退出了聊天室');
                }
                delete waitting_disconnect[socket.name];
            }
        },2000);

    });

    /*
     *      顶号的情况下，被顶号的人        disconnect1          disconnect
     * 下线时，会产生一下待下线数据，            |执行
     * 要把待下线数组里这个人删去，          0.5s|———下线———→ |执行
     * 不然刚上号的人会显示0人在线。             |                   |产生待下线数据
     *      利用时间差，在客户端先执           1s|删除数据           |
     * 行发送disconnect1请求，然后延                                 |
     * 迟0.5秒再下线，下线时产生代下                                 |
     * 线数据，在2秒后检测有没有该用                               2s|检查是否有该用户待下线数据
     * 户的待下线数据，disconnect1会
     * 在发送请求后1秒删除该用户的待
     * 下线数据，等2秒后，disconnect
     * 检测待下线数据时就不会有该用户
     * 的待下线数据。
     */
    socket.on('disconnect1',function(){
        setTimeout(function(){
            delete waitting_disconnect[socket.name];
        },1000);
    });

    //监听用户发布聊天内容
    socket.on('message', function (obj) {
        //向所有客户端广播发布的消息
        obj.content = obj.content.replace(new RegExp("\r|\n","g"),"<br/>");
        obj.content = obj.content.replace(new RegExp(" ","g"),"&nbsp;");
        io.to(room).emit('message', obj);
        write('房间  '+rooms_info[room]['name'] + ':  ' + obj.username + '说：' + obj.content);
    });

    socket.on('image', function (obj) {
        //向所有客户端广播发布的消息
        io.to(room).emit('image', obj);
        write('房间  '+rooms_info[room]['name'] + ':  ' + obj.username + '发送图片：' + obj.url);
    });

});

http.listen(3000, function () {
    console.log('listening on *:3000');
});

function write(msg){
    fs.appendFile('D:\\Program Files (x86)\\wamp\\www\\aaa\\logs\\'+new Date().toLocaleDateString()+'.txt',msg+"\r\n",function(err){
    });
}