# PHP域名查询工具

在查询域名时候无聊用 `PHP` 写了一个基于 **阿里云** 接口的域名查询工具，该工具就只有一个类，采用单例模式，支持 CLI 模式运行，以及作为类文件被引用。



#### 项目结构

```
.
├── README.md
└── domain-tool.php

0 directories, 2 files
```



#### 目前支持

* CLI 模式
* 公用方法



#### 使用示例

**CLI 模式**

```php
(AliyunDomain::getInstance())->run();
```

```shell
$ php domain-tool.php -n baidu.com
{
    "code": 1,
    "msg": "baidu.com 已被注册"
}%
```

**公用方法**

```
$aliyunDomain = AliyunDomain::getInstance();
$result = $aliyunDomain->checkDomain('baidu.com');
print_r($result);
{
    "code": 1,
    "msg": "baidu.com 已被注册"
}
```

