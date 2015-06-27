<?php
/**
 * Created by PhpStorm.
 * User: hong
 * Date: 14-11-11
 * Time: 上午9:33
 */

class InMailCommand extends CConsoleCommand {

    //每日数据
    public function actionSendDetailedDayMail()
    {
        $date = date("Y-m-d", strtotime("1 days ago"));
        $startTime = $date;
        $endTime = $date;
        $subject = "{$date}   上传业务明细";
        $adv_data = AdvData::model();
        $dataList = $adv_data->getDayDataForIn($startTime, $endTime, false);
        
        $str = "";
        $i = 1;
        if($dataList){
            foreach($dataList as $val) {
                if($val['download_number']){
                    if($i%2 == 0){
                        $bgcolor = "#E6E6FA";
                    }else{
                        $bgcolor = "#FFFFFF";
                    }
                    $str .= "<tr align='center' bgcolor='{$bgcolor}'  style='height:35px;border-bottom:1px solid #E6E6FA;'>";
                    $str .= "<td>{$val['user_name']}</td>";
                    $str .= "<td>{$val['date']}</td>";
                    $str .= "<td>{$val['cp_name']}</td>";
                    $str .= "<td>{$val['adv_name']}</td>";
                    $str .= "<td>{$val['channel_name']}</td>";
                    $str .= "<td>{$val['download_number']}</td>";
                    $str .= "<td>{$val['running_account']}</td>";
                    $str .= "<td>{$val['total_price']}</td>";
                    $str .= "<td>{$val['profit']}</td>";
                    $str .= "<td>{$val['profit_margin']}</td>";
                    $str .= "</tr>";

                    $i++;
                }
            }

            $content = <<<EOF
            您好！
            <br/>
            <br/>
            <table  cellpadding="5" cellspacing="0" border="0"  style="font-size:14px; width:800px;" >
                <tr>
                    <td colspan='11' style="height:1px;"  bgcolor="#5d6075"></td>
                </tr>
                <tr align="center" bgcolor="#f0f2fd" style='height:40px; border-bottom:1px solid #f0f2fd;'>
                    <th>负责人</th>
                    <th>日期</th>
                    <th>CP名称</th>
                    <th>推广产品</th>
                    <th>渠道名称</th>
                    <th>激活量</th>
                    <th>流水</th>
                    <th>成本</th>
                    <th>利润</th>
                    <th>利润率</th>
                </tr>
                {$str}
                <tr>
                    <td colspan='11'  bgcolor="#5d6075" style="height:1px;"></td>
                </tr>

            </table>

EOF;

        }else{
            $content = "今日暂无数据";
        }

        
        $emails = array(
          'mahonghong@zhantai.com'
        );

        $emails = $this->_mailerList();
        Yii::app()->mailer->send($emails, $subject, $content);

    }



    public function  actionSendEveryWeekMail()
    {
        $startTime = date("Y-m-d", strtotime("1 weeks ago"));
        $endTime = date("Y-m-d", strtotime("1 days ago"));
        $subject = "{$startTime}到{$endTime}业务明细";
        $this->_detailedContent($startTime, $endTime, $subject);

    }

    public function actionSendEveryMonthMail(){
        $startTime = date("Y-m-d", strtotime("1 months ago"));
        $endTime = date("Y-m-d", strtotime("1 days ago"));
        $subject = "{$startTime}到{$endTime}业务明细";
        $this->_detailedContent($startTime, $endTime, $subject);

    }


    private function _detailedContent($startTime, $endTime, $subject){
        $adv_data = AdvData::model();
        $dataList = $adv_data->getDayDataForIn($startTime, $endTime, false);
        $filename = $this->saveCsv($dataList, $startTime, $endTime);


        $emails = array(
          'mahonghong@zhantai.com'
        );
        $emails = $this->_mailerList();
        $content = "您好，{$subject}如下文件，您可自行下载查看";
        Yii::app()->mailer->sendAttachment($emails, $subject, $content, $filename);

    }

    public function actionSendTotalDayMail()
    {
        $date = date("Y-m-d", strtotime("1 days ago"));
        $subject = $date."业务汇总";
        $startTime = $date;
        $endTime = $date;
        $this->_detailedTotalContent($startTime, $endTime, $subject);
    }

    public function actionSendTotalWeekMail()
    {
        $startTime = date("Y-m-d", strtotime("1 months ago"));
        $endTime = date("Y-m-d", strtotime("1 days ago"));
        $subject = $startTime."到".$endTime."业务汇总";

        $this->_detailedTotalContent($startTime, $endTime, $subject);

    }

    public function actionSendTotalMonthMail()
    {
        $startTime = date("Y-m-d", strtotime("1 weeks ago"));
        $endTime = date("Y-m-d", strtotime("1 days ago"));
        $subject = $startTime."到".$endTime."业务汇总";
        $this->_detailedTotalContent($startTime, $endTime, $subject);

    }

    private function _detailedTotalContent($startTime, $endTime,$subject){
        $adv_data = AdvData::model();
        $dataList = $adv_data->getDayDataForIn($startTime, $endTime, true);
        if($dataList){
        $content = <<<EOF
        您好！
        <br/>
        <br/>
        {$subject}：总激活{$dataList['active_sum']}，流水{$dataList['running_sum']}，利润{$dataList['profit_sum']}，利润率{$dataList['profit_margin_sum']}
EOF;
        }else{
            $content = "暂无业务汇总";
        }

        $emails = array(
          'mahonghong@zhantai.com'
        );
        $emails = $this->_mailerList();

        Yii::app()->mailer->send($emails, $subject, $content);

    }

    public function saveCsv($dataList, $startTime, $endTime)
    {
        $title = array(
          '负责人',
          '日期',
          'CP名称',
          '推广产品',
          '渠道产品',
          '激活量',
          '流水',
          '成本',
          '利润',
          '利润率'
         );
         
        $str = join(',',$title);
        $str .= "\n";

	foreach ($dataList as $val) {
		if($val['download_number']){
			$str .= $val['user_name'].",";
			$str .= $val['date'].",";
			$str .= $val['cp_name'].",";
			$str .= $val['adv_name'].",";
			$str .= $val['channel_name'].",";
			$str .= $val['download_number'].",";
			$str .= $val['running_account'].",";
			$str .= $val['total_price'].",";
			$str .= $val['profit'].",";
			$str .= $val['profit_margin'].",";

			$str .= "\n";
		}
	}
        
        $str = iconv('utf-8','gbk',$str);
        $filename = "{$startTime}到{$endTime}业务明细" . '.csv';

        file_put_contents("extensions/csv/".$filename, $str);
        $filename = "extensions/csv/".$filename;
        return $filename;

    }


    private function _mailerList() {
        $users = User::model()->getAdminUsers();
        $mailers = array();
        foreach($users as $user) {
            $mailers[] = $user['email'];
        }
        return $mailers;
    }


}
