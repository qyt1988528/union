<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
//

return CMap::mergeArray(require_once(dirname(__FILE__) .'/common.php'), array(
    'components' => array(
        'db'=>array(
            'connectionString' => 'mysql:host=localhost;dbname=app_shufawu_com',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ))
    )
);
