<?php

/**
 * Created by PhpStorm.
 * User: lamboJw
 * Date: 2017/11/6
 * Time: 17:23
 */
class Model_download extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->db = $this->load->database('',true);
    }

    public function add($table,$data = array(),$type = ""){
        if(empty($table) || empty($data)){
            return false;
        }
        if($type == ""){
            $this->db->insert($table,$data);
        }else{
            $this->db->insert_batch($table,$data);
        }
    }
}