<?php


Yii::import('application.extensions.*');
require_once('phpQuery/phpQuery.php');
class FetchDataCommand extends CConsoleCommand {

    private $urlPrefix = 'http://stats.np.mobilem.360.cn';
    private $user = '15801655090';
    private $password = '910519*';

    private $ch;

    public function actionIndex() {
//        curl 'http://cps.taohv.com:99/loginout.php' -H 'Cookie: PHPSESSID=8d820972f1cbda5c4719ae7bad025080' -H 'Origin: http://cps.taohv.com:99' -H 'Accept-Encoding: gzip,deflate,sdch' -H 'Accept-Language: zh-CN,zh;q=0.8' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36' -H 'Content-Type: application/x-www-form-urlencoded' -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8' -H 'Cache-Control: max-age=0' -H 'Referer: http://cps.taohv.com:99/login.php' -H 'Connection: keep-alive' --data 'username=cps042&password=cc042cps&act=login' --compressed

        $cached = $this->load();
        if(!$cached) {
            $this->ch = curl_init();
            if($this->login($this->user, $this->password)) {
                $cached = $this->fetch('/manage_wm.php');
                $this->save($cached);
            }
            echo 'fetch';
        }

        $this->parse($cached);
    }

    private function load() {
        return @file_get_contents('/tmp/cache.html');
    }

    private function save($data) {
        file_put_contents('/tmp/cache.html', $data);
    }

    private function login($user, $password) {
        curl_setopt($this->ch, CURLOPT_URL, $this->urlPrefix. '/loginout.php');
        $loginData = array(
            'username' => $user,
            'password' => $password,
            'act' => 'login'
        );
        $this->configCurl($this->ch);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $loginData);
        $data = curl_exec($this->ch);
        if(strpos($data, 'window.location.href="index.html"') !== false) {
            return true;
        } else {
            return fasle;
        }
    }

    private function parse($data) {
        $data = iconv('gbk', 'utf-8', $data);
        $data = tidy_repair_string($data);
        $doc = phpQuery::newDocument($data);
        foreach(pq('tr') as $index => $tr) {
            if($index < 3) {
                continue;
            }
            $item = array(
                'date' => pq($tr)->find('td:eq(0)')->text(),
                'tag' =>  pq($tr)->find('td:eq(2)')->text(),
                'number' =>  pq($tr)->find('td:eq(3)')->text(),
            );
            var_dump($item);
        }
    }

    private function fetch($url)  {
        $url = $this->urlPrefix . $url;
        curl_setopt($this->ch, CURLOPT_URL, $url);
        $this->configCurl($this->ch);
        return curl_exec($this->ch);
    }

    private function configCurl($ch) {
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, "");
    }
}
