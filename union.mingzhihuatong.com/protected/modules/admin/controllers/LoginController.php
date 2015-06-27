<?php

class LoginController extends Controller
{
    public $layout = "unlogin";

    public function actions() {
        return array(
            'captcha' => 'CCaptchaAction',
            'imgcode'=>array(
                'class'=>"application.components.VerifyImage"
            )
        );
    }
	/**
	 * Displays the login page
	 */
	public function actionIndex()
	{
        $this->layout = 'unlogin';

        $loginForm = new LoginForm();
                
        if(Yii::app()->request->isPostRequest) {
            $loginForm->attributes = array(
                'email' => $_POST['email'],
                'password' => $_POST['password']
            );

            $loginForm->verify = $_POST['verify'];

            if($loginForm->authenticateAdmin()) {
                $this->redirect("/admin/");
            }
        }

        $this->render('login',array(
            'model' => $loginForm
        ));
    }
   
    public function actionShowVerify(){
        $email = $_POST['email'];
        $redis = Yii::app()->redis;
        $counts = $redis->get($email);
        if($counts >= 3){
            $result = true;
            echo json_encode($result);
        }
    }
    public function actionFindPassword() {
        $email = isset($_POST['email']) ? $_POST['email'] : null;
        if(Yii::app()->request->isPostRequest) {
            if(preg_match("/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/", $email)) {
                $user = User::model()->findByAttributes(array('email'=>$email));
                if($user) {
                    if($user->requestResetPassword()) {
                        $message = "已发送验证邮件到邮箱{$email}, 请查收";
                    } else {
                        $message = "已发送验证邮件到邮箱{$email}, 请查收";
                    }
                } else {
                    $message = '您尚未注册，前往注册吧';
                }
            } else {
                $message = '邮箱错误';
            }
        }
        $this->render('findPassword', array(
            'userinput' => isset($_POST) ? $_POST : array(),
            'message' => $message
        ));
    }

    public function actionActivate($token, $id) {
        $this->layout = 'unlogin';
        $password = isset($_POST['password']) ? $_POST['password'] : null;

        $user = User::model()->findByPk($id);

        if(!$user) {
            echo '用户不存在';
            return;
        }

        if(Yii::app()->request->isPostRequest) {
            if($user->activate($token, $password)) {
                $this->render('password', array(
                    'ok' => true
                ));
                return;
            } else {
                $this->render('password', array(
                    'ok' => false,
                    'error' => '激活失败，激活码已失效'
                ));
            }
            return;
        }

        if($user->token !== $token) {
            echo '激活码已失效';
            return;
        }

        $this->render('password');

    }

    public function actionSetPassword($id, $token) {
        $this->layout = 'unlogin';

        $user = User::model()->findByPk($id);

        if(!$user) {
            echo '用户不存在';
            return;
        }

        if(Yii::app()->request->isPostRequest) {
            if($user->resetPassword( $token, $_POST['password'])) {
                $this->render('password', array(
                    'ok' => true
                ));
            } else {
                $this->render('password', array(
                    'ok' => false,
                    'error' => '激活失败，激活码已失效'
                ));
            }
            return;
        }

        if($user->token !== $token) {
            echo '激活码已失效';
            return;
        }

        $this->render('password');
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

    private function _sendEmail($id, $email, $token) {
        $link = 'http://'.$_SERVER['HTTP_HOST'].'/site/login/setPassword/id/'.$id.'/token/'.$token;
        $message = <<<EOT
        <p><b>亲爱的用户您好！</b></p>
        <p>这是一封铭智华通广告联盟密码设置邮件，<a target="_blank" href="{$link}">点击设置密码</a></p>
        <p>如果上述文字点击无效，请把下面网页地址复制到浏览器地址栏中打开：</p>
        <p><a target="_blank" href="{$link}">{$link}</a></p>
EOT;
        $subject = '铭智广告联盟密码设置邮件';
        Yii::app()->mailer->send($email, $subject, $message); 
    }
    
       


}
