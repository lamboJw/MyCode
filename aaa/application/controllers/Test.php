<?php

class Test extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        ini_set('max_execution_time', '0');
        include "D:/Program Files (x86)/wamp/www/aaa/application/libraries/phpQuery/phpQuery/phpQuery.php";
        $this->num = 0;
        $this->type = 4;
        $this->first_url = "http://www.xiumm.cc/albums/XiuRen.html";
        $this->load->model("Model_images");
    }

    public function index($next = "")
    {
        if ($next == "") {
            $next = $this->input->get("url", true);
            if (empty($next)) {
                $next = $this->first_url;
            }
        }
        phpQuery::newDocumentFile($next);
        echo date("H:i:s", time()) . " ------->>  开始搞  {$next}<br/>";
        ob_flush();
        flush();
        $list = pq(".gallary_wrap .gallary_item_album");
        foreach ($list as $k => $v) {
            $url = pq($v)->find(".item a")->attr("href");
            $url = "http://www.xiumm.cc" . $url;
            $title = pq($v)->find(".name")->text();
            preg_match("/[v|n]ol?\.\w?\d{1,3}.*/i",$title,$match);
            if(!empty($match)){
                $title = $match[0];
            }else{
                $title = preg_replace("/秀人网\s{0,3}(\[XIUREN\])?\s{0,3}201(5|6)\.\d{1,2}\.\d{1,2}\s{0,3}/", "", $title);
//                $title = preg_replace("/\s.*/", "", $title);
            }
            $title = preg_replace("/\s{2,50}/", "", $title);
            if (empty($title)) {
                $title = time();
            }
            echo $title."<br/>";
            ob_flush();
            flush();
            $this->traversal_page($url, $title);
            $this->num = 0;
        }
        echo "查找下一页<br/>";
        ob_flush();
        flush();
        phpQuery::newDocumentFile($next);
        $next_page = pq(".paginator .next")->find("a")->attr("href");
        if (!empty($next_page)) {
            echo "找到下一页<br/>";
            ob_flush();
            flush();
            if (!strstr($next_page, "http://www.xiumm.cc")) {
                $next_page = "http://www.xiumm.cc" . $next_page;
                $this->index($next_page);
            }
        }else{
            echo "没有下一页<br/>";
        }
        ob_end_flush();
    }

    function traversal_page($first_page, $title)
    {
        phpQuery::newDocumentFile($first_page);
        $list = pq(".item");
        foreach ($list as $v) {
            $url = pq($v)->find("img")->attr("src");
            if (!empty($url)) {
                if (!strstr($url, "http://www.xiumm.cc")) {
                    $url = "http://www.xiumm.cc" . $url;
                }
                $data = array(
                    'title' => $title,
                    'filename' => $title . "(" . $this->num . ").jpg",
                    'url' => $url,
                    'type' => $this->type,
                    'save_time' => time()
                );
                $this->Model_images->addImages($data);
                $this->num++;
            }
        }
        $next_page = pq(".nextPageBtn")->parent()->attr("href");
        if (!empty($next_page)) {
            if (!strstr($next_page, "http://www.xiumm.cc")) {
                $next_page = "http://www.xiumm.cc" . $next_page;
            }
            $this->traversal_page($next_page, $title);
        } else {
            echo date("H:i:s", time()) . " ------->>  {$title}  finish<br/>";
            ob_flush();
            flush();
        }
    }

    function download_img($url, $title, $name)
    {
        if (empty($title)) {
            $title = time();
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        ob_start();
        curl_exec($ch);
        $return_content = ob_get_contents();
        ob_end_clean();
        if (!file_exists("E:/picture/".strtotime("today") )) {
            mkdir("E:/picture/".strtotime("today") );
        }
        if (!file_exists("E:/picture/" . strtotime("today") . "/" . iconv('utf-8', 'gbk', $title))) {
            mkdir("E:/picture/" . strtotime("today") . "/" . iconv('utf-8', 'gbk', $title));
        }
        $filename = "E:/picture/" . strtotime("today") . "/" . iconv('utf-8', 'gbk', $title) . "/" . iconv('utf-8', 'gbk', $name) ;
        $fp = @fopen($filename, "a"); //将文件绑定到流
        fwrite($fp, $return_content); //写入文件
        fclose($fp);
    }

    public function change_error(){
        $where = "id>=46538";
        $rs = $this->Model_images->getImages($where);
        foreach ($rs as $k => $v) {
            $filename = preg_replace("/秀人网\s{0,3}\[XiuRen\]-?/", "", $v['filename']);
            $title = preg_replace("/秀人网\s{0,3}\[XiuRen\]-?/", "", $v['title']);
            $this->Model_images->updateImages(array('id'=>$v['id']),array('filename'=>$filename,'title'=>$title));
        }
        echo "finish";
    }

    public function download(){
        $offset = 0;
        $rs = $this->Model_images->getImages("type=4 and id>62722 limit {$offset},5000");
        echo date("H:i:s",time())." ------->>  begin<br/>";
        foreach($rs as $v){
            $this->download_img($v['url'], $v['title'], $v['filename']);
            echo "下载".$v['filename']."，id:{$v['id']}  成功<br/>";
            ob_flush();
            flush();
        }
        echo date("H:i:s",time())." ------->>  finish,{$offset}";
        ob_end_flush();
    }
}


?>