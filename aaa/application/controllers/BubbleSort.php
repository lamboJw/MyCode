<?php

/**
 * 冒泡排序
 * User: lamboJw
 * Date: 2016/11/17
 * Time: 20:22
 */
class BubbleSort extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper("sort");
    }

    public function index(){
        $arr = [9, 12, 17, 30, 50, 20, 60, 65, 4, 19];
        $arr = $this->bubbleSort($arr);
        echo "<pre>";
        print_r($arr);
        echo "</pre>";
    }

    public function bubbleSort($array){
        $flag = count($array);
        while($flag>0){
            $k = $flag;
            $flag = 0;
            for($i=1;$i<$k;$i++){
                if($array[$i-1]>$array[$i]){
                    swap($array[$i-1],$array[$i]);
                    $flag = $i;
                }
            }
        }
        return $array;
    }
}