<?php

/**
 * 堆排序
 * User: lamboJw
 * Date: 2016/11/17
 * Time: 9:45
 */
class HeapSort extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper("sort");
    }

    public function index()
    {
        $arr = [9, 12, 17, 30, 50, 20, 60, 65, 4, 19];
        $arr = $this->HeapSort($arr);
        echo "<pre>";
        print_r($arr);
        echo "</pre>";
    }

    public function insertPoint($array, $point)     //插入结点
    {
        $array[] = $point;
        $array = $this->fixUp($array);
        return $array;
    }

    public function fixUp($array)   //向上调整
    {
        $i = count($array) - 1;   //最后插入的下标
        $j = ($i - 1) / 2;  //父节点下标
        while ($array[$j] > $array[$i]) {
            swap($array[$j], $array[$i]);
            $i = $j;
            $j = ($i - 1) / 2;
        }
        return $array;
    }

    public function delPoint($array)    //删除结点
    {
        $array[0] = $array[count($array) - 1];  //用最后一个元素替换成第一个元素
        unset($array[count($array) - 1]);   //去除最后一个元素
        $array = $this->fixDown1($array);   //对剩下的堆做向下调整
        return $array;
    }

    public function fixDown1($array, $i = 0)    //向下调正（自己写），n为下标
    {
        $n = count($array) - 1;
        while (2 * $i + 1 <= $n || 2 * $i + 2 <= $n) {  //当这个结点有左子结点或右子节点
            $l = 2 * $i + 1;
            $r = 2 * $i + 2;
            if (isset($array[$r]) && $array[$r] < $array[$l]) {     //如果存在右子结点且右子节点比左子结点小，则用右子结点和父结点比较
                if ($array[$i] > $array[$r]) {  //如果父节点比右子节点大，则与右子结点互换
                    swap($array[$i], $array[$r]);
                    $i = $r;    //当前结点切换成右子节点
                    continue;
                } else {    //因为右子结点是左右子结点中最小的，如果父节点比右子结点还小，则不用换
                    break;
                }
            } elseif ($array[$i] > $array[$l]) {    //不存在右子结点或右子节点比左子结点大，则左子结点为最小。
                swap($array[$i], $array[$l]);
                $i = $l;    //当前结点切换成左子结点
                continue;
            } else {    //因为左子结点是左右子结点中最小的，如果父节点比左子结点还小，则不用换
                break;
            }
        }
        return $array;
    }

    public function fixDown2($array, $i = 0)    //向下调正（网上的）
    {
        $n = count($array) - 1;
        $j = 2 * $i + 1;
        while ($j <= $n) {
            if ($j + 1 <= $n && $array[$j + 1] < $array[$j]) {
                $j++;
            }
            if ($array[$j] > $array[$i]) {
                break;
            }
            swap($array[$i], $array[$j]);
            $i = $j;
            $j = 2 * $i + 1;
        }
        return $array;
    }

    public function makeMinHeap($array)
    {
        $n = count($array);     //数组元素个数
        for ($i = floor($n / 2 - 1); $i >= 0; $i--) {   //最后一个度大于1的结点的下标为（数组元素个数/2）- 1向下取整
            $array = $this->fixDown1($array, $i);
        }
        return $array;
    }

    public function HeapSort($array)
    {
        $array = $this->makeMinHeap($array);    //先把无序序列转化成小顶堆
        $n = count($array) - 1;
        for ($i = $n; $i >= 0; $i--) {
            $arr[] = $array[0];     //因为小顶堆的第一个结点始终最小，所以把它提取出来，然后做删除结点处理。
            $array = $this->delPoint($array);
        }
        return $arr;
    }
}