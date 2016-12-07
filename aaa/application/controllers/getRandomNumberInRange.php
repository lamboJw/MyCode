<?php

/**
 * 获取范围内的N个随机数
 * User: lamboJw
 * Date: 2016/11/21
 * Time: 15:36
 */
class getRandomNumberInRange extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper("sort");
    }

    public function index(){
        $arr = $this->getRandomNumberInRange(10,25,60);
        echo "<pre>";
        print_r($arr);
        echo "</pre>";
    }

    /**
     * @param int $count 需要获得的随机数个数
     * @param int $start 范围的开始
     * @param int $end 范围的结束
     * @return array 返回随机数的数组
     */
    public function getRandomNumberInRange($count,$start,$end){
        $a = array();
        for($i=0;$i<$count;$i++){
            $a[$i] = rand()%($end-$start+1)+$start; //(结束-开始+1)得到范围大小len，rand()%len获得0到len-1的一个随机数r，r+start得到start到end的一个随机数
        }
        return $a;
    }
}