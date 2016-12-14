<?php

/**
 * Created by PhpStorm.
 * User: lamboJw
 * Date: 2016/11/28
 * Time: 17:12
 */
class Socket_IO_Client extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("model_user");
        $this->uid = checkLogin();
    }

    public function index()
    {
        if (!empty($this->uid)) {
            $this->choose_room();
        } else {
            $this->load->view("socket/login");
        }
    }

    public function login()
    {
        $username = htmlspecialchars(trim($this->input->get('username', true)));
        $password = htmlspecialchars(trim($this->input->get('password', true)));
        $password = md5($password);
        $where = "username='{$username}' and password='{$password}'";
        $re = $this->model_user->getUserInfo($where);
        if (!empty($re)) {
            $redis = $this->conn->redis();
            $user = $redis->get('user:' . $re['id']);
            if (empty($user)) {
                $redis->set('user:' . $re['id'], json_encode($re));
            }
            $redis->close();
            set_cookie("uid", base64_encode($re['id']), 86400);
            exit(json_encode(array('code' => 200, 'msg' => '登陆成功')));
        } else {
            exit(json_encode(array('code' => 300, 'msg' => "用户名或密码错误")));
        }
    }

    public function register()
    {
        $username = htmlspecialchars(trim($this->input->get('username', true)));
        $password = htmlspecialchars(trim($this->input->get('password', true)));
        $password1 = htmlspecialchars(trim($this->input->get('password1', true)));
        if (empty($username)) {
            exit(json_encode(array('code' => 300, 'msg' => '用户名不能为空')));
        }
        if (empty($password)) {
            exit(json_encode(array('code' => 301, 'msg' => '密码不能为空')));
        }
        if (empty($password1)) {
            exit(json_encode(array('code' => 302, 'msg' => '确认密码不能为空')));
        }
        $where = "username = '{$username}'";
        $info = $this->model_user->getUserInfo($where);
        if (!empty($info)) {
            exit(json_encode(array('code' => 303, 'msg' => '该用户名已经被注册了')));
        }
        if (strlen($password) < 6 || strlen($password1) < 6) {
            exit(json_encode(array('code' => 304, 'msg' => '密码长度不能小于6位')));
        }
        if ($password !== $password1) {
            exit(json_encode(array('code' => 305, 'msg' => '两次密码不相同')));
        }
        $re = $this->model_user->addUser($username, $password);
        if (!empty($re)) {
            set_cookie("uid", base64_encode($re), 86400);
            exit(json_encode(array('code' => 200, 'msg' => '注册成功', 'uid' => $re)));
        } else {
            exit(json_encode(array('code' => 306, 'msg' => '注册失败')));
        }
    }

    public function choose_room(){
        /*$friend = $this->model_user->getUserFriends($this->uid);
        foreach ($friend as &$val) {
            $val['id'] = base64_encode($val['id']);
        }*/
        $rs = $this->model_user->getRooms();
        foreach ($rs as &$v) {
            $v['id'] = base64_encode($v['id']);
        }
        $re = $this->model_user->getUserInfoById($this->uid);
        $data = array('username'=>$re['username'],'rooms'=>$rs);
        $this->load->view("socket/rooms",$data);
    }

    public function go_chat()
    {
        $room_id = htmlspecialchars(trim($this->input->get('id', true)));
        $room_name = htmlspecialchars(trim($this->input->get('name', true)));
        $room_id = base64_decode($room_id);
        if (!empty($this->uid)) {
            $re = $this->model_user->getUserInfoById($this->uid);
            if (!empty($re)) {
                $data = array('uid' => $re['id'], 'username' => $re['username'],'headimg'=>"/aaa".$re['headimg']);
                if(is_numeric($room_id)){
                    $data['room'] = $room_id;
                    $data['name'] = $room_name;
                    $this->load->view("socket/chat", $data);
                }else{
                    echo "<script>alert('房间id出错！');</script>";
                    $this->choose_room();
                }
            } else {
                delete_cookie('uid');
                $this->index();
            }
        } else {
            $this->index();
        }
    }

    public function logout()
    {
        delete_cookie('uid');
        exit(json_encode(array('code' => 200, 'msg' => '注销成功')));
    }

    public function ajaxUploadPhoto()
    {
        $file = $_FILES['image'];
        $md5_code = md5_file($file['tmp_name']);
        $redis = $this->conn->redis(1);
        $cache = $redis->get("image:".$md5_code);
        if(!empty($cache)){
            $redis->close();
            $cache = json_decode($cache,true);
            echo json_encode(array(
                'code' => 200,
                'imgUrl' => $cache['imgUrl'],
                'imgUrl_thumb' => $cache['imgUrl_thumb'],
            ));
            exit();
        }
        $path = './static/uploads/';
        // 上传图片
        $uploadConfig ['upload_path'] = $path;
        $uploadConfig ['allowed_types'] = 'gif|jpg|jpeg|png';
        $uploadConfig ['max_size'] = '5120';
        $uploadConfig ['encrypt_name'] = true;
        $uploadConfig ['file_ext'] = '.jpg';
        $this->load->library('upload', $uploadConfig);
        if (!$this->upload->do_upload('image')) {
            exit (json_encode(array(
                'code' => 0,
                'message' => $this->upload->display_errors()
            )));
        } else {
            // 上传成功
            $data = $this->upload->data();
            $this->load->library("image_lib");//载入图像处理类库
            $size = $data['image_size_str'];
            $size = str_replace("\"","",$size);
            $size = explode(" ",$size);
            $size[0]=explode("=",$size[0]);
            $size[1]=explode("=",$size[1]);
            if(($size[0][1]>300 || $size[1][1]>300)&& $data['file_type']!="image/gif"){
                $config_big_thumb=array(
                    'image_library' => 'gd2',//gd2图库
                    'source_image' => $data['full_path'],//原图
                    'new_image' => "./static/uploads/big_thumb/".$data['file_name'],//大缩略图
                    'create_thumb' => true,//是否创建缩略图
                    'maintain_ratio' => true,
                    'width' => 300,//缩略图宽度
                    'height' => 300,//缩略图的高度
                    'thumb_marker'=>"_300_300"//缩略图名字后加上 "_300_300",可以代表是一个300*300的缩略图
                );
                $this->image_lib->initialize($config_big_thumb);
                $this->image_lib->resize();//生成big缩略图
                $tname = explode(".",$data['file_name']);
                $tname[0] .= "_300_300";
                $thumb_name = $tname[0].".".$tname[1];
                $img = array(
                    'imgUrl'=>"/aaa/static/uploads/".$data['file_name'],
                    'imgUrl_thumb' => "/aaa/static/uploads/big_thumb/".$thumb_name
                );
                $redis->set("image:".$md5_code,json_encode($img));
                $redis->close();
                @unlink ($data['full_path']);
                echo json_encode(array(
                    'code' => 200,
                    'imgUrl' => "/aaa/static/uploads/".$data['file_name'],
                    'imgUrl_thumb' => "/aaa/static/uploads/big_thumb/".$thumb_name,
                    'data' => $data
                ));
                exit();
            }else{
                $img = array(
                    'imgUrl'=>"/aaa/static/uploads/".$data['file_name'],
                    'imgUrl_thumb' => "/aaa/static/uploads/".$data['file_name']
                );
                $redis->set("image:".$md5_code,json_encode($img));
                $redis->close();
                echo json_encode(array(
                    'code' => 200,
                    'imgUrl' => "/aaa/static/uploads/".$data['file_name'],
                    'imgUrl_thumb' => "/aaa/static/uploads/".$data['file_name'],
                    'data' => $data
                ));
                exit();
            }

        }
    }

    public function ajaxUploadHeadimg()
    {
        $file = $_FILES['image'];
        $md5_code = md5_file($file['tmp_name']);
        $redis = $this->conn->redis(1);
        $cache = $redis->get("headimg:".$md5_code);
        if(!empty($cache)){
            $redis->close();
            $cache = json_decode($cache,true);
            echo json_encode(array(
                'code' => 200,
                'url' => $cache['url']
            ));
            $cache['url'] = str_replace("/aaa/","/",$cache['url']);
            $this->model_user->updateUser(array('headimg'=>$cache['url']),array('id'=>$this->uid),$this->uid);
            exit();
        }
        $path = './static/headimg/';
        // 上传图片
        $uploadConfig ['upload_path'] = $path;
        $uploadConfig ['allowed_types'] = 'gif|jpg|jpeg|png';
        $uploadConfig ['max_size'] = '1024';
        $uploadConfig ['encrypt_name'] = true;
        $uploadConfig ['file_ext'] = '.jpg';
        $this->load->library('upload', $uploadConfig);
        if (!$this->upload->do_upload('image')) {
            exit (json_encode(array(
                'code' => 0,
                'message' => $this->upload->display_errors()
            )));
        } else {
            // 上传成功
            $data = $this->upload->data();
            $this->load->library("image_lib");//载入图像处理类库
            $size = $data['image_size_str'];
            $size = str_replace("\"","",$size);
            $size = explode(" ",$size);
            $size[0]=explode("=",$size[0]);
            $size[1]=explode("=",$size[1]);
            if($size[0][1]>100 || $size[1][1]>100){
                $config_big_thumb=array(
                    'image_library' => 'gd2',//gd2图库
                    'source_image' => $data['full_path'],//原图
                    'new_image' => "./static/headimg/big_thumb/".$data['file_name'],//大缩略图
                    'create_thumb' => true,//是否创建缩略图
                    'maintain_ratio' => true,
                    'width' => 100,//缩略图宽度
                    'height' => 100,//缩略图的高度
                    'thumb_marker'=>"_100_100"//缩略图名字后加上 "_100_100",可以代表是一个100*100的缩略图
                );
                $this->image_lib->initialize($config_big_thumb);
                $this->image_lib->resize();//生成big缩略图
                $tname = explode(".",$data['file_name']);
                $tname[0] .= "_100_100";
                $thumb_name = $tname[0].".".$tname[1];
                $img = array(
                    'url'=>"/aaa/static/headimg/big_thumb/".$thumb_name,
                );
                $redis->set("headimg:".$md5_code,json_encode($img));
                $redis->close();
                @unlink ($data['full_path']);
                $this->model_user->updateUser(array('headimg'=>"/static/headimg/big_thumb/".$thumb_name),array('id'=>$this->uid),$this->uid);
                echo json_encode(array(
                    'code' => 200,
                    'url'=>"/aaa/static/headimg/big_thumb/".$thumb_name,
                ));
                exit();
            }else{
                $img = array(
                    'url'=>"/aaa/static/headimg/".$data['file_name'],
                );
                $redis->set("image:".$md5_code,json_encode($img));
                $redis->close();
                $this->model_user->updateUser(array('headimg'=>"/static/headimg/".$data['file_name']),array('id'=>$this->uid),$this->uid);
                echo json_encode(array(
                    'code' => 200,
                    'url' => "/aaa/static/headimg/".$data['file_name'],
                ));
                exit();
            }

        }
    }

    public function set_chat_record(){
        $uid = $this->uid;
        $type = htmlspecialchars(trim($this->input->post('type', true)));
        if($type == "message"){
            $msg = htmlspecialchars(trim($this->input->post('content', true)));
            $data = array('uid'=>$uid,'type'=>$type,'msg'=>$msg,'save_time'=>time());
        }else{
            $url = htmlspecialchars(trim($this->input->post('url', true)));
            $thumb_url = htmlspecialchars(trim($this->input->post('thumb_url', true)));
            $data = array('uid'=>$uid,'type'=>$type,'url'=>$url,'thumb_url'=>$thumb_url,'save_time'=>time());
        }
        exit(json_encode(array('code' =>200,'msg' => 'success' ,'data'=>$data)));
    }

    public function join_room(){
        $uid = $this->uid;
        $room = htmlspecialchars(trim($this->input->get('room', true)));
        $room = base64_decode($room);
        $redis = $this->conn->redis(3);
        $room_members = $redis->lrange("room:".$room,0,-1);
        if(!array_key_exists($this->uid, $room_members)){
            $redis->lpush("room:".$room,$uid);
        }
        $redis->close();
    }

    public function leave_room(){
        $uid = $this->uid;
        $room = htmlspecialchars(trim($this->input->get('room', true)));
        $room = base64_decode($room);
        $redis = $this->conn->redis(3);
        $redis->lrem("room:".$room,0,$uid);
        $redis->close();
    }

    public function user_index(){
        $ajax = trim($this->input->get("ajax",true));
        if(isset($ajax) && $ajax){
            $uid = intval($this->input->get('uid', true));
        }else{
            $uid = $this->uid;
        }
        $info = $this->model_user->getUserInfoById($uid);
        if(!empty($info)){
            if(empty($info['headimg'])){
                $info['headimg'] = "/aaa/static/images/unknow_face.jpg";
            }else{
                $info['headimg'] = "/aaa".$info['headimg'];
            }
            if($info['birthday']!=0){
                $info['birthday'] = date("Y-m-d",$info['birthday']);
            }else{
                $info['birthday'] = "";
            }
            unset($info['password']);
            unset($info['create_time']);
            unset($info['id']);
            if($ajax){
                exit(json_encode(array('code' => 200, 'msg' => 'success' ,'data'=>$info)));
            }else{
                $this->load->view("socket/user_index",$info);
            }
        }else{
            if($ajax){
                exit(json_encode(array('code' => 300, 'msg' => '无此用户信息')));
            }else{
                echo "<script>alert('无此用户信息！');window.history.back();</script>";
            }
        }
    }

    public function save_info(){
        $username = htmlspecialchars(trim($this->input->get('username', true)));
        $realname = htmlspecialchars(trim($this->input->get('realname', true)));
        $birthday = htmlspecialchars(trim($this->input->get('birthday', true)));
        if(!empty($birthday)){
            $birthday = strtotime($birthday);
            if(is_numeric($birthday)){
                $birthday = strtotime("today",$birthday);
            }else{
                exit(json_encode(array('code' => 301, 'msg' => '日期格式不正确，请填写正确格式：YYYY-mm-dd')));
            }
        }else{
            $birthday = 0;
        }
        if(empty($username)){
            exit(json_encode(array('code' => 303, 'msg' => '用户名不能为空')));
        }
        $where = "username = '{$username}'";
        $info = $this->model_user->getUserInfo($where);
        if (!empty($info) && $info['id']!=$this->uid) {
            exit(json_encode(array('code' => 302, 'msg' => '该用户名已经被注册了')));
        }
        $user_info = $this->model_user->getUserInfoById($this->uid);
        if(!empty($user_info) && $user_info['username'] == $username && $user_info['realname'] == $realname && $user_info['birthday'] == $birthday){
            exit(json_encode(array('code' => 305, 'msg' => '信息没有修改')));
        }
        $data = array(
            'username'=>$username,
            'realname'=>$realname,
            'birthday'=>$birthday
        );
        $re = $this->model_user->updateUser($data,array('id'=>$this->uid),$this->uid);
        if($re){
            exit(json_encode(array('code' => 200, 'msg' => 'success')));
        }else{
            exit(json_encode(array('code' => 304, 'msg' => '保存失败')));
        }
    }
}