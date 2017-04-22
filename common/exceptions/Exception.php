<?php

namespace common\exceptions;

use yii\web\HttpException;

/** 业务逻辑异常基类
 * 默认 HTTP status code 200
 * 逻辑错误码由 $code 返回.
 * Class Exception
 * @package common\exceptions
 */
class Exception extends HttpException
{
    public function __construct($code = 0, $message = null, \Exception $previous = null)
    {
        if($message === null){
            $message = Code::CODE_MESSAGE[$code];
        }
        parent::__construct($status = 400, $message, $code, $previous);
    }
}