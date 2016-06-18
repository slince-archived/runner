# 配置文件

## 配置格式
```
{
    name: "Api Test", //测试名称
    author: "Author": //作者
    requests: [
        {
            id: "album", //测试项id
            url: "http://music.163.com/api/album/", //请求地址
            method: "GET", //请求方式
            query: { //query参数，也可以直接ping装在url地址后面
                id: 2
            },
            followRedirect: true, //如果遇到301,302是否跟着转向
            auth: ["username", "password"], //auth验证账号
            enableCookie: true, //是否启用cookie
            cookies: { //自定义cookie
                "foo": "bar"
            },
            assertions: [ //响应断言
                body: { //关于body的断言
                    isJson: true, //是否是json格式
                    isXml:false, //是否是xml格式
                    hasParameter: "name", //是否含有参数，此项检查会尝试将body转换成json处理，如果是xml返回也会转换，但如果是其他格式可能会提出异常，下同
                    parameterEqual: ["name", "Steven"], //参数等于指定值
                    parameterRegex: ["name", "^Steven"]
                },
                header: {
                    hasHeader: "Content-Length",
                    headerEqual: ["Content-Length", 3000],
                    headerRegex: ["Content-Length", "\d+"],
                    headerHasParameters: ["Accept-Languages": ["zh_CN", 'en_US']]
                },
                response: {
                    statusCode: [200, 201],
                    isOk: true,
                    isNotFound: false,
                    isXXX: true
                }
            ]
            ...
        }
    ]
}
```
