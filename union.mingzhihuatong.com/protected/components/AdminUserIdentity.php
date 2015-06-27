<?php

class AdminUserIdentity extends UserIdentity {
    public function authenticate()
	{
        $user = User::model()->findByAttributes(array('email'=>$this->username));
        if($user && $user->isAdmin()) {
            if(crypt($this->password, $user->password) == $user->password) {
                $this->setState('isAdmin', true);
                $this->setState('id', $user->id);
			    $this->errorCode=self::ERROR_NONE;
            } else {
			    $this->errorCode=self::ERROR_PASSWORD_INVALID;
            }
        } else {
			$this->errorCode=self::ERROR_USERNAME_INVALID;
        }
		return !$this->errorCode;
	}
}