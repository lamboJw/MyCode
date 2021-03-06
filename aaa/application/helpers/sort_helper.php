<?php

/**
 * Created by PhpStorm.
 * User: lamboJw
 * Date: 2016/11/17
 * Time: 9:38
 */

/**
 * 元素互换
 * @param $a
 * @param $b
 */
function swap(&$a, &$b)
{
    $temp = $a;
    $a = $b;
    $b = $temp;
}

function getRandomNumberInRange($count,$start,$end){
    $a = array();
    for($i=0;$i<$count;$i++){
        $a[$i] = rand()%($end-$start+1)+$start;
    }
    return $a;
}

/**
 * 快速排序
 */
function quickSort($array)
{
    $i = 0;
    $j = count($array) - 1;
    if ($j > 1) {
        $x = $array[$i];    //将$array[$i]挖出来
        $side = "r";    //判断方向
        while ($i != $j && $i < $j) {
            if ($side == "r") {
                while ($i < $j) {   //从右边开始向左找
                    if ($array[$j] < $x) {      //找到比坑小的数
                        $array[$i] = $array[$j];    //把这个数填进坑里
                        break 1;
                    } else {
                        $j--;   //没找到，继续向左
                    }
                }
                $side = "l";    //切换成从左边开始向右找
            }
            if ($side == "l") {
                while ($i < $j) {   //从左边开始向右找
                    if ($array[$i] > $x) {      //找到比坑大的数
                        $array[$j] = $array[$i];    //把这个数填进坑里
                        break 1;
                    } else {
                        $i++;   //没找到，继续向右
                    }
                }
                $side = "r";    //切换成从右向左找
            }
        }
        $l_arr = array();
        $r_arr = array();   //分治法
        for ($l = 0; $l < $i; $l++) {
            $l_arr[] = $array[$l];  //把上面结果中$i的左边切出来
        }
        for ($r = $i + 1; $r <= count($array) - 1; $r++) {
            $r_arr[] = $array[$r];  //把上面结果中$i的右边切出来
        }
        $l_arr = quickSort($l_arr);  //左右数组进行挖坑填数
        $r_arr = quickSort($r_arr);
        $l_arr[] = $x;  //把返回的两个数组和中间数连起来
        $array = array_merge($l_arr, $r_arr);
        return $array;
    } elseif ($j == 1) {    //当数组只剩两个元素时，
        if ($array[$i] > $array[$j]) {
            swap($array[$i],$array[$j]);    //直接进行大小判断
        }
        return $array;
    } elseif ($j <= 0) {    //当数组只剩一个元素时，该数组为有序序列，直接返回
        return $array;
    }
}

function checkLogin(){
    $uid = get_cookie("uid");
    if(!empty($uid)){
        $uid = base64_decode($uid);
        if(is_numeric($uid)){
            return $uid;
        }else{
            return null;
        }
    }else{
        return null;
    }
}
