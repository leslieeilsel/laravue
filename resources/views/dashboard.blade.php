<!doctype html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Language" content="en"/>
    <meta name="msapplication-TileColor" content="#2d89ef">
    <meta name="theme-color" content="#4188c9">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <title>大屏展示</title>
    <!-- Dashboard Core -->
    <link href="{{ URL::asset('/assets/css/dashboard.css') }}" rel="stylesheet"/>
    <script type="text/javascript" src="https://cdn.staticfile.org/echarts/4.2.1-rc1/echarts-en.common.js"></script>
    <script type="text/javascript"
            src="http://api.map.baidu.com/api?v=3.0&ak=rdxXZeTCdtOAVL3DlNzYkXas9nR21KNu"></script>
    <style>
        .page {
            background: whitesmoke;
        }

        #container {
            height: 658px;
        }

        #departmentChart {
            height: 238px;
        }

        .card {
            margin-bottom: 3px;
        }

        .row1 {
            margin: 3px 3px 0;
        }

        .top-card {
            padding: 3px;
        }

        .top-card .p-3 {
            padding: 8px !important;
        }

        .col-lg-12 {
            padding: 0;
        }

        .row1 .card,
        .row2 .map-card {
            margin-bottom: 3px;
        }

        .main-left {
            margin: 0 6px 0;
        }

        .main-left,
        .main-right {
            width: 400px;
            height: 660px;
        }

        .big-map {
            width: 560px;
            height: 660px;
        }

        .bar-chart,
        .early-warning {
            padding-left: 12px;
            margin-bottom: 6px;
        }

        .table td {
            padding: 5px;
            font-size: 12px;
        }

        .table tbody td span {
            font-size: 12px;
        }

        .bar-chart .card,
        .early-warning .card {
            overflow: scroll;
            height: 327px;
        }

        .project-sort .card {
            height: 218px;
            overflow: scroll;
        }

        .project-desc .card {
            height: 198px;
            overflow: scroll;
        }

        .department-chart .card {
            height: 238px;
            overflow: scroll;
        }

        .project-sort,
        .project-desc,
        .department-chart {
            padding-left: 6px;
        }

        .card-title {
            font-size: 15px;
            font-weight: 600;
        }

        .card-header {
            padding: 0 24px 0 24px;
        }

        .graycircle {
            position: absolute;
            width: 18px;
            height: 18px;
            background: #d0ceca;
            border-radius: 50%;
            border: 1px solid #d0ceca;
        }

        .greencircle {
            position: absolute;
            width: 18px;
            height: 18px;
            background: #19be6b;
            border-radius: 50%;
            border: 1px solid #19be6b;
        }

        .yellowcircle {
            position: absolute;
            width: 18px;
            height: 18px;
            background: #ff9900;
            border-radius: 50%;
            border: 1px solid #ff9900;
        }

        .redcircle {
            position: absolute;
            width: 18px;
            height: 18px;
            background: #ed4014;
            border-radius: 50%;
            border: 1px solid #ed4014;
        }

        .anchorBL {
            display: none;
        }

        h5 {
            font-size: 12px;
            font-weight: 400;
        }

    </style>
</head>
<body>
<div class="page">
    <div class="page-main">
        <div style="width: 1366px;">
            <div class="row1 row row-cards">
                <div class="top-card col-lg-3">
                    <div class="card">
                        <div class="card-body p-3 text-center">
                            <div class="h1 m-0">{{$data['total']}}</div>
                            <div class="text-muted mb-4">项目总数</div>
                        </div>
                    </div>
                </div>
                <div class="top-card col-lg-3">
                    <div class="card">
                        <div class="card-body p-3 text-center">
                            <div class="h1 m-0">{{$data['totalPlan']}}万元</div>
                            <div class="text-muted mb-4">在建项目总投资</div>
                        </div>
                    </div>
                </div>
                <div class="top-card col-lg-3">
                    <div class="card">
                        <div class="card-body p-3 text-center">
                            <div class="h1 m-0">{{$data['accPlan']}}万元</div>
                            <div class="text-muted mb-4">累计完成总投资</div>
                        </div>
                    </div>
                </div>
                <div class="top-card col-lg-3">
                    <div class="card">
                        <div class="card-body p-3 text-center">
                            <div class="h1 m-0">0</div>
                            <div class="text-muted mb-4">累计完成项目总数</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row2 row row-cards" style="clear: both">
            <div class="main-left">
                <div class="col-lg-12 bar-chart">
                    <div class="card">
                        <div id="main" style="height:310px;"></div>
                    </div>
                </div>
                <div class="col-lg-12 early-warning">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">项目进度预警</h4>
                        </div>
                        <div class="card-body o-auto" style="padding: 0;">
                            <table class="table card-table">
                                <thead>
                                <tr>
                                    <th class="w-1">NO.</th>
                                    <th>项目名称</th>
                                    <th>项目预警</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($data['allWarning'] as $k => $allWarning)
                                    <tr>
                                        <td>{{$k + 1}}</td>
                                        <td>{{$allWarning['title']}}</td>
                                        <td><span class="text-muted">{{$allWarning['tags']}}</span></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="big-map">
                <div class="card map-card">
                    <div id="container"></div>
                </div>
            </div>
            <div class="main-right">
                <div class="col-lg-12 project-sort">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">重点项目排名</h4>
                        </div>
                        <div class="card-body o-auto" style="padding: 0;">
                            <table class="table card-table">
                                <thead>
                                <tr>
                                    <th>项目名称</th>
                                    <th>项目金额</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($data['pointList'] as $k => $pointList)
                                    <tr>
                                        <td>{{$pointList['title']}}</td>
                                        <td class="text-right"><span class="text-muted">{{$pointList['amount']}}</span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 project-desc">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">项目概况</h3>
                        </div>
                        <div class="card-body o-auto" style="padding: 0;">
                            <table class="table card-table">
                                <thead>
                                <tr>
                                    <th>项目状态</th>
                                    <th>房建</th>
                                    <th>绿化</th>
                                    <th>市政</th>
                                    <th>水利</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($data['projectStatus'] as $k => $projectStatus)
                                    <tr>
                                        <td>{{$projectStatus['status']}}</td>
                                        <td class="text-right"><span class="text-muted">{{$projectStatus['fj']}}</span>
                                        </td>
                                        <td class="text-right"><span class="text-muted">{{$projectStatus['lh']}}</span>
                                        </td>
                                        <td class="text-right"><span class="text-muted">{{$projectStatus['sz']}}</span>
                                        </td>
                                        <td class="text-right"><span class="text-muted">{{$projectStatus['sl']}}</span>
                                        </td>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 department-chart">
                    <div class="card">
                        <div id="departmentChart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<script type="text/javascript">
  var barChartData = @json($data['typeOption']);
  var mapData = @json($data['mapData']);
  var departmentChartData = @json($data['departmentChartData']);
  // 基于准备好的dom，初始化echarts实例
  var myChart = echarts.init(document.getElementById('main'));
  var departmentChart = echarts.init(document.getElementById('departmentChart'));

  // 指定图表的配置项和数据
  var option = {
    title: [{
      text: barChartData.count,
      x: '24%',
      y: '45%',
      textStyle: {
        fontSize: 24,
        fontWeight: 'normal',
        fontStyle: 'normal',
        color: '#333333'
      }
    }, {
      text: '总计',
      x: '30%',
      y: '55%',
      textStyle: {
        fontSize: 12,
        fontWeight: 'normal',
        fontStyle: 'normal',
        color: '#666666'
      }
    }],
    tooltip: {
      trigger: 'item',
      formatter: '{a} <br/>{b}: {c} ({d}%)'
    },
    legend: {
      orient: 'vertical',
      top: '40%',
      right: '5%',
      itemGap: 40,
      // formatter: function (name) {
      //   return 'Legend ' + name;
      // },
      textStyle: {
        fontSize: 12,
        padding: [0, 0, 0, 4],
      },
      data: barChartData.legend
    },
    color: ['#fa4b46', '#ffc502', '#ae9fff', '#6ecdfa'],
    series: [{
      name: '告警统计',
      type: 'pie',
      center: ['33.5%', '50%'],
      radius: ['45%', '55%'],
      startAngle: 360,
      avoidLabelOverlap: false,
      label: {
        normal: {
          show: false,
          position: 'center'
        },
        emphasis: {
          show: false,
          textStyle: {
            fontSize: '30',
            fontWeight: 'bold'
          }
        }
      },
      labelLine: {
        normal: {
          show: false
        }
      },
      data: barChartData.data
    }]
  };
  var option2 = {
    backgroundColor: '#fff',
    tooltip: {
      trigger: "axis",
      padding: [8, 10],
      backgroundColor: 'rgba(0,0,0,0.5)',
      axisPointer: {
        type: "shadow",
        textStyle: {
          color: "#fff"
        }
      }
    },
    legend: {
      data: ['计划投资', '实际完成投资'],
      align: 'left',
      right: 0,
      textStyle: {
        color: "#333",
        fontSize: 12,
        fontWeight: 200
      },
      itemWidth: 14,
      itemHeight: 14,
      itemGap: 30
    },
    grid: {
      left: '5',
      right: '5',
      bottom: '5',
      top: '15',
      containLabel: true
    },
    label: {
      show: true,
      position: 'top',
      color: '#333',
      fontSize: 12,
      fontWeight: 700
    },
    xAxis: [{
      type: 'category',
      offset: 1,
      data: departmentChartData.departmentNameArr,
      axisLine: {
        show: false
      },
      axisTick: {
        show: false
      },
      axisLabel: {
        show: true,
        textStyle: {
          color: "#333",
          fontSize: 12,
          fontWeight: 200
        }
      },
    }],
    yAxis: [{
      type: 'value',
      axisLabel: {
        show: false
      },
      axisTick: {
        show: false
      },
      axisLine: {
        show: false
      },
      splitLine: {
        show: false
      }
    }],
    series: [{
      name: '计划投资',
      type: 'bar',
      data: departmentChartData.departmentPlan,
      barWidth: 15, //柱子宽度
      barGap: 1, //柱子之间间距
      itemStyle: {
        normal: {
          color: '#0071c8',
          opacity: 1,
        }
      }
    }, {
      name: '实际完成投资',
      type: 'bar',
      data: departmentChartData.departmentAcc,
      barWidth: 15,
      barGap: 1,
      itemStyle: {
        normal: {
          color: '#fdc508',
          opacity: 1,
        }
      }
    }]
  };

  // 使用刚指定的配置项和数据显示图表。
  myChart.setOption(option);
  departmentChart.setOption(option2);

  // enableMapClick: false 构造底图时，关闭底图可点功能
  let map = new BMap.Map("container", {enableMapClick: false, minZoom: 13, maxZoom: 17});
  map.centerAndZoom(new BMap.Point(108.720027, 34.298497), 15);
  map.enableScrollWheelZoom(true);// 开启鼠标滚动缩放

  let date = new Date();
  let y = date.getFullYear();
  let m = date.getMonth();
  let plan_amount = 0;
  mapData.forEach(function (project) {
    project.projectPlan.forEach(function (year) {
      if (year.date === y) {
        year.month.forEach(function (month) {
          if (m > month.date) {
            plan_amount = parseFloat(plan_amount) + parseFloat(month.amount);
          }
        })
      }
    });
    let acc_complete = 0;
    if (project.scheduleInfo) {
      acc_complete = project.scheduleInfo.acc_complete;
    } else {
      acc_complete = 0;
    }
    let Percentage = 0;
    if (plan_amount > 0 && plan_amount > acc_complete) {
      Percentage = (plan_amount - acc_complete) / plan_amount;
    }
    let Percentage_con = '';
    Percentage = parseFloat(Percentage);
    let war_color = 'greencircle';
    let point_color = '#19be6b';
    if (Percentage <= 0) {
      Percentage_con = "已完成" + acc_complete + "万，" + "和预期一样";
    } else {
      Percentage = Percentage.toFixed(2);
      if (Percentage > 0.1 && Percentage < 0.2) {
        war_color = 'yellowcircle';
        point_color = '#ff9900';
      } else if (Percentage > 0.3) {
        war_color = 'redcircle';
        point_color = '#ed4014';
      }
      Percentage_con = "已完成" + acc_complete + "万，" + "比预期延缓" + Percentage * 100 + "%";
    }
    if (project.is_audit === 1 || project.is_audit === 3) {
      // 添加标注
      let center = project.center_point;
      let centerArr = center.split(",");

      let marker = new BMap.Marker(new BMap.Point(centerArr[0], centerArr[1]), {
        // 指定Marker的icon属性为Symbol
        icon: new BMap.Symbol(BMap_Symbol_SHAPE_POINT, {
          scale: 1.2,//图标缩放大小
          fillColor: point_color,//填充颜色
          fillOpacity: 1//填充透明度
        })
      });
      // let marker = new BMap.Marker(
      //   new BMap.Point(centerArr[0], centerArr[1])
      // );
      // new BMap.Point.setStyle({
      //   color:'#eee'
      // })
      map.addOverlay(marker);
      // 添加多边形
      let positions = project.positions;
      let positionsArr = positions.split(";");
      let pointArr = [];
      positionsArr.forEach(function (e, i) {
        let pArr = e.split(",");
        pointArr[i] = new BMap.Point(pArr[0], pArr[1]);
      });
      let polygon = new BMap.Polygon(pointArr, {
        strokeColor: "blue",
        strokeWeight: 2,
        strokeOpacity: 0.5
      });
      map.addOverlay(polygon);
      // 添加label
      var label = new BMap.Label(project.title, {
        offset: new BMap.Size(25, 3)
      });
      label.setStyle({
        border: "1px solid #2196F3"
      });
      marker.setLabel(label);
      // 添加弹窗
      let statusColor = '';
      switch (project.status) {
        case '计划':
          statusColor = 'yellowcircle';
          break;
        case '在建':
          statusColor = 'greencircle';
          break;
        case '完成':
          statusColor = 'graycircle';
          break;
      }
      let description = project.description;
      description = description === null ? '' : description;
      description = description === undefined ? '' : description;
      let sContent =
        "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>项目名称：" + project.title + "</h5>" +
        "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>项目类型：" + project.type + "</h5>" +
        "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>投资状态：<span class=" + statusColor + "></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>" + project.status + "</span></h5>" +
        "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>投资概况：" + description + "</h5>" +
        "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>投资进度：<span class='" + war_color + "'></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>" + Percentage_con + "</span></h5>";
      addClickHandler(sContent, marker, map);
    }
  });
  // var map = new BMap.Map("container");
  // // 创建地图实例
  // var point = new BMap.Point(108.720027, 34.298497);
  //
  // map.enableScrollWheelZoom();
  // // 创建点坐标
  // map.centerAndZoom(point, 15);
  // map.addControl(new BMap.NavigationControl());
  // map.addControl(new BMap.ScaleControl());
  // map.addControl(new BMap.OverviewMapControl());
  // map.addControl(new BMap.MapTypeControl());
  // // 初始化地图，设置中心点坐标和地图级别
  //
  // var polygon = new BMap.Polygon([
  //   new BMap.Point(108.705233, 34.299387),
  //   new BMap.Point(108.711162, 34.299469),
  //   new BMap.Point(108.706032, 34.295778),
  //   new BMap.Point(108.711809, 34.29589),
  // ], {
  //   strokeColor: "blue",
  //   strokeWeight: 2,
  //   strokeOpacity: 0.1
  // }); //创建多边形
  // map.addOverlay(polygon); //增加多边形
  // var center = new BMap.Point(108.708638, 34.297493);
  // var marker = new BMap.Marker(center); // 创建标注
  // map.addOverlay(marker); // 将标注添加到地图中
  // var label = new BMap.Label("沣西新城西部云谷7号楼项目", {
  //   offset: new BMap.Size(20, -10)
  // });
  // label.setStyle({
  //   border: "1px solid #2196F3"
  // });
  // marker.setLabel(label);
  //
  // var sContent =
  //   "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>项目名称：沣西新城西部云谷7号楼项目</h5>" +
  //   "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>项目类型：房建</h5>" +
  //   "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>投资状态：<span class='greencircle'></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>在建</span></h5>" +
  //   "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>投资概况：本项目建设内容为房建，项目总投资为100万元。资金来源通过自筹及申请中省市专项建设资金解决。</h5>" +
  //   "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>投资进度：<span class='redcircle'></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>已完成53万，比预期延缓10%</span></h5>" +
  //   "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>创建时间：2019-01-05</h5>"
  // var infoWindow = new BMap.InfoWindow(sContent); // 创建信息窗口对象
  // map.centerAndZoom(point, 15);
  // map.addOverlay(marker);
  // marker.addEventListener("click", function () {
  //   this.openInfoWindow(infoWindow);
  // });
  //
  // var polygon2 = new BMap.Polygon([
  //   new BMap.Point(108.730882, 34.30175),
  //   new BMap.Point(108.737997, 34.295159),
  //   new BMap.Point(108.737242, 34.30263),
  //   new BMap.Point(108.732535, 34.294727)
  // ], {
  //   strokeColor: "red",
  //   strokeWeight: 2,
  //   strokeOpacity: 0.5
  // }); //创建多边形
  // map.addOverlay(polygon2); //增加多边形
  // var center2 = new BMap.Point(108.735518, 34.300319);
  // var marker2 = new BMap.Marker(center2); // 创建标注
  // map.addOverlay(marker2); // 将标注添加到地图中
  // var label2 = new BMap.Label("沣西新城第三小学建设项目", {
  //   offset: new BMap.Size(20, -10)
  // });
  // label2.setStyle({
  //   border: "1px solid #2196F3"
  // });
  // marker2.setLabel(label2);
  //
  // var sContent2 =
  //   "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>项目名称：沣西新城西部云谷7号楼</h5>" +
  //   "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>项目类型：房建</h5>" +
  //   "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>投资状态：<span class='greencircle'></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>在建</span></h5>" +
  //   "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>投资概况：本项目建设内容为房建，项目总投资为100万元。资金来源通过自筹及申请中省市专项建设资金解决。</h5>" +
  //   "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>投资进度：<span class='redcircle'></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>已完成53万，比预期延缓10%</span></h5>" +
  //   "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>创建时间：2019-01-05</h5>"
  // var infoWindow2 = new BMap.InfoWindow(sContent2); // 创建信息窗口对象
  // // map.centerAndZoom(point, 15);
  // map.addOverlay(marker2);
  // marker2.addEventListener("click", function () {
  //   this.openInfoWindow(infoWindow2);
  // });
  function addClickHandler(content, marker, map) {
    marker.addEventListener("click", function (e) {
      openInfo(content, e, map);
    });
  }

  function openInfo(content, e, map) {
    let p = e.target;
    let point = new BMap.Point(p.getPosition().lng, p.getPosition().lat);
    let infoWindow = new BMap.InfoWindow(content); // 创建信息窗口对象
    map.openInfoWindow(infoWindow, point); //开启信息窗口
  }
</script>
</html>