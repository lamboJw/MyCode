<?php

/**
 * Created by PhpStorm.
 * User: lamboJw
 * Date: 2016/11/7
 * Time: 20:47
 */
class Model_images extends  CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->db = $this->load->database("",true);
    }

    public function getImages($where,$field = "*"){
        $rs = null;
        $sql = "select $field from pc_images where {$where};";
        $query = $this->db->query($sql);
        $rs = $query->result_array();
        $query->free_result();
        return $rs;
    }

    public function addImages($data){
        if(empty($data)){
            return false;
        }
        $insert_id = $this->db->insert('pc_images',$data);
        return $insert_id;
    }

    /**
     * 修改数据
     * @param $where array
     * @param $data array
     * @return int
     */
    public function updateImages($where,$data){
        $this->db->update('pc_images',$data,$where,1);
        return $this->db->affected_rows();
    }
}