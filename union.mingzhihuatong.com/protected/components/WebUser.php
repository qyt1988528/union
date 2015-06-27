<?php

class WebUser extends CWebUser {
    //被CAccessControlFilter调用
    public function checkAccess($operation, $params = array(), $allowCaching=true) {
        if($operation === 'admin') {
            return $this->isAdmin();
        }
        return parent::checkAccess($operation, $params, $allowCaching);
    }

    public  function isAdmin() {
        return $this->getState('isAdmin');
    }

    public function isChannel() {
        return !$this->isAdmin();
    }

    public function getChannelId() {
        return $this->getState('channel_id');
    }

    public function setChannelId($channel_id) {
        return $this->setState('channel_id', $channel_id);
    }

    public function getId() {
    }

}
