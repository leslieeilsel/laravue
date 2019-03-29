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
        let polygon = '108.62517316456,34.265751768515;108.62690180941,34.26345772659;108.62932106806,34.262003356365;108.63139595822,34.260269096061;108.63416217973,34.258806680023;108.63623729029,34.257922481029;108.63935077934,34.255602953814;108.64211786001,34.254141257635;108.64453822074,34.25353839176;108.64764946502,34.252645092428;108.65041349677,34.252611860492;108.65076106534,34.249485372282;108.64834268388,34.249513806553;108.64799830736,34.24724747527;108.64799905942,34.245827899473;108.64765381817,34.244696885783;108.64627193409,34.243295720108;108.64454412874,34.241616156643;108.64281571422,34.240505612941;108.64039574417,34.239122263857;108.63970437976,34.23742949682;108.63832150392,34.236315360593;108.63659243565,34.236342763933;108.6362468463,34.234644512193;108.63728355189,34.232924380725;108.63866630684,34.231766472154;108.63797432963,34.230357417794;108.63762757057,34.228374947544;108.63762684087,34.226670887199;108.63589803674,34.226698317832;108.63416979038,34.227293773244;108.63416882177,34.225305349085;108.63416740587,34.223317145835;108.63451210034,34.22160740555;108.63623890849,34.219591463655;108.63727451302,34.217586336327;108.63900253955,34.216991101956;108.63934634678,34.21499690541;108.63934399652,34.212724120519;108.63934237525,34.211019508604;108.6393390361,34.207894090569;108.63104180505,34.207456594946;108.6296538402,34.202078957574;108.62861569902,34.200389297902;108.63034173299,34.198942769805;108.63656312192,34.198561088297;108.6365615777,34.196856229548;108.63586871504,34.195446408048;108.6393269944,34.196243980417;108.6431280789,34.194197444789;108.64208913684,34.191939572056;108.64277844297,34.188803531453;108.6431226014,34.18709358258;108.64415906088,34.185942012792;108.6469243648,34.186188074796;108.6493434409,34.186441925451;108.65176119324,34.185562734632;108.65452372355,34.186388926275;108.65659465283,34.186373248031;108.65866385122,34.186076536581;108.66038781894,34.185500522634;108.66142039971,34.182655355277;108.66107471532,34.179246853772;108.66107410282,34.175836920853;108.66107414616,34.172143448827;108.67380608541,34.171891826153;108.67552340262,34.17645347777;108.67689783336,34.180161288101;108.68032962947,34.181338838573;108.68307327782,34.182230132797;108.68650079085,34.182852923055;108.69026941735,34.183487385045;108.69335140782,34.184112697;108.69061365251,34.187187635169;108.68787614022,34.190548306107;108.68650695068,34.192230154463;108.68513809525,34.194196434898;108.68685311248,34.196213885652;108.69096407854,34.196855065301;108.69370331236,34.196621706164;108.69746816236,34.195272483527;108.70123696054,34.197900755087;108.70740181775,34.198007849251;108.70876969269,34.195187785724;108.71013784825,34.192366771909;108.71322297701,34.191556246873;108.71802608064,34.191607184243;108.72455339873,34.191361384916;108.72523796374,34.186248480524;108.73005480102,34.185964705559;108.73005366578,34.183975772376;108.73246359577,34.181410344898;108.73487498541,34.179692742827;108.73763319257,34.17967228625;108.73970292029,34.180221158891;108.73970326175,34.181641622901;108.74660632109,34.180988418916;108.747987304,34.179263638723;108.74936908142,34.180379296589;108.74936998513,34.182652168952;108.74971656501,34.184920172822;108.7514440966,34.186029531627;108.7528269825,34.187712126843;108.75386456766,34.189399721224;108.75248373885,34.190843501683;108.75179470117,34.193127872495;108.75006977234,34.195428702336;108.75041720679,34.197696444055;108.75076503938,34.199964184649;108.75076777981,34.202521380432;108.74973424843,34.205095010374;108.74801008022,34.207678474023;108.74628485176,34.209123841632;108.7449057837,34.211131813574;108.74352669859,34.212854707898;108.74352904922,34.215127494998;108.74491150924,34.21681404896;108.74663948739,34.218494532368;108.74767719504,34.220752195241;108.74664284005,34.222755753879;108.74457219983,34.22420442806;108.74250174771,34.225366527563;108.74077724769,34.227090498789;108.74181337151,34.228498958893;108.74181405306,34.230486750989;108.74043428834,34.232206223013;108.74008967995,34.234197513196;108.74043497708,34.236181777682;108.74043514851,34.238169320542;108.73870995594,34.240174070978;108.73870971956,34.242161485887;108.73939858568,34.244993845648;108.73974302665,34.246977657274;108.74043178876,34.249241410619;108.74008573737,34.250947958416;108.73904945326,34.253229386269;108.73766819487,34.255228562222;108.73766674642,34.257215324111;108.73835496779,34.258911972775;108.73800914055,34.260333939526;108.73731722896,34.262326348933;108.73835057086,34.264020278083;108.74007476604,34.264571002531;108.74318043784,34.264819394156;108.7452514853,34.265360092951;108.74628527811,34.26761576922;108.74801051611,34.269009752224;108.74973585081,34.270685726578;108.75215194952,34.272633887988;108.75353216612,34.274597289623;108.75560385258,34.275981951343;108.7556019584,34.278251862104;108.7556007049,34.280238138488;108.75559959122,34.282224458888;108.7559434695,34.284488664788;108.75697893452,34.286173790477;108.75870544551,34.287563321142;108.76043131121,34.290088597454;108.76077659168,34.292069189885;108.76008585679,34.293783336775;108.7587041953,34.29522523287;108.75697760528,34.297240443928;108.75559646993,34.299250200945;108.75421494133,34.300975641198;108.75387066268,34.303251915307;108.75456269001,34.305510523283;108.75560007812,34.307479780797;108.75698274329,34.309159107965;108.75663901166,34.311435425294;108.75664078758,34.313421842967;108.7559518666,34.315420036842;108.7566449408,34.317962178906;108.7559567805,34.320528104411;108.75354157985,34.323122124581;108.75147048426,34.324574922822;108.75147302963,34.327128623895;108.75251204442,34.329382168742;108.75355025793,34.33135140868;108.75493452526,34.333882023261;108.75631848532,34.33584507716;108.75770180433,34.337807819576;108.75942979909,34.339197736852;108.76150198075,34.339447100425;108.76426364575,34.339402485275;108.76667902384,34.338798195662;108.76978249395,34.338186804955;108.77219513345,34.337872265862;108.77460654023,34.337845037794;108.77632910753,34.338962806517;108.77805145287,34.34121779081;108.77908518862,34.34347939505;108.78011816062,34.34574153757;108.78046308908,34.347725046778;108.78183868661,34.349419192963;108.78252659725,34.351118030073;108.7835573793,34.352531941966;108.78458807852,34.354513990095;108.78493169396,34.356781757239;108.78458814958,34.358767666554;108.78218401235,34.359627129594;108.78046563777,34.358502133053;108.7780579886,34.357668513786;108.77633738878,34.356265315477;108.77530439669,34.354857358828;108.77392673418,34.353170240889;108.77082492632,34.351789077356;108.76668662276,34.35099586858;108.76323485848,34.350198401268;108.76047199556,34.348257886681;108.75770809937,34.346602215237;108.75425186501,34.343256083456;108.75045036825,34.341048052674;108.74595880121,34.33884558554;108.74250530238,34.33747162978;108.73939851198,34.336371082248;108.73456948202,34.334990951807;108.73077959797,34.334156436426;108.72733577431,34.33274187535;108.72183267295,34.331023446547;108.71839707433,34.329864509381;108.71462077748,34.328408188283;108.71119039399,34.327229532024;108.70741964322,34.326037785482;108.70365054327,34.324838821548;108.70056693641,34.323080108957;108.69748338221,34.321035749882;108.69440028529,34.31927452354;108.69200182849,34.317526989366;108.68960305174,34.315780419899;108.6872032702,34.313751892442;108.68514534645,34.311447400497;108.68342938577,34.308582828085;108.68137029568,34.305714802934;108.68033986656,34.302863335092;108.6789663799,34.299440227596;108.67862289291,34.29631459155;108.67827934618,34.29318919608;108.67862300298,34.290639341031;108.67759400593,34.288073708095;108.67622160253,34.285788971262;108.67450593042,34.283218871066;108.67107017332,34.282058339139;108.6669437624,34.280055810298;108.66487994774,34.277783313124;108.66212486218,34.276650768238;108.65867813166,34.275812470813;108.65591911826,34.274694858006;108.65315832692,34.273583260154;108.65005138266,34.272196780189;108.64625158913,34.27082546746;108.64245013342,34.269744829283;108.64003092676,34.26864648809;108.63588232766,34.268144377259;108.63242653334,34.267063839835;108.62931654772,34.266827688505;108.62655366893,34.266300225671;108.62517316456,34.265751768515;';
        let ply = new BMap.Polygon(polygon, {strokeWeight: 2}); //建立多边形覆盖物
        ply.setFillOpacity(0.01);
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
