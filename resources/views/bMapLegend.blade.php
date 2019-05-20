<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="http://139.217.6.78:9000/assets/css/materialdesignicons.min.css">
    <title>百度地图图例</title>
    <style>
        html, body {
            border: none;
        }

        body {
            margin: 0 3px;
            overflow: hidden;
        }

        #success {
            color: #4CAF50;
        }

        #warning {
            color: #FF9800;
        }

        #error {
            color: #F44336;
        }
    </style>
</head>
<body>
<div style="width: 170px;height: 135px;font-size: 12px;">
    <div>
        <div>
            <span class="mdi mdi-domain mdi-18px mdi-dark"></span> 房建: 中心点
        </div>
        <div>
            <span class="mdi mdi-road-variant mdi-18px mdi-dark"></span> 市政: 中心点 + 线
        </div>
        <div>
            <span class="mdi mdi-nature-people mdi-18px mdi-dark"></span> 绿化: 中心点 + 阴影面
        </div>
        <div>
            <span class="mdi mdi-water mdi-18px mdi-dark"></span> 水利: 中心点 + 线
        </div>
        <div>
            <span id="success" class="mdi mdi-checkbox-blank mdi-18px"></span>: 完成率 ≥ 100%
        </div>
        <div>
            <span id="warning" class="mdi mdi-checkbox-blank mdi-18px"></span>: 70% ≤ 完成率 < 100%
        </div>
        <div>
            <span id="error" class="mdi mdi-checkbox-blank mdi-18px"></span>: 完成率 < 70%
        </div>
    </div>
</div>
</body>
</html>