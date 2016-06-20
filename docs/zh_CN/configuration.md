# 配置文件

## 配置格式
```
{
    "name": "Api Test", //测试名称
    "author": "Author", //作者
    "requests": [
        {
            "id": "album", //测试项id
            "url": "http://music.163.com/api/album/", //请求地址
            "method": "GET", //请求方式
            "options": {
                "query": { //query参数，也可以直接ping装在url地址后面
                    "foo": "bar"
                },
                "followRedirect": true, //如果遇到301,302是否跟着转向
                "auth": ["username", "password"], //auth验证账号
                "cert": "/path/to/cert.pem", //请求https接口需要的证书，如果不提供默认使用操作系统的证书
                "timeout": "30", //接口超时时间
                "headers": [
                    "User-Agent" => "Slince-Runner",
                    "Accept" => "application/json",
                    "X-Foo" => ["Bar", "Baz"]
                ],
                "enableCookie": "true", //是否启用cookie
                "cookies": { //自定义cookie
                    "foo": "bar"
                },
                "posts": { //post参数，post请求时有效
                    "foo": "bar"
                },
                "files": { //文件上传，指定文件位置
                    "file": "/path/to/file"
                },
               "proxy": "tcp://127.0.0.1:8888" //代理地址，如果需要配合抓包工具使用，可以使用本参数设置
            },
            "assertions": { //断言
                "body": { //关于body的断言
                    "isJson": "true", //是否是json格式
                    "isXml":"false", //是否是xml格式
                    "hasParameter": "name", //是否含有参数，此项检查会尝试将body转换成json处理，如果是xml返回也会转换，但如果是其他格式可能会提出异常，下同
                    "hasParameters": ["name", "age"], //是否含有某些参数
                    "parameterEqual": { //参数等于指定值
                        "name": "Steven"
                    },
                    "parameterRegex": { //参数符合正则规则
                        "name": "^Steven"
                    }
                },
                "header": {
                    "hasHeader": "Content-Length", //含有某个header
                    "headerEqual": { //header等于
                        "Content-Length": "3000"
                    },
                    "headerRegex": { //header符合某个正则
                        "Content-Length":"\d+"
                    },
                    "headerHasParameters": { //header拥有指定值
                        "Accept-Languages": ["zh_CN", 'en_US']
                    }
                },
                "response": {
                    "isOk": "true", //是否200
                    "isNotFound": "false", //是否404
                    "isXXX": "true" //魔术方法，是否为某个自定义状态码
                }
            },
            "catch": { //待捕获参数
                "body": { //从body捕捉
                    "path.parameter": "foo" //捕捉path.parameter值并以foo命名
                }
                "header": { //从header捕捉
                    "path.parameter": "bar" //捕捉path.parameter值并以bar命名
                }
            }
            ...
        }
    ]
}
```

## 配置说明
通用字段解释见以上备注
- 关于字段捕获
    * 捕获字段支持层级获取
    * 通过{foo}形式使用捕获的字段，注意只有在测试链后面的测试task才能使用前面task捕获的变量
- 关于断言
    所有的断言通过之后，测试task才会通过，否则失败，失败原因可以查看测试报告

