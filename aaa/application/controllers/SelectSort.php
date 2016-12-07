<?php

/**
 * 直接选择排序
 * User: lamboJw
 * Date: 2016/11/18
 * Time: 11:08
 */
class SelectSort extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper("sort");
    }

    public function index(){
        $arr = [6, 4, 1, 3, 8, 5, 4, 2, 7];
        $arr1 = $this->selectSort($arr);
        echo "<pre>";
        print_r($arr1);
        echo "</pre>";
        exit();
    }

    public function selectSort($array){
        for($i=0;$i<count($array);$i++){
            $min = $i;
            for($j=$i+1;$j<count($array);$j++){ //找出最小的元素
                if($array[$j]<$array[$min]){
                    $min = $j;
                }
            }
            swap($array[$min],$array[$i]);  //替换有序序列的后面一位
            echo "<pre>";
            print_r($array);
            echo "</pre>";
        }
        return $array;
    }
}