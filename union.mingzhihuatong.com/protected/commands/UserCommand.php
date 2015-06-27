<?php

class UserCommand extends CConsoleCommand {
    public function actionCreateAdmin($email, $password, $phone) {
        $user = new User;
        $user->role = User::ROLE_ADMIN;
        $user->email = $email;
        $user->password = $password;
        $user->phone = $phone;
        $user->ctime = date("Y-m-d H:i:s");
        $user->save();
    }
    
    /*
    public function actionMail() {
        var_dump(Yii::app()->mailer->send('yuziyu@zhantai.com', 'hello', 'body'));
    }
     */
}
