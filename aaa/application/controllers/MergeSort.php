<?php

/**
 * 归并排序
 * User: lamboJw
 * Date: 2016/11/16
 * Time: 20:42
 */
class MergeSort extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper("sort");
    }

    public function index()
    {
        $arr = [6, 4, 1, 3, 8, 5, 4, 2, 7];
        $arr1 = $this->mergeSort($arr);
        echo "<pre>";
        print_r($arr1);
        echo "</pre>";
        exit();
    }

    public function mergeArray($arrA, $arrB)
    {
        $a_i = $b_i = 0;//设置两个起始位置标记
        $a_len = count($arrA);
        $b_len = count($arrB);
        while ($a_i < $a_len && $b_i < $b_len) {
            //当数组A和数组B都没有越界时
            if ($arrA[$a_i] < $arrB[$b_i]) {
                $arrC[] = $arrA[$a_i++];
            } else {
                $arrC[] = $arrB[$b_i++];
            }
        }
        //判断数组A内的元素是否都用完了，没有的话将其全部插入到C数组内：
        while ($a_i < $a_len) {
            $arrC[] = $arrA[$a_i++];
        }
        //判断数组B内的元素是否都用完了，没有的话将其全部插入到C数组内：
        while ($b_i < $b_len) {
            $arrC[] = $arrB[$b_i++];
        }
        return $arrC;
    }

    public function mergeSort($array)
    {
        $left = 0;
        $right = count($array);
        echo $right . "<br/>";
        if ($right > 2) {
            $center = floor(($left + $right) / 2);    //取中间整数值，舍一位
            $l_arr = array();
            $r_arr = array();
            for ($i = $left; $i < $center; $i++) {
                $l_arr[] = $array[$i];    //分割左边数组
            }
            for ($j = $center; $j < $right; $j++) {
                $r_arr[] = $array[$j];    //分割右边数组
            }
            echo "<pre>";
            print_r($l_arr);
            print_r($r_arr);
            echo "</pre>";
            $l_arr = $this->mergeSort($l_arr);    //继续分割左边数组
            $r_arr = $this->mergeSort($r_arr);    //继续分割右边数组
            $array = $this->mergeArray($l_arr, $r_arr);    //将返回的左右数组进行合并
            return $array;
        } elseif ($right == 2) {    //当数组只剩下两个元素时排序
            if ($array[$left] > $array[$right - 1]) {
                swap($array[$left],$array[$right -1]);
            }
            echo "<pre>";
            print_r($array);
            echo "</pre>";
            return $array;        //返回有序数组
        } elseif ($right == 1) {
            echo "<pre>";
            print_r($array);
            echo "</pre>";
            return $array;    //当数组只剩下一个元素时，已经是有序数组
        }
    }
}

?>