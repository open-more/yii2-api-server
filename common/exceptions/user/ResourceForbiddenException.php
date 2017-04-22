<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 2017/4/18
 * Time: 下午12:28
 */

namespace common\exceptions\user;

use common\exceptions\Code;
use common\exceptions\Exception;


class ResourceForbiddenException extends Exception
{
    public function __construct($message = "资源没有访问权限: 只能访问自己的资源", \Exception $previous = null)
    {
        parent::__construct($message, Code::RESOURCE_FORBIDDEN, $previous);
    }
}