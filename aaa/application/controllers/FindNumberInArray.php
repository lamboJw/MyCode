<?php

/**
 * 公差为1的等差数列中找数
 * User: lamboJw
 * Date: 2016/11/21
 * Time: 17:22
 */
class FindNumberInArray extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper("sort");
    }

    public function index(){
        $arr = [9, 10, 11, 10, 9, 8, 7, 8, 7, 6];   //公差的绝对值为1的等差数列
        $index = $this->findNumberInArray($arr,8);
        echo "<pre>";
        print_r($arr);
        print_r($index);
        echo "</pre>";
    }

    public function FindNumberInArray($array,$num){
        $next = 0;
        while($next < count($array)){
            if($array[$next]!= $num){
                $next += abs($num - $array[$next]);
            }else{
                return $next;
            }
        }
    }
}