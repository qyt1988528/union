<?php
/**
 * Created by PhpStorm.
 * User: mortyu
 * Date: 14-8-26
 * Time: 上午11:40
 */

class AdvertiseChannelController extends AdminController {
    /**
     *
     * 返回组成表单所需要的字段
     *
     */
    public function getFormAttributes(){
        return array(
            'adv_id' => array(
                'type' => 'select',
                'options' => $this->getAdvOptions()
            ),
            'channel_id' => array(
                'type' => 'select',
                'options' => $this->getChannelOptions()
            ),
            'tag' => array(
                'type' => 'input'
            ),
            'price' =>array(
                'type' => 'input'
            ),
            'download_url' => array(
                'type' => 'input'

            )
        );
    }

    private function getAdvOptions() {
        $criteria = new CDbCriteria();
        $criteria->order = 'ctime desc, mtime desc';
        $options = array();
        foreach(Advertise::model()->findAll($criteria) as $adv) {
            $options[$adv->id] = $adv->name;
        }
        return $options;
    }

    private function getChannelOptions() {
        $criteria = new CDbCriteria();
        $criteria->order = 'ctime desc, mtime desc';
        $options = array();
        foreach(Channel::model()->findAll($criteria) as $channel) {
            $options[$channel->id] = $channel->name;
        }
        return $options;
    }

    /**
     *
     * 返回列表界面上需要显示的字段名
     *
     * @return array 字段列表
     */
    public function getAdminAttributes() {
        return array(
            'adv.name',
            'channel.name',
            'tag',
            'price',
            'download_url'
        );
    }

    /**
     * @return string
     *
     * 返回表的名字
     */
    public function getTableName() {
        $filters = $this->getFilterCondition();
        if(isset($filters['adv_id'])) {
            $model = Advertise::model()->findByPk($filters['adv_id']);
            if($model) {
                return $model->cp->name . '-' .$model->name.' 渠道列表';
            }
        }
        return '业务-渠道列表';
    }

    /*
    public function getRowAdminOperations($model) {
        return array_merge(parent::getRowAdminOperations($model), array(
            'upload-data' => array(
                'label' => '上传业务数据',
                'ajax' => false,
                'href' => '/admin/advertise/upload/?f[adv_channel_id]='. $model->id . '&f[adv_id]=' . $this->getCurrentAdvId()
            )
        ));
    }

     */

    private  function getCurrentAdvId() {
        static $filters = null;
        if(!$filters){
           $filters = $this->getFilterCondition();
        }
        return $filters['adv_id'];
    }

    /**
     * @return bool
     *
     * 是否允许上传
     * 默认不允许
     * 修改为不允许
     */
    public function allowUpload(){
        return true;
    }
    /**
     * 11-03
     * 上传
     */
    public function actionUpload() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        @set_time_limit(5 * 60);
        $targetDir = $_SERVER['DOCUMENT_ROOT']. DIRECTORY_SEPARATOR . "upload";
        $cleanupTargetDir = true; // Remove old files
        $maxFileAge = 365 * 24 * 3600; // Temp file age in seconds 1年
        // Create target dir
        if (!file_exists($targetDir)) {
            @mkdir($targetDir);
        }
        if (isset($_REQUEST["name"])) {
            $fileName = $_REQUEST["name"];
        } elseif (!empty($_FILES)) {
            $fileName = $_FILES["file"]["name"];
        } else {
            $fileName = uniqid("file_");
        }
        $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
        if ($cleanupTargetDir) {
            if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
                $a = $_SERVER['DOCUMENT_ROOT'];
                die('{"message": "Failed to open temp directory."}');
            }

            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

                // If temp file is current file proceed to the next
                if ($tmpfilePath == "{$filePath}.part") {
                    continue;
                }
                // Remove temp file if it is older than the max age and is not the current file
                if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
                    @unlink($tmpfilePath);
                }
            }
            closedir($dir);
        }
        // Open temp file
        if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
            die('{"error" : "Failed to open output stream."}');
        }

        if (!empty($_FILES)) {
            if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
                die('{"error" : "Failed to move uploaded file."}');
            }

            // Read binary input stream and append it to temp file
            if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
                die('{"error" : "Failed to open input stream."}');
            }
        } else {
            if (!$in = @fopen("php://input", "rb")) {
                die('{"error" : "Failed to open input stream."}');
            }
        }

        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }

        @fclose($out);
        @fclose($in);

        // Check if file has been uploaded
        if (!$chunks || $chunk == $chunks - 1) {
            // Strip the temp .part suffix off
            rename("{$filePath}.part", $filePath);

        }
        $filePath = basename($filePath);
        $filePath = "/upload/".$filePath;
        // Return Success JSON-RPC response
        die('{"filepath" : "'.$filePath.'","error":"Failed to upload"}');
    }
}
