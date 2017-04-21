<?php

namespace api\modules\v1\controllers;

use Yii;

class UserController extends Controller
{
    /**
     * 登录获取 token
     * {"mobile": "15652056667", "verify_code": "998877"}
     * @return array
     */
    public function actionLogin()
    {
        print_r(Yii::$app->request->bodyParams);
        return ['token' => 123123];
    }
}