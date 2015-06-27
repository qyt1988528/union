<?php
/**
 * Created by PhpStorm.
 * User: hong
 * Date: 14-11-11
 * Time: 上午9:33
 */
class OutMailCommand extends CConsoleCommand
{


    public function actionindex()
    {

        $date = date("Y-m-d", strtotime("1 days ago"));

        $subject = "{$date}   上传业务明细";
        $adv_data = AdvData::model();
        $dataList = $adv_data->getDayToTalData($date);

        foreach ($dataList as $key => $value) {
            $str = "";
            $email = "";
            $i = 1;
            foreach ($value as $val) {
                if ($i % 2 == 0) {
                    $bgcolor = "#E6E6FA";
                } else {
                    $bgcolor = "#FFFFFF";
                }

                $str .= "<tr align='center' bgcolor='{$bgcolor}'  style='height:35px;'>";
                $str .= "<td>{$val['date']}</td>";
                $str .= "<td>{$val['adv_name']}</td>";
                $str .= "<td>{$val['tag']}</td>";
                $str .= "<td>{$val['download_number']}</td>";
                $str .= "<td>{$val['price']}</td>";
                $str .= "<td>{$val['total_price']}</td>";
                $str .= "</tr>";
                $email = $val['email'];
            }


            $content = <<<EOF
        您好！
        <br/>
        <br/>
        <table  cellpadding="5" cellspacing="0" border="0"  style="font-size:14px; width:800px;border-bottom:1px solid #E6E6FA;">
            <tr>
                <th colspan='11' style="height:1px;" bgcolor="#5d6075"></th>
            </tr>

            <tr align="center"  bgcolor="#f0f2fd" style='height:40px; border-bottom:1px solid #f0f2fd;'>
                <td>日期</td>
                <td>业务</td>
                <td>包名</td>
                <td>下载量</td>
                <td>单价</td>
                <td>总价</td>
            </tr>
            {$str}
        </table>

EOF;
            if ($email) {
                Yii::app()->mailer->send($email, $subject, $content);
                sleep(2);
            }

        }
    }
}
