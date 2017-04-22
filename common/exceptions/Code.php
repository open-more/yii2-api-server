<?php

namespace common\exceptions;

class Code
{
    const DEFAULT_ERROR       = 0;
    // 验证码不正确
    const VERIFY_CODE_FAILED = 10001;
    // 用户被拉黑
    const USER_BANNED = 40003;
    // 数据不存在
    const DATA_NOT_FOUND = 40004;
    // 数据验证失败
    const VALIDATE_FAILED = 40022;

    // 没有权限访问资源
    const RESOURCE_FORBIDDEN        = 40100;
    // RBAC 授权失败
    const RBAC_PERMISSION_DENNY     = 40101;
    // 密码不正确
    const USER_INCORRECT_PASSWORD   = 40300;

    // JWT Token
    // token签名错误
    const TOKEN_SIGN_ERROR      = 40300;
    // 域错误
    const TOKEN_SCOPE_ERROR     = 40301;
    // 用户被拒绝访问
    const TOKEN_USER_REFUSE     = 40302;
    // token过期,需要刷新
    const TOKEN_NEED_REFRESH    = 40303;
    // token已经失效,需要重新登录
    const TOKEN_EXPIRED         = 40304;
    // token在其它设备登录
    const TOKEN_KICK_OFF        = 40305;
    // 1分钟内太多请求
    const TOKEN_TOO_MANY_REQ    = 40306;
    // token解码失败
    const TOKEN_PARSE_ERROR    = 40307;

    const CODE_MESSAGE = [
        self::DEFAULT_ERROR => "啊哦~服务器出现了一个错误",
        self::VERIFY_CODE_FAILED => '验证码错误~',
        self::USER_INCORRECT_PASSWORD => '密码错误~',

        self::TOKEN_SIGN_ERROR => '授权失败: token签名错误',
        self::TOKEN_SCOPE_ERROR => '授权失败: 域错误',
        self::TOKEN_USER_REFUSE => '授权失败: 用户被拒绝访问',
        self::TOKEN_NEED_REFRESH => '授权失败: token过期,需要刷新',
        self::TOKEN_EXPIRED => '授权失败: token已经失效,需要重新登录',
        self::TOKEN_KICK_OFF => '授权失败: 账号在其它设备登录',
        self::TOKEN_TOO_MANY_REQ => '授权失败: 请求太频繁',
        self::TOKEN_PARSE_ERROR => '授权失败: token解码失败',

    ];


}