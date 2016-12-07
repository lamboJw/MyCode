<?php

/**
 * 希尔排序
 * User: lamboJw
 * Date: 2016/11/17
 * Time: 21:41
 */
class ShellSort extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper("sort");
    }

    public function index()
    {
        $arr = [6, 4, 1, 3, 8, 5, 4, 2];
        $arr = $this->shellSort($arr);
        echo "<pre>";
        print_r($arr);
        echo "</pre>";
        exit();
    }

    public function shellSort($array)
    {
        for ($gap = floor(count($array) / 2); $gap > 0; $gap = floor($gap / 2)) {   //步长
            echo "gap:".$gap."<br/>";
            for ($i = 0; $i < $gap; $i++) { //对比组，所有相隔步长gap的元素组成的组
                echo "第".$i."组<br/>";
                //直接插入排序↓
                for ($j = $i + $gap; $j < count($array); $j += $gap) {  //无序序列
                    echo "无序序列当前元素a[{$j}]<br/>";
                    for ($x = $j-$gap; $x >= $i && $array[$x+$gap]<$array[$x]; $x -= $gap) {   //当无序序列第一个数比有序序列最后一个数小时，和它替换，一直向前换到它前面一个数比它小时停止
                        echo "有序序列当前元素a[{$x}]<br/>";
                        swap($array[$x+$gap],$array[$x]);
                        echo "<pre>";
                        print_r($array);
                        echo "</pre>";
                    }
                }
            }
        }
        return $array;
    }
}