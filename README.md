## yii2-fast-api

yii2-fast-api是一个Yii2框架的扩展，用于配置完善Yii2，以实现api的快速开发。
此扩展默认的场景是APP的后端接口开发，因此偏向于实用主义，并未完全采用restfull的标准，方便前端开发处理接口数据以及各种异常。

### Installation

#### 使用 Composer 安装
- 在项目中的 `composer.json` 文件中添加依赖：

```json
"require": {
    "deepziyu/yii-fast-api": "*"
}
```

- 执行 `$ php composer.phar update` 或 `$ composer update` 进行安装。

- 在配置文件中（ Yii2 高级版为 main.php，Yii2 基础版为 web.php ）注入 fast-api 的配置：

```php
// $config 为你原本的配置
$config = yii\helpers\ArrayHelper::merge(
    $config,
    \deepziyu\yii\rest\Controller::getConfig()
);

return $config;
```
### Usage

- 建立控制器

```
class YourController extends deepziyu\yii\rest\Controller
{
    /**
     * 示例接口
     * @param int $id 请求参数
     * @return string version api版本
     * @return int yourId 你的请求参数
     */
    public function actionIndex($id)
    {
        return ['version'=>'1.0.0','yourId'=>$id];
    }
}
```
- 发送请求看看

正常请求
```curl
POST /your/index HTTP/1.1
Host: yoursite.com
Content-Type: application/json

{"id":"10"}
```
返回
```json
{
    "code": 200,
    "data": {
        "version": "1.0.0",
        "yourId": "10"
    },
    "message": "OK"
}
```
缺少参数的请求
```curl
POST /your/index HTTP/1.1
Host: yoursite.com
Content-Type: application/json

```
返回错误
```json
{
    "code": 400,
    "data": {},
    "message": "缺少参数：id"
}
```

- 查看自动生成的Api文档

http ://yoursite.com/route/api/index

![mahua](http://ok0rjq3jz.bkt.clouddn.com/QQ%E6%88%AA%E5%9B%BE20170119165300.png)

### Words In The End
感谢@暗夜在火星 的PhalApi项目，为此Yii2扩展提供设计的思路。

### TODO

- 更完善的文档指南
- Signature 过滤器插件
- 限流插件的使用
- RequestID 以及日志存储追踪的参考