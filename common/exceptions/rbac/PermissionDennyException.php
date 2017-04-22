<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 2017/4/16
 * Time: 下午1:36
 */
namespace common\exceptions\rbac;

use common\exceptions\Code;
use common\exceptions\Exception;

class PermissionDennyException extends Exception
{
    public function __construct($message = "您没有权限使用当前功能", \Exception $previous = null)
    {
        parent::__construct(Code::RBAC_PERMISSION_DENNY, $message, $previous);
    }
}