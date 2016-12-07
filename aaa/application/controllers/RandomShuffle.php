<?php

/**
 * 随机重新排列
 * User: lamboJw
 * Date: 2016/11/21
 * Time: 14:58
 */
class RandomShuffle extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper("sort");
    }

    public function index()
    {
        $arr = [9, 12, 17, 30, 50, 20, 60, 65, 4, 19];
        $arr = $this->randomShuffle($arr);
        echo "<pre>";
        print_r($arr);
        echo "</pre>";
    }

    public function randomShuffle($arr)
    {
        for($i=0;$i<count($arr);$i++){
            swap($arr[$i],$arr[rand()%($i+1)]);
        }
        return $arr;
    }
}