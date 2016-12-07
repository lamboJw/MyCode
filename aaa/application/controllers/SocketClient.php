<?php

/**
 * Socket客户端
 * User: lamboJw
 * Date: 2016/11/21
 * Time: 22:10
 */
class SocketClient extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->host = "127.0.0.1";
        $this->port = 2048;
    }

    public function index(){
        set_time_limit(0);

        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)or die("Could not create    socket\n"); // 创建一个Socket

        $connection = socket_connect($socket, $this->host, $this->port) or die("Could not connet server\n");    //  连接
        socket_write($socket, base64_encode("hello socket")) or die("Write failed\n"); // 数据传送 向服务器发送消息
        while ($buff = @socket_read($socket, 1024)) {
            echo("Response was:" . base64_decode($buff) . "\n");
        }
        socket_close($socket);
    }
}