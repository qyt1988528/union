<?php

class SiteController extends Controller
{
    public function actionError()
    {                                                                           
        if($error=Yii::app()->errorHandler->error)                              
        {                                                                       
            if(Yii::app()->request->isAjaxRequest)                              
                echo $error['message'];                                         
            else                                                                
                $this->render('error', $error);                                 
        }                                                                       
    }  

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex()
    {
        $user = Yii::app()->user;
        if(Yii::app()->request->isPostRequest) {
            $type = $_POST['type'];
            if($type == 'login') {
                $identity = new UserIdentity($_POST['LoginForm']['email'], $_POST['LoginForm']['password']);
                if($identity->authenticate()) {
                    $user->login($identity);
                    $this->redirect('/');
                } else {
                }
            }
            return;
        }

        // renders the view file 'protected/views/site/index.php'
        // using the default layout 'protected/views/layouts/main.php'
        if($user->isGuest) {
            $this->render('login', array(
                'userinput' => Yii::app()->request->isPostRequest ? $_POST['LoginForm']: array()
            ));
        } else{
            $criteria = new CDbCriteria();
            if($user->isChannel()) {
                if($user->getChannelId()) {
                    $criteria->compare('channel_id', Yii::app()->user->getChannelId());
                } else {
                    echo '此账户尚未开通，请联系管理员';
                    return;
                }
            } else {
                //管理员
                $channel_id = $user->getChannelId();
                if($user->getChannelId()) {
                    $criteria->compare('channel_id', Yii::app()->user->getChannelId());
                }
            }
           
            
            
            //添加搜索条件
            if($_GET) {
                $tag = $_GET['tag'];
                $adv_name = $_GET['adv_name'];
                $adv_id = "";
                if($adv_name){
                    $advertise = Advertise::model();
                    $adv_id = $advertise->getAdvId($adv_name);
                }

                $startTime = $_GET['startTime'];
                $endTime = $_GET['endTime'];
                $criteria->compare('tag', $tag);
                $criteria->compare('adv_id', $adv_id);
                if($startTime){
                    $criteria->addBetweenCondition('date', $startTime, $endTime);
                }
            }

            $criteria->order = "date desc";
            $dataProvider = new CActiveDataProvider('AdvData',  array(
                    'criteria' => $criteria,
                    'pagination' => array(
                        'pageSize' => 20,
                        'pageVar' => 'page',
                    )
                )
            );

            
            $this->render('index', array(
                'attributes' => array('date', 'adv.name', 'tag', 'download_number', 'price', 'total_price','star_level'),
                'current_channel_id' => $user->getChannelId(),
                'channels' => Yii::app()->user->isAdmin() ? Channel::model()->findAll() : array(),
                'dataProvider' => $dataProvider
            ));
        }
    }

    public function actionRegister() {
        //$this->layout = 'unlogin';
        $email = isset($_POST['email']) ? $_POST['email'] : null;
        $password = isset($_POST['password']) ? $_POST['password'] : null;
        $phone = isset($_POST['phone']) ? $_POST['phone'] : null;
        $msg = null;
        $isEmail = preg_match("/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/", $email);
        if($isEmail && $password) {
            $user = new User;
            $token = $user->makeToken();
            if($user->insertOneUser($email, $password, $phone, $token)) {
                $id = Yii::app()->db()->getLastInsertId();
                $this->_sendEmail($id, $email, $token);
                $this->redirect('/site/login');
            }
        }

        $this->render('register', array(
            'msg'=>$msg
        ));
    }

    public function actionJump($channel_id) {
        if(!Yii::app()->user->isAdmin()) {
            throw new CHttpException(403 ,'无权访问');
        }
        if($channel_id) {
            $channel = Channel::model()->findByPk($channel_id);
            if(!$channel) {
                throw new CHttpException(404 ,'没有这个渠道');
            }
        }
        Yii::app()->user->setChannelId($channel_id);
        $this->redirect('/');
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect('/');
    }

    public function actionActivateUser() {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $token = isset($_GET['token']) ? $_GET['token'] : null;
        if(User::model()->activateUser($id, $token)) {
            echo "激活成功";
            //$this->redirect('/site/login');
        } else {
            echo "验证失败";
        }
    }
}
