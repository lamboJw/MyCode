<?php

/**
 * Created by PhpStorm.
 * User: lamboJw
 * Date: 2016/11/28
 * Time: 20:29
 */
class Model_user extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->db = $this->load->database("", true);
        $this->table = "pc_user";
    }

    public function getUserInfo($where,$field = "*")
    {
        if(empty($where)){
            return false;
        }
        $sql = "select {$field} from `{$this->table}` where {$where}";
        $query = $this->db->query($sql);
        $re = $query->row_array();
        if (!empty($re)) {
            return $re;
        } else {
            return null;
        }
    }

    public function updateUser($data,$where,$uid){
        $this->db->update($this->table,$data,$where,1);
        if($this->db->affected_rows()){
            $redis = $this->conn->redis();
            $re = json_decode($redis->get('user:'.$uid),true);
            foreach ($re as $k=>&$v) {
                if(isset($data[$k])){
                    $v = $data[$k];
                }
            }
            $redis->set('user:'.$uid,json_encode($re));
            $redis->close();
        }
        return $this->db->affected_rows();
    }

    public function addUser($username, $password)
    {
        if(empty($username) || empty($password)){
            return false;
        }
        $data = array(
            'username' => $username,
            'password' => md5($password),
            'create_time' => time(),
            'headimg' => '/static/images/unknow_face.jpg',
        );
        $insert_id = $this->db->insert($this->table, $data);
        return $insert_id;
    }

    public function getUserInfoById($id){
        if(empty($id)){
            return false;
        }
        $redis = $this->conn->redis();
        $re = json_decode($redis->get('user:'.$id),true);
        if(empty($re)){
            $sql = "select * from `{$this->table}` where id={$id}";
            $query = $this->db->query($sql);
            $re = $query->row_array();
            if(!empty($re)){
                $redis->set('user:'.$id,json_encode($re));
            }
        }
        $redis->close();

        if (!empty($re)) {
            return $re;
        } else {
            return null;
        }
    }

    public function getUserFriends($uid){
        if(empty($uid)){
            return false;
        }
        $redis = $this->conn->redis();
        $re = $redis->lrange('user_friend:'.$uid,0,-1);
        if(empty($re)){
            $sql = "select fid from pc_user_friends where uid={$uid} and status!=2";
            $query = $this->db->query($sql);
            $rs = $query->result_array();
            if(!empty($rs)){
                foreach ($rs as $val) {
                    $redis->lpush('user_friend:'.$uid,$val['fid']);
                    $re[] = $val['fid'];
                }
            }
        }
        $redis->close();
        if (!empty($re)) {
            $fids = implode(",",$re);
            $sql1 = "select id,username from pc_user where id in ({$fids})";
            $query = $this->db->query($sql1);
            $info = $query->result_array();
            return $info;
        } else {
            return null;
        }
    }

    public function addUserFriends($uid,$fid){
        if(empty($uid) || empty($fid)){
            return false;
        }
        $data = array(
            'uid'=>$uid,
            'fid'=>$fid,
            'status'=>1,
            'save_time'=>time()
        );
        $insert_id = $this->db->insert('pc_user_friends',$data);
        if($insert_id){
            $redis = $this->conn->redis();
            $redis->lpush("user_friend:".$uid,$fid);
            $redis->close();
        }
        return $insert_id;
    }

    public function getRooms($where = 1){
        $sql = "select * from pc_room where {$where} and `status`=1";
        $query = $this->db->query($sql);
        $rs = $query->result_array();
        return $rs;
    }
}