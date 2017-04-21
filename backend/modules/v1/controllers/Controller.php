<?php

namespace backend\modules\v1\controllers;

use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\web\Response;

class Controller extends \yii\rest\Controller
{
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'corsFilter' => [
                'class' => Cors::class,
            ],
            'authenticator' => [
                'class' => HttpBearerAuth::className(),
                'only' => $this->only(),
                'optional' => $this->optional(),
                'except' => ['options'],
            ],
        ];
    }

    protected function only()
    {
        return [];
    }

    protected function optional()
    {
        return [];
    }
}