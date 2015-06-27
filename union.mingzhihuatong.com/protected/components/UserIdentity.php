<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
        $user = User::model()->findByAttributes(array('email'=>$this->username));
        if($user) {
            if(crypt($this->password, $user->password) == $user->password) {
                if($user->isAdmin()) {
                    $this->setState('isAdmin', true);
                } else {
                    $this->setState('isAdmin', false);
                    $this->setState('channel_id', $user->channel_id);
                }
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
