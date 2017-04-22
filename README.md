Openmore 基于Yii2 开源API服务
===============================
关于Openmore
-------------------
Openmore团队是目前北京的一家创业公司内里的几个主程自发组织的开源团队, 团队目标是将创业过程中技术团队遇到的技术经验进行开源分享, 本着更开放,更高效的原则帮助中国的移动开发者填坑,减少开发成本,同时吸收大家的意见与建议。
项目组成
-------
```
yii2-api-server: App接口及后台管理接口服务, 基于Yii2框架实现, 通过DI对业务逻辑尽量进行解耦, 中间添加service层
vue-app-cms:    App的后台管理端,基于Vue2.0实现, 前后端完全分离, 所有接口通过yii2-api-sever提供
ios-demo:       IOS客户端, 使用Objc实现, 实现了App里经常出现的绝大多数业务, 如:推送, 业务后台管理, 运营后台管理, 在线即时沟通, 三方登录分享, 订单业务管理, 优惠券==
android-demo:   android客户端, 内容同上
```
运行环境
-------
```
1. Linux（推荐centos7+）
2. Php >=7.0
3. Mysql >=5.5
4. Nodejs >=4.x
5. redis
6. nginx
# 推荐1台以上的云主机,如:一台Server,一台DB,一台开发环境(测试环境)
```
第三方技术
---------
一个好的有效率团队最主要的工作是避免造轮子,本项目中使用到了一些第三方的技术, 这些技术经过我们长达两年的使用,稳定性和可靠性都不错,因此我们在项目里直接集成了这些技术,并且在服务器端进行了深度的模块化,在使用时非常方便
```
1. 极光推送;
2. 云片短信平台;
3. 诸葛io统计;
4. 七牛云存储;
5. 微信开放平台;
```
CI及一键发布
----------
1. [Jenkins](https://jenkins-ci.org)
2. [capistrano](http://www.capistranorb.com)

推荐服务器架构
------------
```
API Server & Web Server       DB & redis            Development Env
+---------------------+ +---------------------+ +---------------------+
|                     | |                     | |                     |
|   Php7.0 + nginx    | |    Mysql + Redis    | |   API Dev Server    |
|                     | |                     | |   + Web Server      |
|     + Nodejs        | |                     | |   + DB & Redis      |
|                     | |                     | |                     |
+---------------------+ +---------------------+ +---------------------+
```
使用方法
-------
1. fork 代码`git clone https://github.com/open-more/yii2-api-server.git`
2. 安装 vendor`composer install`
3. 复制配置文件 `php init --env=Development --overwrite=all`
4. 检查本地环境 `php requirements`
5. 创建自己的数据, 修改 `common/config/main-local.php` 配置数据库信息
6. 在项目根目录下,执行 `php yii migrate && php yii migrate --migrationPath=@yii/rbac/migrations`
DIRECTORY STRUCTURE
-------------------

```
common
    config/                 通用配置, 数据库等
    activeRecords/          基于表的基础模型
    queries/                基本模型的查询逻辑
    services/               通用业务逻辑层
    components/             自定义组件
    exceptions/             自定义异常, 正常返回200, 自定义异常返回400
    tests/                  通用业务测试
api
    config/                 通用配置, 数据库等
    web/                    API入口
    services/               API相关业务逻辑层
    modules/                API下的模块
        v1/                 v1接口
            controllers/    控制器
            models/         和UI逻辑相关模型类

    tests/                  通用业务测试
backend                     与api目录结构基本一致
    ...
frontend                    与api目录结构基本一致
    ...
vendor                      第三方composer包
```
接口规范
-------
本代码基于RESTful规范编写, 具体参考:https://en.wikipedia.org/wiki/Representational_state_transfer, 中文请参考:http://www.ruanyifeng.com/blog/2011/09/restful

####请求及响应数据都使用json, 请求Head示例:
```
Accept = application/json
Content-Type: application/json; charset=utf-8
Authorization: Bearer [ACCESS_TOKEN]  授权token
Accept-Encoding: deflate,gzip
X-DEV-MODE：开发环境下为dev, 测试环境下为beta，生产环境下没有或prod
X-PLATFORM：android为ANDROID, IOS为IOS，PC浏览器为PC，手机浏览器为MOBILE
X-APP-VERSION：App版本，固定为A.B.C格式
X-DEVICE：Android为设备厂商名如：huawei P10，IOS为iPhone7，浏览器为浏览器名：qq v5.1
X-DEVICE-TOKEN：dev_id的加密token，当App第一次启动时会写入到设备里并加密。
X-IDFA：ios的广告id(可无)
```
####响应结果示例:

####请求成功，业务成功
Http status code = 200
```
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiIsImp0aSI6IjE0OTA5NTQwOTBTWlJVIn0.eyJpc3MiOiJodHRwOlwvXC93d3cud2VueGlhb3lvdS5jb20iLCJqdGkiOiIxNDkwOTU0MDkwU1pSVSIsImlhdCI6MTQ5MDk1NDA5MCwiZXhwIjoxNDkyMTYzNjkwLCJzY29wZSI6ImFwcCIsIm5vbmNlIjoiOTcyM0MzIn0.6ICcLPr5E6ca9wG5005djtrs6dZexx4nC1vZP9Z1koc",
  "expires_in": 1490961290
}
```
####请求成功，业务失败
Http status code = 400
```
{
  "name": "Bad Request",
  "message": "用户:[michael@openmore.org]已经存在",
  "code": 40022,
  "status": 400,
  "type": "common\\exceptions\\data\\ValidateFailedException"
}
```

####请求失败
```
{
  "name": "Unauthorized",
  "message": "Your request was made with invalid credentials.",
  "code": 0,
  "status": 401,
  "type": "yii\\web\\UnauthorizedHttpException"
}
```
status：表示Http状态码，如果Http请求成功，该值为200

code：表示业务状态码，如果code = 0，表示业务请求成功，可以从data里取结果数据

message：表示状态信息，业务请求成功，返回OK，其它情况下返回提示信息

name、type：只有Http请求失败时才返回，用于调试信息。

```