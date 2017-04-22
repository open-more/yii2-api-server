<?php

namespace common\exceptions\data;

use common\exceptions\Code;
use common\exceptions\Exception;

class DataNotFoundException extends Exception
{
    public function __construct($message = "没有数据", \Exception $previous = null)
    {
        parent::__construct(Code::DATA_NOT_FOUND, $message, $previous);
    }
}