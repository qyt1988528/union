<?php

class DailyReportCommand extends CConsoleCommand {

    public function actionMail() {
        $mailers = $this->_mailerList();
        $subject = date('Y-m-d') . '今日上传业务汇总';
        $body = $this->_mailBody();

        Yii::app()->mailer->send($mailers, $subject, $body);
    }

    private function _mailerList() {
        $users = User::model()->getAdminUsers();
        $mailers = array();
        foreach($users as $user) {
            $mailers[] = $user['email'];
        }
        return $mailers;
    }
    private function _mailBody() {
        //改成前一天的
        $date = date('Y-m-d');
        $datas = AdvData::model()->getDailyTotalData($date); 
        $body = '';
        foreach($datas as $date=> $data) {
            foreach($data as $item) {
                $body .= "<tr><td>{$date}</td><td>{$item['adv_name']}</td><td>{$item['download_number']}</td><td>{$item['total_price']}</td></tr>"; 
            }
        }
        return $body ? '<table cellspacing="0" cellpadding="1" border="1"><tr><th>日期</th><th>业务名称</th><th>下载总量</th><th>总价</th></tr>'.$body.'</table>' : '暂无今日数据';
    }
}
