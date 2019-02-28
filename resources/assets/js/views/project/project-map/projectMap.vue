<template>
  <Card>
    <div id="map" style="height: 800px"></div>
  </Card>
</template>
<script>
import "./projectMap.css";
import { getAllProjects } from "../../../api/project";

export default {
  data() {
    return {
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
    createMap() {
      // 百度地图API功能
      let map = new BMap.Map("map", {minZoom: 13, maxZoom: 16}, {enableMapClick: false});// 构造底图时，关闭底图可点功能
      map.centerAndZoom(new BMap.Point(108.720027, 34.298497), 15);
      map.enableScrollWheelZoom(true);// 开启鼠标滚动缩放
      getAllProjects().then(e => {
        let _this = this;
        e.result.forEach(function(project) {
          // 添加标注
          let center = project.center_point;
          let centerArr = center.split(",");
          let marker = new BMap.Marker(
            new BMap.Point(centerArr[0], centerArr[1])
          );
          map.addOverlay(marker);
          // 添加多边形
          let positions = project.positions;
          let positionsArr = positions.split(";");
          let pointArr = [];
          positionsArr.forEach(function(e, i) {
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
          var sContent =
            "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>项目名称：" + project.title + "</h5>" +
            "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>项目类型：" + project.type + "</h5>" +
            "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>投资状态：<span class=" + statusColor + "></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>" + project.status + "</span></h5>" +
            "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>投资概况：" + project.description + "</h5>" +
            "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>投资进度：<span class='redcircle'></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>已完成53万，比预期延缓10%</span></h5>" +
            "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>创建时间：" + project.created_at + "</h5>";
          _this.addClickHandler(sContent, marker, map);
        });
      });
    },
    addClickHandler(content, marker, map) {
      let _this = this;
      marker.addEventListener("click", function(e) {
        _this.openInfo(content, e, map);
      });
    },
    openInfo(content, e, map) {
      let p = e.target;
      let point = new BMap.Point(p.getPosition().lng, p.getPosition().lat);
      let infoWindow = new BMap.InfoWindow(content); // 创建信息窗口对象
      map.openInfoWindow(infoWindow, point); //开启信息窗口
    }
  }
};
</script>
