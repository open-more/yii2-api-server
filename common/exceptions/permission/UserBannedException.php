<?php

namespace common\exceptions\permission;

use common\exceptions\Code;
use common\exceptions\Exception;

class UserBannedException extends Exception
{
    public function __construct($message = "登陆异常", \Exception $previous = null)
    {
        parent::__construct(Code::USER_BANNED, $message, $previous);
    }
}