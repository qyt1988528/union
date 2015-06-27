<?php

class IndexController extends AdminController {

    public function filters()
    {
        return array(
            'accessControl'
        );
    }
    public function accessRules() {                                             
        //除登录操作外，其他action均需登录                                      
        return array(                                                           

            array(                                                              
                'allow',
                'actions'=>array('index'),                                                        
                'users' => array('@'),                                          
            ),                                                                  
            array(
                'deny',
                'users' => array('*'),
            ),
        );                                                                      
    }        
    public function actionIndex() {
        $this->render('index');
    }
}
