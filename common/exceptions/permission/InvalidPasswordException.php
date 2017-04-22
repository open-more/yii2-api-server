<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 2017/4/18
 * Time: 下午12:28
 */

namespace common\exceptions\permission;


use common\exceptions\Code;
use common\exceptions\Exception;

class InvalidPasswordException extends Exception
{
    public function __construct($message = "密码不正确", \Exception $previous = null)
    {
        parent::__construct(Code::USER_INCORRECT_PASSWORD, $message, $previous);
    }
}