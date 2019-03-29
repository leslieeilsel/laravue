<template>
  <Card>
    <Row>
      <Form ref="searchForm" :model="searchForm" inline :label-width="70" class="search-form">
        <FormItem label="项目状态" prop="status">
          <Select v-model="searchForm.status" style="width: 200px">
            <!-- <Option value=0 key=0>全部</Option> -->
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
              <Option v-for="item in dict.type" :value="item.value" :key="item.value">{{ item.title }}</Option>
            </Select>
          </FormItem>
          <FormItem label="建设性质" prop="build_type">
            <Select v-model="searchForm.build_type" style="width: 200px" filterable>
              <Option v-for="item in dict.build_type" :value="item.value" :key="item.value">{{ item.title }}</Option>
            </Select>
          </FormItem>
          <FormItem label="资金来源">
            <Select v-model="searchForm.money_from" prop="money_from" style="width: 200px">
              <Option v-for="item in dict.money_from" :value="item.value" :key="item.value">{{ item.title }}</Option>
            </Select>
          </FormItem>
          <FormItem label="项目标识" prop="is_gc">
            <Select @on-change="onSearchIsGcChange" v-model="searchForm.is_gc" style="width: 200px"
                    placeholder="是否为国民经济计划">
              <Option v-for="item in dict.is_gc" :value="item.value" :key="item.value">{{item.title}}</Option>
            </Select>
          </FormItem>
          <FormItem label="国民经济计划分类" prop="nep_type">
            <Select v-model="searchForm.nep_type" style="width: 200px" :disabled="searchNepDisabled">
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
  import {getAllProjects, getProjectDictData} from "../../../api/project";

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
        let map = new BMap.Map("map", {enableMapClick: false});
        map.centerAndZoom(new BMap.Point(108.720027, 34.298497), 15);
        map.enableScrollWheelZoom(true);// 开启鼠标滚动缩放
        map.addControl(new BMap.NavigationControl());
        map.addControl(new BMap.MapTypeControl({mapTypes: [BMAP_NORMAL_MAP, BMAP_HYBRID_MAP]}));
        this.getDictData();
        let polygon = '108.61427307129,34.261189027715;108.61598968506,34.258919002446;108.61839294434,34.257500205546;108.62045288086,34.255797617679;108.62319946289,34.254378768137;108.62525939941,34.253527446926;108.6283493042,34.251257214919;108.63109588623,34.24983828881;108.63349914551,34.249270711667;108.63658905029,34.248419338776;108.63933563232,34.248419338776;108.63967895508,34.245297564482;108.6372756958,34.245297564482;108.63693237305,34.243027110447;108.63693237305,34.241608045574;108.63658905029,34.240472776451;108.63521575928,34.239053668516;108.63349914551,34.237350707418;108.63178253174,34.236215380881;108.62937927246,34.23479620118;108.62869262695,34.233093153964;108.62731933594,34.231957770017;108.62560272217,34.231957770017;108.62525939941,34.230254665393;108.62628936768,34.228551526324;108.62766265869,34.227416081143;108.62697601318,34.22599675314;108.62663269043,34.224009653753;108.62663269043,34.222306388396;108.62491607666,34.222306388396;108.62319946289,34.222874147342;108.62319946289,34.22088697429;108.62319946289,34.218899754362;108.62354278564,34.217196385687;108.62525939941,34.215209078706;108.62628936768,34.213221724853;108.62800598145,34.212653900858;108.6283493042,34.210666486742;108.6283493042,34.208395098933;108.6283493042,34.206691517903;108.6283493042,34.203568196586;108.62010955811,34.203000307549;108.61873626709,34.197605170898;108.61770629883,34.195901371807;108.6194229126,34.194481512932;108.62560272217,34.194197538288;108.62560272217,34.192493670342;108.62491607666,34.191073754089;108.6283493042,34.19192570671;108.63212585449,34.189937803874;108.63109588623,34.187665857545;108.63178253174,34.184541831417;108.63212585449,34.182837768401;108.63315582275,34.181701707266;108.63590240479,34.181985723984;108.63830566406,34.182269739746;108.64070892334,34.181417689592;108.64345550537,34.182269739746;108.64551544189,34.182269739746;108.64757537842,34.181985723984;108.64929199219,34.181417689592;108.65032196045,34.178577460266;108.6499786377,34.17516905887;108.6499786377,34.171760519803;108.6499786377,34.168067780462;108.66268157959,34.167783716898;108.66439819336,34.172328619208;108.66577148438,34.176021172126;108.66920471191,34.177157309749;108.67195129395,34.178009402928;108.67538452148,34.178577460266;108.67916107178,34.17914551378;108.68225097656,34.17971356347;108.67950439453,34.182837768401;108.6767578125,34.186245860012;108.67538452148,34.187949854183;108.67401123047,34.189937803874;108.67572784424,34.19192570671;108.67984771729,34.192493670342;108.68259429932,34.192209689004;108.68637084961,34.19078976797;108.6901473999,34.193345608618;108.69632720947,34.193345608618;108.69770050049,34.190505780894;108.6990737915,34.187665857545;108.70216369629,34.186813861893;108.70697021484,34.186813861893;108.71349334717,34.186529861431;108.71417999268,34.181417689592;108.71898651123,34.181133670962;108.71898651123,34.17914551378;108.72138977051,34.17658924285;108.72379302979,34.174885019206;108.72653961182,34.174885019206;108.72859954834,34.175453097579;108.72859954834,34.176873276778;108.73546600342,34.176305207966;108.73683929443,34.174600978586;108.73821258545,34.175737135331;108.73821258545,34.178009402928;108.7385559082,34.180281609335;108.74027252197,34.181417689592;108.74164581299,34.183121781294;108.74267578125,34.184825838573;108.74130249023,34.186245860012;108.74061584473,34.18851784459;108.73889923096,34.19078976797;108.73924255371,34.193061630149;108.73958587646,34.195333431125;108.73958587646,34.197889134066;108.7385559082,34.200444759541;108.73683929443,34.203000307549;108.73512268066,34.204420022968;108.73374938965,34.206407584384;108.73237609863,34.208111171152;108.73237609863,34.210382566614;108.73374938965,34.212086073036;108.73546600342,34.213789545023;108.73649597168,34.216060787437;108.73546600342,34.218048074329;108.73340606689,34.219467536268;108.73134613037,34.220603088599;108.7296295166,34.222306388396;108.73065948486,34.223725778585;108.73065948486,34.225712884669;108.72928619385,34.227416081143;108.72894287109,34.229403100164;108.72928619385,34.231390072303;108.72928619385,34.233376997559;108.72756958008,34.235363875931;108.72756958008,34.237350707418;108.72825622559,34.240188956778;108.72859954834,34.242175674394;108.72928619385,34.244446151396;108.72894287109,34.246148968955;108.72791290283,34.248419338776;108.72653961182,34.250405862125;108.72653961182,34.252392338579;108.72722625732,34.254094995357;108.72688293457,34.255513849685;108.72619628906,34.257500205546;108.72722625732,34.259202758955;108.72894287109,34.259770269101;108.73203277588,34.260054022738;108.7340927124,34.26062152714;108.73512268066,34.262891506464;108.73683929443,34.264310212432;108.7385559082,34.266012628005;108.74095916748,34.267998735952;108.7423324585,34.269984796992;108.74439239502,34.271403383302;108.74439239502,34.273673071617;108.74439239502,34.275658998631;108.74439239502,34.277644878734;108.74473571777,34.27991439855;108.74576568604,34.2816164982;108.7474822998,34.283034888245;108.74919891357,34.285587930004;108.74954223633,34.287573575531;108.74885559082,34.289275520066;108.7474822998,34.290693780847;108.74576568604,34.292679305721;108.74439239502,34.294664783672;108.743019104,34.296366584569;108.74267578125,34.298635598803;108.74336242676,34.300904551746;108.74439239502,34.302889835291;108.74576568604,34.304591469549;108.74542236328,34.306860261593;108.74542236328,34.308845404347;108.74473571777,34.310830500168;108.74542236328,34.313382697259;108.74473571777,34.315934816763;108.7423324585,34.318486858676;108.74027252197,34.319904626212;108.74027252197,34.322456547424;108.74130249023,34.324724856691;108.7423324585,34.326709577003;108.74370574951,34.329261291283;108.74507904053,34.331245904293;108.74645233154,34.333230470355;108.74816894531,34.334647988799;108.75022888184,34.334931489613;108.75297546387,34.334931489613;108.75537872314,34.334364487026;108.75846862793,34.333797480607;108.76087188721,34.33351397596;108.76327514648,34.33351397596;108.76499176025,34.334647988799;108.76670837402,34.336915968484;108.76773834229,34.339183886847;108.76876831055,34.341451743883;108.7691116333,34.343436068483;108.77048492432,34.345136880767;108.77117156982,34.346837658554;108.77220153809,34.348254947022;108.77323150635,34.350239110628;108.7735748291,34.352506668676;108.77323150635,34.354490731653;108.77082824707,34.355341029981;108.7691116333,34.35420729696;108.76670837402,34.353356987131;108.76499176025,34.351939784914;108.76396179199,34.350522558738;108.76258850098,34.348821855701;108.75949859619,34.347404576816;108.75537872314,34.346554197985;108.75194549561,34.345703810529;108.74919891357,34.343719539593;108.74645233154,34.34201869856;108.743019104,34.338616913005;108.73924255371,34.336348979312;108.73477935791,34.334080984296;108.73134613037,34.332663456271;108.72825622559,34.331529416605;108.72344970703,34.330111845464;108.71967315674,34.329261291283;108.7162399292,34.327843681819;108.71074676514,34.326142518847;108.7073135376,34.325008391038;108.7035369873,34.323590709721;108.70010375977,34.322456547424;108.69632720947,34.321322369798;108.69255065918,34.320188176845;108.68946075439,34.318486858676;108.68637084961,34.316501943894;108.68328094482,34.314800551007;108.68087768555,34.313099123636;108.67847442627,34.311397661782;108.67607116699,34.309412579371;108.67401123047,34.307143856288;108.6722946167,34.304307866234;108.67023468018,34.301471780404;108.66920471191,34.298635598803;108.6678314209,34.295232054469;108.66748809814,34.292112017688;108.66714477539,34.288991865038;108.66748809814,34.286438926689;108.66645812988,34.283885910782;108.66508483887,34.2816164982;108.6633682251,34.2790633358;108.65993499756,34.277928572062;108.65581512451,34.275942698661;108.65375518799,34.273673071617;108.65100860596,34.272538235118;108.64757537842,34.271687097692;108.64482879639,34.270552234388;108.64208221436,34.269417355767;108.63899230957,34.267998735952;108.63521575928,34.266580092205;108.63143920898,34.265445159976;108.62903594971,34.264310212432;108.62491607666,34.263742732917;108.62148284912,34.262607762399;108.61839294434,34.262324017376;108.6156463623,34.26175652446;108.61427307129,34.261189027715;'
        let ply = new BMap.Polygon(polygon, {strokeWeight: 2, strokeColor: "#ff0000"}); //建立多边形覆盖物
        ply.setFillOpacity(0.35);
        map.addOverlay(ply);  //添加覆盖物
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
                "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>投资状态：<span class=" + statusColor + "></span><span class='project-status'>" + project.status + "</span></h5>" +
                "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>投资概况：" + description + "</h5>" +
                "<h5 style='margin:0 0 5px 0;padding:0.2em 0'>投资进度：<span class='" + war_color + "'></span><span class='project-stage'>" + Percentage_con + "</span></h5>" +
                "<a href='http://www.baidu.com' target='blank'>查看详情</a>";
              _this.addClickHandler(sContent, marker, map);
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
