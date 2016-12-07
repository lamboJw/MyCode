<?php

/**
 * 获得总和为X的N个随机数
 * User: lamboJw
 * Date: 2016/11/21
 * Time: 15:58
 */
class getRandomNumberEqualSum extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper("sort");
    }

    public function index(){
        $arr = $this->getRandomNumberEqualSum(10,100);
        echo "<pre>";
        print_r($arr);
        echo "</pre>";
        $sum = 0;
        foreach($arr as $v){
            $sum += $v;
        }
        echo "总和：".$sum;
    }

    /**
     * @param int $count 随机数个数
     * @param int $sum 总和
     * @return array $arr
     */
    public function getRandomNumberEqualSum($count,$sum){
        $array = getRandomNumberInRange($count-1,0,$sum);
        $array[] = 0;
        $array = quickSort($array);
        for($i=count($array)-1;$i>=0;$i--){
            $x = $sum-$array[$i];
            $arr[] = $x;
            $sum = $array[$i];
        }
        return $arr;
    }
}