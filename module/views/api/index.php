<?php
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Api-文档-首页</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">

    <link rel="stylesheet" href="https://staticfile.qnssl.com/semantic-ui/2.1.6/semantic.min.css">
    <link rel="stylesheet" href="https://staticfile.qnssl.com/semantic-ui/2.1.6/components/table.min.css">
    <link rel="stylesheet" href="https://staticfile.qnssl.com/semantic-ui/2.1.6/components/container.min.css">
    <link rel="stylesheet" href="https://staticfile.qnssl.com/semantic-ui/2.1.6/components/message.min.css">
    <link rel="stylesheet" href="https://staticfile.qnssl.com/semantic-ui/2.1.6/components/label.min.css">
    <style>
        /* 布局 */
        .site-tree, .site-content {
            display: inline-block;
            *display: inline;
            *zoom: 1;
            vertical-align: top;
            font-size: 14px;
        }

        .site-tree {
            width: 325px;
            min-height: 900px;
            padding: 5px 0 20px;
        }

        .site-content {
            width: 899px;
            min-height: 900px;
            padding: 20px 0 10px 20px;
        }

        /* 文档 */
        .site-tree {
            border-right: 1px solid #eee;
        }

        .site-tree .layui-tree {
            list-style: none;
            line-height: 32px;
        }

        .site-tree .layui-tree li i {
            position: relative;
            font-size: 22px;
            color: #000
        }

        .site-tree .layui-tree li a cite {
            padding: 0 8px;
        }

        .site-tree .layui-tree .site-tree-noicon a cite {
            padding-left: 15px;
        }

        .site-tree .layui-tree li a em {
            font-size: 12px;
            color: #bbb;
            padding-right: 5px;
            font-style: normal;
        }

        .site-tree .layui-tree li h2 {
            line-height: 36px;
            border-left: 5px solid #009E94;
            margin: 15px 0 5px;
            padding: 0 10px;
            background-color: #f2f2f2;
        }

        .site-tree .layui-tree li ul {
            list-style: none;
            margin-left: 27px;
            line-height: 28px;
        }

        .site-tree .layui-tree li ul a,
        .site-tree .layui-tree li ul a i {
            color: #777;
        }

        .site-tree .layui-tree li ul a:hover {
            color: #333;
        }

        .site-tree .layui-tree li ul li {
            margin-left: 25px;
            overflow: visible;
            list-style-type: disc; /*list-style-position: inside;*/
        }

        .site-tree .layui-tree li ul li cite,
        .site-tree .layui-tree .site-tree-noicon ul li cite {
            padding-left: 0;
        }

        .site-tree .layui-tree .layui-this a {
            color: #01AAED;
        }

        .site-tree .layui-tree .layui-this .layui-icon {
            color: #01AAED;
        }

        .site-fix .site-tree {
            position: fixed;
            top: 0;
            bottom: 0;
            z-index: 666;
            min-height: 0;
            overflow: auto;
            background-color: #fff;
        }

        .site-fix .site-content {
            margin-left: 220px;
        }

        .site-fix-footer .site-tree {
            margin-bottom: 120px;
        }

        .site-title {
            margin: 30px 0 20px;
        }

        .site-title fieldset {
            border: none;
            padding: 0;
            border-top: 1px solid #eee;
        }

        .site-title fieldset legend {
            margin-left: 20px;
            padding: 0 10px;
            font-size: 22px;
            font-weight: 300;
        }

        .site-text a {
            color: #01AAED;
        }

        .site-h1 {
            margin-bottom: 20px;
            line-height: 60px;
            padding-bottom: 10px;
            color: #393D49;
            border-bottom: 1px solid #eee;
            font-size: 28px;
            font-weight: 300;
        }

        .site-h1 .layui-icon {
            position: relative;
            top: 5px;
            font-size: 50px;
            margin-right: 10px;
        }

        .site-text {
            position: relative;
        }

        .site-text p {
            margin-bottom: 10px;
            line-height: 22px;
        }

        .site-text em {
            padding: 0 3px;
            font-weight: 500;
            font-style: italic;
            color: #666;
        }

        .site-text code {
            margin: 0 5px;
            padding: 3px 10px;
            border: 1px solid #e2e2e2;
            background-color: #fbfbfb;
            color: #666;
            border-radius: 2px;
        }

        .site-table {
            width: 100%;
            margin: 10px 0;
        }

        .site-table thead {
            background-color: #f2f2f2;
        }

        .site-table th,
        .site-table td {
            padding: 6px 15px;
            min-height: 20px;
            line-height: 20px;
            border: 1px solid #ddd;
            font-size: 14px;
            font-weight: 400;
        }

        .site-table tr:nth-child(even) {
            background: #fbfbfb;
        }

        .site-block {
            padding: 20px;
            border: 1px solid #eee;
        }

        .site-block .layui-form {
            margin-right: 200px;
        }
    </style>

</head>
<body>
<div class="ui r" style="max-width: none !important;">
    <div class="site-tree">
        <ul class="layui-tree">
            <?php foreach ($routes as $id => $item): ?>
                <?php if (preg_match('/\*$/', $id)): ?>
                    <li><h2><a href="#<?= $id ?>"><?= $id ?></a></h2></li>
                <?php else: ?>
                    <li class="site-tree-noicon <?= $id == '' ? 'layui-this' : '' ?>">
                        <a href="#<?= $id ?>"><cite><?= $item['description'] ?></cite></a>
                    </li>
                <?php endif; ?>

            <?php endforeach; ?>
        </ul>
    </div>

    <div class="site-content">
        <?php foreach ($routes as $id => $item): ?>
            <div id="<?= $id ?>">
                <?php if (preg_match('/\*$/', $id)): ?>
                    <h1 class="site-h1"><?= $id ?></h1>
                <?php else: ?>
                    <div class="ui raised segment">
                        <span class="ui red ribbon label"><?= $item['description'] ?></span>
                        <div class="ui message">
                            <p>接口链接：<?= $item['id'] ?></p>
                            <p>说明：<?= $item['descComment'] ?></p>
                        </div>
                        <table class="ui red celled striped table">
                            <thead>
                            <tr>
                                <th>请求参数</th>
                                <th>类型</th>
                                <th>是否必须</th>
                                <th>默认值</th>
                                <th>其他</th>
                                <th>说明</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($item['request'] as $param): ?>
                                <tr>
                                    <td><?= $param['name'] ?></td>
                                    <td><?= $param['type'] ?></td>
                                    <td><?= $param['require'] ? '是' : '否' ?></td>
                                    <td><?= $param['default'] ?></td>
                                    <td><?= $param['other'] ?></td>
                                    <td><?= $param['desc'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        <table class="ui blue celled striped table">
                            <thead>
                            <tr>
                                <th>返回参数</th>
                                <th>类型</th>
                                <th>说明</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($item['response'] as $param): ?>
                                <tr>
                                    <td><?= $param[1] ?></td>
                                    <td><?= $param[0] ?></td>
                                    <td><?= $param[2] ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <br/>

                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="layui-footer footer footer-doc">
        <div class="layui-main">
            <p><a href="/layui.com">power by layui</a></p>
            <p>
            </p>
        </div>
    </div>
</body>
</html>
