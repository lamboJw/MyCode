<?php

/**
 * 直接插入排序
 * User: lamboJw
 * Date: 2016/11/17
 * Time: 21:14
 */
class InsertSort extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper("sort");
    }

    public function index()
    {
        $arr = [6, 4, 1, 3, 8, 5, 4, 2, 7];
        $arr = $this->insertSort($arr);
        echo "<pre>";
        print_r($arr);
        echo "</pre>";
        exit();
    }

    public function insertSort($array)
    {
        for ($i = 1; $i < count($array); $i++) {
            for ($j = 0; $j < $i; $j++) {
                if ($array[$i] < $array[$j]) {
                    $temp = $array[$i];
                    for ($k = $i - 1; $k >= $j; $k--) {
                        $array[$k + 1] = $array[$k];
                    }
                    $array[$j] = $temp;
                }
            }
        }
        return $array;
    }

    public function insertSort2($array)
    {
        $n = count($array);
        for ($i = 1; $i < $n; $i++)
            for ($j = $i - 1; $j >= 0 && $array[$j] > $array[$j + 1]; $j--) //当无序序列第一个数比有序序列最后一个数小时，和它替换，一直向前换到它前面一个数比它小时停止
                swap($array[$j], $array[$j + 1]);   //替换完之后，$array[$i-1]到$array[0]变成了有序序列
    }
}