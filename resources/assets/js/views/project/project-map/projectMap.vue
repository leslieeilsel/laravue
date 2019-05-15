<template>
  <Card>
    <Row>
      <Form ref="searchForm" :model="searchForm" inline :label-width="70" class="search-form">
        <FormItem label="项目状态" prop="status">
          <Select v-model="searchForm.status" style="width: 200px">
            <Option value='-1' key='-1'>全部</Option>
            <Option v-for="item in dict.status" :value="item.value" :key="item.value">{{item.title}}</Option>
          </Select>
        </FormItem>
        <FormItem label="项目名称" prop="title">
          <Input clearable v-model="searchForm.title" placeholder="支持模糊搜索" style="width: 200px"/>
        </FormItem>
        <span v-if="drop">
          <Form-item label="项目编号" prop="num">
            <Input clearable v-model="searchForm.num" placeholder="请输入项目编号" style="width: 200px"/>
          </Form-item>
          <Form-item label="投资主体" prop="subject">
            <Input clearable v-model="searchForm.subject" placeholder="支持模糊搜索" style="width: 200px"/>
          </Form-item>
          <Form-item label="承建单位" prop="unit">
            <Input clearable v-model="searchForm.unit" placeholder="支持模糊搜索" style="width: 200px"/>
          </Form-item>
          <FormItem label="项目类型" prop="type">
            <Select v-model="searchForm.type" style="width: 200px">
              <Option value='-1' key='-1'>全部</Option>
              <Option v-for="item in dict.type" :value="item.value" :key="item.value">{{ item.title }}</Option>
            </Select>
          </FormItem>
          <FormItem label="建设性质" prop="build_type">
            <Select v-model="searchForm.build_type" style="width: 200px" filterable>
              <Option value='-1' key='-1'>全部</Option>
              <Option v-for="item in dict.build_type" :value="item.value" :key="item.value">{{ item.title }}</Option>
            </Select>
          </FormItem>
          <FormItem label="资金来源">
            <Select v-model="searchForm.money_from" prop="money_from" style="width: 200px">
              <Option value='-1' key='-1'>全部</Option>
              <Option v-for="item in dict.money_from" :value="item.value" :key="item.value">{{ item.title }}</Option>
            </Select>
          </FormItem>
          <FormItem label="项目标识" prop="is_gc">
            <Select @on-change="onSearchIsGcChange" v-model="searchForm.is_gc" style="width: 200px"
                    placeholder="是否为国民经济计划">
              <Option value='-1' key='-1'>全部</Option>
              <Option v-for="item in dict.is_gc" :value="item.value" :key="item.value">{{item.title}}</Option>
            </Select>
          </FormItem>
          <FormItem label="国民经济计划分类" prop="nep_type">
            <Select v-model="searchForm.nep_type" style="width: 200px" :disabled="searchNepDisabled">
              <Option value='-1' key='-1'>全部</Option>
              <Option v-for="item in dict.nep_type" :value="item.value" :key="item.value">{{item.title}}</Option>
            </Select>
          </FormItem>
        </span>
        <Form-item style="margin-left:-70px;" class="br">
          <Button @click="createMap" type="primary" icon="ios-search">搜索</Button>
          <Button @click="handleResetSearch('searchForm')">重置</Button>
          <a class="drop-down" @click="dropDown">
            {{dropDownContent}}
            <Icon :type="dropDownIcon"></Icon>
          </a>
        </Form-item>
      </Form>
    </Row>
    <div id="map" style="height: 740px"></div>
  </Card>
</template>
<script>
  import "./projectMap.css";
  import {getAllProjects, getProjectDictData, locationPosition} from "../../../api/project";

  export default {
    data() {
      return {
        searchForm: {
          status: 0,
          title: '',
          num: '',
          subject: '',
          unit: '',
          type: '',
          build_type: '',
          money_from: '',
          is_gc: '',
          nep_type: ''
        },
        dict: {
          type: [],
          is_gc: [],
          nep_type: [],
          status: [],
          money_from: [],
          build_type: []
        },
        dictName: {
          type: '工程类项目分类',
          is_gc: '是否为国民经济计划',
          nep_type: '国民经济计划分类',
          status: '项目状态',
          money_from: '资金来源',
          build_type: '建设性质'
        },
        dropDownContent: '展开',
        drop: false,
        dropDownIcon: "ios-arrow-down",
        searchNepDisabled: true,
        positionLocation: [],
        overlays: []
      };
    },
    mounted() {
      this.init();
    },
    methods: {
      init() {
        console.log("初始化百度地图脚本...");
        const AK = "rdxXZeTCdtOAVL3DlNzYkXas9nR21KNu";
        const apiVersion = "3.0";
        const timestamp = new Date().getTime();
        const BMap_URL = "http://api.map.baidu.com/getscript?v=" + apiVersion + "&ak=" + AK + "&services=&t=" + timestamp;
        return new Promise((resolve, reject) => {
          // 插入script脚本
          let scriptNode = document.createElement("script");
          scriptNode.setAttribute("type", "text/javascript");
          scriptNode.setAttribute("src", BMap_URL);
          document.body.appendChild(scriptNode);

          // 等待页面加载完毕回调
          let timeout = 0;
          let interval = setInterval(() => {
            // 超时10秒加载失败
            if (timeout >= 20) {
              reject();
              clearInterval(interval);
              console.error("百度地图脚本初始化失败...");
              this.$Message.error('地图加载失败，请检查网络连接是否正常!');
            }
            // 加载成功
            if (typeof BMap !== "undefined") {
              resolve(BMap);
              clearInterval(interval);
              console.log("百度地图脚本初始化成功...");
              this.createMap();
            }
            timeout += 1;
          }, 500);
        });
      },
      loadStaticMapData(fileName, map) {
        let basePath = window.document.location.host;
        let _this = this;
        this.$http.get('http://' + basePath + '/assets/json/' + fileName).then(response => {
          if (fileName === 'xingzheng.geo.json') {
            let data = response.body.features[0];
            let polygonArr = data.geometry.coordinates[0];
            let polygon = '';
            polygonArr.forEach(function (e) {
              polygon += e.join(',') + ';';
            });
            let xingzheng = new BMap.Polygon(polygon, data.properties);
            map.addOverlay(xingzheng);
          } else if (fileName === 'luwang.geo.json') {
            let polylineArr = response.body.features;
            polylineArr.forEach(function (e) {
              let polyline = '';
              e.geometry.coordinates.forEach(function (el) {
                polyline += el.join(',') + ';';
              });
              let shizheng = new BMap.Polyline(polyline.substr(0, polyline.length - 1), e.properties);
              map.addOverlay(shizheng);
              _this.overlays.push(shizheng);
            });
          } else {
            let lvHuaArr = response.body.features;
            lvHuaArr.forEach(function (e) {
              let polygonArr = e.geometry.coordinates[0];
              let polygon = '';
              polygonArr.forEach(function (e) {
                polygon += e.join(',') + ';';
              });
              let lvhua = new BMap.Polygon(polygon, e.properties);
              map.addOverlay(lvhua);
              _this.overlays.push(lvhua);
            });
          }
        }, response => {
          this.$Message.error('路网数据读取失败!');
        });
      },
      getDictData() {
        getProjectDictData(this.dictName).then(res => {
          if (res.result) {
            this.dict = res.result;
          }
        });
      },
      onSearchIsGcChange(value) {
        this.searchNepDisabled = value !== 1;
        if (this.searchNepDisabled) {
          this.searchForm.nep_type = '';
        }
      },
      createMap() {
        let map = new BMap.Map("map", {enableMapClick: false, mapType: BMAP_HYBRID_MAP});
        map.centerAndZoom(new BMap.Point(108.720027, 34.298497), 14);
        map.enableScrollWheelZoom(true);// 开启鼠标滚动缩放
        map.addControl(new BMap.NavigationControl());
        map.addControl(new BMap.MapTypeControl({mapTypes: [BMAP_NORMAL_MAP, BMAP_HYBRID_MAP]}));

        let _this = this;

        // 添加路网切换
        function ZoomControl() {
          this.defaultAnchor = BMAP_ANCHOR_TOP_RIGHT;
          this.defaultOffset = new BMap.Size(100, 10);
        }

        ZoomControl.prototype = new BMap.Control();
        ZoomControl.prototype.initialize = function (map) {
          let select = document.createElement("select");
          select.setAttribute('id', 'vselect');
          select.options[0] = new Option("市政路网", '0');
          select.options[1] = new Option("绿化路网", '1');
          select.options[2] = new Option("全部路网", '2');
          select.style.cursor = "pointer";
          select.style.border = "1px solid gray";
          select.style.borderRadius = "3px";
          select.style.backgroundColor = "white";
          select.onchange = function (e) {
            let obj = document.getElementById('vselect');
            let index = obj.selectedIndex;
            let value = obj.options[index].value;
            for (let i = 0; i < _this.overlays.length; i++) {
              map.removeOverlay(_this.overlays[i]);
            }
            _this.overlays.length = 0;
            if (value === '1') {
              _this.loadStaticMapData('lvhua.geo.json', map);
            } else if (value === '0') {
              _this.loadStaticMapData('luwang.geo.json', map);
            } else {
              _this.loadStaticMapData('luwang.geo.json', map);
              _this.loadStaticMapData('lvhua.geo.json', map);
            }
          }
          map.getContainer().appendChild(select);

          return select;
        }
        let myZoomCtrl = new ZoomControl();
        map.addControl(myZoomCtrl);

        // 添加图例
        function LegendControl() {
          this.defaultAnchor = BMAP_ANCHOR_BOTTOM_RIGHT;
          this.defaultOffset = new BMap.Size(0, 0);
        }

        LegendControl.prototype = new BMap.Control();
        LegendControl.prototype.initialize = function (map) {
          let div = document.createElement("div");
          div.style.cursor = "pointer";
          div.style.width = "160px";
          div.style.height = "135px";
          // div.style.padding = "5px";
          div.style.border = "1px solid gray";
          div.style.borderRadius = "3px";
          div.style.backgroundColor = "white";

          let iframe = document.createElement('iframe');
          iframe.src = "http://localhost:5788/bMapLegend";
          iframe.style.border = "none";
          iframe.style.width = "170px";
          iframe.style.height = "175px";
          div.appendChild(iframe);

          map.getContainer().appendChild(div);

          return div;
        }
        let myLegendCtrl = new LegendControl();
        map.addControl(myLegendCtrl);

        // 获取数据字典数据
        this.getDictData();

        // 加载行政区划
        this.loadStaticMapData('xingzheng.geo.json', map);
        // 加载市政路网
        this.loadStaticMapData('luwang.geo.json', map);

        // 监听当前缩放级别
        // map.addEventListener("zoomend", function (e) {
        //   let ZoomNum = map.getZoom();
        //   console.log(ZoomNum);
        // });

        // 获取地图数据
        getAllProjects(this.searchForm).then(e => {
          let _this = this;
          let date = new Date();
          let y = date.getFullYear();
          let m = date.getMonth() + 1;
          let plan_amount;
          let last_month;
          let points = [];
          e.result.forEach(function (project) {
            // console.log('--------------------')
            // console.log(project)
            plan_amount = 0;
            last_month = 0;
            let war_color;
            let point_color;
            let warningColor;
            let Percentage_con;
            // console.log(project.title)
            project.projectPlan.forEach(function (year) {
              if (year.date === y) {
                let monthAmount = 0;
                year.month.forEach(function (month) {
                  if (month.date < m) {
                    monthAmount = month.amount;
                    // console.log(month.date + '月：' + monthAmount)
                    if (monthAmount !== null) {
                      monthAmount = monthAmount.replace(/,/g, "");
                      monthAmount = parseFloat(monthAmount);
                      plan_amount += monthAmount;
                    }
                    // console.log('1-' + month.date + '月合计：' + plan_amount)
                  }
                });
                // console.log("计划" + monthAmount)
                let month_act_complete = 0;
                if (project.scheduleInfo) {
                  month_act_complete = parseFloat(project.scheduleInfo.month_act_complete);
                } else {
                  month_act_complete = 0;
                }
                // console.log("实际完成" + month_act_complete)
                let Percentage = 0;
                if (month_act_complete !== 0) {
                  Percentage = month_act_complete / monthAmount;
                  if (isNaN(Percentage)) {
                    Percentage = 0;
                  }
                  // console.log(Percentage)
                }
                if (monthAmount == 0) {
                  Percentage = 1;
                }
                // console.log(Percentage)
                Percentage_con = '';
                Percentage = parseFloat(Percentage).toFixed(2);
                war_color = 'greencircle';
                point_color = '#4CAF50';
                warningColor = 'success';

                if (Percentage < 0.7) {
                  war_color = 'redcircle';
                  point_color = '#F44336';
                  warningColor = 'error';
                } else if (Percentage < 0.8 && Percentage >= 0.7) {
                  war_color = 'yellowcircle';
                  point_color = '#FF9800';
                  warningColor = 'warning';
                } else if (Percentage < 0.9 && Percentage >= 0.8) {
                  war_color = 'greencircle';
                  point_color = '#4CAF50';
                  warningColor = 'success';
                }

                Percentage_con = "计划完成：" + monthAmount + "万，已完成" + month_act_complete + "万，" + "完成率" + Percentage * 100 + "%";
              }
            });
            // console.log("当年月计划合计：" + plan_amount)

            if (project.is_audit === 1 || project.is_audit === 3) {
              // 添加标注
              let center = project.center_point;
              if (center !== null) {
                let iconName = 'default-' + warningColor;
                if (project.type === '绿化') {
                  iconName = 'lh-' + warningColor;
                } else if (project.type === '市政') {
                  iconName = 'sz-' + warningColor;
                } else if (project.type === '水利') {
                  iconName = 'sl-' + warningColor;
                } else if (project.type === '房建') {
                  iconName = 'fj-' + warningColor;
                }

                let centerPoint = JSON.parse(center).coordinates;
                let myIcon = new BMap.Icon("http://139.217.6.78:9000/storage/images/icon/" + iconName + ".png", new BMap.Size(21, 21));
                let marker = new BMap.Marker(new BMap.Point(centerPoint.lng, centerPoint.lat), {icon: myIcon});  // 创建标注
                points.push(marker.point);
                map.addOverlay(marker);
                let labell;
                // 添加多边形
                if (project.type === '绿化' || project.type === '市政' || project.type === '水利') {
                  let positions = JSON.parse(project.positions);
                  positions.forEach(function (e) {
                    let positionsPoints = e.coordinates;
                    let pointArr = [];
                    positionsPoints.forEach(function (el, i) {
                      pointArr[i] = new BMap.Point(el.lng, el.lat);
                    });
                    labell = new BMap.Label(project.title, {
                      position: _this.getCenterPoint(pointArr),
                    });
                    if (e.drawingMode === 'polygon') {
                      let polygon = new BMap.Polygon(pointArr, {
                        strokeOpacity: 0.01,
                        fillColor: point_color,
                        fillOpacity: 0.7,
                      });
                      map.addOverlay(polygon);
                      polygon.addEventListener('mouseover', function () {
                        map.addOverlay(labell);
                      });
                      polygon.addEventListener('mouseout', function () {
                        map.removeOverlay(labell);
                      })
                    } else {
                      let polyline = new BMap.Polyline(pointArr, {
                        strokeColor: point_color,
                        strokeWeight: 3,
                        strokeOpacity: 0.85,
                        fillColor: ''
                      });
                      polyline.addEventListener('mouseover', function (e) {
                        map.addOverlay(labell);
                      });
                      polyline.addEventListener('mouseout', function (e) {
                        map.removeOverlay(labell);
                      });
                      map.addOverlay(polyline);
                    }
                    points.push.apply(points, pointArr);
                  });
                }

                // 添加label
                let label = new BMap.Label(project.title, {
                  offset: new BMap.Size(25, 3)
                });
                label.setStyle({
                  display: "none",
                  border: "1px solid #2196F3"
                });
                marker.setLabel(label);
                marker.addEventListener("mouseover", function () {
                  label.setStyle({
                    display: "block"
                  });
                  map.removeOverlay(labell);
                });
                marker.addEventListener("mouseout", function () {
                  label.setStyle({
                    display: "none"
                  });
                  // map.addOverlay(labell);
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
                  "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>投资状态：<span class=" + statusColor + "></span><span class='project-status'>" + project.status + "</span></h5>" +
                  "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>投资概况：" + description + "</h5>" +
                  "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>投资进度：" + Percentage_con + "</h5>" +
                  "<a href='/#/projects/preview?id=" + project.id + "'>查看详情</a>";
                _this.addClickHandler(sContent, marker, map);
              }
            }
          });
          // this.setZoom(points, map);
        });
      },
      getCenterPoint(points) {
        let total = points.length;
        let X = 0, Y = 0, Z = 0;
        $.each(points, function (index, lnglat) {
          let lng = lnglat.lng * Math.PI / 180;
          let lat = lnglat.lat * Math.PI / 180;
          let x, y, z;
          x = Math.cos(lat) * Math.cos(lng);
          y = Math.cos(lat) * Math.sin(lng);
          z = Math.sin(lat);
          X += x;
          Y += y;
          Z += z;
        });

        X = X / total;
        Y = Y / total;
        Z = Z / total;

        let Lng = Math.atan2(Y, X);
        let Hyp = Math.sqrt(X * X + Y * Y);
        let Lat = Math.atan2(Z, Hyp);

        return {lng: Lng * 180 / Math.PI, lat: Lat * 180 / Math.PI};
      },
      handleResetSearch(name) {
        this.$refs[name].resetFields();
        this.createMap();
      },
      addClickHandler(content, marker, map) {
        let _this = this;
        marker.addEventListener("click", function (e) {
          _this.openInfo(content, e, map);
        });
      },
      openInfo(content, e, map) {
        let p = e.target;
        let point = new BMap.Point(p.getPosition().lng, p.getPosition().lat);
        let infoWindow = new BMap.InfoWindow(content); // 创建信息窗口对象
        map.openInfoWindow(infoWindow, point); //开启信息窗口
      },
      setZoom(bPoints, map) {
        let view = map.getViewport(eval(bPoints));
        let mapZoom = view.zoom;
        let centerPoint = view.center;
        map.centerAndZoom(centerPoint, mapZoom);
      },
      dropDown() {
        if (this.drop) {
          this.dropDownContent = "展开";
          this.dropDownIcon = "ios-arrow-down";
        } else {
          this.dropDownContent = "收起";
          this.dropDownIcon = "ios-arrow-up";
        }
        this.drop = !this.drop;
      },
    }
  };
</script>
