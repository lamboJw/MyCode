<?php
define('SCRIPT_ROOT',dirname(__FILE__).'/');
/**
 * Created by PhpStorm.
 * User: lamboJw
 * Date: 2016/12/14
 * Time: 21:06
 */
class Test1 extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        ini_set('max_execution_time', '0');
        include "/application/libraries/phpQuery/phpQuery/phpQuery.php";
        $this->num = 0;
        $this->type = 4;
        $this->first_url = "http://showfm.net/novel/index_1.asp?nj=Readme";
        $this->load->model("model_download");
    }

    public function index(){
        /*$ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_URL, $this->first_url);
        curl_setopt($ch, CURLOPT_REFERER, $this->first_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $page = curl_exec($ch);
        curl_close($ch);
        $page = mb_convert_encoding($page ,"UTF-8","GBK");
        echo htmlspecialchars($page);exit();
        phpQuery::newDocumentHTML($page);*/
        phpQuery::newDocumentFile($this->first_url);
        $url_list = pq(".xzdiv");
        $data = [];
        foreach($url_list as $key => $val){
            if($key == 0){
                continue;
            }
            $url = pq($val)->find("a")->attr("href");
            $url = "http://showfm.net" . $url;
            $arr['download_page'] = $url;
            $arr['type'] = 1;
            $data[] = $arr;
        }
        $this->model_download->add("pc_showfm",$data,"batch");
    }


}