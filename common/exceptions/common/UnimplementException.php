<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 2017/4/18
 * Time: 下午12:28
 */

namespace common\exceptions\common;


use common\exceptions\Code;
use common\exceptions\Exception;

class UnimplementException extends Exception
{
    public function __construct($message = "功能还未实现", \Exception $previous = null)
    {
        parent::__construct(Code::USER_INCORRECT_PASSWORD, $message, $previous);
    }
}