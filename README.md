医² (Medchannel) 服务端项目
===============================
1. fork 代码`git clone https://git.oschina.net/medchannel/server.git medchannel`
2. 安装 vendor`composer install`
3. 复制配置文件 `php init --env=Development --overwrite=all`
4. 检查本地环境 `php requirements`
5. 修改 `common/config/main-local.php` 配置数据库信息

DIRECTORY STRUCTURE
-------------------

```
common
    config/              通用配置, 数据库等
    models/              基于表的基础模型
    tests/               通用业务测试  
api
    config/              Api 接口配置, 路由等
    models/              继承模型
    runtime/             运行时文件
    tests/               Api 测试   
    web/                 入口脚本
```
