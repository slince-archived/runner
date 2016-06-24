# 接口自动化测试工具

基于php的接口自动化测试工具

## 安装
程序采用composer安装，如果您没有安装composer请先安装composer，参考链接
[composer官网](https://getcomposer.org)，[composer中文网](http://www.phpcomposer.com/)
在命令行或者终端执行
```
composer global require slince/runner *@dev
```
即可安装

## 使用

runner采用json文件配置，支持的字段及解释见[配置](./docs/zh_CN/configuration.md);
或者你可以拷贝[样例文件](./runner.json)到你的工作目录下;

打开控制台或者linux终端切换到你的工作目录，执行命令
```
runner
```
等待运行结束，可以查看工作目录下的report.xlsx文件查看接口测试结果

注意：如果提示您没有找到runner命令，您需要将composer全局bin目录加入到全局变量中去

### 请求关键参数设置

- 设置代理，proxy
如果您的测试需要代理，或者您需要配合抓包工具比如charles或者fiddler抓包，那么您可以设置本参数，
比如设置抓包代理 `"proxy": "tcp://127.0.0.1:8888"`，测试工具发出的请求与响应您都可以通过代理工具查看

- 自定义header，headers
自定义header比较容易实现，只要传递键值对即可，具体实例看参考文档

- 设置cookie,cookies
cookies字段接受数组，每个数组元素是一个cookie对象，cookie对象支持的属性有name,value,expires,path和domian

```
"cookies": [
  {"name": "foo","value": "bar"}
]
```
cookie的支持是默认开启的，如果你想关闭cookie，需要设置enableCookie字段为false，同时也要从配置里移除cookies字段；
因为如果使用cookies字段则意味着你需要cookie

### 测试断言

一个接口的测试成功与否是通过断言实现的，runner支持三种断言，response、header和body断言

- response断言，response仅仅是对响应状态码的判断，支持三个断言方法
    * `isOk` 是否是200
    * `isNotFound` 是否是404
    * `isXXX` 魔术断言方法，xxx可以被任意自定义状态码取代
- header断言，是对响应头的断言，支持的断言方法
    * `hasHeader` 是否存在header
    * `headerEqual` header值是否等于
    * `headerRegex` header是否符合某个正则规则
    * `headerHasValues` header是否有某些值，比如Accept-Language是否存在zh_CN， en_US
- body断言，body是对响应内容的断言
    * `isJson` 是否是合法的json响应
    * `isXMl` 是否是xml响应
    * `hasParameter` 是否存在某个参数
    * `hasParameters` 是否存在某些参数
    * `parameterEqual` 参数是否等于
    * `parameterRegex` 参数是否符合某个正则规则
    
注意：
1. 关于json与xml的判断并不依赖响应头信息的`Content-Type`；
2. 如果需要参数断言方法判断的话，runner会优先将body转换成json去处理,如果body不是个合法的结构那么所有的参数判断都将无法通过；
3. 参数名支持层级获取，例
```
{
   path: {
      to: {
         foo:bar
      }
   }
}
```
你可以通过`path.to.foo`的形式去判断bar