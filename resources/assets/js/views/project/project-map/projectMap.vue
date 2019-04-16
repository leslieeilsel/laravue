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
        positionLocation: []
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
        this.$http.get('http://' + basePath + '/assets/json/' + fileName).then(response => {
          if (fileName === 'xingzheng.geo.json') {
            let data = response.body.features[0];
            let polygonArr = data.geometry.coordinates[0];
            let polygon = '';
            polygonArr.forEach(function (e) {
              polygon += e.join(',') + ';';
            });
            let ply = new BMap.Polygon(polygon, data.properties);
            map.addOverlay(ply);
          } else {
            let polylineArr = response.body.features;
            polylineArr.forEach(function (e) {
              let polyline = '';
              e.geometry.coordinates.forEach(function (el) {
                polyline += el.join(',') + ';';
              });
              let poly = new BMap.Polyline(polyline.substr(0, polyline.length - 1), e.properties);
              map.addOverlay(poly);
            });
          }
        }, response => {
          this.$Message.error('据读取失败!');
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
        // enableMapClick: false 构造底图时，关闭底图可点功能
        let map = new BMap.Map("map", {enableMapClick: false, mapType: BMAP_HYBRID_MAP});
        map.centerAndZoom(new BMap.Point(108.720027, 34.298497), 15);
        map.enableScrollWheelZoom(true);// 开启鼠标滚动缩放
        map.addControl(new BMap.NavigationControl());
        map.addControl(new BMap.MapTypeControl({mapTypes: [BMAP_NORMAL_MAP, BMAP_HYBRID_MAP]}));
        this.getDictData();
        // 加载行政区划
        this.loadStaticMapData('xingzheng.geo.json', map);
        // 加载路网
        this.loadStaticMapData('luwang.geo.json', map);
        getAllProjects(this.searchForm).then(e => {
          let _this = this;
          let date = new Date();
          let y = date.getFullYear();
          let m = date.getMonth();
          let plan_amount = 0;
          let points = [];
          e.result.forEach(function (project) {
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
              if (center !== null) {
                let centerArr = center.split(",");

                let marker = new BMap.Marker(new BMap.Point(centerArr[0], centerArr[1]), {
                  // 指定Marker的icon属性为Symbol
                  icon: new BMap.Symbol(BMap_Symbol_SHAPE_POINT, {
                    scale: 1.2,//图标缩放大小
                    fillColor: point_color,//填充颜色
                    fillOpacity: 1//填充透明度
                  })
                });
                points.push(marker.point);
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
                  strokeOpacity: 0.5,
                  fillColor: ''
                });
                map.addOverlay(polygon);
                // let center = project.center_point;
                // let positions = project.positions;
                // let marker;
                // locationPosition({center:center,positions:positions}).then(res=>{
                //   marker = new BMap.Marker(new BMap.Point(res.result.center[0].lng, res.result.center[0].lat), {
                //     // 指定Marker的icon属性为Symbol
                //     icon: new BMap.Symbol(BMap_Symbol_SHAPE_POINT, {
                //       scale: 1.2,//图标缩放大小
                //       fillColor: point_color,//填充颜色
                //       fillOpacity: 1//填充透明度
                //     })
                //   });
                //   points.push(marker.point);
                //   map.addOverlay(marker);
                //   //添加多边形
                //     let pointArr1 = [];
                //     res.result.positions.forEach(function (e, i) {
                //       pointArr1[i] = new BMap.Point(e.lng,e.lat);
                //     });
                //     let polygon = new BMap.Polygon(pointArr1, {
                //       strokeColor: "blue",
                //       strokeWeight: 2,
                //       strokeOpacity: 0.5
                //     });
                //     map.addOverlay(polygon);

                // 添加label
                let label = new BMap.Label(project.title, {
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
                  "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>投资状态：<span class=" + statusColor + "></span><span class='project-status'>" + project.status + "</span></h5>" +
                  "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>投资概况：" + description + "</h5>" +
                  "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>投资进度：计划完成:1000万,累计额完成:350万,完成比:35%</h5>" +
                  "<a href='/#/investment/projects'>查看详情</a>";
                _this.addClickHandler(sContent, marker, map);
              }
              // })
            }
          });
          this.setZoom(points, map);
        });
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
