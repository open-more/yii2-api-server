<?php

namespace common\exceptions\data;

use common\exceptions\Code;
use common\exceptions\Exception;

class ValidateFailedException extends Exception
{
    public function __construct($message = "数据验证失败",  \Exception $previous = null)
    {
        parent::__construct($code = Code::VALIDATE_FAILED, $$message, previous);
    }
}